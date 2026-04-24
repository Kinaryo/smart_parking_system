#include <ESP8266WiFi.h>
#include <WebSocketsClient.h>
#include <ArduinoJson.h>
#include <Servo.h>

// =====================
// WIFI
// =====================
const char* ssid = "NAMA_WIFI_KAMU";
const char* password = "PASSWORD_WIFI_KAMU";

// =====================
// PUSHER
// =====================
const char* pusherKey  = "932058cc5ddfa4033d8b";
const char* pusherHost = "ws-ap1.pusher.com";
const char* channel    = "slot-tracker";

// =====================
// HARDWARE
// =====================
const int pinGateMasuk = D5;
const int pinGateKeluar = D6;
const int pinSensorSlot = D2;

Servo gateMasuk;
Servo gateKeluar;

WebSocketsClient webSocket;

// =====================
// SLOT STATE
// =====================
bool lastSlotState = false;
unsigned long lastDebounceTime = 0;
const unsigned long debounceDelay = 1000;

// =====================
// SEND SLOT UPDATE KE PUSHER
// =====================
void sendSlotUpdate(bool occupied) {

  StaticJsonDocument<256> doc;
  doc["event"] = "client-slot-update";

  JsonObject data = doc.createNestedObject("data");
  data["slot"] = "A1";
  data["status"] = occupied ? "terisi" : "kosong";
  data["device"] = "esp8266";

  String output;
  serializeJson(doc, output);

  webSocket.sendTXT(output);

  Serial.println("[WS] SLOT UPDATE SENT:");
  Serial.println(output);
}

// =====================
// HANDLE COMMAND DARI LARAVEL
// =====================
void handleCommand(String command, JsonObject payload) {

  if (command == "OPEN_GATE_ENTRY") {
    Serial.println("[CMD] OPEN ENTRY");
    gateMasuk.write(90);
    delay(2500);
    gateMasuk.write(0);
  }

  else if (command == "OPEN_GATE_EXIT") {
    Serial.println("[CMD] OPEN EXIT");
    gateKeluar.write(90);
    delay(2500);
    gateKeluar.write(0);
  }

  else if (command == "UPDATE_DISPLAY_QR") {
    const char* qr = payload["qr_string"] | "";
    Serial.print("[QR] ");
    Serial.println(qr);
  }

  else if (command == "SYNC_INITIAL_QR") {
    const char* qr = payload["qr_string"] | "";
    Serial.print("[INIT QR] ");
    Serial.println(qr);
  }

  else if (command == "SLOT_UPDATE") {
    Serial.println("[CMD] SLOT UPDATE FROM SERVER");
  }
}

// =====================
// WEBSOCKET EVENT
// =====================
void onWebSocketEvent(WStype_t type, uint8_t * payload, size_t length) {

  switch(type) {

    case WStype_CONNECTED:
      Serial.println("[WS] CONNECTED");

      webSocket.sendTXT(
        String("{\"event\":\"pusher:subscribe\",\"data\":{\"channel\":\"") +
        channel + "\"}}"
      );
      break;

    case WStype_DISCONNECTED:
      Serial.println("[WS] DISCONNECTED");
      break;

    case WStype_TEXT: {

      StaticJsonDocument<1024> doc;
      DeserializationError err = deserializeJson(doc, payload, length);

      if (err) {
        Serial.println("[WS] JSON ERROR");
        return;
      }

      String event = doc["event"] | "";

      if (event == "EspHardwareCommand") {

        StaticJsonDocument<512> data;
        deserializeJson(data, doc["data"]);

        String command = data["command"] | "";
        JsonObject payloadObj = data["payload"];

        handleCommand(command, payloadObj);
      }

      break;
    }

    default:
      break;
  }
}

// =====================
// SETUP
// =====================
void setup() {

  Serial.begin(115200);

  gateMasuk.attach(pinGateMasuk);
  gateKeluar.attach(pinGateKeluar);

  gateMasuk.write(0);
  gateKeluar.write(0);

  pinMode(pinSensorSlot, INPUT_PULLUP);

  // WIFI CONNECT
  WiFi.begin(ssid, password);

  Serial.print("Connecting WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi Connected");

  // PUSHER PATH
  String path = "/app/" + String(pusherKey) +
                "?protocol=7&client=js&version=4.3.1";

  webSocket.begin(pusherHost, 80, path);
  webSocket.onEvent(onWebSocketEvent);
  webSocket.setReconnectInterval(5000);

  Serial.println("[SYSTEM] READY");
}

// =====================
// LOOP SENSOR
// =====================
void loop() {

  webSocket.loop();

  bool currentState = (digitalRead(pinSensorSlot) == LOW);

  if (currentState != lastSlotState) {

    if (millis() - lastDebounceTime > debounceDelay) {

      lastDebounceTime = millis();
      lastSlotState = currentState;

      Serial.println(currentState ? "[SLOT] TERISI" : "[SLOT] KOSONG");

      // kirim ke pusher (REALTIME)
      sendSlotUpdate(currentState);
    }
  }
}