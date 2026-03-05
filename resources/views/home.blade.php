<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Beranda</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Pusher JS untuk real-time -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <!-- Ionicons untuk icon (web equivalent) -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: {
                            DEFAULT: '#7B1E5A',
                            light:   '#9B2E72',
                            dark:    '#4A0E35',
                            pale:    '#FFF9FB',
                            soft:    '#F3E6FA',
                            muted:   '#A2397B',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

        /* ── Desktop phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex; align-items: flex-start; justify-content: center;
                min-height: 100vh; padding: 32px 0;
                background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 60%, #e8d5ee 100%);
            }
            .phone-frame {
                width: 390px; min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(123,30,90,0.25),
                            0 0 0 8px #1a0d14, 0 0 0 10px #2d1020;
                overflow: hidden;
                position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        /* ── Header wave ── */
        .header-bg {
            background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%);
        }
        .header-wave {
            border-radius: 0 0 30px 30px;
        }

        /* ── Slider ── */
        .slider-track {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }
        .slide-item {
            min-width: 100%;
            height: 180px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .slide-placeholder {
            min-width: 100%; height: 180px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* ── Dot indicator ── */
        .dot {
            width: 6px; height: 6px; border-radius: 999px;
            background: rgba(255,255,255,0.45);
            transition: all 0.3s ease;
        }
        .dot.active {
            background: #ffffff;
            width: 20px;
        }

        /* ── Menu card ── */
        .menu-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .menu-card:active {
            transform: scale(0.93);
        }

        /* ── Badge pulse ── */
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.15); }
        }
        .badge-pulse { animation: badgePulse 1.5s ease-in-out infinite; }

        /* ── Skeleton shimmer ── */
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f0dcea 25%, #fce8f5 50%, #f0dcea 75%);
            background-size: 400px 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 12px;
        }

        /* ── Slide-up enter ── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up            { animation: slideUp 0.4s ease forwards; }
        .anim-up.delay-1    { animation-delay: 0.05s; opacity: 0; }
        .anim-up.delay-2    { animation-delay: 0.12s; opacity: 0; }
        .anim-up.delay-3    { animation-delay: 0.20s; opacity: 0; }
        .anim-up.delay-4    { animation-delay: 0.28s; opacity: 0; }

        /* ── Chat FAB bounce ── */
        @keyframes fabIn {
            0%   { transform: scale(0) rotate(-20deg); opacity: 0; }
            70%  { transform: scale(1.1) rotate(5deg); }
            100% { transform: scale(1) rotate(0); opacity: 1; }
        }
        .fab-in { animation: fabIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s forwards; opacity: 0; }

        /* ── Scrollbar hide ── */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ── Empty state ── */
        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- ─── STATUS BAR (desktop only) ─────────────────────────────────────── -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    <!-- ─── HEADER ────────────────────────────────────────────────────────── -->
    <div class="header-bg header-wave px-5 pt-10 pb-10 relative shrink-0">

        <!-- Decorative circle -->
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white/5 translate-y-6 -translate-x-6"></div>

        <div class="relative flex items-center justify-between">
            <!-- Greeting -->
            <div class="anim-up delay-1">
                <p class="text-white/70 text-xs font-medium mb-1" id="greetingText">Selamat Pagi!</p>
                <h1 class="text-white text-2xl font-extrabold tracking-wide leading-tight">
                    {{ session('user')['name'] ?? 'Pengguna' }}
                </h1>
                <p class="text-white/50 text-xs mt-1">
                    {{ session('user')['role'] ?? '' }}
                </p>
            </div>

            <!-- Chat Button -->
            <a href="{{ route('chat.list') }}"
               id="chatBtn"
               class="fab-in relative flex items-center justify-center w-12 h-12 rounded-2xl bg-white shadow-lg shadow-plum/30"
               onclick="clearUnread()"
            >
                <ion-icon name="chatbubble-ellipses" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                <!-- Unread Badge -->
                <span id="unreadBadge"
                      class="badge-pulse absolute -top-1.5 -right-1.5 hidden min-w-[22px] h-[22px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white px-1">
                    0
                </span>
            </a>
        </div>
    </div>

    <!-- ─── SCROLLABLE BODY ───────────────────────────────────────────────── -->
    <div class="flex-1 overflow-y-auto no-scrollbar" id="mainScroll">

        <!-- ── BANNER SLIDER ─────────────────────────────────────────────── -->
        <div class="px-4 anim-up delay-2 mt-6">
            <div class="rounded-3xl overflow-hidden shadow-xl shadow-plum/15 bg-white relative">
                <!-- Slides -->
                <div class="overflow-hidden">
                    <div id="sliderTrack" class="slider-track">

                        @php
                            $banners = [
                                asset('image/banner1.jpeg'),
                                asset('image/banner2.jpeg'),
                                asset('image/banner3.jpeg'),
                            ];
                        @endphp

                        @foreach($banners as $i => $src)
                        <img src="{{ $src }}"
                             alt="Banner {{ $i + 1 }}"
                             class="slide-item"
                             style="min-width:100%; height:220px; object-fit:cover; flex-shrink:0; display:block;"
                        />
                        @endforeach

                    </div>
                </div>

                <!-- Pagination dots -->
                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                    <div class="bg-black/20 rounded-full flex gap-1.5 px-3 py-1.5">
                        @foreach([0,1,2] as $i)
                        <div class="dot {{ $i === 0 ? 'active' : '' }}" data-dot="{{ $i }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ── MENU SECTION ──────────────────────────────────────────────── -->
        <div class="px-4 pt-6 pb-16 anim-up delay-3">

            <!-- Section header -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-plum-dark text-lg font-extrabold">Menu Utama</h2>
                    <p class="text-plum-muted text-xs mt-0.5">Akses fitur favorit kamu</p>
                </div>
                <div class="bg-plum-soft rounded-xl px-3 py-1.5 flex items-center gap-1">
                    <ion-icon name="apps-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                    <span class="text-plum text-xs font-bold">{{ count($menus) }} Menu</span>
                </div>
            </div>

            <!-- Menu Grid -->
            @if(count($menus) > 0)
            <div class="bg-white rounded-3xl p-4 shadow-sm shadow-plum/10">
                <div id="menuGrid" class="grid grid-cols-3 gap-3">
                    @foreach($menus as $i => $menu)
                    @php
                        try {
                            $menuUrl = $menu['route'] ? route($menu['route']) : '#';
                        } catch (\Exception $e) {
                            $menuUrl = '#';
                        }
                    @endphp
                    <a href="{{ $menuUrl }}"
                       class="menu-card flex flex-col items-center justify-center p-3 rounded-2xl bg-plum-pale border border-plum-soft hover:bg-plum-soft active:scale-95 transition-all"
                       style="animation: slideUp 0.3s ease {{ $i * 0.05 }}s both"
                    >
                        <div class="w-12 h-12 rounded-xl bg-plum-soft flex items-center justify-center mb-2 shadow-sm">
                            <ion-icon name="{{ $menu['icon'] ?? 'apps-outline' }}" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <span class="text-plum-dark text-[11px] font-semibold text-center leading-tight line-clamp-2">
                            {{ $menu['nama'] }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>

            @else
            <!-- Empty state -->
            <div class="bg-white rounded-3xl p-10 flex flex-col items-center shadow-sm shadow-plum/10">
                <div class="float-anim w-20 h-20 rounded-full bg-plum-soft flex items-center justify-center mb-4">
                    <ion-icon name="apps-outline" style="font-size:36px;color:#E0BBE4;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-base mb-1">Tidak ada menu</h3>
                <p class="text-plum-muted text-xs text-center leading-relaxed">
                    Hubungi administrator<br>untuk akses menu
                </p>
            </div>
            @endif

        </div>

        <!-- Spacer bottom (untuk clearance dari bottom nav) -->
        <div class="h-4"></div>
    </div>

    <!-- ─── BOTTOM NAV ───────────────────────────────────────────────────── -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<!-- ─── JAVASCRIPT ────────────────────────────────────────────────────────── -->
<script>
// ── Config dari Laravel (passed via Blade) ──────────────────────────────────
const USER_ID = {{ session('user')['id_user'] ?? 'null' }};
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const UNREAD_API     = "{{ route('api.unread') }}";
const CSRF_TOKEN     = "{{ csrf_token() }}";

// ── Status bar clock ─────────────────────────────────────────────────────────
function updateClock() {
    const now  = new Date();
    const h    = String(now.getHours()).padStart(2, '0');
    const m    = String(now.getMinutes()).padStart(2, '0');
    const el   = document.getElementById('statusTime');
    if (el) el.textContent = `${h}:${m}`;
}
updateClock();
setInterval(updateClock, 30000);

// ── Greeting ─────────────────────────────────────────────────────────────────
function updateGreeting() {
    const h = new Date().getHours();
    let txt;
    if      (h < 12) txt = 'Selamat Pagi! ☀️';
    else if (h < 15) txt = 'Selamat Siang! 🌤️';
    else if (h < 18) txt = 'Selamat Sore! 🌇';
    else             txt = 'Selamat Malam! 🌙';
    document.getElementById('greetingText').textContent = txt;
}
updateGreeting();

// ── Auto Slider ───────────────────────────────────────────────────────────────
(function initSlider() {
    const track  = document.getElementById('sliderTrack');
    const dots   = document.querySelectorAll('[data-dot]');
    const total  = dots.length;
    if (!track || total <= 1) return;

    let current = 0;

    function goTo(idx) {
        current = (idx + total) % total;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => {
            d.classList.toggle('active', i === current);
        });
    }

    // Auto advance every 3 seconds
    setInterval(() => goTo(current + 1), 3000);

    // Touch swipe support
    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend',   e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(current + (diff > 0 ? 1 : -1));
    });
})();

// ── Unread Count ─────────────────────────────────────────────────────────────
let unreadCount = 0;

function updateBadge(count) {
    unreadCount = Math.max(0, count);
    const badge = document.getElementById('unreadBadge');
    if (!badge) return;
    if (unreadCount > 0) {
        badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
        badge.classList.remove('hidden');
        badge.classList.add('flex');
    } else {
        badge.classList.add('hidden');
        badge.classList.remove('flex');
    }
}

async function fetchUnreadCount() {
    if (!AUTH_TOKEN) return;
    try {
        const res  = await fetch(UNREAD_API, {
            headers: { 'Authorization': `Bearer ${AUTH_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success && data.data) {
            updateBadge(data.data.unread_count || 0);
        }
    } catch (e) {
        // silent fail
    }
}

function clearUnread() {
    updateBadge(0);
}

// Fetch on load
fetchUnreadCount();
// Refresh when tab regains focus
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') fetchUnreadCount();
});

// ── Pusher Real-time ──────────────────────────────────────────────────────────
(function initPusher() {
    if (!USER_ID || !AUTH_TOKEN || !PUSHER_KEY) return;

    Pusher.logToConsole = false;

    const pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER,
        forceTLS: true,
        authEndpoint: PUSHER_AUTH_EP,
        auth: {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Authorization': `Bearer ${AUTH_TOKEN}`,
            }
        }
    });

    const channel = pusher.subscribe(`private-chat.${USER_ID}`);

    channel.bind('pusher:subscription_error', (status) => {
        console.warn('[Pusher] Subscription error:', status);
    });

    channel.bind('chat.new', (event) => {
        // Hanya tambah unread jika pesan untuk user ini
        if (event?.chat?.id_penerima == USER_ID) {
            updateBadge(unreadCount + 1);
        }
    });
})();
</script>

@include('partials.auth-guard')

</body>
</html>