{{-- resources/views/majikan/nanny-anda.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Nanny Anda</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .nanny-card { transition: transform .15s ease; }
        .nanny-card:active { transform: scale(0.98); }

        .search-wrapper:focus-within {
            border-color: #8B46D3;
            box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
        }
        .search-input:focus { outline: none; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    {{-- STATUS BAR --}}
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('dashboard') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">List Nanny</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">List Of Nannies On Duty</p>
            </div>
        </div>
    </div>

    {{-- SCROLLABLE CONTENT --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

        {{-- SEARCH BAR --}}
        <div class="anim delay-2">
            <form action="{{ route('majikan-nanny') }}" method="GET" id="searchForm">
                <div class="search-wrapper flex items-center bg-white rounded-full px-4 py-3 border border-[#DDD6EF] gap-2 transition-all shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
                    <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search nanny...."
                        class="search-input flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent"
                    >
                    @if(request('search'))
                    <a href="{{ route('majikan-nanny') }}" class="text-[#A8A2C2]">
                        <ion-icon name="close-circle" style="font-size:16px;"></ion-icon>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- CONTENT --}}
        <div class="anim delay-3">

            @if(isset($assignments) && count($assignments) > 0)

            {{-- CARD LIST --}}
            <div class="flex flex-col gap-3 pb-6">
                @foreach($assignments as $i => $item)
                @php
                    // Hitung bulan kerja dari tanggal_mulai sampai sekarang (atau tanggal_selesai)
                    $tglMulai    = $item['tanggal_mulai'] ?? null;
                    $tglSelesai  = $item['tanggal_selesai'] ?? null;
                    $bulanKerja  = $item['bulan_kerja'] ?? null;

                    if (!$bulanKerja && $tglMulai) {
                        try {
                            $start      = new \DateTime($tglMulai);
                            $end        = $tglSelesai ? new \DateTime($tglSelesai) : new \DateTime();
                            $diff       = $start->diff($end);
                            $bulanKerja = ($diff->y * 12) + $diff->m;
                            if ($bulanKerja < 1) $bulanKerja = 1;
                        } catch (\Exception $e) {
                            $bulanKerja = null;
                        }
                    }

                    $monthLabel = $bulanKerja
                        ? strtoupper($bulanKerja . ' ' . ($bulanKerja == 1 ? 'MONTH' : 'MONTHS') . ' OF WORK')
                        : 'ON DUTY';
                    $badgeBg    = '#DCFCE7';
                    $badgeColor = '#166534';
                @endphp
                <a href="{{ route('majikan-nanny-anda-detail', $item['id_nanny']) }}"
                   class="nanny-card block bg-white rounded-[14px] px-3 py-3 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5]"
                   style="animation: slideUp 0.35s ease {{ $i * 0.05 }}s both; opacity:0;">

                    <div class="flex items-center gap-3">
                        {{-- FOTO --}}
                        @if(!empty($item['nanny_foto']))
                        <img src="{{ $item['nanny_foto'] }}"
                             alt="{{ $item['nanny_name'] }}"
                             class="w-[56px] h-[56px] rounded-[10px] object-cover bg-[#F3F0FD] shrink-0"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-[56px] h-[56px] rounded-[10px] items-center justify-center hidden bg-[#F3F0FD] shrink-0">
                            <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                        </div>
                        @else
                        <div class="w-[56px] h-[56px] rounded-[10px] flex items-center justify-center bg-[#F3F0FD] shrink-0">
                            <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                        </div>
                        @endif

                        {{-- INFO --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $item['nanny_name'] }}</p>
                                {{-- BADGE MONTHS OF WORK --}}
                                <span class="text-[9px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0 whitespace-nowrap"
                                      style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                    {{ $monthLabel }}
                                </span>
                            </div>

                            {{-- EMAIL --}}
                            <div class="flex items-center gap-1 mb-0.5">
                                <ion-icon name="at-outline" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                                <span class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $item['nanny_email'] ?? '-' }}</span>
                            </div>

                            {{-- PHONE --}}
                            <div class="flex items-center gap-1">
                                <ion-icon name="call-outline" style="font-size:11px;color:#F59E0B;flex-shrink:0;"></ion-icon>
                                <span class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $item['nanny_no_hp'] ?? $item['no_hp'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                </a>
                @endforeach
            </div>

            @elseif(request('search'))
            {{-- EMPTY — search tidak ditemukan --}}
            <div class="flex flex-col items-center pt-16 pb-10 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                    <ion-icon name="search-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                </div>
                <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Nanny tidak ditemukan</h3>
                <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                    Tidak ada nanny sesuai pencarian
                    "<span class="font-semibold text-[#8B46D3]">{{ request('search') }}</span>"
                </p>
                <a href="{{ route('majikan-nanny') }}"
                   class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-[0_8px_18px_rgba(139,70,211,0.35)]">
                    Lihat Semua Nanny
                </a>
            </div>

            @else
            {{-- EMPTY — belum ada nanny --}}
            <div class="flex flex-col items-center pt-16 pb-10 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                    <ion-icon name="people-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                </div>
                <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Belum ada nanny aktif</h3>
                <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                    Anda belum memiliki nanny yang sedang bertugas saat ini
                </p>
            </div>
            @endif

        </div>
    </div>

    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
    (function () {
        const el = document.getElementById('statusTime');
        function tick() {
            const now = new Date();
            if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        tick();
        setInterval(tick, 30000);
    })();

    // Auto-submit search on enter
    document.getElementById('searchForm')?.addEventListener('submit', function(e) {
        // allow normal submit
    });
</script>
@include('partials.auth-guard')
</body>
</html>