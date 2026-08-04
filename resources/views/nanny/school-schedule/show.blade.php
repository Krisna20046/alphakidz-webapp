@extends('layouts.app')

@section('title', 'Schedule Details')

@php
    $s = $schedule;
    $color = $s['subject']['color'] ?? '#8B46D3';
    $childName = $childNames[$s['id_anak']] ?? 'Child';
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
        <a href="{{ route('school-schedule.index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Schedule Details</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Full school schedule information</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Profile Card --}}
    <div class="anim delay-2 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 text-white text-3xl font-bold"
                 style="background:{{ $color }};">
                @if(!empty($s['subject']['icon']) && str_starts_with($s['subject']['icon'], 'http'))
                    <img src="{{ $s['subject']['icon'] }}" alt="icon" class="w-8 h-8 object-contain">
                @elseif(!empty($s['subject']['icon']))
                    <ion-icon name="{{ $s['subject']['icon'] }}" style="font-size:30px;"></ion-icon>
                @else
                    {{ strtoupper(substr($s['subject']['name'] ?? '?',0,1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-[#1E1B2E] font-extrabold text-lg leading-tight">{{ $s['subject']['name'] ?? 'Subject' }}</h2>
                <p class="text-[#8B86A5] text-sm mt-0.5">Child: {{ $childName }}</p>
            </div>
        </div>
    </div>

    {{-- Waktu --}}
    <div class="anim delay-2 bg-white rounded-3xl p-5 border border-[#DDD6EF] space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="calendar-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
            </div>
            <div>
                <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Day</p>
                <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $s['day_of_week'] ?? '-' }}</p>
            </div>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="time-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
            </div>
            <div>
                <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Time</p>
                <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">
                    {{ substr($s['start_time'] ?? '',0,5) }} – {{ substr($s['end_time'] ?? '',0,5) }}
                </p>
            </div>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="person-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
            </div>
            <div>
                <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Teacher</p>
                <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $s['teacher_name'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="anim delay-3 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider mb-2">Notes</p>
        <p class="text-[#1E1B2E] text-sm font-semibold">{{ $s['notes'] ?? '-' }}</p>
    </div>

    {{-- Actions --}}
    <div class="anim delay-4 space-y-3">
        <a href="{{ route('school-schedule.edit', $s['id']) }}"
           class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm shadow-lg shadow-[#8B46D3]/30">
            <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
            Edit Schedule
        </a>
        <form action="{{ route('school-schedule.destroy', $s['id']) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                onclick="return confirm('Delete this schedule permanently? This action cannot be undone.')"
                class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-sm bg-red-50 text-red-500 border border-red-200">
                <ion-icon name="trash-outline" style="font-size:18px;"></ion-icon>
                Delete Schedule
            </button>
        </form>
    </div>

</div>
@endsection
