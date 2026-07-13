@extends('layouts.app')

@section('title', 'Nanny Anda')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .nanny-card { transition: transform .15s ease; }
    .nanny-card:active { transform: scale(0.98); }

    .search-wrapper:focus-within {
        border-color: #8B46D3;
        box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
    }
    .search-input:focus { outline: none; }
</style>
@endpush

@section('content')
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

                        {{-- KONSULTAN — hanya tampil jika ada konsultan --}}
                        @if(!empty($item['konsultan_name']))
                        <div class="flex items-center gap-1 mt-1.5 pt-1.5 border-t border-[#EAE6F5]">
                            <div class="flex items-center gap-1.5 flex-1 min-w-0">
                                @if(!empty($item['konsultan_foto']))
                                <img src="{{ $item['konsultan_foto'] }}" alt="{{ $item['konsultan_name'] }}"
                                     class="w-[14px] h-[14px] rounded-full object-cover shrink-0"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-[14px] h-[14px] rounded-full items-center justify-center hidden bg-[#EDE9FE] shrink-0">
                                    <ion-icon name="person" style="font-size:10px;color:#8B46D3;"></ion-icon>
                                </div>
                                @else
                                <div class="w-[14px] h-[14px] rounded-full flex items-center justify-center bg-[#EDE9FE] shrink-0">
                                    <ion-icon name="person" style="font-size:10px;color:#8B46D3;"></ion-icon>
                                </div>
                                @endif
                                <span class="text-[#8B46D3] text-[10px] font-bold truncate">Konsultan: {{ $item['konsultan_name'] }}</span>
                            </div>
                        </div>
                        @endif
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
@endsection
