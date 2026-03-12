<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Beranda</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        .badge-pulse { animation: badgePulse 1.5s ease-in-out infinite; }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.30s; }
        .delay-5 { animation-delay: 0.38s; }
        
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F8F7FF] min-h-screen flex flex-col relative">

    <!-- STATUS BAR (desktop only) -->
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
                <rect x="13.5" y="0" width="3" height="11" rx="0.6" fill="white" opacity="0.25"/>
            </svg>
            <svg width="16" height="12" viewBox="0 0 16 12" fill="white">
                <path d="M8 3C5.5 3 3.3 4 1.7 5.6L0 3.8C2.1 1.7 5 0.5 8 0.5s5.9 1.2 8 3.3L14.3 5.6C12.7 4 10.5 3 8 3z" opacity="0.5"/>
                <path d="M8 6.5c-1.5 0-2.8.6-3.8 1.5L2.5 6.2C3.9 4.8 5.9 4 8 4s4.1.8 5.5 2.2L11.8 8C10.8 7.1 9.5 6.5 8 6.5z" opacity="0.75"/>
                <circle cx="8" cy="10.5" r="2"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[30px] pt-[60px] pb-[70px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-[52px] h-[52px] rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 text-[26px]">
                    <img src="{{ asset('assets/logo-1.png') }}" alt="avatar" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-white/80 text-xs font-semibold mb-0.5" id="greetText">Good Morning,</p>
                    <h1 class="text-white text-lg font-extrabold leading-tight">{{ session('user')['name'] ?? 'Pengguna' }}</h1>
                    <p class="text-white/70 text-xs font-medium mt-0.5">Ready to find the best care?</p>
                </div>
            </div>

            <!-- Notification / Chat button -->
            <a href="{{ route('chat.list') }}"
               id="chatBtn"
               class="w-11 h-11 rounded-full bg-white/15 border-[1.5px] border-white/25 flex items-center justify-center relative cursor-pointer no-underline">
                <ion-icon name="notifications" class="text-white text-xl"></ion-icon>
                <span id="unreadBadge"
                      class="badge-pulse hidden absolute top-[6px] right-[7px] min-w-[18px] h-[18px] bg-[#FCD34D] text-[#1E1B2E] text-[9px] font-extrabold rounded-full hidden items-center justify-center border-[1.5px] border-[#8B46D3] px-[3px]"
                      style="display: none;">
                    0
                </span>
            </a>
        </div>
    </div>

    <!-- SCROLLABLE BODY -->
    <div class="flex-1 overflow-y-auto px-[30px] pt-[30px] pb-20 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 flex flex-col gap-5 hide-scrollbar" id="mainScroll">

        <!-- BANNER SLIDER -->
        <div class="anim delay-2">
            <div class="rounded-[15px] overflow-hidden relative h-[180px] shadow-[0_4px_20px_rgba(124,58,237,0.12)]">
                <div class="flex transition-transform duration-[0.45s] ease-[cubic-bezier(0.4,0,0.2,1)] h-full" id="sliderTrack">

                    @php
                        $banners = [
                            ['src' => asset('image/banner1.jpeg'), 'title' => 'Expert Care for your Loved Ones',   'sub' => 'Trusted by 27,000+ happy families worldwide.'],
                            ['src' => asset('image/banner2.jpeg'), 'title' => 'Find Trusted Nannies Near You',     'sub' => 'Verified & background checked professionals.'],
                            ['src' => asset('image/banner3.jpeg'), 'title' => 'Book a Consultation Today',         'sub' => 'Expert advice for every parenting challenge.'],
                        ];
                    @endphp

                    @foreach($banners as $banner)
                    <div class="min-w-full h-[180px] flex-shrink-0 relative overflow-hidden">
                        <div class="absolute inset-0 bg-cover bg-center brightness-75"
                             style="background-image: url('{{ $banner['src'] }}');"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                            <p class="text-white text-sm font-extrabold leading-tight">{{ $banner['title'] }}</p>
                            <p class="text-white/75 text-[10px] font-medium mt-0.5">{{ $banner['sub'] }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>

                <!-- Dots -->
                <div class="absolute bottom-3 right-3.5 flex gap-[5px] items-center">
                    @foreach($banners as $i => $banner)
                    <div class="w-1.5 h-1.5 rounded-full bg-white/45 transition-all duration-300 {{ $i === 0 ? 'bg-white w-[18px]' : '' }}" data-dot="{{ $i }}"></div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- MAIN MENU -->
        <div class="anim delay-3">
            <div class="flex items-center justify-between mb-[14px]">
                <span class="text-[#1E1B2E] text-[17px] font-extrabold">Main Menu</span>
            </div>

            @if(count($menus) > 0)
            @php
                $menuVariants = [
                    ['iconBg' => 'bg-[#EDE9FE]', 'text' => 'text-[#8B46D3]', 'icon' => '#7C3AED'],
                    ['iconBg' => 'bg-[#FDF2F8]', 'text' => 'text-[#EC4899]', 'icon' => '#EC4899'],
                    ['iconBg' => 'bg-[#FFFBEB]', 'text' => 'text-[#F59E0B]', 'icon' => '#F59E0B'],
                    ['iconBg' => 'bg-[#EEF2FF]', 'text' => 'text-[#6366F1]', 'icon' => '#6366F1'],
                ];
            @endphp

            <div class="grid grid-cols-2 gap-3">
                @foreach($menus as $i => $menu)
                @php
                    try { $menuUrl = $menu['route'] ? route($menu['route']) : '#'; }
                    catch (\Exception $e) { $menuUrl = '#'; }
                    $variant = $menuVariants[$i % count($menuVariants)];
                @endphp
                <a href="{{ $menuUrl }}"
                   class="bg-white rounded-[15px] p-[18px_14px] flex flex-col items-center justify-center gap-2.5 cursor-pointer no-underline transition-transform duration-150 ease-in-out hover:scale-[0.96] active:scale-[0.94] shadow-[0_2px_12px_rgba(0,0,0,0.07)]"
                   style="animation: slideUp 0.35s ease {{ $i * 0.06 }}s both;">
                    <div class="w-[52px] h-[52px] rounded-2xl flex items-center justify-center {{ $variant['iconBg'] }}">
                        <ion-icon name="{{ $menu['icon'] ?? 'apps-outline' }}" style="font-size:26px;color:{{ $variant['icon'] }};"></ion-icon>
                    </div>
                    <span class="text-xs font-bold text-center leading-tight {{ $variant['text'] }}">{{ $menu['nama'] }}</span>
                </a>
                @endforeach
            </div>

            @else
            <!-- Empty state -->
            <div class="bg-white rounded-[50px] p-10 flex flex-col items-center shadow-[0_2px_12px_rgba(0,0,0,0.07)]">
                <div class="w-[72px] h-[72px] rounded-full bg-[#EDE9FE] flex items-center justify-center mb-[14px] text-3xl">
                    📋
                </div>
                <h3 class="text-[#1E1B2E] text-sm font-extrabold mb-1.5">Tidak ada menu</h3>
                <p class="text-[#9CA3AF] text-xs text-center leading-relaxed">
                    Hubungi administrator<br>untuk akses menu
                </p>
            </div>
            @endif
        </div>

        <!-- PARENTING ARTICLES -->
        <div class="anim delay-4">
            <div class="flex items-center justify-between mb-[14px]">
                <span class="text-[#1E1B2E] text-[17px] font-extrabold">Parenting Articles</span>
                <a href="{{ route('artikel.index') }}" class="text-[#8B46D3] text-xs font-bold no-underline">See All</a>
            </div>

            <div class="flex gap-3 overflow-x-auto pb-1 hide-scrollbar">
                @forelse($artikels ?? [] as $artikel)
                <a class="min-w-[200px] bg-white rounded-[15px] overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.07)] flex-shrink-0 no-underline flex flex-col transition-transform duration-150 ease-in-out active:scale-[0.96]" href="{{ route('artikel.show', $artikel['id']) }}">
                    @if(!empty($artikel['thumbnail']))
                        <img class="w-full h-[110px] object-cover"
                             src="{{ $artikel['thumbnail'] }}"
                             alt="{{ $artikel['judul'] }}"
                             loading="lazy">
                    @else
                        <div class="w-full h-[110px] flex items-center justify-center text-4xl bg-gradient-to-br from-[#EDE9FE] to-[#FDF2F8]">📖</div>
                    @endif
                    <div class="p-3 flex-1">
                        <span class="inline-block px-2.5 py-[3px] rounded-full bg-[#FDF2F8] text-[#EC4899] text-[9px] font-bold mb-2">{{ $artikel['kategori'] ?? 'Artikel' }}</span>
                        <p class="text-xs font-bold text-[#1E1B2E] leading-relaxed line-clamp-3">{{ $artikel['judul'] }}</p>
                        <div class="flex items-center gap-2.5 mt-2">
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="time-outline" class="text-[10px]"></ion-icon>
                                {{ $artikel['read_time'] ?? '5' }} min read
                            </span>
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="eye-outline" class="text-[10px]"></ion-icon>
                                {{ $artikel['views'] ?? '0' }} views
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                {{-- Dummy articles when no data --}}
                @php
                    $dummyArticles = [
                        ['emoji' => '🌻', 'bg' => 'bg-gradient-to-br from-[#EDE9FE] to-[#FDF2F8]', 'tag' => 'Tips & Trick',  'title' => '5 Tips to Maintain Your Little One\'s Sleep Pattern', 'time' => '5', 'views' => '1.2k'],
                        ['emoji' => '🍼', 'bg' => 'bg-gradient-to-br from-[#FDF2F8] to-[#FFFBEB]', 'tag' => 'Nutrition',     'title' => 'Best Foods for Toddlers: A Complete Nutrition Guide',  'time' => '7', 'views' => '3.4k'],
                        ['emoji' => '🧩', 'bg' => 'bg-gradient-to-br from-[#EEF2FF] to-[#EDE9FE]', 'tag' => 'Development',  'title' => 'How to Stimulate Your Baby\'s Cognitive Development',  'time' => '4', 'views' => '890'],
                    ];
                @endphp
                @foreach($dummyArticles as $dummy)
                <a class="min-w-[200px] bg-white rounded-[15px] overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.07)] flex-shrink-0 no-underline flex flex-col active:scale-[0.96] transition-transform" href="{{ route('artikel.index') }}">
                    <div class="w-full h-[110px] flex items-center justify-center text-4xl {{ $dummy['bg'] }}">{{ $dummy['emoji'] }}</div>
                    <div class="p-3">
                        <span class="inline-block px-2.5 py-[3px] rounded-full bg-[#FDF2F8] text-[#EC4899] text-[9px] font-bold mb-2">{{ $dummy['tag'] }}</span>
                        <p class="text-xs font-bold text-[#1E1B2E] leading-relaxed line-clamp-3">{{ $dummy['title'] }}</p>
                        <div class="flex items-center gap-2.5 mt-2">
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="time-outline" class="text-[10px]"></ion-icon>
                                {{ $dummy['time'] }} min read
                            </span>
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="eye-outline" class="text-[10px]"></ion-icon>
                                {{ $dummy['views'] }} views
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
                @endforelse
            </div>
        </div>

        <div class="h-5"></div>
    </div>

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<!-- JAVASCRIPT -->
<script>
// ── Config dari Laravel (passed via Blade) ──────────────────────────────────
const USER_ID = {{ session('user_id') ?? 'null' }};
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const UNREAD_API     = "{{ route('api.unread') }}";
const CSRF           = "{{ csrf_token() }}";


// Status bar clock
(function() {
    const el = document.getElementById('statusTime');
    function tick() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        if (el) el.textContent = `${h}:${m}`;
    }
    tick();
    setInterval(tick, 30000);
})();

// Greeting
(function() {
    const h = new Date().getHours();
    const map = [
        [0,  12, 'Good Morning,'],
        [12, 15, 'Good Afternoon,'],
        [15, 18, 'Good Evening,'],
        [18, 24, 'Good Night,'],
    ];
    const greeting = map.find(([s, e]) => h >= s && h < e)?.[2] ?? 'Hello,';
    const el = document.getElementById('greetText');
    if (el) el.textContent = greeting;
})();

// Auto Slider
(function initSlider() {
    const track = document.getElementById('sliderTrack');
    const dots  = document.querySelectorAll('[data-dot]');
    const total = dots.length;
    if (!track || total <= 1) return;

    let current = 0;

    function goTo(idx) {
        current = (idx + total) % total;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => {
            if (i === current) {
                d.classList.add('bg-white', 'w-[18px]');
                d.classList.remove('bg-white/45', 'w-1.5');
            } else {
                d.classList.remove('bg-white', 'w-[18px]');
                d.classList.add('bg-white/45', 'w-1.5');
            }
        });
    }

    setInterval(() => goTo(current + 1), 3500);

    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend',   e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(current + (diff > 0 ? 1 : -1));
    });
})();

// Unread badge
let unreadCount = 0;

function updateBadge(count) {
    unreadCount = Math.max(0, count);
    const badge = document.getElementById('unreadBadge');
    if (!badge) return;
    if (unreadCount > 0) {
        badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
        badge.style.display = 'flex';
        badge.classList.remove('hidden');
    } else {
        badge.style.display = 'none';
        badge.classList.add('hidden');
    }
}

function clearUnread() { updateBadge(0); }

async function fetchUnreadCount() {
    if (!AUTH_TOKEN) return;
    try {
        const res  = await fetch(UNREAD_API, {
            headers: { 'Authorization': `Bearer ${AUTH_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success && data.data) updateBadge(data.data.unread_count || 0);
    } catch (e) { /* silent */ }
}

fetchUnreadCount();
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') fetchUnreadCount();
});

// Pusher real-time
(function initPusher() {
    if (!USER_ID || !AUTH_TOKEN || !PUSHER_KEY) return;

    const pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER,
        forceTLS: true,
        authEndpoint: PUSHER_AUTH_EP,
        auth: { headers: { 'X-CSRF-TOKEN': CSRF, 'Authorization': `Bearer ${AUTH_TOKEN}` } }
    });

    const channel = pusher.subscribe(`private-chat.${USER_ID}`);
    channel.bind('chat.new', (event) => {
        if (event?.chat?.id_penerima == USER_ID) updateBadge(unreadCount + 1);
    });
})();
</script>

@include('partials.auth-guard')

</body>
</html>