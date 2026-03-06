// public/sw.js
// Service Worker - NannyApp PWA
// Strategy:
//   - App shell (HTML halaman)    → Network First, fallback offline page
//   - Static assets (CSS/JS/font) → Cache First
//   - API calls (/api/*, external) → Network Only (jangan cache data sensitif)
//   - Gambar/icons                 → Cache First dengan batas ukuran

const APP_VERSION   = 'v1.0.0';
const SHELL_CACHE   = `alphakidz-shell-${APP_VERSION}`;
const STATIC_CACHE  = `alphakidz-static-${APP_VERSION}`;
const IMAGE_CACHE   = `alphakidz-images-${APP_VERSION}`;

// ── App Shell: halaman-halaman utama yang di-precache ─────────────────────────
const SHELL_URLS = [
    '/offline',
    '/login',
    '/dashboard',
];

// ── Static assets yang di-precache ────────────────────────────────────────────
const STATIC_URLS = [
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// ── URL patterns yang TIDAK pernah di-cache ───────────────────────────────────
const NEVER_CACHE = [
    /\/api\//,            // API proxy routes Laravel
    /\/broadcasting\//,   // Pusher auth
    /\/logout/,           // Logout
    /\/fcm\//,            // FCM token management
    /pusher\.com/,        // Pusher external
    /fonts\.googleapis/,  // Google Fonts (biarkan browser cache sendiri)
];

// ─────────────────────────────────────────────────────────────────────────────
// INSTALL — precache app shell
// ─────────────────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        Promise.all([
            caches.open(SHELL_CACHE).then(cache => {
                // addAll gagal total jika salah satu URL error
                // pakai add() satu per satu agar lebih toleran
                return Promise.allSettled(
                    SHELL_URLS.map(url => cache.add(url).catch(e =>
                        console.warn(`[SW] Precache skip: ${url}`, e.message)
                    ))
                );
            }),
            caches.open(STATIC_CACHE).then(cache => {
                return Promise.allSettled(
                    STATIC_URLS.map(url => cache.add(url).catch(e =>
                        console.warn(`[SW] Precache static skip: ${url}`, e.message)
                    ))
                );
            }),
        ]).then(() => self.skipWaiting())
    );
});

// ─────────────────────────────────────────────────────────────────────────────
// ACTIVATE — hapus cache lama
// ─────────────────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    const validCaches = [SHELL_CACHE, STATIC_CACHE, IMAGE_CACHE];

    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys
                    .filter(key => !validCaches.includes(key))
                    .map(key => {
                        console.log('[SW] Deleting old cache:', key);
                        return caches.delete(key);
                    })
            ))
            .then(() => self.clients.claim())
    );
});

// ─────────────────────────────────────────────────────────────────────────────
// FETCH — routing strategy
// ─────────────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Abaikan non-GET & chrome-extension
    if (request.method !== 'GET') return;
    if (url.protocol === 'chrome-extension:') return;

    // Abaikan URL yang tidak boleh di-cache
    if (NEVER_CACHE.some(pattern => pattern.test(request.url))) return;

    // ── Gambar → Cache First (max 50 entries, 30 hari) ──────────────────────
    if (request.destination === 'image') {
        event.respondWith(cacheFirstImage(request));
        return;
    }

    // ── Static assets (JS, CSS, font, woff) → Cache First ───────────────────
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
        return;
    }

    // ── Navigasi (HTML halaman) → Network First, fallback offline ───────────
    if (request.mode === 'navigate') {
        event.respondWith(networkFirstNavigate(request));
        return;
    }
});

// ─────────────────────────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────────────────────────

function isStaticAsset(url) {
    const ext = url.pathname.split('.').pop();
    return ['js', 'css', 'woff', 'woff2', 'ttf', 'otf'].includes(ext)
        || url.hostname.includes('cdn.tailwindcss.com')
        || url.hostname.includes('fonts.gstatic.com')
        || url.hostname.includes('unpkg.com')
        || url.hostname.includes('cdnjs.cloudflare.com');
}

// Network First → cache → offline page
async function networkFirstNavigate(request) {
    const cache = await caches.open(SHELL_CACHE);
    try {
        const networkRes = await fetch(request);
        // Simpan response HTML yang berhasil ke cache (kecuali halaman error)
        if (networkRes.ok) {
            cache.put(request, networkRes.clone());
        }
        return networkRes;
    } catch (_) {
        // Offline: coba dari cache
        const cached = await cache.match(request);
        if (cached) return cached;

        // Tidak ada cache → tampilkan halaman offline
        return cache.match('/offline') || new Response(offlineFallbackHTML(), {
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
        });
    }
}

// Cache First → network → simpan ke cache
async function cacheFirst(request, cacheName) {
    const cache  = await caches.open(cacheName);
    const cached = await cache.match(request);
    if (cached) return cached;

    try {
        const networkRes = await fetch(request);
        if (networkRes.ok) cache.put(request, networkRes.clone());
        return networkRes;
    } catch (_) {
        return new Response('', { status: 503 });
    }
}

// Cache First khusus gambar dengan batas 60 entries
async function cacheFirstImage(request) {
    const cache  = await caches.open(IMAGE_CACHE);
    const cached = await cache.match(request);
    if (cached) return cached;

    try {
        const networkRes = await fetch(request);
        if (networkRes.ok) {
            // Batasi jumlah cache gambar
            cache.put(request, networkRes.clone());
            trimCache(IMAGE_CACHE, 60);
        }
        return networkRes;
    } catch (_) {
        return new Response('', { status: 503 });
    }
}

// Hapus entry lama jika cache melebihi maxItems
async function trimCache(cacheName, maxItems) {
    const cache = await caches.open(cacheName);
    const keys  = await cache.keys();
    if (keys.length > maxItems) {
        await cache.delete(keys[0]);
        trimCache(cacheName, maxItems);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// OFFLINE FALLBACK HTML (dipakai jika /offline belum ter-cache)
// ─────────────────────────────────────────────────────────────────────────────
function offlineFallbackHTML() {
    return `<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tidak Ada Koneksi</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:#FFF9FB;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
        .card{background:#fff;border-radius:24px;padding:40px 32px;text-align:center;max-width:320px;border:2px solid #F3E6FA;box-shadow:0 8px 32px rgba(123,30,90,0.1)}
        .icon{width:72px;height:72px;border-radius:50%;background:#F3E6FA;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:36px}
        h1{color:#4A0E35;font-size:20px;font-weight:800;margin-bottom:8px}
        p{color:#A2397B;font-size:14px;line-height:1.6;margin-bottom:24px}
        button{background:#7B1E5A;color:#fff;border:none;border-radius:14px;padding:14px 32px;font-size:15px;font-weight:700;cursor:pointer;width:100%;font-family:inherit}
        button:active{transform:scale(0.97)}
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📡</div>
        <h1>Tidak Ada Koneksi</h1>
        <p>Halaman ini tidak tersedia secara offline. Periksa koneksi internet Anda dan coba lagi.</p>
        <button onclick="location.reload()">Coba Lagi</button>
    </div>
</body>
</html>`;
}

// ─────────────────────────────────────────────────────────────────────────────
// PUSH NOTIFICATION (opsional — siapkan handler-nya)
// ─────────────────────────────────────────────────────────────────────────────
self.addEventListener('push', event => {
    if (!event.data) return;

    let data = {};
    try { data = event.data.json(); } catch (_) { data = { title: 'NannyApp', body: event.data.text() }; }

    event.waitUntil(
        self.registration.showNotification(data.title || 'NannyApp', {
            body   : data.body   || '',
            icon   : '/icons/icon-192x192.png',
            badge  : '/icons/icon-72x72.png',
            data   : { url: data.url || '/dashboard' },
            vibrate: [100, 50, 100],
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/dashboard';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            const existing = list.find(c => c.url.includes(targetUrl));
            if (existing) return existing.focus();
            return clients.openWindow(targetUrl);
        })
    );
});

// ─────────────────────────────────────────────────────────────────────────────
// MESSAGE HANDLER — terima postMessage dari halaman (foreground notification)
// Dipakai saat app terbuka di Android: new Notification() tidak bisa heads-up,
// tapi SW showNotification() bisa. Halaman kirim ke sini via postMessage.
// ─────────────────────────────────────────────────────────────────────────────
self.addEventListener('message', event => {
    if (!event.data || event.data.type !== 'SHOW_NOTIFICATION') return;

    const { title, body, tag, url } = event.data;

    event.waitUntil(
        self.registration.showNotification(title || 'Pesan Baru', {
            body               : body  || '',
            icon               : '/icons/icon-192x192.png',
            badge              : '/icons/icon-192x192.png',
            tag                : tag   || 'chat-msg',
            renotify           : true,
            requireInteraction : true,
            vibrate            : [200, 100, 200],
            data               : { url: url || '/chat' },
        })
    );
});