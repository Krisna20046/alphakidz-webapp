{{-- resources/views/artikel/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Artikel</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { plum:{ DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B', accent:'#B895C8' } },
                fontFamily: { sans:['Plus Jakarta Sans','sans-serif'] }
            }}
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; margin: 0; }

        @media (min-width: 640px) {
            .phone-wrapper { display: flex; align-items: flex-start; justify-content: center; min-height: 100vh; padding: 32px 0; background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 60%, #e8d5ee 100%); }
            .phone-frame   { width: 390px; min-height: 844px; border-radius: 44px; box-shadow: 0 40px 80px rgba(123,30,90,.25), 0 0 0 8px #1a0d14, 0 0 0 10px #2d1020; overflow: hidden; position: relative; }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }
        .phone-frame { background: #FFF9FB; display: flex; flex-direction: column; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ── Animations ── */
        @keyframes slideUp {
            from { opacity:0; transform:translateY(22px) }
            to   { opacity:1; transform:translateY(0) }
        }
        @keyframes floatY {
            0%,100% { transform: translateY(0px) rotate(-2deg); }
            50%      { transform: translateY(-10px) rotate(2deg); }
        }
        @keyframes pulseRing {
            0%   { transform:scale(1);   opacity:.6; }
            100% { transform:scale(1.55);opacity:0; }
        }
        @keyframes shimmerCard {
            0%   { transform:rotate(-2deg) scale(1);   opacity:.35; }
            50%  { transform:rotate(1deg)  scale(1.03);opacity:.55; }
            100% { transform:rotate(-2deg) scale(1);   opacity:.35; }
        }
        @keyframes tickerScroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes dotBlink {
            0%,80%,100% { transform:scale(0); opacity:0; }
            40%          { transform:scale(1); opacity:1; }
        }
        @keyframes gradientShift {
            0%,100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        .anim-up    { animation: slideUp .45s ease forwards; opacity:0; }
        .d1 { animation-delay:.05s }
        .d2 { animation-delay:.13s }
        .d3 { animation-delay:.21s }
        .d4 { animation-delay:.29s }
        .d5 { animation-delay:.37s }

        .float-icon { animation: floatY 4s ease-in-out infinite; }
        .card-bg    { animation: shimmerCard 6s ease-in-out infinite; }

        /* Pulse ring around icon */
        .pulse-ring::before,
        .pulse-ring::after {
            content:'';
            position:absolute;
            inset:-6px;
            border-radius:50%;
            border:2px solid #9B2E72;
            animation: pulseRing 2.2s ease-out infinite;
        }
        .pulse-ring::after {
            animation-delay:.8s;
        }

        /* Ticker */
        .ticker-wrap { overflow:hidden; white-space:nowrap; }
        .ticker-inner { display:inline-flex; animation:tickerScroll 22s linear infinite; }
        .ticker-inner:hover { animation-play-state:paused; }

        /* Gradient animated text */
        .gradient-text {
            background: linear-gradient(90deg, #7B1E5A, #C05B9E, #7B1E5A, #4A0E35);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease infinite;
        }

        /* Typing dots */
        .dot { display:inline-block; width:5px; height:5px; border-radius:50%; background:#9B2E72; margin:0 2px; }
        .dot:nth-child(1) { animation:dotBlink 1.4s ease .0s infinite; }
        .dot:nth-child(2) { animation:dotBlink 1.4s ease .2s infinite; }
        .dot:nth-child(3) { animation:dotBlink 1.4s ease .4s infinite; }

        /* Ghost article cards (decorative) */
        .ghost-card {
            background: linear-gradient(135deg, rgba(243,230,250,.8), rgba(255,249,251,.9));
            border: 1.5px solid rgba(168,92,168,.15);
            backdrop-filter: blur(8px);
        }

        /* Notify chip */
        .notify-btn {
            background: linear-gradient(135deg, #7B1E5A, #9B2E72);
            transition: transform .2s, box-shadow .2s;
        }
        .notify-btn:active { transform:scale(.96); }
        .notify-btn:hover  { box-shadow: 0 8px 24px rgba(123,30,90,.35); }

        /* Category pill active */
        .cat-pill { transition: all .2s ease; cursor:default; }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum shrink-0">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1.5 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8">
                <rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/>
            </svg>
            <div class="flex items-center">
                <div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch">
                    <div class="bg-white flex-1"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="bg-gradient-to-br from-plum to-plum-light px-5 pt-10 pb-16 relative shrink-0 overflow-hidden"
         style="border-radius:0 0 32px 32px;">
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white/5 translate-y-8 -translate-x-6 pointer-events-none"></div>

        <a href="{{ route('dashboard') }}"
           class="absolute top-10 left-5 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:white;"></ion-icon>
        </a>

        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                <ion-icon name="newspaper-outline" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-tight">Artikel</h1>
            <p class="text-white/60 text-sm mt-1 font-medium">Tips & informasi parenting</p>
        </div>
    </div>

    <!-- TICKER (dekoratif) -->
    <div class="ticker-wrap bg-plum-dark/90 py-2 shrink-0">
        <div class="ticker-inner">
            @php
            $tickers = [
                '✦ Parenting Tips', '✦ Tumbuh Kembang', '✦ Nutrisi Anak', '✦ Aktivitas Kreatif',
                '✦ Kesehatan Balita', '✦ Tips Nanny', '✦ Perkembangan Kognitif', '✦ Imunisasi',
                '✦ Parenting Tips', '✦ Tumbuh Kembang', '✦ Nutrisi Anak', '✦ Aktivitas Kreatif',
                '✦ Kesehatan Balita', '✦ Tips Nanny', '✦ Perkembangan Kognitif', '✦ Imunisasi',
            ];
            @endphp
            @foreach($tickers as $t)
                <span class="text-white/50 text-[11px] font-semibold mx-4">{{ $t }}</span>
            @endforeach
        </div>
    </div>

    <!-- SCROLL BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar -mt-4 px-4 pb-28">

        <!-- ── HERO COMING SOON ─────────────────────────────────── -->
        <div class="anim-up d1 relative mt-6 mb-5">

            <!-- Ghost cards behind (decorative) -->
            <div class="card-bg absolute -top-2 left-3 right-3 h-28 rounded-3xl ghost-card"
                 style="animation-delay:0s;"></div>
            <div class="card-bg absolute -top-1 left-1.5 right-1.5 h-28 rounded-3xl ghost-card"
                 style="animation-delay:1.5s; opacity:.25;"></div>

            <!-- Main coming-soon card -->
            <div class="relative bg-white rounded-3xl p-6 shadow-xl shadow-plum/15 border border-plum-soft/50 flex flex-col items-center text-center overflow-hidden">

                <!-- Dot pattern bg -->
                <div class="absolute inset-0 opacity-[0.035]"
                     style="background-image:radial-gradient(#7B1E5A 1px,transparent 1px);background-size:18px 18px;"></div>

                <!-- Icon with pulse -->
                <div class="relative mb-5 mt-1 pulse-ring">
                    <div class="float-icon w-20 h-20 rounded-full bg-gradient-to-br from-plum-soft to-plum-pale border-2 border-plum-soft flex items-center justify-center shadow-lg">
                        <ion-icon name="newspaper" style="font-size:36px;color:#7B1E5A;"></ion-icon>
                    </div>
                </div>

                <!-- Badge -->
                <div class="flex items-center gap-1.5 bg-plum-soft rounded-full px-3 py-1 mb-4">
                    <div class="w-2 h-2 rounded-full bg-plum animate-pulse"></div>
                    <span class="text-plum text-[11px] font-bold uppercase tracking-widest">Segera Hadir</span>
                </div>

                <!-- Heading -->
                <h2 class="text-2xl font-extrabold leading-tight mb-3">
                    <span class="gradient-text">Fitur Artikel</span><br>
                    <span class="text-plum-dark">Sedang Disiapkan</span>
                </h2>

                <p class="text-plum-muted text-sm leading-relaxed max-w-xs mb-5">
                    Kami sedang menyiapkan konten artikel berkualitas seputar parenting, tumbuh kembang anak, dan tips pengasuhan terbaik untuk Anda.
                </p>

                <!-- Typing indicator -->
                <div class="flex items-center gap-2 bg-plum-pale rounded-full px-4 py-2.5 mb-5 border border-plum-soft">
                    <ion-icon name="create-outline" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                    <span class="text-plum-muted text-xs font-semibold">Tim sedang menulis</span>
                    <div class="flex items-center ml-0.5" style="gap:3px;">
                        <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                    </div>
                </div>

                <!-- Notify button -->
                <button class="notify-btn w-full text-white font-bold text-sm py-3.5 rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-plum/25"
                        onclick="handleNotify(this)">
                    <ion-icon name="notifications-outline" id="notifIcon" style="font-size:18px;"></ion-icon>
                    <span id="notifText">Beritahu Saya</span>
                </button>
            </div>
        </div>

        <!-- ── KATEGORI (preview dekoratif) ──────────────────────── -->
        <div class="anim-up d2 mb-5">
            <p class="text-plum-dark font-extrabold text-sm mb-3">Kategori Artikel</p>
            <div class="flex gap-2 flex-wrap">
                @php
                $categories = [
                    ['icon'=>'leaf-outline',        'label'=>'Nutrisi',      'color'=>'#D1FAE5','text'=>'#065F46'],
                    ['icon'=>'fitness-outline',      'label'=>'Kesehatan',    'color'=>'#FEE2E2','text'=>'#991B1B'],
                    ['icon'=>'bulb-outline',         'label'=>'Edukasi',      'color'=>'#FEF3C7','text'=>'#92400E'],
                    ['icon'=>'game-controller-outline','label'=>'Aktivitas',  'color'=>'#EDE9FE','text'=>'#5B21B6'],
                    ['icon'=>'heart-outline',        'label'=>'Parenting',    'color'=>'#FCE7F3','text'=>'#9D174D'],
                    ['icon'=>'medkit-outline',       'label'=>'Imunisasi',    'color'=>'#DBEAFE','text'=>'#1E40AF'],
                ];
                @endphp
                @foreach($categories as $cat)
                <div class="cat-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold opacity-60"
                     style="background:{{ $cat['color'] }};color:{{ $cat['text'] }};">
                    <ion-icon name="{{ $cat['icon'] }}" style="font-size:13px;"></ion-icon>
                    {{ $cat['label'] }}
                </div>
                @endforeach
            </div>
        </div>

        <!-- ── ARTIKEL PLACEHOLDER CARDS ──────────────────────────── -->
        <div class="anim-up d3 mb-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-plum-dark font-extrabold text-sm">Artikel Populer</p>
                <span class="text-plum-muted text-xs font-semibold bg-plum-soft px-2.5 py-1 rounded-full">Segera</span>
            </div>
            <div class="space-y-3">
                @php
                $placeholders = [
                    ['title'=>'Cara Tepat Memperkenalkan Makanan Padat pada Bayi',    'cat'=>'Nutrisi',   'icon'=>'leaf-outline',    'dur'=>'5 menit', 'delay'=>'.05s'],
                    ['title'=>'7 Aktivitas Stimulasi Terbaik untuk Balita 1–3 Tahun', 'cat'=>'Aktivitas', 'icon'=>'game-controller-outline', 'dur'=>'7 menit', 'delay'=>'.12s'],
                    ['title'=>'Jadwal Imunisasi Lengkap yang Wajib Diketahui Orang Tua','cat'=>'Imunisasi','icon'=>'medkit-outline',  'dur'=>'4 menit', 'delay'=>'.19s'],
                ];
                @endphp
                @foreach($placeholders as $p)
                <div class="anim-up bg-white rounded-2xl p-4 flex items-center gap-3 shadow-sm shadow-plum/8 border border-plum-soft/40 relative overflow-hidden"
                     style="animation-delay:{{ $p['delay'] }}; opacity:0;">
                    <!-- Lock overlay -->
                    <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10 rounded-2xl">
                        <div class="flex items-center gap-1.5 bg-plum-soft/90 rounded-full px-3 py-1">
                            <ion-icon name="lock-closed" style="font-size:11px;color:#7B1E5A;"></ion-icon>
                            <span class="text-plum text-[11px] font-bold">Coming Soon</span>
                        </div>
                    </div>
                    <!-- Card content (blurred under overlay) -->
                    <div class="w-12 h-12 rounded-xl bg-plum-soft flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $p['icon'] }}" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-plum-dark font-bold text-xs leading-snug line-clamp-2 mb-1">{{ $p['title'] }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-plum-muted text-[10px] font-semibold">{{ $p['cat'] }}</span>
                            <span class="text-plum-soft text-[10px]">•</span>
                            <span class="text-plum-muted text-[10px]">{{ $p['dur'] }} baca</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward" style="font-size:14px;color:#A2397B;flex-shrink:0;"></ion-icon>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ── PROGRESS / TIMELINE ───────────────────────────────── -->
        <div class="anim-up d4 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 border border-plum-soft/40 mb-5">
            <div class="flex items-center gap-2 mb-4">
                <ion-icon name="rocket-outline" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                <p class="text-plum-dark font-extrabold text-sm">Progress Pengembangan</p>
            </div>
            <div class="space-y-3">
                @php
                $steps = [
                    ['label'=>'Desain konten & kategori artikel', 'done'=>true],
                    ['label'=>'Penulisan artikel oleh tim ahli',  'done'=>true],
                    ['label'=>'Review & kurasi konten',           'done'=>false],
                    ['label'=>'Integrasi ke aplikasi',            'done'=>false],
                    ['label'=>'Peluncuran fitur artikel',         'done'=>false],
                ];
                @endphp
                @foreach($steps as $i => $step)
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0
                                {{ $step['done'] ? 'bg-plum' : 'bg-plum-soft border-2 border-plum-soft' }}">
                        @if($step['done'])
                            <ion-icon name="checkmark" style="font-size:12px;color:white;"></ion-icon>
                        @else
                            <div class="w-2 h-2 rounded-full {{ $i === 2 ? 'bg-plum animate-pulse' : 'bg-plum-soft' }}"></div>
                        @endif
                    </div>
                    <span class="text-sm font-semibold {{ $step['done'] ? 'text-plum-dark' : ($i === 2 ? 'text-plum font-bold' : 'text-plum-muted') }}">
                        {{ $step['label'] }}
                        @if($i === 2)
                            <span class="text-[10px] font-bold text-plum bg-plum-soft px-2 py-0.5 rounded-full ml-1">Proses</span>
                        @endif
                    </span>
                </div>
                @if(!$loop->last)
                <div class="ml-3 w-px h-3 {{ $step['done'] ? 'bg-plum/30' : 'bg-plum-soft' }}"></div>
                @endif
                @endforeach
            </div>
        </div>

        <!-- ── NEWSLETTER CTA ────────────────────────────────────── -->
        <div class="anim-up d5 bg-gradient-to-br from-plum to-plum-light rounded-3xl p-5 shadow-lg shadow-plum/25 relative overflow-hidden mb-2">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
            <div class="relative flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                    <ion-icon name="mail" style="font-size:22px;color:white;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-white font-extrabold text-sm mb-1">Dapatkan Notifikasi</p>
                    <p class="text-white/70 text-xs leading-relaxed mb-3">
                        Jadilah yang pertama membaca artikel terbaru begitu fitur ini diluncurkan.
                    </p>
                    <div class="flex items-center gap-2 text-white/60 text-xs font-semibold">
                        <ion-icon name="shield-checkmark-outline" style="font-size:13px;"></ion-icon>
                        Tidak ada spam, hanya konten terbaik
                    </div>
                </div>
            </div>
        </div>

        <div class="h-4"></div>
    </div>

    @include('partials.bottom-nav', ['active' => 'artikel'])

</div>{{-- /phone-frame --}}
</div>{{-- /phone-wrapper --}}

<script>
// Clock
function updateClock() {
    const el = document.getElementById('statusTime');
    if (!el) return;
    const n = new Date();
    el.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
}
updateClock();
setInterval(updateClock, 30000);

// Notify button feedback
function handleNotify(btn) {
    const icon = document.getElementById('notifIcon');
    const text = document.getElementById('notifText');
    if (btn.dataset.notified) return;
    btn.dataset.notified = '1';
    icon.setAttribute('name', 'checkmark-circle');
    text.textContent = 'Notifikasi Aktif!';
    btn.style.background = 'linear-gradient(135deg,#16A34A,#15803D)';
    btn.style.boxShadow  = '0 8px 24px rgba(22,163,74,.3)';
}
</script>

@include('partials.auth-guard')
</body>
</html>