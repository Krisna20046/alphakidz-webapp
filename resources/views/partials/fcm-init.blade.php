{{--
  =====================================================================
  PATCH: Tambahkan snippet FCM ini ke home.blade.php
  Letakkan di dalam <head> SETELAH tailwind config:
  =====================================================================
--}}

{{-- Firebase config (dari .env via Blade) --}}
<script>
    // Diteruskan ke fcmService.js via window variable
    window.FIREBASE_API_KEY             = "{{ env('FIREBASE_API_KEY') }}";
    window.FIREBASE_AUTH_DOMAIN         = "{{ env('FIREBASE_AUTH_DOMAIN') }}";
    window.FIREBASE_PROJECT_ID          = "{{ env('FIREBASE_PROJECT_ID') }}";
    window.FIREBASE_STORAGE_BUCKET      = "{{ env('FIREBASE_STORAGE_BUCKET') }}";
    window.FIREBASE_MESSAGING_SENDER_ID = "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}";
    window.FIREBASE_APP_ID              = "{{ env('FIREBASE_APP_ID') }}";
    window.FIREBASE_VAPID_KEY           = "{{ env('FIREBASE_VAPID_KEY') }}";
    window.CSRF_TOKEN                   = "{{ csrf_token() }}";
</script>

{{--
  =====================================================================
  PATCH: Tambahkan snippet FCM ini di bagian <script> di bawah home.blade.php
  Letakkan SETELAH block Pusher real-time:
  =====================================================================
--}}
<script type="module">
import { initFCM } from '/js/fcmService.js';

// ─── Init FCM setelah halaman siap ────────────────────────────────────────────
// onMessage callback: handle notifikasi chat dari FCM (foreground)
initFCM(window.CSRF_TOKEN, function(data) {
    // Sama seperti React Native: cek type dan update UI
    if (data?.type === 'chat' && data?.sender_id) {
        // Tambah unread badge (sama logic dengan Pusher real-time)
        const currentCount = parseInt(
            document.getElementById('unreadBadge')?.textContent || '0'
        ) || 0;
        updateBadge(currentCount + 1);
    }
});
</script>