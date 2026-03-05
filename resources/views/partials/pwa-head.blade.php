{{-- resources/views/partials/pwa-head.blade.php --}}
{{-- Include di dalam <head> di SEMUA halaman:
     @include('partials.pwa-head')
--}}

{{-- ── Manifest ─────────────────────────────────────────────────────────── --}}
<link rel="manifest" href="/manifest.json">

{{-- ── Theme color (status bar warna plum di mobile browser) ─────────────── --}}
<meta name="theme-color" content="#7B1E5A">
<meta name="msapplication-TileColor" content="#7B1E5A">
<meta name="msapplication-TileImage" content="/icons/icon-144x144.png">

{{-- ── Mobile Web App capable ──────────────────────────────────────────────── --}}
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="NannyApp">
<meta name="application-name" content="NannyApp">

{{-- ── Apple Touch Icons ────────────────────────────────────────────────────── --}}
<link rel="apple-touch-icon" href="/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="72x72"   href="/icons/icon-72x72.png">
<link rel="apple-touch-icon" sizes="96x96"   href="/icons/icon-96x96.png">
<link rel="apple-touch-icon" sizes="128x128" href="/icons/icon-128x128.png">
<link rel="apple-touch-icon" sizes="144x144" href="/icons/icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

{{-- ── Favicon ──────────────────────────────────────────────────────────────── --}}
<link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-72x72.png">

{{-- ── Apple Splash Screens (portrait) ────────────────────────────────────── --}}
{{-- iPhone 14 Pro Max --}}
<link rel="apple-touch-startup-image"
      media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
      href="/icons/splash-1290x2796.png">
{{-- iPhone 14 / 13 / 12 --}}
<link rel="apple-touch-startup-image"
      media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
      href="/icons/splash-1170x2532.png">
{{-- iPhone SE --}}
<link rel="apple-touch-startup-image"
      media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
      href="/icons/splash-750x1334.png">

{{-- ── Register Service Worker ─────────────────────────────────────────────── --}}
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(reg => {
                // Cek update setiap kali halaman di-load
                reg.update();

                // Kalau ada SW baru yang menunggu, reload otomatis (silent update)
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // SW baru siap — tampilkan banner update (opsional)
                            showUpdateBanner();
                        }
                    });
                });
            })
            .catch(err => console.warn('[PWA] SW registration failed:', err));

        // Kalau SW baru sudah aktif, reload halaman
        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (!refreshing) { refreshing = true; window.location.reload(); }
        });
    });
}

// Banner update app (muncul di bawah halaman)
function showUpdateBanner() {
    if (document.getElementById('__pwaUpdateBanner')) return;
    const banner = document.createElement('div');
    banner.id = '__pwaUpdateBanner';
    banner.innerHTML = `
        <div style="
            position:fixed; bottom:80px; left:50%; transform:translateX(-50%);
            background:#4A0E35; color:#fff; border-radius:16px; padding:12px 20px;
            display:flex; align-items:center; gap:12px; z-index:9999;
            font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600;
            box-shadow:0 8px 24px rgba(74,14,53,0.35); white-space:nowrap;
            animation:__slideUpBanner .3s ease;
        ">
            <style>@keyframes __slideUpBanner{from{opacity:0;transform:translateX(-50%) translateY(16px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}</style>
            🔄 Versi baru tersedia!
            <button onclick="window.location.reload()" style="
                background:#7B1E5A; border:none; color:#fff; padding:6px 14px;
                border-radius:10px; cursor:pointer; font-weight:700; font-size:12px;
                font-family:inherit;
            ">Perbarui</button>
            <button onclick="this.closest('#__pwaUpdateBanner').remove()" style="
                background:none; border:none; color:rgba(255,255,255,.6);
                cursor:pointer; font-size:18px; line-height:1; padding:0 4px;
            ">×</button>
        </div>
    `;
    document.body.appendChild(banner);
}
</script>