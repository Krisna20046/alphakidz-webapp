@extends('layouts.app')

@section('title', 'Subject Details')

@php
    $s = $subject;
    $color = $s['color'] ?? '#8B46D3';
    $initial = strtoupper(substr($s['name'] ?? '?', 0, 1));
@endphp

@push('styles')
<style>
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.94); }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('admin-school-subject') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Subject Details</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Complete subject information</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Profile Card --}}
    <div class="anim delay-2 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 text-white text-3xl font-bold"
                 style="background:{{ $color }};">
                @if(!empty($s['icon']) && str_starts_with($s['icon'], 'http'))
                    <img src="{{ $s['icon'] }}" alt="icon" class="w-8 h-8 object-contain">
                @elseif(!empty($s['icon']))
                    <ion-icon name="{{ $s['icon'] }}" style="font-size:30px;"></ion-icon>
                @else
                    {{ $initial }}
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <h2 class="text-[#1E1B2E] font-extrabold text-lg leading-tight">{{ $s['name'] ?? '-' }}</h2>
                <p class="text-[#8B86A5] text-sm mt-0.5 flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full inline-block" style="background:{{ $color }};"></span>
                    {{ $color }}
                </p>
            </div>
        </div>
    </div>

    {{-- Info Ikon --}}
    <div class="anim delay-2 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <h3 class="text-[#1E1B2E] font-extrabold text-sm mb-4 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="color-palette-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            </div>
            Icon Information
        </h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <ion-icon name="grid-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Ikon</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $s['icon'] ?? 'None' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Warna --}}
    <div class="anim delay-3 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <h3 class="text-[#1E1B2E] font-extrabold text-sm mb-4 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="earth-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            </div>
            Color Information
        </h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <ion-icon name="eye-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Color Code</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $color }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="anim delay-4 space-y-3">
        <a href="{{ route('admin-school-subject.edit', $s['id']) }}"
           class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm shadow-lg shadow-[#8B46D3]/30">
            <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
            Edit Subject Data
        </a>

        <form action="{{ route('admin-school-subject.destroy', $s['id']) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                onclick="return confirm('Delete subject \'{{ addslashes($s['name'] ?? '') }}\' permanently? This action cannot be undone.')"
                class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-sm bg-red-50 text-red-500 border border-red-200">
                <ion-icon name="trash-outline" style="font-size:18px;"></ion-icon>
                Delete Subject
            </button>
        </form>
    </div>

</div>
@endsection