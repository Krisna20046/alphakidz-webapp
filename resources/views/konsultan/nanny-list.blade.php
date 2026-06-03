@extends('layouts.app')

@section('title', 'Daftar Nanny')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .nanny-card { transition: transform .15s ease; }
    .nanny-card:active { transform: scale(0.98); }

    .search-wrapper:focus-within {
        border-color: #8B46D3;
        box-shadow: 0 0 0 3px rgba(139, 70, 211, 0.14);
    }
    .search-input:focus { outline: none; }

    .badge-available { background: #DCFCE7; color: #166534; }
    .badge-mine { background: #EDE9FE; color: #6D28D9; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">List Nanny</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ count($nannies ?? []) }} nanny tersedia</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">
    <div class="anim delay-2">
        <form action="{{ route('konsultan-nanny-list') }}" method="GET" class="flex gap-2 w-full">
            <div class="search-wrapper flex-1 flex items-center bg-[#F4F4F4] rounded-full px-4 py-2.5 border border-[#DDD6EF] gap-2 transition-all">
                <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search nanny...."
                    class="search-input flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent"
                >
                @if(request('search'))
                <a href="{{ route('konsultan-nanny-list') }}" class="text-[#A8A2C2]">
                    <ion-icon name="close-circle" style="font-size:16px;"></ion-icon>
                </a>
                @endif
            </div>
            <button type="submit"
                    class="w-9 h-9 bg-[#8B46D3] rounded-full flex items-center justify-center flex-shrink-0 active:scale-95 transition-all shadow-[0_8px_18px_rgba(139,70,211,0.25)]">
                <ion-icon name="search-outline" style="font-size:16px;color:white;"></ion-icon>
            </button>
        </form>
    </div>

    <div class="anim delay-3">
        @if(isset($nannies) && count($nannies) > 0)
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-[#5A556E] text-[18px] font-extrabold">Nanny's Recommendation</h2>
            <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                <span class="text-[#8B46D3] text-xs font-bold">{{ count($nannies) }} Nanny</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 pb-6">
            @foreach($nannies as $i => $nanny)
            @php
                $isMine = !empty($nanny['is_mine']);
                $badgeClass = $isMine ? 'badge-mine' : 'badge-available';
                $badgeText = $isMine ? 'PENGAWASAN' : 'TERSEDIA';
                $role = $nanny['role'] ?? 'Nanny';
                $subtitle = $nanny['experience'] ?? ($isMine ? 'Sudah berada dalam pengawasan Anda' : 'Siap ditinjau dan ditambahkan ke pengawasan');
                $rating = $nanny['rating'] ?? null;
                $reviews = $nanny['reviews'] ?? null;
            @endphp
            <a href="{{ route('konsultan-nanny-detail', $nanny['id']) }}"
               class="nanny-card block bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]"
               style="animation: slideUp 0.35s ease {{ $i * 0.05 }}s both; opacity:0;">
                <div class="flex items-center gap-3">
                    @if(!empty($nanny['foto']))
                    <img src="{{ $nanny['foto'] }}"
                         alt="{{ $nanny['name'] }}"
                         class="w-[50px] h-[50px] rounded-[8px] object-cover bg-[#F3F0FD]"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="w-[50px] h-[50px] rounded-[8px] items-center justify-center hidden bg-[#F3F0FD]">
                        <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
                    </div>
                    @else
                    <div class="w-[50px] h-[50px] rounded-[8px] flex items-center justify-center bg-[#F3F0FD]">
                        <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $nanny['name'] }}</p>
                            <span class="{{ $badgeClass }} text-[10px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0">
                                {{ $badgeText }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1 mt-0.5">
                            <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                            <span class="text-[#1E1B2E] text-[12px] font-extrabold truncate">{{ $role }}</span>
                            @if($rating)
                            <span class="text-[#D1D5DB]">-</span>
                            <ion-icon name="star" style="font-size:12px;color:#F59E0B;"></ion-icon>
                            <span class="text-[#1E1B2E] text-[12px] font-extrabold">{{ $rating }}</span>
                            @if($reviews)
                            <span class="text-[#8B86A5] text-[11px] font-semibold">({{ $reviews }} reviews)</span>
                            @endif
                            @endif
                        </div>

                        <p class="text-[#8B86A5] text-[11px] italic font-semibold mt-0.5 truncate">
                            "{{ $subtitle }}"
                        </p>
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
            <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Nanny tidak ditemukan</h3>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                Tidak ada nanny sesuai pencarian
                "<span class="font-semibold text-[#8B46D3]">{{ request('search') }}</span>"
            </p>
            <a href="{{ route('konsultan-nanny-list') }}"
               class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-[0_8px_18px_rgba(139,70,211,0.35)]">
                Lihat Semua Nanny
            </a>
        </div>

        @else
        <div class="flex flex-col items-center pt-16 pb-10 px-8">
            <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                <ion-icon name="people-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Belum ada nanny</h3>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                Daftar nanny akan muncul di sini
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
