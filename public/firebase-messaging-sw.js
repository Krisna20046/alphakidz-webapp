importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey           : "AIzaSyCOdUtA0YLnwxWARVT0GCpZb70SsMmNgis",
    authDomain       : "alphakidz-a98b3.firebaseapp.com",
    projectId        : "alphakidz-a98b3",
    storageBucket    : "alphakidz-a98b3.firebasestorage.app",
    messagingSenderId: "63843037965",
    appId            : "1:63843037965:web:c4df4b57f8f9a23795f644"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    const notification = payload.notification || {};
    const data         = payload.data         || {};

    const title     = data.title     || notification.title || 'Pesan Baru - AlphaKidz';
    const body      = data.body      || notification.body  || 'Anda memiliki pesan baru';
    const senderId  = data.sender_id || '';
    const chatUrl   = data.url
        ? data.url
        : (data.sender_id
            ? '/chat/' + data.sender_id + '?nama=' + encodeURIComponent(data.sender_name || title)
            : '/chat');

    return self.registration.showNotification(title, {
        body               : body,
        icon               : '/icons/icon-192x192.png',
        badge              : '/icons/icon-192x192.png',
        tag                : 'chat-' + (senderId || 'msg'),
        renotify           : true,
        requireInteraction : true,
        vibrate            : [200, 100, 200],
        data               : { url: chatUrl },
        actions            : [
            { action: 'open',    title: 'Buka Chat' },
            { action: 'dismiss', title: 'Tutup'     },
        ],
    });
});

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