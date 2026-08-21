@extends('layouts.app')

@section('title', 'Your Consultants')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .konsultan-card { transition: transform .15s ease; }
    .konsultan-card:active { transform: scale(0.98); }

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
            <span class="text-white text-[17px] font-extrabold tracking-wide">Your Consultants</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ count($konsultans ?? []) }} consultants assigned</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- CONTENT --}}
    <div class="anim delay-3">

        @if(isset($konsultans) && count($konsultans) > 0)

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-[#5A556E] text-[18px] font-extrabold">Active Consultants</h2>
            <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                <span class="text-[#8B46D3] text-xs font-bold">{{ count($konsultans) }} Consultants</span>
            </div>
        </div>

        {{-- CARD LIST --}}
        <div class="flex flex-col gap-3 pb-6">
            @foreach($konsultans as $i => $konsultan)
            @php
                $genderText = ($konsultan['gender'] ?? '') === 'L' ? 'Male' : (($konsultan['gender'] ?? '') === 'P' ? 'Female' : '-');
            @endphp
            <a href="{{ route('majikan-konsultan-detail', $konsultan['id']) }}"
               class="konsultan-card block bg-white rounded-[14px] px-3 py-3 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5]"
               style="animation: slideUp 0.35s ease {{ $i * 0.05 }}s both; opacity:0;">

                <div class="flex items-center gap-3">
                    {{-- FOTO --}}
                    @if(!empty($konsultan['foto']))
                    <img src="{{ $konsultan['foto'] }}"
                         alt="{{ $konsultan['name'] }}"
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
                            <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $konsultan['name'] }}</p>
                            <span class="bg-[#EDE9FE] text-[#8B46D3] text-[9px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0 whitespace-nowrap">
                                CONSULTANT
                            </span>
                        </div>

                        {{-- EMAIL --}}
                        <div class="flex items-center gap-1 mb-0.5">
                            <ion-icon name="at-outline" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $konsultan['email'] ?? '-' }}</span>
                        </div>

                        {{-- PHONE --}}
                        <div class="flex items-center gap-1">
                            <ion-icon name="call-outline" style="font-size:11px;color:#F59E0B;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $konsultan['no_hp'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

            </a>
            @endforeach
        </div>

        @else
        {{-- EMPTY — belum ada konsultan --}}
        <div class="flex flex-col items-center pt-16 pb-10 px-8">
            <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                <ion-icon name="people-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">No consultants yet</h3>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                You have no consultants assigned at this time
            </p>
        </div>
        @endif

    </div>
</div>
@endsection
