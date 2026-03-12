{{-- resources/views/partials/pwa-head.blade.php --}}
{{-- Include di dalam <head> di SEMUA halaman: @include('partials.pwa-head') --}}

<link rel="manifest" href="/manifest.json">

<meta name="theme-color" content="#7B1E5A">
<meta name="msapplication-TileColor" content="#7B1E5A">
<meta name="msapplication-TileImage" content="/icons/icon-192x192.png">

<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="NannyApp">
<meta name="application-name" content="NannyApp">

<link rel="apple-touch-icon" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="72x72"   href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="96x96"   href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="128x128" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="144x144" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

<link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-192x192.png">
<link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-192x192.png">

{{-- Firebase SDK --}}
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>

<script>
const FIREBASE_CONFIG = {
    apiKey           : "{{ config('services.firebase.api_key') }}",
    authDomain       : "{{ config('services.firebase.auth_domain') }}",
    projectId        : "{{ config('services.firebase.project_id') }}",
    storageBucket    : "{{ config('services.firebase.storage_bucket') }}",
    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
    appId            : "{{ config('services.firebase.app_id') }}"
};

const VAPID_KEY  = "BLIPLi-whuk0HS_Afbafommm_oXpkhACm22IfFG6U1gusVhNCrTZ9IDcQZm-U77Gw76HnkeVziEnNB__cu7Nmus";
const CSRF_TOKEN = "{{ csrf_token() }}";
const IS_AUTH    = {{ session('token') ? 'true' : 'false' }};

// ── Register Service Worker ────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {

        Promise.all([
            navigator.serviceWorker.register('/sw.js', { scope: '/' }),
            navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' }),
        ]).then(function (regs) {
            var mainReg = regs[0];

            // ⚠️ TIDAK pakai mainReg.update() — itu yang menyebabkan
            // updatefound selalu terpicu setiap page load

            mainReg.addEventListener('updatefound', function () {
                var nw = mainReg.installing;
                if (!nw) return;

                nw.addEventListener('statechange', function () {
                    if (
                        nw.state === 'installed' &&
                        navigator.serviceWorker.controller &&
                        navigator.serviceWorker.controller !== nw
                    ) {
                        // Jangan tampilkan di localhost / development
                        var isLocalhost = ['localhost', '127.0.0.1', '::1']
                            .includes(location.hostname);
                        if (isLocalhost) return;

                        // Jangan tampilkan lebih dari sekali per 10 menit
                        var lastShown = localStorage.getItem('pwa_update_shown');
                        if (lastShown && Date.now() - parseInt(lastShown) < 10 * 60 * 1000) return;

                        localStorage.setItem('pwa_update_shown', Date.now().toString());
                        showPwaUpdateBanner();
                    }
                });
            });

            navigator.serviceWorker.ready.then(function () {
                if (IS_AUTH) initFcm();
            });

        }).catch(function (err) {
            console.warn('[PWA] SW registration failed:', err);
        });

        // ⚠️ controllerchange listener DIHAPUS — terpicu saat SW pertama kali
        // claim halaman (bukan hanya saat update), menyebabkan reload loop.
        // Reload sekarang hanya terjadi jika user klik tombol "Perbarui" di banner.
    });
}

// ── Init FCM ───────────────────────────────────────────────────────────────
async function initFcm() {
    if (!('Notification' in window) || !('PushManager' in window)) return;

    try {
        if (!firebase.apps.length) firebase.initializeApp(FIREBASE_CONFIG);
        const messaging = firebase.messaging();

        if (Notification.permission === 'denied') return;

        if (Notification.permission === 'default') {
            showNotifPrompt(messaging);
            return;
        }

        await getFcmTokenAndSend(messaging);

    } catch (err) {
        console.warn('[FCM] Init error:', err);
    }
}

// ── Soft permission prompt ─────────────────────────────────────────────────
function showNotifPrompt(messaging) {
    const dismissed = localStorage.getItem('fcm_prompt_dismissed');
    if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) return;
    if (document.getElementById('__fcmPrompt')) return;

    const el = document.createElement('div');
    el.id = '__fcmPrompt';
    el.innerHTML = `
        <div style="position:fixed;bottom:80px;left:12px;right:12px;background:#fff;
                    border-radius:20px;padding:16px 18px;
                    box-shadow:0 8px 32px rgba(123,30,90,0.18);
                    border:2px solid #F3E6FA;z-index:9998;
                    font-family:'Plus Jakarta Sans',sans-serif;
                    display:flex;align-items:flex-start;gap:14px;">
            <div style="width:44px;height:44px;border-radius:14px;background:#F3E6FA;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <img src="{{ asset('icons/icon-192x192.png') }}" alt="Logo AlphaKidz" style="width:22px;height:22px;">
            </div>
            <div style="flex:1;">
                <p style="color:#4A0E35;font-weight:700;font-size:14px;margin:0 0 4px;">
                    Aktifkan Notifikasi Chat
                </p>
                <p style="color:#A2397B;font-size:12px;margin:0 0 12px;line-height:1.5;">
                    Dapatkan notifikasi pesan masuk meski app ditutup.
                </p>
                <div style="display:flex;gap:8px;">
                    <button id="__fcmAllow"
                        style="flex:1;background:#7B1E5A;color:#fff;border:none;
                               border-radius:12px;padding:10px;font-size:13px;
                               font-weight:700;cursor:pointer;font-family:inherit;">
                        Aktifkan
                    </button>
                    <button id="__fcmLater"
                        style="flex:1;background:#F3E6FA;color:#7B1E5A;border:none;
                               border-radius:12px;padding:10px;font-size:13px;
                               font-weight:600;cursor:pointer;font-family:inherit;">
                        Nanti
                    </button>
                </div>
            </div>
        </div>`;
    document.body.appendChild(el);

    document.getElementById('__fcmAllow').onclick = async function () {
        el.remove();
        const permission = await Notification.requestPermission();
        if (permission === 'granted') await getFcmTokenAndSend(messaging);
    };
    document.getElementById('__fcmLater').onclick = function () {
        el.remove();
        localStorage.setItem('fcm_prompt_dismissed', Date.now().toString());
    };
}

// ── Ambil token & kirim ke backend ────────────────────────────────────────
async function getFcmTokenAndSend(messaging) {
    try {
        const token = await messaging.getToken({ vapidKey: VAPID_KEY });
        if (!token) return;

        if (localStorage.getItem('fcm_web_token') === token) {
            attachForegroundListener(messaging);
            return;
        }

        const res = await fetch('/fcm/update-token', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : CSRF_TOKEN,
            },
            body: JSON.stringify({ fcm_token: token, device_type: 'web' }),
        });

        const data = await res.json();
        if (data.success) {
            localStorage.setItem('fcm_web_token', token);
        }

        messaging.onTokenRefresh(async function () {
            localStorage.removeItem('fcm_web_token');
            await getFcmTokenAndSend(messaging);
        });

        attachForegroundListener(messaging);

    } catch (err) {
        console.warn('[FCM] getToken error:', err);
    }
}

// ── Foreground: kirim ke SW agar tampil sebagai heads-up di Android ───────
function attachForegroundListener(messaging) {
    messaging.onMessage(function (payload) {
        const n        = payload.notification || {};
        const d        = payload.data         || {};
        const title    = d.title     || n.title || 'Pesan Baru';
        const body     = d.body      || n.body  || '';
        const senderId = d.sender_id || 'msg';
        const chatUrl  = d.url
            ? d.url
            : (d.sender_id
                ? '/chat/' + d.sender_id + '?nama=' + encodeURIComponent(d.sender_name || title)
                : '/chat');

        // Kirim ke SW — SW pakai showNotification() yang bisa heads-up di Android
        // new Notification() dari halaman tidak bisa heads-up di Android Chrome
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type   : 'SHOW_NOTIFICATION',
                title  : title,
                body   : body,
                tag    : 'chat-' + senderId,
                url    : chatUrl,
            });
        }

        // In-app banner tetap ada sebagai fallback visual di dalam app
        showInAppBanner(title, body, chatUrl);
    });
}

// ── In-app banner (fallback) ───────────────────────────────────────────────
function showInAppBanner(title, body, url) {
    const old = document.getElementById('__fcmInApp');
    if (old) old.remove();

    const el = document.createElement('div');
    el.id = '__fcmInApp';
    el.innerHTML = `
        <div style="position:fixed;top:16px;left:12px;right:12px;background:#fff;
                    border-radius:18px;padding:14px 16px;
                    box-shadow:0 8px 32px rgba(123,30,90,0.2);
                    border:2px solid #F3E6FA;z-index:99999;
                    font-family:'Plus Jakarta Sans',sans-serif;
                    display:flex;align-items:center;gap:12px;cursor:pointer;"
             onclick="window.location.href='${url}';this.closest('#__fcmInApp').remove();">
            <div style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <img src="{{ asset('icons/icon-192x192.png') }}" alt="Logo AlphaKidz" style="width:22px;height:22px;">
            </div>
            <div style="flex:1;min-width:0;">
                <p style="color:#4A0E35;font-weight:700;font-size:13px;margin:0 0 2px;
                          overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    ${title}
                </p>
                <p style="color:#A2397B;font-size:12px;margin:0;
                          overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    ${body}
                </p>
            </div>
            <button onclick="event.stopPropagation();this.closest('#__fcmInApp').remove()"
                style="background:none;border:none;color:#B895C8;font-size:20px;
                       cursor:pointer;padding:0 2px;flex-shrink:0;">×</button>
        </div>`;
    document.body.appendChild(el);
    setTimeout(function () { if (el.parentNode) el.remove(); }, 5000);
}

// ── Hapus token saat logout ────────────────────────────────────────────────
function removeFcmTokenOnLogout() {
    localStorage.removeItem('fcm_web_token');
    navigator.sendBeacon
        ? navigator.sendBeacon('/fcm/remove-token', new Blob(
            [JSON.stringify({ _token: CSRF_TOKEN })],
            { type: 'application/json' }
          ))
        : fetch('/fcm/remove-token', {
            method   : 'POST',
            headers  : { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' },
            keepalive: true,
          });
}

// ── PWA update banner ──────────────────────────────────────────────────────
function showPwaUpdateBanner() {
    if (document.getElementById('__pwaUpdateBanner')) return;
    const b = document.createElement('div');
    b.id = '__pwaUpdateBanner';
    b.innerHTML = `
        <div style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);
                    background:#4A0E35;color:#fff;border-radius:16px;padding:12px 20px;
                    display:flex;align-items:center;gap:12px;z-index:9999;
                    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:600;
                    box-shadow:0 8px 24px rgba(74,14,53,0.35);white-space:nowrap;">
            🔄 Versi baru tersedia!
            <button onclick="window.location.reload(true)"
                style="background:#7B1E5A;border:none;color:#fff;padding:6px 14px;
                       border-radius:10px;cursor:pointer;font-weight:700;font-size:12px;
                       font-family:inherit;">Perbarui</button>
            <button onclick="this.closest('#__pwaUpdateBanner').remove()"
                style="background:none;border:none;color:rgba(255,255,255,.6);
                       cursor:pointer;font-size:20px;line-height:1;padding:0 2px;">×</button>
        </div>`;
    document.body.appendChild(b);
}
</script>