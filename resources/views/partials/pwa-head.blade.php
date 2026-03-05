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

{{-- Firebase SDK (hanya untuk Web Push / FCM) --}}
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>

<script>
// ── Firebase config ────────────────────────────────────────────────────────
// Ambil dari Firebase Console → Project Settings → Your apps → Web app
const FIREBASE_CONFIG = {
    apiKey: "AIzaSyCOdUtA0YLnwxWARVT0GCpZb70SsMmNgis",
    authDomain: "alphakidz-a98b3.firebaseapp.com",
    projectId: "alphakidz-a98b3",
    storageBucket: "alphakidz-a98b3.firebasestorage.app",
    messagingSenderId: "63843037965",
    appId: "1:63843037965:web:c4df4b57f8f9a23795f644"
};

// VAPID key — Firebase Console → Project Settings → Cloud Messaging → Web Push certificates
const VAPID_KEY = "BLIPLi-whuk0HS_Afbafommm_oXpkhACm22IfFG6U1gusVhNCrTZ9IDcQZm-U77Gw76HnkeVziEnNB__cu7Nmus";

// ── CSRF untuk request Laravel ─────────────────────────────────────────────
// const CSRF_TOKEN = "{{ csrf_token() }}";

// ── Register Service Worker ────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(function (reg) {
                reg.update();

                reg.addEventListener('updatefound', function () {
                    var newWorker = reg.installing;
                    newWorker.addEventListener('statechange', function () {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showPwaUpdateBanner();
                        }
                    });
                });

                // Setelah SW ready, init FCM (hanya jika user sudah login)
                navigator.serviceWorker.ready.then(function () {
                    initFcm();
                });
            })
            .catch(function (err) {
                console.warn('[PWA] SW registration failed:', err);
            });

        var refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', function () {
            if (!refreshing) { refreshing = true; window.location.reload(); }
        });
    });
}

// ── Init FCM Web Push ──────────────────────────────────────────────────────
async function initFcm() {
    // Cek: user harus sudah login (ada session token yang diteruskan Blade)
    // Jika halaman guest (login/register), skip
    const isAuthenticated = {{ session('token') ? 'true' : 'false' }};
    if (!isAuthenticated) return;

    // Cek browser support
    if (!('Notification' in window) || !('PushManager' in window)) {
        console.warn('[FCM] Browser tidak mendukung notifikasi');
        return;
    }

    try {
        // Init Firebase
        if (!firebase.apps.length) {
            firebase.initializeApp(FIREBASE_CONFIG);
        }
        const messaging = firebase.messaging();

        // Cek permission saat ini
        const currentPermission = Notification.permission;

        if (currentPermission === 'denied') {
            // User sudah blokir — jangan minta lagi
            console.warn('[FCM] Notifikasi diblokir oleh user');
            return;
        }

        if (currentPermission === 'default') {
            // Belum pernah minta — tampilkan banner soft ask dulu
            // agar tidak langsung popup browser yang terasa agresif
            showNotifPrompt(messaging);
            return;
        }

        // Sudah granted — langsung ambil & kirim token
        await getFcmTokenAndSend(messaging);

    } catch (err) {
        console.warn('[FCM] Init error:', err);
    }
}

// ── Soft prompt sebelum minta permission browser ───────────────────────────
function showNotifPrompt(messaging) {
    // Jangan tampilkan lagi jika user sudah dismiss dalam 7 hari
    const dismissed = localStorage.getItem('fcm_prompt_dismissed');
    if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) return;

    // Jangan tampilkan duplikat
    if (document.getElementById('__fcmPrompt')) return;

    const el = document.createElement('div');
    el.id = '__fcmPrompt';
    el.innerHTML = `
        <div style="
            position:fixed; bottom:80px; left:12px; right:12px;
            background:#fff; border-radius:20px; padding:16px 18px;
            box-shadow:0 8px 32px rgba(123,30,90,0.18);
            border:2px solid #F3E6FA; z-index:9998;
            font-family:'Plus Jakarta Sans',sans-serif;
            display:flex; align-items:flex-start; gap:14px;
            animation:__promptIn .35s cubic-bezier(.34,1.56,.64,1);
        ">
        <style>
            @keyframes __promptIn {
                from { opacity:0; transform:translateY(20px); }
                to   { opacity:1; transform:translateY(0); }
            }
        </style>
        <div style="width:44px;height:44px;border-radius:14px;background:#F3E6FA;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="font-size:22px;">💬</span>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="color:#4A0E35;font-weight:700;font-size:14px;margin:0 0 4px;">
                Aktifkan Notifikasi Chat
            </p>
            <p style="color:#A2397B;font-size:12px;margin:0 0 12px;line-height:1.5;">
                Dapatkan notifikasi saat ada pesan chat masuk, bahkan saat app ditutup.
            </p>
            <div style="display:flex;gap:8px;">
                <button id="__fcmAllow"
                    style="flex:1;background:#7B1E5A;color:#fff;border:none;
                           border-radius:12px;padding:10px;font-size:13px;font-weight:700;
                           cursor:pointer;font-family:inherit;">
                    Aktifkan
                </button>
                <button id="__fcmLater"
                    style="flex:1;background:#F3E6FA;color:#7B1E5A;border:none;
                           border-radius:12px;padding:10px;font-size:13px;font-weight:600;
                           cursor:pointer;font-family:inherit;">
                    Nanti
                </button>
            </div>
        </div>
        </div>
    `;
    document.body.appendChild(el);

    document.getElementById('__fcmAllow').onclick = async function () {
        el.remove();
        try {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                await getFcmTokenAndSend(messaging);
            }
        } catch (e) {
            console.warn('[FCM] Permission request failed:', e);
        }
    };

    document.getElementById('__fcmLater').onclick = function () {
        el.remove();
        localStorage.setItem('fcm_prompt_dismissed', Date.now().toString());
    };
}

// ── Ambil FCM token & kirim ke backend Laravel ─────────────────────────────
async function getFcmTokenAndSend(messaging) {
    try {
        const currentToken = await messaging.getToken({ vapidKey: VAPID_KEY });

        if (!currentToken) {
            console.warn('[FCM] Tidak bisa mendapatkan token');
            return;
        }

        // Cek apakah token sudah pernah dikirim (hindari request berulang)
        const savedToken = localStorage.getItem('fcm_web_token');
        if (savedToken === currentToken) return;

        // Kirim ke Laravel → FcmController@updateToken → API backend
        const res = await fetch('/fcm/update-token', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : CSRF_TOKEN,
            },
            body: JSON.stringify({
                fcm_token  : currentToken,
                device_type: 'web',
            }),
        });

        const data = await res.json();
        if (data.success) {
            localStorage.setItem('fcm_web_token', currentToken);
            console.log('[FCM] Token berhasil dikirim ke backend');
        }

        // Listener jika token di-refresh oleh Firebase
        messaging.onTokenRefresh(async () => {
            localStorage.removeItem('fcm_web_token');
            await getFcmTokenAndSend(messaging);
        });

        // Foreground message handler
        // (Saat app terbuka di browser — SW push tidak aktif, jadi handle di sini)
        messaging.onMessage(function (payload) {
            console.log('[FCM] Foreground message:', payload);
            showInAppNotification(payload);
        });

    } catch (err) {
        console.warn('[FCM] getToken error:', err);
    }
}

// ── Notifikasi in-app (saat halaman sedang terbuka / foreground) ───────────
function showInAppNotification(payload) {
    const notification = payload.notification || {};
    const data         = payload.data         || {};

    const title = notification.title || 'Pesan Baru';
    const body  = notification.body  || '';
    const url   = data.url           || '/chat';

    // Hapus notif lama jika ada
    const existing = document.getElementById('__fcmInAppNotif');
    if (existing) existing.remove();

    const el = document.createElement('div');
    el.id = '__fcmInAppNotif';
    el.innerHTML = `
        <div style="
            position:fixed; top:16px; left:12px; right:12px;
            background:#fff; border-radius:18px; padding:14px 16px;
            box-shadow:0 8px 32px rgba(123,30,90,0.2);
            border:2px solid #F3E6FA; z-index:99999;
            font-family:'Plus Jakarta Sans',sans-serif;
            display:flex; align-items:center; gap:12px; cursor:pointer;
            animation:__notifIn .3s cubic-bezier(.34,1.56,.64,1);
        " onclick="window.location.href='${url}'; this.closest('#__fcmInAppNotif').remove();">
        <style>
            @keyframes __notifIn {
                from { opacity:0; transform:translateY(-16px); }
                to   { opacity:1; transform:translateY(0); }
            }
        </style>
        <div style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="font-size:20px;">💬</span>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="color:#4A0E35;font-weight:700;font-size:13px;margin:0 0 2px;
                      white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                ${title}
            </p>
            <p style="color:#A2397B;font-size:12px;margin:0;
                      white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                ${body}
            </p>
        </div>
        <button onclick="event.stopPropagation();this.closest('#__fcmInAppNotif').remove()"
            style="background:none;border:none;color:#B895C8;font-size:18px;
                   cursor:pointer;padding:0 2px;line-height:1;flex-shrink:0;">
            ×
        </button>
        </div>
    `;
    document.body.appendChild(el);

    // Auto hilang setelah 5 detik
    setTimeout(() => { if (el.parentNode) el.remove(); }, 5000);
}

// ── Update banner ──────────────────────────────────────────────────────────
function showPwaUpdateBanner() {
    if (document.getElementById('__pwaUpdateBanner')) return;
    var banner = document.createElement('div');
    banner.id = '__pwaUpdateBanner';
    banner.innerHTML = `
        <div style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);
                    background:#4A0E35;color:#fff;border-radius:16px;padding:12px 20px;
                    display:flex;align-items:center;gap:12px;z-index:9999;
                    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:600;
                    box-shadow:0 8px 24px rgba(74,14,53,0.35);white-space:nowrap;">
            🔄 Versi baru tersedia!
            <button onclick="window.location.reload()"
                style="background:#7B1E5A;border:none;color:#fff;padding:6px 14px;
                       border-radius:10px;cursor:pointer;font-weight:700;font-size:12px;
                       font-family:inherit;">Perbarui</button>
            <button onclick="this.closest('#__pwaUpdateBanner').remove()"
                style="background:none;border:none;color:rgba(255,255,255,.6);
                       cursor:pointer;font-size:20px;line-height:1;padding:0 2px;">×</button>
        </div>
    `;
    document.body.appendChild(banner);
}

// ── Hapus FCM token saat logout ────────────────────────────────────────────
// Panggil fungsi ini sebelum form logout di-submit
function removeFcmTokenOnLogout() {
    localStorage.removeItem('fcm_web_token');
    // Kirim remove ke backend (best-effort)
    navigator.sendBeacon
        ? navigator.sendBeacon('/fcm/remove-token', new Blob(
            [JSON.stringify({ _token: CSRF_TOKEN })],
            { type: 'application/json' }
          ))
        : fetch('/fcm/remove-token', {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' },
            keepalive: true,
          });
}
</script>