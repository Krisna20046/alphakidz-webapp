/**
 * fcmService.js
 * Web equivalent dari src/services/fcmService.ts (React Native)
 *
 * Letakkan file ini di: public/js/fcmService.js
 *
 * Cara pakai di blade:
 *   <script type="module">
 *     import { initFCM, removeFCMToken } from '/js/fcmService.js';
 *     initFCM();  // panggil saat user sudah login
 *   </script>
 *
 * Requirement:
 *   - Firebase project dengan Web App
 *   - VAPID key dari Firebase Console (Project Settings > Cloud Messaging)
 *   - Service worker: /public/firebase-messaging-sw.js  (lihat bawah)
 */

import { initializeApp }        from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js';
import { getMessaging, getToken, deleteToken, onMessage }
    from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging.js';

// ─── Firebase config (isi sesuai project kamu) ───────────────────────────────
const FIREBASE_CONFIG = {
    apiKey:            window.FIREBASE_API_KEY            || '',
    authDomain:        window.FIREBASE_AUTH_DOMAIN        || '',
    projectId:         window.FIREBASE_PROJECT_ID         || '',
    storageBucket:     window.FIREBASE_STORAGE_BUCKET     || '',
    messagingSenderId: window.FIREBASE_MESSAGING_SENDER_ID || '',
    appId:             window.FIREBASE_APP_ID             || '',
};

// VAPID public key dari Firebase Console > Cloud Messaging > Web Push certificates
const VAPID_KEY = window.FIREBASE_VAPID_KEY || '';

// ─── Init Firebase ────────────────────────────────────────────────────────────
let app       = null;
let messaging = null;

function getFirebaseMessaging() {
    if (messaging) return messaging;
    app       = initializeApp(FIREBASE_CONFIG);
    messaging = getMessaging(app);
    return messaging;
}

// ─── Request permission + get token ──────────────────────────────────────────
async function requestNotificationPermission() {
    const permission = await Notification.requestPermission();
    return permission === 'granted';
}

async function getFCMToken() {
    try {
        const msg   = getFirebaseMessaging();

        // Daftarkan service worker dulu
        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

        const token = await getToken(msg, {
            vapidKey:          VAPID_KEY,
            serviceWorkerRegistration: registration,
        });

        return token || null;
    } catch (error) {
        console.warn('[FCM] Gagal mendapatkan token:', error);
        return null;
    }
}

// ─── Update token ke Laravel backend (proxy ke API) ──────────────────────────
async function updateFCMToken(csrfToken) {
    try {
        const fcmToken = await getFCMToken();
        if (!fcmToken) {
            console.warn('[FCM] Token kosong, skip update.');
            return;
        }

        const response = await fetch('/fcm/update-token', {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  csrfToken,
            },
            body: JSON.stringify({
                fcm_token:   fcmToken,
                device_type: 'web',
            }),
        });

        if (!response.ok) {
            console.warn('[FCM] Update token gagal:', response.status);
        } else {
            console.log('[FCM] Token berhasil diperbarui ke backend');
        }
    } catch (error) {
        console.warn('[FCM] Gagal update token:', error);
    }
}

// ─── Remove token saat logout ─────────────────────────────────────────────────
async function removeFCMToken(csrfToken) {
    try {
        // 1. Hapus dari backend
        await fetch('/fcm/remove-token', {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  csrfToken,
            },
        });

        // 2. Hapus dari Firebase (browser-side)
        const msg = getFirebaseMessaging();
        await deleteToken(msg);

        console.log('[FCM] Token berhasil dihapus');
    } catch (error) {
        console.warn('[FCM] Gagal hapus token:', error);
    }
}

// ─── Handle foreground message (app terbuka di tab aktif) ────────────────────
function listenForegroundMessages(onReceive) {
    const msg = getFirebaseMessaging();

    onMessage(msg, (payload) => {
        console.log('[FCM] Foreground message:', payload);

        const title = payload.data?.title || payload.notification?.title || 'Pesan Baru';
        const body  = payload.data?.body  || payload.notification?.body  || '';
        const data  = payload.data || {};

        // Tampilkan via Notification API (browser native)
        if (Notification.permission === 'granted') {
            const notif = new Notification(title, {
                body,
                icon:  '/images/icon.png',   // sesuaikan path
                badge: '/images/badge.png',  // sesuaikan path
                data,
                tag:   'fcm-' + Date.now(),
            });

            notif.onclick = () => {
                window.focus();
                notif.close();
                if (typeof onReceive === 'function') onReceive(data);
            };
        }

        // Juga panggil callback (misal: update unread count di UI)
        if (typeof onReceive === 'function') onReceive(data);
    });
}

// ─── Main init ────────────────────────────────────────────────────────────────
/**
 * initFCM() — panggil ini setelah user berhasil login
 * @param {string} csrfToken  - window.CSRF_TOKEN dari blade
 * @param {function} onMessage - callback saat ada pesan masuk
 */
async function initFCM(csrfToken, onMessage) {
    // 1. Cek support
    if (!('Notification' in window) || !('serviceWorker' in navigator)) {
        console.warn('[FCM] Browser tidak support notifikasi.');
        return;
    }

    // 2. Minta permission
    const hasPermission = await requestNotificationPermission();
    if (!hasPermission) {
        console.warn('[FCM] Permission notifikasi ditolak.');
        return;
    }

    // 3. Kirim token ke backend
    await updateFCMToken(csrfToken);

    // 4. Listen foreground message
    listenForegroundMessages(onMessage);
}

export { initFCM, removeFCMToken, updateFCMToken };