<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $nama_gate
 * @property string $tipe_gate
 * @property string $qr_code
 * @property string $mqtt_topic_command
 * @property string $mqtt_topic_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereMqttTopicCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereMqttTopicStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereNamaGate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereTipeGate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gate whereUpdatedAt($value)
 */
	class Gate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property string|null $lokasi
 * @property int $kapasitas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SlotParkir> $slotParkirs
 * @property-read int|null $slot_parkirs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir whereKapasitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir whereLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantungParkir whereUpdatedAt($value)
 */
	class KantungParkir extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $jenis
 * @property string $plat_nomor
 * @property string|null $merk
 * @property string|null $warna
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereMerk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan wherePlatNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kendaraan whereWarna($value)
 */
	class Kendaraan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $topic
 * @property array<array-key, mixed> $payload
 * @property string $direction
 * @property string|null $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog whereTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MqttLog whereUpdatedAt($value)
 */
	class MqttLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $kendaraan_id
 * @property int|null $slot_parkir_id
 * @property int $gate_masuk_id
 * @property int|null $gate_keluar_id
 * @property string $waktu_masuk
 * @property string|null $waktu_keluar
 * @property int|null $total_waktu
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kendaraan $kendaraan
 * @property-read \App\Models\SlotParkir|null $slotParkir
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereGateKeluarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereGateMasukId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereKendaraanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereSlotParkirId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereTotalWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereWaktuKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParkirTransaksi whereWaktuMasuk($value)
 */
	class ParkirTransaksi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $kantung_parkir_id
 * @property string $kode_slot
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KantungParkir $kantungParkir
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir whereKantungParkirId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir whereKodeSlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SlotParkir whereUpdatedAt($value)
 */
	class SlotParkir extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $no_hp
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

