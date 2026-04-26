<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'QR Gate Parkir - Smart Parking')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0061ff">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('partials.css.petugas-style')
    @stack('styles')

    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.9);
            --accent-color: #38bdf8;
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(56, 189, 248, 0.05) 0px, transparent 50%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }

        .card-box {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 100%;
            max-width: 420px;
            max-height: 95vh;
            padding: 2rem 1.5rem;
            border-radius: 32px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.5rem;
        }

        .title {
            font-weight: 800;
            letter-spacing: -0.02em;
            font-size: clamp(1.25rem, 5vw, 1.75rem);
            margin: 0;
        }

        .instruction-text {
            color: #94a3b8;
            font-size: clamp(0.8rem, 3vw, 0.9rem);
            margin: 0;
        }

        .qr-container {
            background: white;
            padding: 15px;
            border-radius: 24px;
            display: inline-block;
            align-self: center;
            box-shadow: 0 0 40px rgba(56, 189, 248, 0.1);
            min-height: 250px;
            min-width: 250px;
        }

        .qr-container svg, .qr-container img {
            width: 60vw !important;
            height: 60vw !important;
            max-width: 250px !important;
            max-height: 250px !important;
            display: block;
        }

        .kode-display {
            font-family: 'Monaco', monospace;
            font-weight: 700;
            letter-spacing: 3px;
            font-size: 1rem;
            color: var(--accent-color);
            background: rgba(56, 189, 248, 0.1);
            padding: 10px 20px;
            border-radius: 12px;
            display: inline-block;
        }

        .status-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
            100% { transform: scale(1); opacity: 1; }
        }

        .status-text {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="card-box">
        <div>
            <h1 class="title">QR Gate Parkir</h1>
            <p class="instruction-text">Arahkan kamera ponsel Anda ke kode QR di bawah ini untuk membuka palang pintu secara otomatis</p>
        </div>

        <div class="qr-container" id="qr-box">
            {!! QrCode::size(250)->margin(1)->generate($qr->kode) !!}
        </div>

        <div>
            <div class="kode-display" id="kode">
                {{ $qr->kode }}
            </div>
        </div>

        <div class="status-container">
            <div class="status-dot"></div>
            <div class="status-text" id="status">
                {{ $qr->status }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @include('partials.scripts.main')
    @stack('scripts')

    <script>
        const QR_URL = "/ajax-qr-show";
        let lastKode = "{{ $qr->kode }}";

        async function loadQR() {
            try {
                const res = await fetch(QR_URL);
                const data = await res.json();
                
                if (data.success) {
                    if (data.kode !== lastKode) {
                        document.getElementById("qr-box").innerHTML = data.svg;
                        document.getElementById("kode").innerText = data.kode;
                        lastKode = data.kode;
                    }
                    document.getElementById("status").innerText = data.status;
                }
            } catch (e) { 
                console.error("Gagal polling data QR:", e); 
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            setInterval(loadQR, 3000);
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(function (registration) {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(function (error) {
                        console.log('SW failed:', error);
                    });
            });
        }
    </script>
</body>

</html>