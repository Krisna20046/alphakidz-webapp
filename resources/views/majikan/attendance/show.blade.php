@extends('layouts.app')

@section('title', 'Attendance - ' . $namaAnak)

@php
    $todayRec = count($today) > 0 ? $today[0] : null;
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-attendance') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Attendance</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('majikan-attendance-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
        <button type="button" onclick="atTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Today status card (read-only) --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="time-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Today's Attendance</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
            @if($todayRec && !empty($todayRec['checkin_time']))
                @php $outDate = !empty($todayRec['checkout_time']); @endphp
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $outDate ? 'bg-[#F0FDF4] text-[#16A34A]' : 'bg-[#FEF3C7] text-[#D97706]' }}">
                    <ion-icon name="{{ $outDate ? 'checkmark-circle' : 'time-outline' }}" style="font-size:11px;"></ion-icon>
                    {{ $outDate ? 'Completed' : 'Checked In' }}
                </span>
            @else
            <span class="px-2.5 py-1 rounded-full bg-[#F3F4F6] text-[10px] font-bold text-[#6B7280]">
                Not Checked In
            </span>
            @endif
        </div>

        @if($todayRec && !empty($todayRec['checkin_time']))
        <div class="rounded-xl bg-[#F3F0FD] border border-[#E5DFF3] p-3 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="{{ empty($todayRec['checkout_time']) ? 'time-outline' : 'checkmark-circle' }}"
                    style="font-size:20px;color:{{ empty($todayRec['checkout_time']) ? '#D97706' : '#16A34A' }};"></ion-icon>
            </div>
            <div class="flex-1">
                <span class="text-[13px] font-extrabold text-[#4B4763] block">
                    {{ empty($todayRec['checkout_time']) ? 'Checked in' : 'Attendance complete' }}
                </span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">
                    Check-in {{ \Carbon\Carbon::parse($todayRec['checkin_time'])->format('H:i') }}
                    @if(!empty($todayRec['checkout_time']))
                    &nbsp;· Check-out {{ \Carbon\Carbon::parse($todayRec['checkout_time'])->format('H:i') }}
                    @endif
                </span>
            </div>
        </div>
        @if(!empty($todayRec['nanny_name']))
        <div class="flex items-center gap-1.5 mt-1.5">
            <ion-icon name="person-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <span class="text-[11px] font-semibold text-[#4B4763]">By {{ $todayRec['nanny_name'] }}</span>
        </div>
        @endif
        @if(!empty($todayRec['lat']) && !empty($todayRec['lng']))
        <div class="flex items-center gap-1.5 mt-1">
            <ion-icon name="location" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <span class="text-[10px] font-bold text-[#4B4763]" data-geo="{{ $todayRec['lat'] }},{{ $todayRec['lng'] }}">{{ round($todayRec['lat'], 4) }}, {{ round($todayRec['lng'], 4) }}</span>
        </div>
        @endif
        @else
        <div class="rounded-xl bg-[#F9FAFB] border border-[#E5E7EB] p-3 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#F3F4F6] flex items-center justify-center shrink-0">
                <ion-icon name="time-outline" style="font-size:20px;color:#9CA3AF;"></ion-icon>
            </div>
            <div class="flex-1">
                <span class="text-[13px] font-extrabold text-[#6B7280] block">No check-in yet today</span>
                <span class="text-[11px] font-semibold text-[#9CA3AF]">The nanny has not logged attendance for this child today.</span>
            </div>
        </div>
        @endif
    </div>

    {{-- History (paginated, via partial) --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Attendance History</span>
            <span class="text-[10px] font-bold text-[#8B86A5]">Daily check-in/out</span>
        </div>
        @include('majikan.attendance._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination])
    </div>
</div>

@php
    $attendanceSteps = [
        ['color' => '#8B46D3', 'icon' => 'time-outline',       'title' => 'Daily attendance',
         'body'  => 'Attendance is recorded <b>per child</b>. Open a child to see today’s check-in / out status.'],
        ['color' => '#16A34A', 'icon' => 'checkmark-circle',   'title' => 'Today’s status',
         'body' => 'The card shows whether your child has been <b>checked in</b> for the day and whether check-out is done.'],
        ['color' => '#8B86A5', 'icon' => 'person-outline',     'title' => 'Nanny',
         'body' => 'Each entry names the nanny who recorded the attendance and their GPS location proof.'],
        ['color' => '#8B86A5', 'icon' => 'file-tray-outline',  'title' => 'History',
         'body' => 'Past records for this child appear below — swipe through pages to review.'],
    ];
@endphp

@include('attendance._tutorial')
@include('attendance._detail_modal')

@endsection

@push('scripts')
<script>
async function atGoToPage(page) {
    const url = "{{ route('majikan-attendance-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('historyList').outerHTML = html;
    window.resolveGeoPlaces && window.resolveGeoPlaces();
}
</script>
@endpush