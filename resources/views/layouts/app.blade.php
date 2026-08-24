<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'AlphaKids')</title>

    @include('partials.pwa-head')

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { screens: { sm: '1024px' } } } };
    </script>
    <script>
        // Force mobile layout on phones even when "Desktop Site" mode is active
        (function() {
            var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            var isPhoneScreen = window.screen.width <= 430 || window.screen.height <= 932;
            if (isTouchDevice && isPhoneScreen && window.innerWidth >= 1024) {
                var meta = document.querySelector('meta[name="viewport"]');
                if (meta) meta.content = 'width=430, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                var s = document.createElement('style');
                s.textContent = '.phone-wrapper{min-height:100vh!important;display:block!important;padding:0!important;background:#F0EDFB!important}.phone-frame{min-height:100vh!important;width:100%!important;border-radius:0!important;box-shadow:none!important}';
                document.head.appendChild(s);
            }
        })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    @include('partials.api-cache')

    @stack('styles')

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Nunito', sans-serif; }

        /* Phone frame for desktop */
        @media (min-width: 1024px) {
            .phone-wrapper {
                display: flex;
                align-items: flex-start;
                justify-content: center;
                min-height: 100vh;
                padding: 32px 0 60px;
                background: #E5E2F5;
            }
            .phone-frame {
                width: 390px;
                min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(124,58,237,0.28), 0 0 0 8px #1a1030, 0 0 0 10px #2d1a50;
                overflow: hidden;
                position: relative;
            }
        }
        @media (max-width: 1023px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame  { min-height: 100vh; }
        }

        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.30s; }
        .delay-5 { animation-delay: 0.38s; }

        /* Hide scrollbar */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="phone-wrapper">
<div id="phoneFrame" class="phone-frame bg-[#F0EDFB] min-h-screen flex flex-col relative">

    @include('components.status-bar')

    @yield('content')

    @if(!isset($hideBottomNav) || !$hideBottomNav)
        @include('partials.bottom-nav', ['active' => $activeNav ?? 'home'])
    @endif

</div>
</div>

@stack('modals')

{{-- In-app alert toast (global). Pakai showAppAlert(msg, 'ok'|'error') ganti alert() native --}}
<div id="appAlertMount" style="display:none;position:fixed;top:14px;left:16px;right:16px;z-index:90;justify-content:center;pointer-events:none;">
    <div id="appAlertBox" style="display:flex;align-items:flex-start;gap:10px;width:100%;max-width:380px;padding:13px 14px;border-radius:16px;background:#fff;border:1px solid #FEE2E2;box-shadow:0 10px 30px rgba(30,27,46,.18);transition:transform .25s ease,opacity .25s ease;transform:translateY(-10px);opacity:0;">
        <ion-icon id="appAlertIcon" name="alert-circle-outline" style="font-size:20px;flex-shrink:0;color:#DC2626;margin-top:1px;"></ion-icon>
        <div id="appAlertText" style="flex:1;font-size:13px;font-weight:700;color:#1E1B2E;line-height:1.5;word-break:break-word;"></div>
        <button id="appAlertClose" type="button" aria-label="Tutup" style="flex-shrink:0;background:none;border:none;cursor:pointer;padding:2px;color:#9CA3AF;line-height:0;">
            <ion-icon name="close" style="font-size:16px;"></ion-icon>
        </button>
    </div>
</div>

<script>
    // ── Reverse geocode: tampilkan nama tempat dari koordinat ──────────────
    // Elemen dengan data-geo="lat,lng" menampilkan nama tempat (fallback: koordinat).
    // Pakai cache agar tidak memanggil Nominatim berulang utk koordinat sama.
    (function () {
        const cache = {};   // "lat,lng" -> nama tempat (string)
        let inflight = {};  // "lat,lng" -> [handlers]

        function resolve(lat, lng, cb) {
            const key = lat.toFixed(6) + ',' + lng.toFixed(6);
            if (cache[key]) { cb(cache[key]); return; }
            (inflight[key] = inflight[key] || []).push(cb);
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=16', {
                headers: { 'Accept-Language': 'id' }
            }).then(r => r.json()).then(geo => {
                const name = (geo && geo.display_name) || key;
                cache[key] = name;
                (inflight[key] || []).forEach(f => f(name));
                delete inflight[key];
            }).catch(() => {
                cache[key] = key;
                (inflight[key] || []).forEach(f => f(key));
                delete inflight[key];
            });
        }

        // Ganti konten semua [data-geo="lat,lng"] di dalam container (default document).
        window.resolveGeoPlaces = function (container) {
            (container || document).querySelectorAll('[data-geo]').forEach(el => {
                const raw = (el.getAttribute('data-geo') || '').split(',');
                const lat = parseFloat(raw[0]), lng = parseFloat(raw[1]);
                if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) { el.textContent = '—'; return; }
                el.textContent = 'Mencari lokasi…';
                resolve(lat, lng, name => { el.textContent = name; });
            });
        };
    })();

    // Auto-resolve setelah DOM siap.
    document.addEventListener('DOMContentLoaded', function () { window.resolveGeoPlaces(); });
</script>

<script>
    // Global in-app alert (pengganti alert() native). type: 'ok' (hijau) | 'error' (merah, default).
    window.showAppAlert = function (msg, type) {
        const mount = document.getElementById('appAlertMount');
        if (!mount) return;
        const box   = document.getElementById('appAlertBox');
        const icon  = document.getElementById('appAlertIcon');
        const text  = document.getElementById('appAlertText');
        const ok    = type === 'ok';

        box.style.borderColor = ok ? '#BBF7D0' : '#FEE2E2';
        icon.setAttribute('name', ok ? 'checkmark-circle-outline' : 'alert-circle-outline');
        icon.style.color = ok ? '#16A34A' : '#DC2626';
        text.textContent = msg;

        mount.style.display = 'flex';
        requestAnimationFrame(() => { box.style.transform = 'translateY(0)'; box.style.opacity = '1'; });

        const autoClose = () => { box.style.transform = 'translateY(-10px)'; box.style.opacity = '0'; setTimeout(() => { mount.style.display = 'none'; }, 260); };
        const timer = setTimeout(autoClose, 4000);

        const closeBtn = document.getElementById('appAlertClose');
        closeBtn.onclick = null; // reset listener
        closeBtn.onclick = () => { clearTimeout(timer); autoClose(); };
    };
</script>

<script>
    // Status bar clock
    (function() {
        const el = document.getElementById('statusTime');
        function tick() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2,'0');
            const m = String(now.getMinutes()).padStart(2,'0');
            if (el) el.textContent = `${h}:${m}`;
        }
        tick();
        setInterval(tick, 30000);
    })();
</script>

<script>
    // ── Online Status Heartbeat ────────────────────────────────────────────────
    (function() {
        const PING_URL = '{{ route("api.user.ping") }}';
        const OFFLINE_URL = '{{ route("api.user.offline") }}';
        const PING_INTERVAL = 120000; // 2 menit

        function sendPing() {
            fetch(PING_URL, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                keepalive: true,
            }).catch(function() {});
        }

        function sendOffline() {
            try {
                navigator.sendBeacon(OFFLINE_URL, new URLSearchParams({
                    '_token': '{{ csrf_token() }}'
                }));
            } catch(e) {
                fetch(OFFLINE_URL, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    keepalive: true,
                }).catch(function() {});
            }
        }

        // Ping saat halaman pertama kali dimuat
        sendPing();

        // Ping periodik
        setInterval(sendPing, PING_INTERVAL);

        // Deteksi visibility change — offline saat tab tersembunyi, online saat kembali
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                sendOffline();
            } else {
                sendPing();
            }
        });

        // Deteksi beforeunload — offline saat tab ditutup
        window.addEventListener('beforeunload', function() {
            sendOffline();
        });

        // Deteksi online/offline koneksi — ping saat koneksi kembali
        window.addEventListener('online', function() {
            sendPing();
        });
    })();
</script>

@stack('scripts')

@include('partials.auth-guard')

</body>
</html>
