{{-- resources/views/majikan/konsultan-list.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Konsultan</title>
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
        .konsultan-card { transition: transform .15s ease; }
        .konsultan-card:active { transform: scale(0.98); }

        .search-wrapper:focus-within {
            border-color: #8B46D3;
            box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
        }
        .search-input:focus { outline: none; }

        .badge-available { background: #DCFCE7; color: #166534; }
        .badge-hired     { background: #FEF3C7; color: #B45309; }

        #filterModal { transition: opacity .22s ease; }
        #filterSheet  { transition: transform .28s cubic-bezier(0.22, 1, 0.36, 1); }

        .filter-chip {
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1;
            transition: all .15s ease;
        }
        .filter-chip[data-group="status"][data-value="all"].active,
        .filter-chip[data-group="sort"][data-value="all"].active {
            background: #8B46D3; color: #fff;
        }
        .filter-chip[data-group="status"][data-value="available"].active {
            background: #E0E7FF; color: #4F46E5;
        }
        .filter-chip[data-group="status"][data-value="hired"].active {
            background: #FEF3C7; color: #B45309;
        }
        .filter-chip[data-group="sort"][data-value="latest"].active {
            background: #E0E7FF; color: #4F46E5;
        }
        .filter-chip[data-group="sort"][data-value="oldest"].active {
            background: #FEF3C7; color: #B45309;
        }
        .filter-chip:not(.active) {
            background: #F3F4F6;
            color: #9CA3AF;
        }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">
<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    {{-- STATUS BAR (desktop mock) --}}
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
                <span class="text-white text-[17px] font-extrabold tracking-wide">List Konsultan</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">{{ count($konsultans ?? []) }} konsultan tersedia</p>
            </div>
        </div>
    </div>

    {{-- SCROLLABLE CONTENT --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

        {{-- SEARCH + FILTER --}}
        <div class="anim delay-2">
            <form action="{{ route('majikan-konsultan-list') }}" method="GET" class="flex gap-2 w-full" id="searchForm">
                <div class="search-wrapper flex-1 flex items-center bg-[#F4F4F4] rounded-full px-4 py-2.5 border border-[#DDD6EF] gap-2 transition-all">
                    <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari konsultan..."
                        class="search-input flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent"
                    >
                    @if(request('search'))
                    <a href="{{ route('majikan-konsultan-list') }}" class="text-[#A8A2C2]">
                        <ion-icon name="close-circle" style="font-size:16px;"></ion-icon>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- LIST --}}
        <div class="anim delay-3">
            @if(isset($konsultans) && count($konsultans) > 0)
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-[#5A556E] text-[18px] font-extrabold">Rekomendasi Konsultan</h2>
                <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                    <span class="text-[#8B46D3] text-xs font-bold">{{ count($konsultans) }} Konsultan</span>
                </div>
            </div>

            <div class="flex flex-col gap-2 pb-6">
                @foreach($konsultans as $i => $konsultan)
                @php
                    $statusRaw  = strtolower($konsultan['status'] ?? ($konsultan['availability'] ?? 'available'));
                    $isHired    = in_array($statusRaw, ['hired', 'busy', 'booked']);
                    $badgeClass = $isHired ? 'badge-hired' : 'badge-available';
                    $badgeText  = $isHired ? 'HIRED' : 'AVAILABLE';
                    $rating     = $konsultan['rating']     ?? '4.9';
                    $reviews    = $konsultan['reviews']    ?? 42;
                    $role       = $konsultan['role']       ?? 'Konsultan Parenting';
                @endphp
                <a href="{{ route('majikan-konsultan-detail', $konsultan['id']) }}"
                   class="konsultan-card block bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]"
                   style="animation: slideUp 0.35s ease {{ $i * 0.05 }}s both; opacity:0;">
                    <div class="flex items-center gap-3">
                        {{-- Avatar --}}
                        @if(!empty($konsultan['foto']))
                        <img src="{{ $konsultan['foto'] }}"
                             alt="{{ $konsultan['name'] }}"
                             class="w-[50px] h-[50px] rounded-[8px] object-cover bg-[#F3F0FD]"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-[50px] h-[50px] rounded-[8px] items-center justify-center hidden bg-[#F3F0FD]">
                            <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
                        </div>
                        @else
                        <div class="w-[50px] h-[50px] rounded-[8px] flex items-center justify-center bg-[#F3F0FD]">
                            <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
                        </div>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $konsultan['name'] }}</p>
                                <span class="{{ $badgeClass }} text-[10px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0">
                                    {{ $badgeText }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1 mt-0.5">
                                <ion-icon name="star" style="font-size:12px;color:#F59E0B;"></ion-icon>
                                <span class="text-[#1E1B2E] text-[12px] font-extrabold">{{ $rating }}</span>
                                <span class="text-[#8B86A5] text-[11px] font-semibold">({{ $reviews }} reviews)</span>
                            </div>

                            <div class="flex items-center gap-1 mt-0.5">
                                <ion-icon name="briefcase-outline" style="font-size:11px;color:#C4B5FD;flex-shrink:0;"></ion-icon>
                                <p class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $role }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            @elseif(request('search'))
            <div class="flex flex-col items-center pt-16 pb-10 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                    <ion-icon name="search-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                </div>
                <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Konsultan tidak ditemukan</h3>
                <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                    Tidak ada konsultan sesuai pencarian
                    "<span class="font-semibold text-[#8B46D3]">{{ request('search') }}</span>"
                </p>
                <a href="{{ route('majikan-konsultan-list') }}"
                   class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-[0_8px_18px_rgba(139,70,211,0.35)]">
                    Lihat Semua Konsultan
                </a>
            </div>

            @else
            <div class="flex flex-col items-center pt-16 pb-10 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                    <ion-icon name="people-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                </div>
                <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Belum ada konsultan</h3>
                <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                    Daftar konsultan akan muncul di sini
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

    (function () {
        const modal       = document.getElementById('filterModal');
        const sheet       = document.getElementById('filterSheet');
        const openBtn     = document.getElementById('openFilterBtn');
        const closeBtn    = document.getElementById('closeFilterBtn');
        const applyBtn    = document.getElementById('applyFilterBtn');
        const resetBtn    = document.getElementById('resetFilterBtn');
        const statusInput = document.getElementById('statusInput');
        const sortInput   = document.getElementById('sortInput');
        const form        = document.getElementById('searchForm');

        let selectedStatus = statusInput.value || 'all';
        let selectedSort   = sortInput.value   || 'all';

        function paintChips() {
            document.querySelectorAll('.filter-chip').forEach((chip) => {
                const group    = chip.dataset.group;
                const value    = chip.dataset.value;
                const isActive = (group === 'status' && value === selectedStatus)
                              || (group === 'sort'   && value === selectedSort);
                chip.classList.toggle('active', isActive);
            });
        }

        function openModal() {
            paintChips();
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.style.opacity = '1';
                sheet.style.transform = 'translateY(0)';
            });
        }

        function closeModal() {
            modal.style.opacity = '0';
            sheet.style.transform = 'translateY(100%)';
            setTimeout(() => modal.classList.add('hidden'), 220);
        }

        document.querySelectorAll('.filter-chip').forEach((chip) => {
            chip.addEventListener('click', () => {
                if (chip.dataset.group === 'status') selectedStatus = chip.dataset.value;
                if (chip.dataset.group === 'sort')   selectedSort   = chip.dataset.value;
                paintChips();
            });
        });

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        resetBtn.addEventListener('click', () => {
            selectedStatus    = 'all';
            selectedSort      = 'all';
            statusInput.value = 'all';
            sortInput.value   = 'all';
            form.submit();
        });

        applyBtn.addEventListener('click', () => {
            statusInput.value = selectedStatus;
            sortInput.value   = selectedSort;
            form.submit();
        });
    })();
</script>
@include('partials.auth-guard')
</body>
</html>