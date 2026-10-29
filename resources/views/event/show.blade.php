@extends('layouts.app')

@php
    $titleEvent = $event['title'] ?? 'Event';
@endphp

@section('title', $titleEvent)

@push('styles')
<style>
    .section-card {
        background: #FFFFFF;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.09);
    }
</style>
@endpush

@section('content')
{{-- HEADER --}}
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Event &amp; Info</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Detail dari tim Nexus</p>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
            bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
            rounded-t-[50px] -mt-[50px] relative z-20
            hide-scrollbar space-y-4">

    {{-- BANNER --}}
    <div class="anim delay-2">
        @if(!empty($event['banner_url']))
        <div class="w-full rounded-2xl overflow-hidden shadow-md bg-[#EDE9FE]">
            <img src="{{ $event['banner_url'] }}"
                 alt="{{ $event['title'] ?? '' }}"
                 class="w-full h-auto block"
                 loading="lazy">
        </div>
        @else
        <div class="w-full aspect-[16/9] rounded-2xl bg-[#EDE9FE] flex items-center justify-center">
            <ion-icon name="megaphone-outline" style="font-size:64px;color:#C4B5FD;"></ion-icon>
        </div>
        @endif
    </div>

    {{-- BADGES --}}
    <div class="anim delay-2 flex gap-2 flex-wrap">
        @if(!empty($event['type']))
        <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-[#EDE9FE] text-[#8B46D3]">
            {{ $event['type'] }}
        </span>
        @endif
        @php
            $nowTs = now()->timestamp;
            $isFuture = !empty($event['publish_at']) && \Carbon\Carbon::parse($event['publish_at'])->timestamp > $nowTs;
            $isExpired = !empty($event['expired_at']) && \Carbon\Carbon::parse($event['expired_at'])->timestamp < $nowTs;
            if (!$event['is_active']) {
                $status = ['Nonaktif', 'bg-[#F3F4F6] text-[#6B7280]'];
            } elseif ($isFuture) {
                $status = ['Segera tayang', 'bg-[#FEF3C7] text-[#D97706]'];
            } elseif ($isExpired) {
                $status = ['Berakhir', 'bg-[#F3F4F6] text-[#6B7280]'];
            } else {
                $status = ['Aktif', 'bg-[#F0FDF4] text-[#16A34A]'];
            }
        @endphp
        <span class="text-[10px] font-bold px-3 py-1 rounded-full {{ $status[1] }}">{{ $status[0] }}</span>
    </div>

    {{-- TITLE + SUBTITLE --}}
    <div class="anim delay-3 space-y-1">
        <h1 class="text-[#1E1B2E] text-xl font-extrabold leading-snug">
            {{ $event['title'] }}
        </h1>
        @if(!empty($event['subtitle']))
        <p class="text-[#4B4763] text-[13px] font-semibold leading-relaxed">{{ $event['subtitle'] }}</p>
        @endif
    </div>

    {{-- JADWAL (bila ada) --}}
    @if(!empty($event['event_start_at']))
    <div class="anim delay-3 section-card p-4 flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
            <ion-icon name="calendar-outline" style="font-size:19px;color:#8B46D3;"></ion-icon>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] font-extrabold text-[#8B86A5] uppercase tracking-wide">Jadwal</p>
            <p class="text-[13px] font-bold text-[#1E1B2E] mt-0.5">
                {{ \Carbon\Carbon::parse($event['event_start_at'])->format('d M Y H:i') }}
                @if(!empty($event['event_end_at']))
                    <span class="text-[#8B86A5] font-semibold">s/d</span>
                    {{ \Carbon\Carbon::parse($event['event_end_at'])->format('d M Y H:i') }}
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- DESKRIPSI --}}
    @if(!empty($event['description']))
    <div class="anim delay-3 section-card p-5">
        <p class="text-[#374151] text-[13px] leading-relaxed font-medium">{!! nl2br(e($event['description'])) !!}</p>
    </div>
    @endif

    {{-- INFO --}}
    @php
        $hasInfo = !empty($event['location']) || $event['price'] !== null || $event['quota'] !== null
                || !empty($event['contact_name']) || !empty($event['contact_phone']);
    @endphp
    @if($hasInfo)
    <div class="anim delay-3 section-card p-5 space-y-3">
        <p class="text-[#1E1B2E] text-[14px] font-extrabold">Informasi</p>

        @if(!empty($event['location']))
        <div class="flex items-start gap-2.5">
            <ion-icon name="location-outline" style="font-size:16px;color:#8B46D3;" class="mt-0.5 shrink-0"></ion-icon>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-[#9CA3AF]">Lokasi</p>
                <p class="text-[13px] font-bold text-[#1E1B2E]">{{ $event['location'] }}</p>
            </div>
        </div>
        @endif

        @if($event['price'] !== null)
        <div class="flex items-start gap-2.5">
            <ion-icon name="pricetag-outline" style="font-size:16px;color:#8B46D3;" class="mt-0.5 shrink-0"></ion-icon>
            <div>
                <p class="text-[11px] font-bold text-[#9CA3AF]">Harga</p>
                <p class="text-[13px] font-bold text-[#1E1B2E]">
                    {{ (int) $event['price'] === 0 ? 'Gratis' : 'Rp ' . number_format((int) $event['price'], 0, ',', '.') }}
                </p>
            </div>
        </div>
        @endif

        @if($event['quota'] !== null)
        <div class="flex items-start gap-2.5">
            <ion-icon name="people-outline" style="font-size:16px;color:#8B46D3;" class="mt-0.5 shrink-0"></ion-icon>
            <div>
                <p class="text-[11px] font-bold text-[#9CA3AF]">Kuota</p>
                <p class="text-[13px] font-bold text-[#1E1B2E]">{{ number_format((int) $event['quota'], 0, ',', '.') }} orang</p>
            </div>
        </div>
        @endif

        @if(!empty($event['contact_name']) || !empty($event['contact_phone']))
        <div class="flex items-start gap-2.5">
            <ion-icon name="call-outline" style="font-size:16px;color:#8B46D3;" class="mt-0.5 shrink-0"></ion-icon>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-[#9CA3AF]">Kontak</p>
                @if(!empty($event['contact_name']) && !empty($event['contact_phone']))
                <p class="text-[13px] font-bold text-[#1E1B2E]">{{ $event['contact_name'] }} — <a href="tel:{{ $event['contact_phone'] }}" class="text-[#8B46D3]">{{ $event['contact_phone'] }}</a></p>
                @elseif(!empty($event['contact_phone']))
                <p class="text-[13px] font-bold text-[#1E1B2E]"><a href="tel:{{ $event['contact_phone'] }}" class="text-[#8B46D3]">{{ $event['contact_phone'] }}</a></p>
                @else
                <p class="text-[13px] font-bold text-[#1E1B2E]">{{ $event['contact_name'] }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- AKSI --}}
    @if(!empty($event['map_url']) || !empty($event['link_url']))
    <div class="anim delay-3 space-y-2.5">
        @if(!empty($event['link_url']))
        <a href="{{ $event['link_url'] }}" target="_blank" rel="noopener"
           class="w-full py-3.5 rounded-2xl bg-[#8B46D3] text-white text-[14px] font-extrabold flex items-center justify-center gap-2 shadow-[0_4px_16px_rgba(139,70,211,0.35)] active:scale-[0.98] transition-transform no-underline">
            <ion-icon name="open-outline" style="font-size:17px;"></ion-icon>
            {{ $event['link_label'] ?: 'Buka Tautan' }}
        </a>
        @endif
        @if(!empty($event['map_url']))
        <a href="{{ $event['map_url'] }}" target="_blank" rel="noopener"
           class="w-full py-3.5 rounded-2xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[14px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.98] transition-transform no-underline">
            <ion-icon name="map-outline" style="font-size:17px;"></ion-icon>
            Buka Peta
        </a>
        @endif
    </div>
    @endif

    <div class="h-4"></div>
</div>
@endsection

@push('modals')
    @include('partials.permission-modals')
@endpush
