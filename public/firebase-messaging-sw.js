// public/firebase-messaging-sw.js
// Service Worker khusus Firebase Messaging (background push)
// File ini TERPISAH dari sw.js dan harus ada di public/

// ── Import Firebase SW scripts ─────────────────────────────────────────────
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

// ── Firebase config (SAMA persis dengan yang di pwa-head.blade.php) ────────
firebase.initializeApp({
    apiKey           : "AIzaSyCOdUtA0YLnwxWARVT0GCpZb70SsMmNgis",
    authDomain       : "alphakidz-a98b3.firebaseapp.com",
    projectId        : "alphakidz-a98b3",
    storageBucket    : "alphakidz-a98b3.firebasestorage.app",
    messagingSenderId: "63843037965",
    appId            : "1:63843037965:web:c4df4b57f8f9a23795f644"
});

const messaging = firebase.messaging();

// ── Background push handler ────────────────────────────────────────────────
// Dipanggil saat app TIDAK terbuka / di background
messaging.onBackgroundMessage(function (payload) {
    const notification = payload.notification || {};
    const data         = payload.data         || {};

    const title   = notification.title || 'Pesan Baru - AlphaKidz';
    const body    = notification.body  || 'Anda memiliki pesan baru';
    const chatUrl = data.url           || '/chat';

    return self.registration.showNotification(title, {
        body   : body,
        icon   : '/icons/icon-192x192.png',
        badge  : '/icons/icon-192x192.png',
        tag    : 'chat-' + (senderId || 'msg'),
        renotify           : true,
        requireInteraction : true,
        data   : { url: chatUrl },
        vibrate: [200, 100, 200],
        actions: [
            { action: 'open',    title: 'Buka Chat' },
            { action: 'dismiss', title: 'Tutup'     },
        ],
    });
});

// ── Notification click handler ─────────────────────────────────────────────
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const targetUrl = event.notification.data?.url || '/chat';
    const fullUrl   = self.location.origin + targetUrl;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
            // Cari tab yang sudah buka app — fokus dan navigate
            for (const client of list) {
                if (client.url.startsWith(self.location.origin) && 'focus' in client) {
                    client.focus();
                    return client.navigate(fullUrl);
                }
            }
            // Tidak ada tab — buka baru
            return clients.openWindow(fullUrl);
        })
    );
});