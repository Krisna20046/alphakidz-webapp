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
                s.textContent = '.phone-wrapper{min-height:100vh!important;display:block!important;padding:0!important;background:#FFF!important}.phone-frame{min-height:100vh!important;width:100%!important;border-radius:0!important;box-shadow:none!important}';
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

        /* Desktop phone frame */
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

        /* Hero background */
        .hero-bg {
            background-color: #8B46D3;
            background-image: url('/assets/bg-texture-login.png');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-color: #8B46D3;
            opacity: 0.60;
            pointer-events: none;
        }

        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.45s ease forwards; opacity: 0; }
        .d1 { animation-delay: 0.05s; }
        .d2 { animation-delay: 0.14s; }
        .d3 { animation-delay: 0.23s; }
        .d4 { animation-delay: 0.32s; }
        .d5 { animation-delay: 0.41s; }
        .d6 { animation-delay: 0.50s; }
        .d7 { animation-delay: 0.59s; }

        /* Toast */
        #toast {
            transition: all 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }
        #toast.show { transform: translateY(0); opacity: 1; }
    </style>
</head>
<body class="bg-[#E5E2F5]">

<div class="phone-wrapper">
<div class="phone-frame bg-white">

    @include('components.status-bar')

    <!-- Toast -->
    <div id="toast" class="fixed sm:absolute top-0 left-0 right-0 z-50 mx-auto max-w-sm">
        <div id="toast-inner" class="mx-4 mt-2 bg-red-500 text-white text-sm font-bold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span id="toast-msg"></span>
        </div>
    </div>

    @yield('content')

</div>
</div>

<script>
    // Toast helper
    function showToast(msg, type = 'error') {
        const toast = document.getElementById('toast');
        const inner = document.getElementById('toast-inner');
        document.getElementById('toast-msg').textContent = msg;
        inner.className = `mx-4 mt-2 text-white text-sm font-bold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2 ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }
</script>

@stack('scripts')

</body>
</html>
