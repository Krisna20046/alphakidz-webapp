{{-- resources/views/konsultan/nanny-anda.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Nanny Anda</title>
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
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

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
                overflow: hidden; position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        .header-bg { background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%); }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up         { animation: slideUp 0.4s ease forwards; }
        .anim-up.delay-1 { animation-delay: 0.05s; opacity: 0; }
        .anim-up.delay-2 { animation-delay: 0.12s; opacity: 0; }
        .anim-up.delay-3 { animation-delay: 0.20s; opacity: 0; }

        .nanny-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }
        .nanny-card:hover  { box-shadow: 0 6px 20px rgba(123,30,90,0.12); opacity: 0.92; }
        .nanny-card:active { transform: scale(0.98); opacity: 0.75; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .badge-aktif    { background: #DCFCE7; color: #166534; }
        .badge-nonaktif { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <a href="{{ route('dashboard') }}"
           class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        <div class="flex flex-col items-center anim-up delay-1">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg shadow-plum-dark/20">
                <ion-icon name="people" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Nanny Anda</h1>
            <p class="text-white/60 text-xs font-medium">Daftar nanny di bawah pengawasan Anda</p>
        </div>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 pt-5 pb-4">

        @if(session('success'))
        <div id="flash-success"
             class="mb-4 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-2xl flex items-center gap-2">
            <ion-icon name="checkmark-circle" style="font-size:16px;color:#16A34A;flex-shrink:0;"></ion-icon>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div id="flash-error"
             class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-4 py-3 rounded-2xl flex items-center gap-2">
            <ion-icon name="alert-circle" style="font-size:16px;color:#DC2626;flex-shrink:0;"></ion-icon>
            {{ session('error') }}
        </div>
        @endif

        @if(isset($nannies) && count($nannies) > 0)

        <!-- List Header -->
        <div class="flex items-center justify-between mb-4 anim-up delay-2">
            <h2 class="text-plum-dark font-extrabold text-base">Daftar Nanny</h2>
            <div class="bg-plum-soft px-3 py-1 rounded-full">
                <span class="text-plum text-xs font-extrabold">{{ count($nannies) }}</span>
            </div>
        </div>

        <!-- Cards -->
        <div class="space-y-4 anim-up delay-3 pb-16">
            @foreach($nannies as $i => $nanny)
            <a href="{{ route('konsultan-nanny-anda-detail', $nanny['id']) }}"
               class="nanny-card bg-white rounded-3xl border-2 border-plum-soft overflow-hidden block"
               style="animation: slideUp 0.3s ease {{ $i * 0.06 }}s both; opacity:0;"
            >
                <!-- Card Header -->
                <div class="flex items-center gap-3 p-4">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if(!empty($nanny['foto']))
                        <img src="{{ $nanny['foto'] }}"
                             alt="{{ $nanny['name'] }}"
                             class="w-14 h-14 rounded-full object-cover"
                             style="border: 3px solid #F3E6FA;"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                        >
                        <div class="w-14 h-14 rounded-full items-center justify-center hidden"
                             style="background:#F3E6FA; border: 3px solid #F3E6FA;">
                            <ion-icon name="person" style="font-size:24px;color:#7B1E5A;"></ion-icon>
                        </div>
                        @else
                        <div class="w-14 h-14 rounded-full flex items-center justify-center"
                             style="background:#F3E6FA; border: 3px solid #F3E6FA;">
                            <ion-icon name="person" style="font-size:24px;color:#7B1E5A;"></ion-icon>
                        </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-plum-dark font-extrabold text-base truncate mb-1">{{ $nanny['name'] }}</p>
                        <div class="flex items-center gap-1">
                            @php $isMale = ($nanny['gender'] ?? '') === 'L'; @endphp
                            <ion-icon name="{{ $isMale ? 'male' : 'female' }}" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                            <span class="text-plum text-xs font-semibold">
                                {{ $isMale ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                    </div>

                    <!-- Status badge + chevron -->
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        @php $isActive = (int)($nanny['is_active'] ?? 1) === 1; @endphp
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $isActive ? 'badge-aktif' : 'badge-nonaktif' }}">
                            {{ $isActive ? '● AKTIF' : '● NONAKTIF' }}
                        </span>
                        <div class="w-7 h-7 rounded-full bg-plum-soft flex items-center justify-center">
                            <ion-icon name="chevron-forward" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="h-px bg-plum-soft mx-4"></div>

                <!-- Detail Row -->
                <div class="px-4 py-3 space-y-2">
                    <!-- Email -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="mail" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Email</p>
                            <p class="text-plum-dark text-xs font-semibold truncate">{{ $nanny['email'] ?? '-' }}</p>
                        </div>
                    </div>

                    @if(!empty($nanny['catatan']))
                    <!-- Catatan -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon name="document-text" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Catatan</p>
                            <p class="text-plum-dark text-xs font-semibold leading-snug">{{ $nanny['catatan'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        @else
        <!-- Empty State -->
        <div class="flex flex-col items-center pt-16 pb-10 px-8 anim-up delay-2">
            <div class="float-anim w-28 h-28 rounded-full bg-plum-soft flex items-center justify-center mb-6">
                <ion-icon name="people-outline" style="font-size:52px;color:#B895C8;"></ion-icon>
            </div>
            <h3 class="text-plum-dark font-extrabold text-lg mb-2">Belum ada nanny</h3>
            <p class="text-plum-muted text-sm text-center leading-relaxed">
                Anda belum memiliki nanny yang terdaftar di bawah pengawasan Anda
            </p>
            <a href="{{ route('konsultan-nanny-list') }}"
               class="mt-6 bg-plum text-white text-sm font-bold px-6 py-3 rounded-2xl hover:bg-plum-light transition-colors shadow-md shadow-plum/30 flex items-center gap-2">
                <ion-icon name="search" style="font-size:16px;"></ion-icon>
                Cari Nanny
            </a>
        </div>
        @endif

        <div class="h-6"></div>
    </div>

    <!-- BOTTOM NAV -->
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

    setTimeout(() => {
        ['flash-success','flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }, 4000);
</script>
@include('partials.auth-guard')
</body>
</html>