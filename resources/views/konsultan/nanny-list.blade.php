{{-- resources/views/konsultan/nanny-list.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Nanny — Konsultan</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                            accent:  '#B895C8',
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

        .header-bg {
            background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up            { animation: slideUp 0.4s ease forwards; }
        .anim-up.delay-1    { animation-delay: 0.05s; opacity: 0; }
        .anim-up.delay-2    { animation-delay: 0.12s; opacity: 0; }
        .anim-up.delay-3    { animation-delay: 0.20s; opacity: 0; }

        .nanny-card {
            transition: transform 0.15s ease, opacity 0.15s ease, box-shadow 0.15s ease;
        }
        .nanny-card:hover  { opacity: 0.88; box-shadow: 0 6px 20px rgba(123,30,90,0.15); }
        .nanny-card:active { transform: scale(0.95); opacity: 0.7; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        /* Scrollbar hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Search focus ring */
        .search-input:focus { outline: none; }
        .search-wrapper:focus-within {
            border-color: #7B1E5A !important;
            box-shadow: 0 0 0 3px rgba(123,30,90,0.12);
        }

        /* Badge added */
        .badge-mine { background: #DCFCE7; color: #166534; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- ─── STATUS BAR ────────────────────────────────────────────────────── -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    <!-- ─── HEADER ────────────────────────────────────────────────────────── -->
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <!-- Back button -->
        <a href="{{ route('dashboard') }}"
           class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        <!-- Header content -->
        <div class="flex flex-col items-center anim-up delay-1">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg shadow-plum-dark/20">
                <ion-icon name="people" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Daftar Nanny</h1>
            <p class="text-white/60 text-xs font-medium">Cari dan temukan nanny yang sesuai</p>
        </div>
    </div>

    <!-- ─── SCROLLABLE BODY ───────────────────────────────────────────────── -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 pt-5" id="mainScroll">

        <!-- ── SEARCH BAR ─────────────────────────────────────────────────── -->
        <div class="flex gap-2 mb-4 anim-up delay-2">
            <form action="{{ route('konsultan-nanny-list') }}" method="GET" class="flex gap-2 w-full">
                <div class="search-wrapper flex-1 flex items-center bg-white rounded-2xl px-4 py-3 border-2 border-plum-soft gap-3 transition-all">
                    <ion-icon name="search" style="font-size:18px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nanny berdasarkan nama..."
                        class="search-input flex-1 text-sm font-medium text-plum-dark placeholder-plum-accent bg-transparent"
                    >
                    @if(request('search'))
                    <a href="{{ route('konsultan-nanny-list') }}" class="text-plum-accent hover:text-plum-muted">
                        <ion-icon name="close-circle" style="font-size:18px;"></ion-icon>
                    </a>
                    @endif
                </div>
                <button type="submit"
                        class="w-12 h-12 bg-plum rounded-2xl flex items-center justify-center flex-shrink-0 hover:bg-plum-light active:scale-95 transition-all shadow-md shadow-plum/30">
                    <ion-icon name="search" style="font-size:18px;color:white;"></ion-icon>
                </button>
            </form>
        </div>

        <!-- ── RESULT INFO ────────────────────────────────────────────────── -->
        @if(isset($nannies) && count($nannies) > 0)
        <div class="mb-3 anim-up delay-2">
            <p class="text-plum-muted text-xs font-semibold">
                Menampilkan {{ count($nannies) }} nanny
            </p>
        </div>
        @endif

        <!-- ── NANNY GRID ─────────────────────────────────────────────────── -->
        <div class="anim-up delay-3">
            @if(isset($nannies) && count($nannies) > 0)

            <div class="flex flex-wrap justify-between pb-10">
                @foreach($nannies as $i => $nanny)
                <a href="{{ route('konsultan-nanny-detail', $nanny['id']) }}"
                   class="nanny-card bg-white rounded-2xl py-4 px-2 flex flex-col items-center mb-4 relative"
                   style="width:31%; border: 2px solid #F3E6FA; animation: slideUp 0.3s ease {{ $i * 0.05 }}s both; opacity:0;"
                >
                    {{-- Badge jika sudah jadi nanny konsultan ini --}}
                    @if(!empty($nanny['is_mine']))
                    <span class="absolute top-2 right-2 text-[9px] font-bold px-1.5 py-0.5 rounded-full badge-mine leading-tight">
                        Saya
                    </span>
                    @endif

                    <!-- Avatar -->
                    <div class="mb-3">
                        @if(!empty($nanny['foto']))
                        <img src="{{ $nanny['foto'] }}"
                             alt="{{ $nanny['name'] }}"
                             class="w-16 h-16 rounded-full object-cover"
                             style="background:#F3E6FA; border: 3px solid #F3E6FA;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <div class="w-16 h-16 rounded-full items-center justify-center hidden"
                             style="background:#F3E6FA; border: 3px solid #F3E6FA;">
                            <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                        </div>
                        @else
                        <div class="w-16 h-16 rounded-full flex items-center justify-center"
                             style="background:#F3E6FA; border: 3px solid #F3E6FA;">
                            <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                        </div>
                        @endif
                    </div>

                    <!-- Name -->
                    <p class="text-sm font-bold text-center mb-1.5 px-1 line-clamp-1 w-full"
                       style="color:#4A0E35;">
                        {{ $nanny['name'] }}
                    </p>

                    <!-- Role -->
                    @if(!empty($nanny['role']))
                    <div class="flex items-center px-1" style="gap:4px;">
                        <ion-icon name="briefcase-outline" style="font-size:12px;color:#A2397B;flex-shrink:0;"></ion-icon>
                        <span class="text-xs font-medium text-center line-clamp-1" style="color:#A2397B;">
                            {{ $nanny['role'] }}
                        </span>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>

            @elseif(request('search'))
            <!-- ── EMPTY SEARCH STATE ─────────────────────────────────────── -->
            <div class="flex flex-col items-center pt-16 pb-10 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-plum-soft flex items-center justify-center mb-5">
                    <ion-icon name="search-outline" style="font-size:44px;color:#B895C8;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-lg mb-2">Nanny tidak ditemukan</h3>
                <p class="text-plum-muted text-sm text-center leading-relaxed">
                    Tidak ada nanny yang sesuai dengan pencarian
                    "<span class="font-semibold text-plum">{{ request('search') }}</span>"
                </p>
                <a href="{{ route('konsultan-nanny-list') }}"
                   class="mt-6 bg-plum text-white text-sm font-bold px-6 py-3 rounded-2xl hover:bg-plum-light transition-colors shadow-md shadow-plum/30">
                    Lihat Semua Nanny
                </a>
            </div>

            @else
            <!-- ── EMPTY STATE ─────────────────────────────────────────────── -->
            <div class="flex flex-col items-center pt-16 pb-10 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-plum-soft flex items-center justify-center mb-5">
                    <ion-icon name="people-outline" style="font-size:44px;color:#B895C8;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-lg mb-2">Belum ada nanny</h3>
                <p class="text-plum-muted text-sm text-center leading-relaxed">
                    Daftar nanny akan muncul di sini
                </p>
            </div>
            @endif
        </div>

        <div class="h-8"></div>
    </div>

    <!-- ─── BOTTOM NAV ───────────────────────────────────────────────────── -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const el = document.getElementById('statusTime');
        if (el) el.textContent = `${h}:${m}`;
    }
    updateClock();
    setInterval(updateClock, 30000);
</script>
@include('partials.auth-guard')
</body>
</html>