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
        :root {
            --purple:       #8B46D3;
            --purple-light: #8B5CF6;
            --purple-soft:  #EDE9FE;
            --purple-pale:  #F5F3FF;
            --pink:         #EC4899;
            --yellow:       #F59E0B;
            --blue:         #6366F1;
            --text-dark:    #1E1B2E;
            --text-mid:     #6B7280;
            --text-light:   #9CA3AF;
            --bg:           #F8F7FF;
            --white:        #FFFFFF;
            --radius:       15px;
            --radius-lg:    50px;
            --shadow:       0 4px 20px rgba(124,58,237,0.12);
            --shadow-card:  0 2px 12px rgba(0,0,0,0.07);
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; margin: 0; padding: 0; }
        body { font-family: 'Nunito', sans-serif; background: #E5E2F5; }

        /* ── Desktop phone frame ─────────────────────────────────────────── */
        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex; align-items: flex-start; justify-content: center;
                min-height: 100vh; padding: 32px 0 60px;
            }
            .phone-frame {
                width: 390px; min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(124,58,237,0.28),
                            0 0 0 8px #1a1030, 0 0 0 10px #2d1a50;
                overflow: hidden;
            }
        }
        @media (max-width: 639px) {
            body { background: var(--bg); }
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        /* ── Layout ──────────────────────────────────────────────────────── */
        .phone-frame {
            background: var(--bg);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* ── Status bar ──────────────────────────────────────────────────── */
        .status-bar {
            background: var(--purple);
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 24px 0;
            color: white; font-size: 12px; font-weight: 700;
        }
        .status-icons { display: flex; align-items: center; gap: 6px; }

        /* ── Header ──────────────────────────────────────────────────────── */
        .header {
            background: url("/assets/bg-texture.png") repeat center center;
            background-size: cover;
            background-color: var(--purple);
            padding: 60px 20px 70px;
            position: relative; 
            overflow: hidden;
            z-index: 1;
        }

        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--purple);
            opacity: 0.6;
            z-index: -1;
        }
        .header-inner {
            display: flex; align-items: center; justify-content: space-between;
            position: relative; z-index: 1;
        }
        .header-left { display: flex; align-items: center; gap: 12px; }

        .avatar-wrap {
            width: 52px; height: 52px; border-radius: 50%;
            /* background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.4); */
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 26px;
        }

        .greeting-text  { color: rgba(255,255,255,0.8); font-size: 12px; font-weight: 600; margin-bottom: 2px; }
        .greeting-name  { color: white; font-size: 18px; font-weight: 800; line-height: 1.2; }
        .greeting-sub   { color: rgba(255,255,255,0.7); font-size: 11px; font-weight: 500; margin-top: 2px; }

        .notif-btn {
            width: 44px; height: 44px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            position: relative; cursor: pointer; text-decoration: none;
        }
        .notif-dot {
            position: absolute; top: 8px; right: 9px;
            width: 9px; height: 9px;
            background: #FCD34D; border-radius: 50%;
            border: 1.5px solid var(--purple);
        }

        /* ── Scrollable body ─────────────────────────────────────────────── */
        .main-scroll {
            display: flex;
            flex-direction: column;
            flex: 1; overflow-y: auto;
            padding: 30px;
            padding-bottom: 80px;
            background: linear-gradient(to bottom, var(--bg) 70%, rgba(212,186,239,0.5));
            border-radius: 50px 50px 0 0;
            margin-top: -50px;
            position: relative; z-index: 2;
            gap: 20px;
        }
        .main-scroll::-webkit-scrollbar { display: none; }

        /* ── Banner slider ───────────────────────────────────────────────── */
        /* .banner-section { padding: 20px 16px 0; } */
        .banner-wrap {
            border-radius: var(--radius);
            overflow: hidden; position: relative;
            height: 180px; box-shadow: var(--shadow);
        }
        .slider-track {
            display: flex;
            transition: transform 0.45s cubic-bezier(0.4,0,0.2,1);
            height: 100%;
        }
        .slide {
            min-width: 100%; height: 180px;
            flex-shrink: 0; position: relative; overflow: hidden;
        }
        .slide-bg {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
            filter: brightness(0.75);
        }
        .slide-content {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 16px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
        }
        .slide-title { color: white; font-size: 15px; font-weight: 800; line-height: 1.3; }
        .slide-sub   { color: rgba(255,255,255,0.75); font-size: 10px; font-weight: 500; margin-top: 2px; }
        .slider-dots {
            position: absolute; bottom: 12px; right: 14px;
            display: flex; gap: 5px; align-items: center;
        }
        .dot {
            width: 6px; height: 6px; border-radius: 99px;
            background: rgba(255,255,255,0.45);
            transition: all 0.3s ease;
        }
        .dot.active { background: white; width: 18px; }

        /* ── Section ─────────────────────────────────────────────────────── */
        /* .section { padding: 20px 16px 0; } */
        .section-head {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 14px;
        }
        .section-title { font-size: 17px; font-weight: 800; color: var(--text-dark); }
        .see-all { font-size: 12px; font-weight: 700; color: var(--purple); text-decoration: none; }

        /* ── Menu grid ───────────────────────────────────────────────────── */
        .menu-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .menu-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 18px 14px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 10px; cursor: pointer; text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: var(--shadow-card);
        }
        .menu-card:active { transform: scale(0.94); }

        .menu-icon-wrap {
            width: 52px; height: 52px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .menu-label {
            font-size: 13px; font-weight: 700;
            text-align: center; line-height: 1.3;
        }

        /* Card colour variants */
        .card-purple .menu-icon-wrap { background: var(--purple-soft); }
        .card-purple .menu-label     { color: var(--purple); }
        .card-pink   .menu-icon-wrap { background: #FDF2F8; }
        .card-pink   .menu-label     { color: var(--pink); }
        .card-yellow .menu-icon-wrap { background: #FFFBEB; }
        .card-yellow .menu-label     { color: var(--yellow); }
        .card-blue   .menu-icon-wrap { background: #EEF2FF; }
        .card-blue   .menu-label     { color: var(--blue); }

        /* ── Articles ────────────────────────────────────────────────────── */
        .articles-row {
            display: flex; gap: 12px;
            overflow-x: auto; padding-bottom: 4px;
        }
        .articles-row::-webkit-scrollbar { display: none; }

        .article-card {
            min-width: 200px; background: var(--white);
            border-radius: var(--radius); overflow: hidden;
            box-shadow: var(--shadow-card); flex-shrink: 0;
            text-decoration: none; display: flex; flex-direction: column;
            transition: transform 0.15s ease;
        }
        .article-card:active { transform: scale(0.96); }

        .article-img {
            width: 100%; height: 110px; object-fit: cover;
        }
        .article-img-placeholder {
            width: 100%; height: 110px;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
        }

        .article-body { padding: 12px; flex: 1; }
        .article-tag {
            display: inline-block; padding: 3px 10px;
            border-radius: 99px; font-size: 9px; font-weight: 700;
            margin-bottom: 8px;
            background: #FDF2F8; color: var(--pink);
        }
        .article-title {
            font-size: 12px; font-weight: 700; color: var(--text-dark);
            line-height: 1.45;
            display: -webkit-box; -webkit-line-clamp: 3;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .article-meta {
            display: flex; align-items: center; gap: 10px; margin-top: 8px;
        }
        .meta-item {
            display: flex; align-items: center; gap: 3px;
            font-size: 9px; color: var(--text-light); font-weight: 600;
        }

        /* ── Unread badge ────────────────────────────────────────────────── */
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.15); }
        }
        .badge-pulse { animation: badgePulse 1.5s ease-in-out infinite; }

        /* ── Animations ──────────────────────────────────────────────────── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim    { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.30s; }
        .delay-5 { animation-delay: 0.38s; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame">

    <!-- ─── STATUS BAR (desktop only) ─────────────────────────────────────── -->
    <div class="hidden sm:block status-bar">
        <span id="statusTime">9:41</span>
        <div class="status-icons">
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
            <div style="display:flex;align-items:center;">
                <div style="width:22px;height:11px;border:1.5px solid rgba(255,255,255,0.7);border-radius:3px;padding:1.5px;">
                    <div style="background:white;border-radius:1.5px;height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── HEADER ────────────────────────────────────────────────────────── -->
    <div class="header anim delay-1">
        <div class="header-inner">
            <div class="header-left">
                <div class="avatar-wrap">
                    <img src="{{ asset('assets/logo-1.png') }}" alt="avatar">
                </div>
                <div>
                    <p class="greeting-text" id="greetText">Good Morning,</p>
                    <h1 class="greeting-name">{{ session('user')['name'] ?? 'Pengguna' }}</h1>
                    <p class="greeting-sub">Ready to find the best care?</p>
                </div>
            </div>

            <!-- Notification / Chat button -->
            <a href="{{ route('chat.list') }}"
               id="chatBtn"
               class="notif-btn"
               onclick="clearUnread()">
                <ion-icon name="notifications" style="font-size:20px;color:white;"></ion-icon>
                <span id="unreadBadge"
                      class="badge-pulse hidden"
                      style="position:absolute;top:6px;right:7px;
                             min-width:18px;height:18px;
                             background:#FCD34D;color:#1E1B2E;
                             font-size:9px;font-weight:800;
                             border-radius:99px;
                             display:none;align-items:center;justify-content:center;
                             border:1.5px solid var(--purple);padding:0 3px;">
                    0
                </span>
            </a>
        </div>
    </div>

    <!-- ─── SCROLLABLE BODY ───────────────────────────────────────────────── -->
    <div class="main-scroll" id="mainScroll">

        <!-- ── BANNER SLIDER ─────────────────────────────────────────────── -->
        <div class="banner-section anim delay-2">
            <div class="banner-wrap">
                <div class="slider-track" id="sliderTrack">

                    @php
                        $banners = [
                            ['src' => asset('image/banner1.jpeg'), 'title' => 'Expert Care for your Loved Ones',   'sub' => 'Trusted by 27,000+ happy families worldwide.'],
                            ['src' => asset('image/banner2.jpeg'), 'title' => 'Find Trusted Nannies Near You',     'sub' => 'Verified & background checked professionals.'],
                            ['src' => asset('image/banner3.jpeg'), 'title' => 'Book a Consultation Today',         'sub' => 'Expert advice for every parenting challenge.'],
                        ];
                    @endphp

                    @foreach($banners as $banner)
                    <div class="slide">
                        <div class="slide-bg"
                             style="background-image: url('{{ $banner['src'] }}');"></div>
                        <div class="slide-content">
                            <p class="slide-title">{{ $banner['title'] }}</p>
                            <p class="slide-sub">{{ $banner['sub'] }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>

                <!-- Dots -->
                <div class="slider-dots">
                    @foreach($banners as $i => $banner)
                    <div class="dot {{ $i === 0 ? 'active' : '' }}" data-dot="{{ $i }}"></div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ── ALL MENUS ─────────────────────────────────────────────────── -->
        <div class="section anim delay-3">
            <div class="section-head">
                <span class="section-title">All Menus</span>
            </div>

            @if(count($menus) > 0)
            @php
                $cardColors = ['card-purple', 'card-pink', 'card-yellow', 'card-blue'];
                $iconColors = ['#7C3AED', '#EC4899', '#F59E0B', '#6366F1'];
            @endphp

            <div class="menu-grid">
                @foreach($menus as $i => $menu)
                @php
                    try { $menuUrl = $menu['route'] ? route($menu['route']) : '#'; }
                    catch (\Exception $e) { $menuUrl = '#'; }
                    $colorClass = $cardColors[$i % count($cardColors)];
                    $iconColor  = $iconColors[$i % count($iconColors)];
                @endphp
                <a href="{{ $menuUrl }}"
                   class="menu-card {{ $colorClass }}"
                   style="animation: slideUp 0.35s ease {{ $i * 0.06 }}s both;">
                    <div class="menu-icon-wrap">
                        <ion-icon name="{{ $menu['icon'] ?? 'apps-outline' }}"
                                  style="font-size:26px;color:{{ $iconColor }};"></ion-icon>
                    </div>
                    <span class="menu-label">{{ $menu['nama'] }}</span>
                </a>
                @endforeach
            </div>

            @else
            <!-- Empty state -->
            <div style="background:white;border-radius:var(--radius-lg);padding:40px 20px;
                        display:flex;flex-direction:column;align-items:center;
                        box-shadow:var(--shadow-card);">
                <div style="width:72px;height:72px;border-radius:50%;
                            background:var(--purple-soft);
                            display:flex;align-items:center;justify-content:center;
                            margin-bottom:14px;font-size:32px;">
                    📋
                </div>
                <h3 style="color:var(--text-dark);font-size:15px;font-weight:800;margin-bottom:6px;">
                    Tidak ada menu
                </h3>
                <p style="color:var(--text-light);font-size:12px;text-align:center;line-height:1.6;">
                    Hubungi administrator<br>untuk akses menu
                </p>
            </div>
            @endif
        </div>

        <!-- ── PARENTING ARTICLES ─────────────────────────────────────────── -->
        <div class="section anim delay-4">
            <div class="section-head">
                <span class="section-title">Parenting Articles</span>
                <a href="{{ route('artikel.index') }}" class="see-all">See All</a>
            </div>

            <div class="articles-row">
                @forelse($artikels ?? [] as $artikel)
                <a class="article-card" href="{{ route('artikel.show', $artikel['id']) }}">
                    @if(!empty($artikel['thumbnail']))
                        <img class="article-img"
                             src="{{ $artikel['thumbnail'] }}"
                             alt="{{ $artikel['judul'] }}"
                             loading="lazy">
                    @else
                        <div class="article-img-placeholder"
                             style="background:linear-gradient(135deg,#EDE9FE,#FDF2F8);">📖</div>
                    @endif
                    <div class="article-body">
                        <span class="article-tag">{{ $artikel['kategori'] ?? 'Artikel' }}</span>
                        <p class="article-title">{{ $artikel['judul'] }}</p>
                        <div class="article-meta">
                            <span class="meta-item">
                                <ion-icon name="time-outline" style="font-size:10px;"></ion-icon>
                                {{ $artikel['read_time'] ?? '5' }} min read
                            </span>
                            <span class="meta-item">
                                <ion-icon name="eye-outline" style="font-size:10px;"></ion-icon>
                                {{ $artikel['views'] ?? '0' }} views
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                {{-- Dummy articles when no data ─────────────────────────────── --}}
                @php
                    $dummyArticles = [
                        ['emoji' => '🌻', 'bg' => 'linear-gradient(135deg,#EDE9FE,#FDF2F8)', 'tag' => 'Tips & Trick',  'title' => '5 Tips to Maintain Your Little One\'s Sleep Pattern', 'time' => '5', 'views' => '1.2k'],
                        ['emoji' => '🍼', 'bg' => 'linear-gradient(135deg,#FDF2F8,#FFFBEB)', 'tag' => 'Nutrition',     'title' => 'Best Foods for Toddlers: A Complete Nutrition Guide',  'time' => '7', 'views' => '3.4k'],
                        ['emoji' => '🧩', 'bg' => 'linear-gradient(135deg,#EEF2FF,#EDE9FE)', 'tag' => 'Development',  'title' => 'How to Stimulate Your Baby\'s Cognitive Development',  'time' => '4', 'views' => '890'],
                    ];
                @endphp
                @foreach($dummyArticles as $dummy)
                <a class="article-card" href="{{ route('artikel.index') }}">
                    <div class="article-img-placeholder"
                         style="background:{{ $dummy['bg'] }};">{{ $dummy['emoji'] }}</div>
                    <div class="article-body">
                        <span class="article-tag">{{ $dummy['tag'] }}</span>
                        <p class="article-title">{{ $dummy['title'] }}</p>
                        <div class="article-meta">
                            <span class="meta-item">
                                <ion-icon name="time-outline" style="font-size:10px;"></ion-icon>
                                {{ $dummy['time'] }} min read
                            </span>
                            <span class="meta-item">
                                <ion-icon name="eye-outline" style="font-size:10px;"></ion-icon>
                                {{ $dummy['views'] }} views
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
                @endforelse
            </div>
        </div>

        <div style="height:20px;"></div>
    </div>

    <!-- ─── BOTTOM NAV ───────────────────────────────────────────────────── -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<!-- ─── JAVASCRIPT ────────────────────────────────────────────────────────── -->
<script>
const USER_ID        = {{ session('user')['id_user'] ?? 'null' }};
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const UNREAD_API     = "{{ route('api.unread') }}";

// ── Status bar clock ──────────────────────────────────────────────────────────
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

// ── Greeting ──────────────────────────────────────────────────────────────────
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

// ── Auto Slider ───────────────────────────────────────────────────────────────
(function initSlider() {
    const track = document.getElementById('sliderTrack');
    const dots  = document.querySelectorAll('[data-dot]');
    const total = dots.length;
    if (!track || total <= 1) return;

    let current = 0;

    function goTo(idx) {
        current = (idx + total) % total;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    setInterval(() => goTo(current + 1), 3500);

    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend',   e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(current + (diff > 0 ? 1 : -1));
    });
})();

// ── Unread badge ──────────────────────────────────────────────────────────────
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

// ── Pusher real-time ──────────────────────────────────────────────────────────
(function initPusher() {
    if (!USER_ID || !AUTH_TOKEN || !PUSHER_KEY) return;

    const pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER,
        forceTLS: true,
        authEndpoint: PUSHER_AUTH_EP,
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Authorization': `Bearer ${AUTH_TOKEN}`,
            }
        }
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