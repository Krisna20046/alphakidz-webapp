@extends('layouts.app')

@section('title', 'Select Child Diary')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .anak-card { transition: opacity .15s ease, transform .15s ease; }
    .anak-card:hover { opacity: .85; }
    .anak-card:active { transform: scale(0.98); opacity: .7; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <a href="{{ route('dashboard') }}"
       class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
       style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
        <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
    </a>
    <div class="flex flex-col items-center relative z-10">
        <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg"
             style="width:64px;height:64px;">
            <ion-icon name="book" style="font-size:30px;color:#8B46D3;"></ion-icon>
        </div>
        <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Select Child Diary</h1>
        <p style="font-size:14px;color:rgba(255,255,255,0.8);font-weight:500;">Select a child to record the diary</p>
    </div>
</div>

@php
    $anak       = $assignmentData['anak'] ?? [];
    $majikan    = $assignmentData['majikan_name'] ?? null;
    $tglMulai   = $assignmentData['tanggal_mulai'] ?? '';
    $tglSelesai = $assignmentData['tanggal_selesai'] ?? '';
@endphp

@if(count($anak) > 0)
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Assignment info --}}
    <div class="anim delay-2 mb-4">
        <div class="flex items-center bg-white rounded-[16px] border-2 border-[#EAE6F5] p-[14px] gap-3">
            <div class="flex items-center justify-center shrink-0 w-11 h-11 rounded-[12px] bg-[#F3F0FD]">
                <ion-icon name="briefcase" style="font-size:20px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-semibold text-[#8B86A5] mb-0.5">Assignment with</p>
                <p class="text-[15px] font-extrabold text-[#1E1B2E] truncate mb-0.5">{{ $majikan ?? '-' }}</p>
                <div class="flex items-center gap-1">
                    <ion-icon name="calendar-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                    <span class="text-[12px] font-semibold text-[#8B86A5]">{{ $tglMulai }} – {{ $tglSelesai }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- List header --}}
    <div class="flex items-center justify-between anim delay-2 mb-3">
        <span class="text-[18px] font-extrabold text-[#1E1B2E]">Child List</span>
        <span class="bg-[#EDE9FE] px-3 py-1 rounded-full text-[12px] font-extrabold text-[#8B46D3]">
            {{ count($anak) }}
        </span>
    </div>

    {{-- Scrollable list --}}
    <div class="flex flex-col gap-3 pb-6">
        @foreach($anak as $i => $child)
        @php
            $lahir  = new DateTime($child['tanggal_lahir'] ?? 'now');
            $diff   = $lahir->diff(new DateTime());
            $umur   = ($diff->y > 0 ? $diff->y.' years ' : '') . $diff->m . ' months';
            $isMale = ($child['gender'] ?? '') === 'L';
        @endphp
        <a href="{{ route('nanny-diary', ['id_anak' => $child['id']]) }}"
           class="anak-card flex items-center bg-white rounded-[16px] border-2 border-[#EAE6F5] p-4"
           style="animation:slideUp 0.3s ease {{ $i*0.06 }}s both;opacity:0;">
            {{-- Avatar --}}
            <div class="mr-3 shrink-0">
                @if(!empty($child['foto']))
                <img src="{{ $child['foto'] }}" alt="{{ $child['nama'] }}" class="object-cover"
                     style="width:56px;height:56px;border-radius:28px;border:3px solid #EDE9FE;"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="items-center justify-center hidden"
                     style="width:56px;height:56px;border-radius:28px;background:#F3F0FD;border:3px solid #EDE9FE;">
                    <ion-icon name="body" style="font-size:24px;color:#8B46D3;"></ion-icon>
                </div>
                @else
                <div class="flex items-center justify-center"
                     style="width:56px;height:56px;border-radius:28px;background:#F3F0FD;border:3px solid #EDE9FE;">
                    <ion-icon name="body" style="font-size:24px;color:#8B46D3;"></ion-icon>
                </div>
                @endif
            </div>
            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-[16px] font-extrabold text-[#1E1B2E] truncate mb-1.5">
                    {{ $child['nama'] }}
                </p>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        <ion-icon name="gift" style="font-size:13px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[13px] font-semibold text-[#8B86A5]">{{ trim($umur) }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <ion-icon name="{{ $isMale ? 'male' : 'female' }}" style="font-size:13px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[13px] font-semibold text-[#8B86A5]">{{ $isMale ? 'Male' : 'Female' }}</span>
                    </div>
                </div>
            </div>
            {{-- Arrow --}}
            <div class="flex items-center justify-center shrink-0 w-8 h-8 rounded-full bg-[#EDE9FE] ml-2">
                <ion-icon name="chevron-forward" style="font-size:20px;color:#C4B5FD;"></ion-icon>
            </div>
        </a>
        @endforeach
    </div>
</div>

@else
{{-- Empty state --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <div class="flex flex-col items-center justify-center" style="padding:80px 40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#EDE9FE;margin-bottom:24px;">
            <ion-icon name="body-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-center text-[18px] font-extrabold text-[#1E1B2E] mb-2">No child data yet</p>
        <p class="text-center text-[14px] font-semibold text-[#8B86A5] leading-relaxed">
            @if($assignmentData)
                Assignment with {{ $assignmentData['majikan_name'] ?? '' }}<br>
                {{ $assignmentData['tanggal_mulai'] ?? '' }} – {{ $assignmentData['tanggal_selesai'] ?? '' }}
            @else
                No active assignment right now
            @endif
        </p>
    </div>
</div>
@endif
@endsection
