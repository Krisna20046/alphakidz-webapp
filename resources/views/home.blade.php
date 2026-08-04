@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    @keyframes badgePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
    .badge-pulse { animation: badgePulse 1.5s ease-in-out infinite; }

    #homeReminderSection:not(:empty),
    #homeLowStockSection:not(:empty),
    #homeSharedStockSection:not(:empty) {
        display: block;
    }

    #homeReminderSection:empty,
    #homeLowStockSection:empty,
    #homeSharedStockSection:empty {
        display: none;
    }

    @keyframes reminderIn {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .reminder-card-anim { animation: reminderIn 0.35s ease forwards; }

    @keyframes alertSlideIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .alert-card-enter { animation: alertSlideIn 0.3s ease forwards; margin-bottom: 12px; }

    /* GPS Tracker Card */
    .gps-card {
        background: #FFFFFF;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .gps-mini-map {
        width: 100%;
        height: 260px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #E5E1F0;
    }
    .gps-nanny-item {
        background: #F8F8FB;
        border: 1px solid #ECEAF4;
        border-radius: 12px;
        transition: transform 0.15s ease;
    }
    .gps-nanny-item:active { transform: scale(0.98); }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }
    .gps-live-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #22C55E;
        animation: pulse-dot 1.8s ease-in-out infinite;
        display: inline-block;
    }
    .gps-live-dot.offline {
        background: #A8A2C2;
        animation: none;
    }
    .gps-refresh-btn {
        transition: transform 0.3s ease;
    }
    .gps-refresh-btn.spinning {
        transform: rotate(360deg);
    }
    .gps-mini-map .leaflet-control-zoom {
        border: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
        border-radius: 8px !important;
        overflow: hidden;
    }
    .gps-mini-map .leaflet-control-zoom a {
        width: 32px;
        height: 32px;
        line-height: 32px;
        font-size: 16px;
        font-weight: 700;
        color: #4B5563;
        background: white;
        border: none !important;
    }
    .gps-mini-map .leaflet-control-zoom a:hover {
        background: #F3F0FC;
        color: #8B46D3;
    }
    .gps-mini-map .leaflet-control-zoom a.leaflet-control-zoom-in {
        border-bottom: 1px solid #EDE9FE !important;
    }
    .gps-mini-map .leaflet-control-attribution {
        font-size: 9px;
        background: rgba(255,255,255,0.85);
        padding: 2px 6px;
        border-radius: 4px 0 0 0;
    }
    @keyframes marker-pulse {
        0% { box-shadow: 0 0 0 0 rgba(139,70,211,0.5); }
        70% { box-shadow: 0 0 0 14px rgba(139,70,211,0); }
        100% { box-shadow: 0 0 0 0 rgba(139,70,211,0); }
    }
    .gps-marker-pulse {
        animation: marker-pulse 2s ease-out infinite;
    }
    #gpsFullscreenOverlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #fff;
        display: none;
        flex-direction: column;
    }
    #gpsFullscreenOverlay.active { display: flex; }
    #gpsFullscreenMap {
        flex: 1;
        width: 100%;
        min-height: 0;
    }
</style>
@endpush

@section('content')
<!-- HEADER -->
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[30px] pt-[60px] pb-[70px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center justify-between relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-[52px] h-[52px] rounded-full overflow-hidden flex items-center justify-center flex-shrink-0 text-[26px]">
                <img src="{{ asset('assets/logo-1.png') }}" alt="avatar" class="w-full h-full object-cover">
            </div>
            <div>
                <p class="text-white/80 text-xs font-semibold mb-0.5" id="greetText">Good Morning,</p>
                <h1 class="text-white text-lg font-extrabold leading-tight">{{ session('user')['name'] ?? 'User' }}</h1>
                <p class="text-white/70 text-xs font-medium mt-0.5">Ready to find the best care?</p>
            </div>
        </div>

        <!-- Notification / Chat button -->
        <a href="{{ route('chat.list') }}"
           id="chatBtn"
           class="w-11 h-11 rounded-full bg-white/15 border-[1.5px] border-white/25 flex items-center justify-center relative cursor-pointer no-underline">
            <ion-icon name="chatbox-ellipses" class="text-white text-xl"></ion-icon>
            <span id="unreadBadge"
                  class="badge-pulse hidden absolute top-[3px] right-[2px] min-w-[16px] h-[16px] bg-[#FCD34D] text-[#1E1B2E] text-[9px] font-extrabold rounded-full hidden items-center justify-center border-[1.5px] border-[#8B46D3] px-[3px]"
                  style="display: none;">
                0
            </span>
        </a>
    </div>
</div>

<!-- SCROLLABLE BODY -->
    <div class="flex-1 overflow-y-auto px-[30px] pt-[30px] pb-20 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 flex flex-col gap-5 hide-scrollbar" id="mainScroll">

        {{-- UPCOMING REMINDER --}}
        <div id="homeReminderSection" class="hidden"></div>

        {{-- LOW STOCK ALERT --}}
        <div id="homeLowStockSection" class="hidden"></div>

        {{-- SHARED STOCK ALERT --}}
        <div id="homeSharedStockSection" class="hidden"></div>

        <!-- BANNER SLIDER -->
        <div class="anim delay-2">
            <div class="rounded-[15px] overflow-hidden relative h-[180px] shadow-[0_4px_20px_rgba(124,58,237,0.12)]">
                <div class="flex transition-transform duration-[0.45s] ease-[cubic-bezier(0.4,0,0.2,1)] h-full" id="sliderTrack">

                    @php
                        $banners = [
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

        {{-- GPS TRACKER — untuk MAJIKAN: lihat lokasi nanny --}}
        <div id="homeGpsSection" class="hidden">
            <div class="gps-card p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="locate" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div>
                            <h3 class="text-[#1E1B2E] text-[15px] font-extrabold leading-tight">Nanny Live Location</h3>
                            <p id="gpsStatusText" class="text-[#9CA3AF] text-[10px] font-semibold">Loading location...</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button id="gpsFullscreenBtn" onclick="openGpsFullscreen()" class="w-8 h-8 rounded-full bg-[#F3F0FC] flex items-center justify-center border-0 cursor-pointer shrink-0" title="Fullscreen">
                            <ion-icon name="expand-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </button>
                        <button id="gpsRefreshBtn" onclick="refreshGpsLocations()" class="gps-refresh-btn w-8 h-8 rounded-full bg-[#F3F0FC] flex items-center justify-center border-0 cursor-pointer shrink-0" title="Refresh">
                            <ion-icon name="refresh" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </button>
                    </div>
                </div>
                <div class="gps-mini-map mb-3" id="gpsMiniMap"></div>
                <div id="gpsNannyList" class="space-y-2">
                    <div class="gps-nanny-item p-3 flex items-center gap-3 animate-pulse">
                        <div class="w-10 h-10 rounded-full bg-[#ECE8FA] shrink-0"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-3 bg-[#ECE8FA] rounded-full w-2/3"></div>
                            <div class="h-2.5 bg-[#ECE8FA] rounded-full w-1/2"></div>
                        </div>
                        <div class="h-3 w-12 bg-[#ECE8FA] rounded-full"></div>
                    </div>
                    <div class="gps-nanny-item p-3 flex items-center gap-3 animate-pulse">
                        <div class="w-10 h-10 rounded-full bg-[#ECE8FA] shrink-0"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-3 bg-[#ECE8FA] rounded-full w-1/2"></div>
                            <div class="h-2.5 bg-[#ECE8FA] rounded-full w-3/4"></div>
                        </div>
                    </div>
                </div>
                <div id="gpsEmptyState" class="hidden flex flex-col items-center py-4">
                    <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-2">
                        <ion-icon name="locate-outline" style="font-size:28px;color:#C4B5FD;"></ion-icon>
                    </div>
                    <p class="text-[#9CA3AF] text-[12px] font-bold">No nanny assigned yet</p>
                </div>
            </div>
        </div>

        {{-- GPS FULLSCREEN OVERLAY --}}
        <div id="gpsFullscreenOverlay">
            <div class="flex items-center justify-between px-4 py-3 bg-white shadow-sm" style="z-index:1000;">
                <div class="flex items-center gap-2">
                    <button onclick="closeGpsFullscreen()" class="w-9 h-9 rounded-full bg-[#F3F0FC] flex items-center justify-center border-0 cursor-pointer">
                        <ion-icon name="arrow-back" style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                    <span class="text-[#1E1B2E] text-[15px] font-extrabold">Nanny Live Location</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button onclick="refreshGpsLocations()" class="gps-refresh-btn w-9 h-9 rounded-full bg-[#F3F0FC] flex items-center justify-center border-0 cursor-pointer" title="Refresh">
                        <ion-icon name="refresh" style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                    <button onclick="zoomToFitNannies()" class="w-9 h-9 rounded-full bg-[#F3F0FC] flex items-center justify-center border-0 cursor-pointer" title="Zoom to fit all">
                        <ion-icon name="scan-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                </div>
            </div>
            <div id="gpsFullscreenMap"></div>
        </div>

        {{-- GPS TRACKER — untuk NANNY: bagikan lokasi ke majikan --}}
        <div id="nannyGpsSection" class="hidden anim delay-3">
            <div class="gps-card p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="locate" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div>
                            <h3 class="text-[#1E1B2E] text-[15px] font-extrabold leading-tight">Share My Location</h3>
                            <p id="nannyGpsStatus" class="text-[#9CA3AF] text-[10px] font-semibold">Start sharing your location</p>
                        </div>
                    </div>
                    {{-- Toggle Switch --}}
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="nannyGpsToggle" class="sr-only peer" onchange="toggleNannyGps()">
                        <div class="w-11 h-6 bg-[#D1D5DB] rounded-full peer peer-checked:bg-[#8B46D3] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>

                {{-- Status card --}}
                <div id="nannyGpsInfo" class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[12px] p-3 flex flex-col items-center gap-2">
                    <div id="nannyGpsDot" class="w-12 h-12 rounded-full bg-[#D1D5DB] flex items-center justify-center">
                        <ion-icon name="location-outline" style="font-size:24px;color:white;"></ion-icon>
                    </div>
                    <p id="nannyGpsStatusLabel" class="text-[#9CA3AF] text-[12px] font-bold">Location sharing is OFF</p>
                    <p id="nannyGpsAddress" class="text-[#9CA3AF] text-[10px] font-semibold text-center hidden">📍 <span></span></p>
                    <p id="nannyGpsCoords" class="text-[#8B46D3] text-[9px] font-bold hidden"></p>
                    <p id="nannyGpsLastUpdate" class="text-[#9CA3AF] text-[9px] font-medium hidden">Last update: -</p>
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
                <h3 class="text-[#1E1B2E] text-sm font-extrabold mb-1.5">No menu</h3>
                <p class="text-[#9CA3AF] text-xs text-center leading-relaxed">
                    Contact the administrator<br>to get menu access
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
                <a class="w-[200px] bg-white rounded-[15px] overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.07)] flex-shrink-0 no-underline flex flex-col transition-transform duration-150 ease-in-out active:scale-[0.96]" href="{{ route('artikel.show', $artikel['id']) }}">
                    @if(!empty($artikel['thumbnail']))
                        <img class="w-full h-[110px] object-cover"
                             src="{{ $artikel['thumbnail'] }}"
                             alt="{{ $artikel['judul'] }}"
                             loading="lazy">
                    @else
                        <div class="w-full h-[110px] flex items-center justify-center text-4xl bg-gradient-to-br from-[#EDE9FE] to-[#FDF2F8]">📖</div>
                    @endif
                    <div class="p-3 flex-1">
            <span class="inline-block px-2.5 py-[3px] rounded-full bg-[#FDF2F8] text-[#EC4899] text-[9px] font-bold mb-2">{{ $artikel['kategori'] ?? 'Article' }}</span>
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
@include('partials.reminder')
@endsection

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const API_BASE  = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "https://api.alpha-kidz.com/api")), "/") }}';
@php
    $resolvedUserId = session('user_id') ?: data_get(session('user'), 'id_user');
@endphp
const USER_ID        = @json($resolvedUserId);
const USER_ROLE      = @json(session('user')['id_role'] ?? null);
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const UNREAD_API     = "{{ route('api.unread') }}";
const CSRF           = "{{ csrf_token() }}";

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

// ── Upcoming Reminder (closest to today) ─────────────────────
(async function loadUpcomingReminder() {
    if (!AUTH_TOKEN || !USER_ID) return;
    try {
        const remindersUrl = `${API_BASE}/reminders/${USER_ID}`;
        const data = await window.apiCache.fetch(remindersUrl, {
            ttl: 3 * 60 * 1000, // cache 3 menit
            headers: { 'Authorization': 'Bearer ' + AUTH_TOKEN }
        });
        const reminders = data && data.data ? data.data : [];
        if (!reminders.length) return;

        const now = new Date();

        // Cari reminder dengan is_active=true, paling dekat dengan hari ini
        const active = reminders.filter(r => r.is_active !== false);
        if (!active.length) return;

        // Urutkan: repeat weekly → tampilkan yg pertama; one-time → urutkan berdasar date
        let nearest = null;
        const oneTime = active
            .filter(r => !r.is_repeat_weekly && r.date)
            .map(r => ({ r, dt: new Date(r.date + 'T' + (r.time || '00:00:00')) }))
            .sort((a, b) => Math.abs(a.dt - now) - Math.abs(b.dt - now));

        const repeats = active.filter(r => r.is_repeat_weekly);

        // Prioritaskan one-time yang paling dekat dengan hari ini
        nearest = oneTime.length ? oneTime[0].r : (repeats.length ? repeats[0] : null);
        if (!nearest) return;

        // Hitung jarak hari
        let daysLabel = '';
        if (!nearest.is_repeat_weekly && nearest.date) {
            const target = new Date(nearest.date);
            target.setHours(0,0,0,0);
            const today  = new Date(); today.setHours(0,0,0,0);
            const diff   = Math.round((target - today) / 86400000);
            if (diff === 0)      daysLabel = 'Today';
            else if (diff === 1) daysLabel = 'Tomorrow';
            else if (diff > 1)   daysLabel = diff + ' days left';
            else if (diff < 0)   daysLabel = Math.abs(diff) + ' days ago';
        } else {
            daysLabel = 'Weekly';
        }

        const section = document.getElementById('homeReminderSection');
        if (!section) return;

        section.innerHTML = `
        <div class="alert-card-enter bg-[#FFF5F5] rounded-[15px] px-4 py-3 flex items-center gap-3 shadow-[0_2px_12px_rgba(236,72,153,0.10)]">
            <div class="w-10 h-10 rounded-full bg-[#FDE8F0] flex items-center justify-center flex-shrink-0">
                <ion-icon name="notifications" style="color:#EC4899;font-size:18px;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[#EC4899] text-[13px] font-extrabold truncate">${escHtmlHome(nearest.label)}</p>
                <p class="text-[#9CA3AF] text-[11px] font-semibold mt-0.5">${daysLabel}</p>
            </div>
            <a href="{{ route('reminder.index') }}" class="text-[10px] font-extrabold text-[#EC4899] bg-[#FDE8F0] rounded-full px-3 py-1 no-underline flex-shrink-0">
                DISMISS
            </a>
        </div>`;
        showNonEmptySections();
    } catch (e) { /* silent */ }
})();

// ── Low Stock Alert ───────────────────────────────────────────
(async function loadLowStock() {
    if (!AUTH_TOKEN || !USER_ID) return;
    try {
        const stockUrl = `${API_BASE}/stock/${USER_ID}`;
        const data = await window.apiCache.fetch(stockUrl, {
            ttl: 3 * 60 * 1000, // cache 3 menit
            headers: { 'Authorization': 'Bearer ' + AUTH_TOKEN }
        });
        const items = (data && data.data || []).filter(item =>
            item.low_stock_alert &&
            (item.quantity ?? 0) <= (item.alert_threshold ?? 1)
        );
        if (!items.length) return;

        const section = document.getElementById('homeLowStockSection');
        if (!section) return;

        const itemsHtml = items.slice(0, 3).map(item => `
            <div class="flex items-center gap-2 bg-[#FFFBEB] rounded-[10px] px-3 py-2">
                <ion-icon name="cube-outline" style="color:#F59E0B;font-size:14px;flex-shrink:0;"></ion-icon>
                <span class="text-[12px] font-bold text-[#92400E] truncate flex-1">${escHtmlHome(item.name)}</span>
                <span class="text-[10px] font-extrabold text-[#F59E0B] flex-shrink-0">${item.quantity} left</span>
            </div>`).join('');

        const moreText = items.length > 3 ? `<p class="text-[10px] font-bold text-[#F59E0B] text-center mt-1">+${items.length - 3} more low stock item${items.length - 3 > 1 ? 's' : ''}</p>` : '';

        section.innerHTML = `
        <div class="alert-card-enter bg-white rounded-[15px] px-4 py-3 shadow-[0_2px_12px_rgba(245,158,11,0.12)]">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#FEF3C7] flex items-center justify-center flex-shrink-0">
                        <ion-icon name="warning" style="color:#F59E0B;font-size:14px;"></ion-icon>
                    </div>
                    <span class="text-[13px] font-extrabold text-[#1E1B2E]">Low Stock Alert</span>
                </div>
                <a href="{{ route('stock.index') }}" class="text-[10px] font-extrabold text-[#8B46D3] no-underline">View All</a>
            </div>
            <div class="flex flex-col gap-1.5">
                ${itemsHtml}
                ${moreText}
            </div>
        </div>`;
        showNonEmptySections();
    } catch (e) { /* silent */ }
})();

// Shared Stock Alert
(async function loadSharedLowStock() {
    if (!AUTH_TOKEN || !USER_ID) return;
    try {
        // Cache assignment list — jarang berubah
        const assignUrl = `${API_BASE}/shared-stock/my-assignments?user_id=${USER_ID}`;
        const assignmentData = await window.apiCache.fetch(assignUrl, {
            ttl: 5 * 60 * 1000, // cache 5 menit
            headers: { 'Authorization': 'Bearer ' + AUTH_TOKEN }
        });
        const assignments = assignmentData && assignmentData.data || [];
        if (!assignments.length) return;

        const lowSharedItems = [];

        for (const assignment of assignments) {
            // Cache per assignment data — data jarang berubah
            const detailUrl = `${API_BASE}/shared-stock/assignment/${assignment.assignment_id}?user_id=${USER_ID}`;
            const sharedData = await window.apiCache.fetch(detailUrl, {
                ttl: 5 * 60 * 1000,
                cacheKey: `shared_stock_${assignment.assignment_id}`,
                headers: { 'Authorization': 'Bearer ' + AUTH_TOKEN }
            });
            const partner = assignment.role === 'nanny' ? assignment.majikan : assignment.nanny;
            const partnerName = partner?.name || (assignment.role === 'nanny' ? 'Employer' : 'Nanny');

            const items = (sharedData.data || []).filter(item =>
                item.low_stock_alert &&
                (item.quantity ?? 0) <= (item.alert_threshold ?? 1)
            ).map(item => ({
                ...item,
                partner_name: partnerName,
            }));

            lowSharedItems.push(...items);
        }

        if (!lowSharedItems.length) return;

        const section = document.getElementById('homeSharedStockSection');
        if (!section) return;

        const itemsHtml = lowSharedItems.slice(0, 3).map(item => `
            <div class="flex items-center gap-2 bg-[#EEF6FF] rounded-[10px] px-3 py-2">
                <ion-icon name="people-outline" style="color:#3B82F6;font-size:14px;flex-shrink:0;"></ion-icon>
                <div class="min-w-0 flex-1">
                    <p class="text-[12px] font-bold text-[#1E3A8A] truncate">${escHtmlHome(item.name)}</p>
                    <p class="text-[10px] font-semibold text-[#64748B] truncate">Shared with ${escHtmlHome(item.partner_name)}</p>
                </div>
                <span class="text-[10px] font-extrabold text-[#2563EB] flex-shrink-0">${item.quantity} left</span>
            </div>`).join('');

        const moreText = lowSharedItems.length > 3
            ? `<p class="text-[10px] font-bold text-[#2563EB] text-center mt-1">+${lowSharedItems.length - 3} more shared stock item${lowSharedItems.length - 3 > 1 ? 's' : ''}</p>`
            : '';

        section.innerHTML = `
        <div class="alert-card-enter bg-white rounded-[15px] px-4 py-3 shadow-[0_2px_12px_rgba(59,130,246,0.12)]">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#DBEAFE] flex items-center justify-center flex-shrink-0">
                        <ion-icon name="people" style="color:#2563EB;font-size:14px;"></ion-icon>
                    </div>
                    <span class="text-[13px] font-extrabold text-[#1E1B2E]">Shared Stock Alert</span>
                </div>
                <a href="{{ route('stock.index') }}" class="text-[10px] font-extrabold text-[#8B46D3] no-underline">View All</a>
            </div>
            <div class="flex flex-col gap-1.5">
                ${itemsHtml}
                ${moreText}
            </div>
        </div>`;
        showNonEmptySections();
    } catch (e) { /* silent */ }
})();

function showNonEmptySections() {
    const sections = ['homeReminderSection', 'homeLowStockSection', 'homeSharedStockSection'];
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section && section.innerHTML.trim() !== '') {
            section.classList.remove('hidden');
        }
    });
}

function escHtmlHome(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

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
        // Unread count: TTL pendek (30 detik) karena real-time
        var data = window.apiCache.get('unread_count');
        if (!data) {
            var res  = await fetch(UNREAD_API, {
                headers: { 'Authorization': `Bearer ${AUTH_TOKEN}`, 'Accept': 'application/json' }
            });
            data = await res.json();
            if (data.success && data.data) {
                window.apiCache.set('unread_count', data, 30 * 1000);
            }
        }
        if (data && data.success && data.data) updateBadge(data.data.unread_count || 0);
    } catch (e) { /* silent */ }
}

fetchUnreadCount();
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') fetchUnreadCount();
});

// ── GPS Nanny Tracker ──────────────────────────────────────────
let gpsMap = null;
let gpsMarkers = {};
let gpsRefreshInterval = null;
let gpsFullscreenMap = null;
let gpsFullscreenMarkers = {};
let gpsNanniesData = []; // stored nanny data for popups

function createGpsMap(containerId, showControls = true) {
    const map = L.map(containerId, {
        zoomControl: showControls,
        attributionControl: true,
        dragging: true,
        scrollWheelZoom: true,
        touchZoom: true,
        doubleClickZoom: true,
        boxZoom: true,
        keyboard: true,
    }).setView([-6.2088, 106.8456], 12);

    // Tile: CartoDB Voyager — lebih modern, jalan detail, seperti Google Maps
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
    }).addTo(map);

    return map;
}

function gpsMarkerIcon(name, isOnline = true) {
    const bg = isOnline ? '#8B46D3' : '#A8A2C2';
    const initial = (name || '?').charAt(0).toUpperCase();
    return L.divIcon({
        className: '',
        html: `
        <div style="position:relative;display:flex;flex-direction:column;align-items:center;">
            <div style="background:${bg};color:white;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:900;border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,0.35);position:relative;z-index:2;">${initial}</div>
            ${isOnline ? '<div style="width:34px;height:34px;border-radius:50%;background:rgba(139,70,211,0.25);position:absolute;top:0;left:0;animation:marker-pulse 2s ease-out infinite;"></div>' : ''}
            <div style="width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:8px solid ${bg};margin-top:-2px;"></div>
        </div>`,
        iconSize: [34, 50],
        iconAnchor: [17, 50],
        popupAnchor: [0, -55],
    });
}

function buildGpsPopup(n) {
    const lat = parseFloat(n.latitude);
    const lng = parseFloat(n.longitude);
    const hasLoc = !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
    const isLive = hasLoc && n.is_online !== false && isLocationFresh(n.last_update);
    const statusColor = isLive ? '#22C55E' : '#A8A2C2';
    const statusText = isLive ? 'Online' : 'Offline';
    const coords = hasLoc ? `${lat.toFixed(6)}, ${lng.toFixed(6)}` : '-';
    const mapsLink = hasLoc ? `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}` : null;

    return `
    <div style="font-family:Nunito,sans-serif;min-width:160px;">
        <p style="font-weight:800;font-size:14px;margin:0 0 4px;color:#1E1B2E;">${escHtmlHome(n.name)}</p>
        <p style="font-size:11px;margin:0 0 2px;color:#6B7280;">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${statusColor};margin-right:4px;"></span>
            ${statusText}
        </p>
        <p style="font-size:10px;margin:0 0 2px;color:#9CA3AF;">📍 ${coords}</p>
        <p style="font-size:10px;margin:0 0 4px;color:#9CA3AF;">🕐 ${n.last_update ? getTimeAgo(n.last_update) : '-'}</p>
        ${mapsLink ? `<a href="${mapsLink}" target="_blank" style="display:inline-block;margin-top:4px;padding:6px 14px;background:#8B46D3;color:white;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;">📍 Open in Google Maps</a>` : ''}
    </div>`;
}

function updateGpsMarkers(map, markerStore, nannies) {
    Object.values(markerStore).forEach(m => map.removeLayer(m));
    const bounds = [];
    let hasLocation = false;

    nannies.forEach(n => {
        const lat = parseFloat(n.latitude);
        const lng = parseFloat(n.longitude);
        const hasLoc = !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
        if (!hasLoc) return;

        bounds.push([lat, lng]);
        hasLocation = true;
        const isLive = hasLoc && n.is_online !== false && isLocationFresh(n.last_update);
        const icon = gpsMarkerIcon(n.name, isLive);
        const marker = L.marker([lat, lng], { icon }).addTo(map);
        marker.bindPopup(buildGpsPopup(n));
        markerStore[n.id] = marker;
    });

    if (hasLocation) {
        if (bounds.length === 1) {
            map.setView(bounds[0], 15);
        } else {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
        }
    }
}

async function refreshGpsLocations() {
    const section = document.getElementById('homeGpsSection');
    const list = document.getElementById('gpsNannyList');
    const emptyState = document.getElementById('gpsEmptyState');
    const statusText = document.getElementById('gpsStatusText');

    document.querySelectorAll('.gps-refresh-btn').forEach(btn => {
        btn.classList.add('spinning');
        setTimeout(() => btn.classList.remove('spinning'), 600);
    });

    try {
        const gpsUrl = `${API_BASE}/majikan/nanny-locations?user_id=${USER_ID}`;
        const data = await window.apiCache.fetch(gpsUrl, {
            ttl: 55 * 1000, // cache ~55 detik (sama dengan refresh interval 60s)
            headers: { 'Authorization': 'Bearer ' + AUTH_TOKEN }
        });
        const nannies = data && data.data || [];

        if (!nannies.length) {
            section.classList.add('hidden');
            return;
        }

        section.classList.remove('hidden');
        gpsNanniesData = nannies;
        const online = nannies.filter(n => isLocationFresh(n.last_update) && parseFloat(n.latitude) && parseFloat(n.longitude)).length;
        statusText.textContent = `${nannies.length} nanny${nannies.length > 1 ? 'ies' : ''} assigned · ${online} online`;

        // Init mini map dengan drag/zoom aktif
        if (!gpsMap) {
            gpsMap = createGpsMap('gpsMiniMap', false);
        }
        updateGpsMarkers(gpsMap, gpsMarkers, nannies);

        // Update fullscreen map jika sedang terbuka
        if (gpsFullscreenMap) {
            updateGpsMarkers(gpsFullscreenMap, gpsFullscreenMarkers, nannies);
        }

        // Render list
        list.innerHTML = nannies.map((n, idx) => {
            const lat = parseFloat(n.latitude);
            const lng = parseFloat(n.longitude);
            const hasLoc = !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
            const isLive = hasLoc && n.is_online !== false && isLocationFresh(n.last_update);
            const timeAgo = getTimeAgo(n.last_update);

            return `
            <div class="gps-nanny-item p-3 flex items-center gap-3" style="animation: slideUp 0.3s ease ${idx * 0.06}s both;">
                <div class="w-10 h-10 rounded-full bg-[#F3F0FD] flex items-center justify-center text-[#8B46D3] font-extrabold text-sm shrink-0 border-2 border-[#EDE9FE]">
                    ${escHtmlHome((n.name||'?')[0].toUpperCase())}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#1E1B2E] text-[13px] font-extrabold truncate">${escHtmlHome(n.name)}</span>
                        <span class="gps-live-dot ${!isLive ? 'offline' : ''}"></span>
                    </div>
                    <p class="text-[#9CA3AF] text-[10px] font-semibold mt-0.5 truncate">
                        ${hasLoc ? (n.address || `${lat.toFixed(6)}, ${lng.toFixed(6)}`) : 'Location unavailable'}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10px] font-bold ${isLive ? 'text-[#22C55E]' : 'text-[#A8A2C2]'}">${isLive ? '● Live' : timeAgo}</p>
                    ${hasLoc ? `<a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="text-[9px] font-semibold text-[#6366F1] no-underline">📍 Map</a>` : ''}
                </div>
            </div>`;
        }).join('');

        emptyState.classList.add('hidden');

    } catch (e) {
        console.error('GPS fetch error', e);
        section?.classList.add('hidden');
    }
}

function openGpsFullscreen() {
    const overlay = document.getElementById('gpsFullscreenOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    if (!gpsFullscreenMap) {
        setTimeout(() => {
            gpsFullscreenMap = createGpsMap('gpsFullscreenMap', true);
            setTimeout(() => gpsFullscreenMap.invalidateSize(), 100);
            // Copy current data from mini map markers
            const nannies = getNanniesFromMarkers();
            updateGpsMarkers(gpsFullscreenMap, gpsFullscreenMarkers, nannies);
        }, 200);
    } else {
        setTimeout(() => gpsFullscreenMap.invalidateSize(), 100);
    }
}

function closeGpsFullscreen() {
    document.getElementById('gpsFullscreenOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

function zoomToFitNannies() {
    if (!gpsFullscreenMap) return;
    const bounds = Object.values(gpsFullscreenMarkers).map(m => m.getLatLng());
    if (bounds.length === 1) {
        gpsFullscreenMap.setView(bounds[0], 15);
    } else if (bounds.length > 1) {
        gpsFullscreenMap.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
    }
}

function getNanniesFromMarkers() {
    // Kirim data nanny yang sudah disimpan dari API
    return gpsNanniesData.filter(n => {
        const lat = parseFloat(n.latitude);
        const lng = parseFloat(n.longitude);
        return !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
    });
}

function getTimeAgo(dateStr) {
    if (!dateStr) return '-';
    const now = new Date();
    const d = new Date(dateStr);
    const diffMs = now - d;
    const diffMin = Math.floor(diffMs / 60000);
    if (diffMin < 1) return 'Just now';
    if (diffMin < 60) return diffMin + 'm ago';
    const diffHr = Math.floor(diffMin / 60);
    if (diffHr < 24) return diffHr + 'h ago';
    return Math.floor(diffHr / 24) + 'd ago';
}

function isLocationFresh(lastUpdate) {
    if (!lastUpdate) return false;
    const now = new Date();
    const d = new Date(lastUpdate);
    const diffMs = now - d;
    return diffMs < 5 * 60 * 1000; // within 5 minutes
}

// ── NANNY GPS SHARING (UI local — logic global ada di nanny-gps-sharer) ──
async function toggleNannyGps() {
    const toggle = document.getElementById('nannyGpsToggle');
    if (!toggle) return;

    if (toggle.checked) {
        if (!navigator.geolocation) {
            toggle.checked = false;
            alert('GPS is not supported by this browser.');
            return;
        }
        if (window.startNannyGps) await window.startNannyGps();

        // Update UI local
        const dot = document.getElementById('nannyGpsDot');
        if (dot) dot.className = 'w-12 h-12 rounded-full bg-[#22C55E] flex items-center justify-center';
        const statusLabel = document.getElementById('nannyGpsStatusLabel');
        if (statusLabel) { statusLabel.textContent = 'Location sharing is ON'; statusLabel.className = 'text-[#166534] text-[12px] font-bold'; }
        document.getElementById('nannyGpsCoords')?.classList.remove('hidden');
        document.getElementById('nannyGpsLastUpdate')?.classList.remove('hidden');
        document.getElementById('nannyGpsAddress')?.classList.remove('hidden');
        const nannyStatus = document.getElementById('nannyGpsStatus');
        if (nannyStatus) nannyStatus.textContent = 'Getting location...';

    } else {
        if (window.stopNannyGps) await window.stopNannyGps(true);

        // Update UI local
        const dot = document.getElementById('nannyGpsDot');
        if (dot) dot.className = 'w-12 h-12 rounded-full bg-[#D1D5DB] flex items-center justify-center';
        const statusLabel = document.getElementById('nannyGpsStatusLabel');
        if (statusLabel) { statusLabel.textContent = 'Location sharing is OFF'; statusLabel.className = 'text-[#9CA3AF] text-[12px] font-bold'; }
        document.getElementById('nannyGpsCoords')?.classList.add('hidden');
        document.getElementById('nannyGpsLastUpdate')?.classList.add('hidden');
        document.getElementById('nannyGpsAddress')?.classList.add('hidden');
        const nannyStatus = document.getElementById('nannyGpsStatus');
        if (nannyStatus) nannyStatus.textContent = 'Start sharing your location';
    }
}

// ── Init GPS ──────────────────────────────────────────────────
(async function initGpsTracker() {
    if (USER_ROLE == '2') {
        // Role MAJIKAN — lihat lokasi nanny
        const section = document.getElementById('homeGpsSection');
        try {
            await refreshGpsLocations();
            gpsRefreshInterval = setInterval(refreshGpsLocations, 60000);
        } catch(e) {
            section?.classList.add('hidden');
        }
    } else if (USER_ROLE == '3') {
        // Role NANNY — bagikan lokasi
        document.getElementById('nannyGpsSection').classList.remove('hidden');
    }
})();

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
        if (event?.chat?.id_penerima == USER_ID) {
            // Clear unread cache so next fetch is fresh
            window.apiCache.delete('unread_count');
            updateBadge(unreadCount + 1);
        }
    });

    // Real-time nanny location update via Pusher
    channel.bind('nanny.location', (event) => {
        // Clear GPS cache → apiCache.delete() already adds 'ak_cache_' prefix internally
        window.apiCache.delete(`${API_BASE}/majikan/nanny-locations?user_id=${USER_ID}`);
        refreshGpsLocations();
    });
})();
</script>

@include('partials.auth-guard')
@include('partials.permission-modals')
@endpush
