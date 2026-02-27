/**
 * firebase-messaging-sw.js
 * Letakkan di: public/firebase-messaging-sw.js  (ROOT public folder!)
 *
 * Service worker ini wajib ada di root domain agar Firebase bisa menangani
 * push notification saat tab browser sedang tertutup / background.
 *
 * File ini TIDAK bisa pakai ES module import — harus pakai importScripts()
 */

importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

// ─── Firebase config (sama persis dengan fcmService.js) ──────────────────────
// Nilai ini di-hardcode di sini karena service worker tidak bisa akses DOM/window
firebase.initializeApp({
    apiKey:            'YOUR_API_KEY',
    authDomain:        'YOUR_AUTH_DOMAIN',
    projectId:         'YOUR_PROJECT_ID',
    storageBucket:     'YOUR_STORAGE_BUCKET',
    messagingSenderId: 'YOUR_MESSAGING_SENDER_ID',
    appId:             'YOUR_APP_ID',
});

const messaging = firebase.messaging();

// ─── Background message handler ───────────────────────────────────────────────
// Dipanggil saat app BACKGROUND (tab tidak aktif atau browser minimize)
messaging.onBackgroundMessage((payload) => {
    console.log('[SW] Background message:', payload);

    const title = payload.data?.title || payload.notification?.title || 'Pesan Baru';
    const body  = payload.data?.body  || payload.notification?.body  || '';
    const data  = payload.data || {};

    const options = {
        body,
        icon:  '/images/icon.png',   // sesuaikan
        badge: '/images/badge.png',  // sesuaikan
        data,
        // Klik notifikasi akan membuka / focus tab app
        actions: [{ action: 'open', title: 'Buka' }],
    };

    self.registration.showNotification(title, options);
});

// ─── Handle klik notifikasi (background) ─────────────────────────────────────
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const data      = event.notification.data || {};
    const targetUrl = data.type === 'chat'
        ? `/chat/${data.sender_id}`
        : '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            // Kalau sudah ada tab yang buka app, fokus ke sana
            for (const client of windowClients) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            // Kalau tidak ada, buka tab baru
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});