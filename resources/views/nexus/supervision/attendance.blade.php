@extends('layouts.app')

@section('title', 'Riwayat Kehadiran')

@push('styles')
<style>
    .info-card { border-radius:16px; border:2px solid #EAE6F5; padding:16px; background:#fff; margin-bottom:12px; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nexus-supervision-nanny', $idNanny) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide block truncate">Riwayat Kehadiran</span>
            <p class="text-white/60 text-xs font-medium mt-0.5 truncate">{{ $nanny['name'] ?? 'Nanny' }}</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <div class="flex flex-col gap-4">

        {{-- Filter tanggal --}}
        <div class="info-card anim delay-2" style="margin-bottom:0;">
            <form method="GET" action="{{ route('nexus-supervision-nanny-attendance', $idNanny) }}" class="flex flex-wrap items-end gap-2">
                <div class="flex-1">
                    <label class="text-[11px] font-bold text-[#8B86A5] block mb-1">Dari</label>
                    <input type="date" name="from" value="{{ $from }}"
                           class="w-full bg-[#F8F7FF] border border-[#ECEAF4] rounded-[12px] px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E]">
                </div>
                <div class="flex-1">
                    <label class="text-[11px] font-bold text-[#8B86A5] block mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ $to }}"
                           class="w-full bg-[#F8F7FF] border border-[#ECEAF4] rounded-[12px] px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E]">
                </div>
                <button type="submit"
                        class="bg-[#8B46D3] text-white text-[13px] font-extrabold rounded-[12px] px-4 py-2.5 border-none cursor-pointer w-[100%]">
                    Filter
                </button>
            </form>
        </div>

        <div class="flex items-center justify-between anim delay-3 mb-1">
            <span style="font-size:14px;font-weight:800;color:#1E1B2E;">{{ $meta['total'] ?? 0 }} record</span>
            @if(count($rows) > 0)
            <span class="text-[11px] font-bold text-[#8B86A5]">Halaman {{ $meta['current_page'] ?? 1 }} / {{ $meta['last_page'] ?? 1 }}</span>
            @endif
        </div>

        @if(count($rows) === 0)
        <div class="flex flex-col items-center justify-center anim delay-3" style="padding:60px 40px;">
            <div class="flex items-center justify-center float-anim"
                 style="width:120px;height:120px;border-radius:60px;background:#F6F1FE;margin-bottom:24px;">
                <ion-icon name="finger-print-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-center" style="font-size:17px;font-weight:700;color:#1E1B2E;margin-bottom:8px;">Tidak ada data</p>
            <p class="text-center" style="font-size:14px;color:#9CA3AF;line-height:20px;">Coba ubah rentang tanggal filter.</p>
        </div>
        @else
        <div class="flex flex-col gap-2.5">
            @foreach($rows as $att)
            <div class="info-card anim" style="margin-bottom:0;padding:14px;">
                <div class="flex items-center justify-between mb-2">
                    @php $stat = $att['status'] ?? 'present'; $statColor = $stat === 'absent' ? '#DC2626' : ($stat === 'late' ? '#D97706' : '#16A34A'); @endphp
                    <span class="text-[11px] font-bold rounded-full px-2.5 py-0.5" style="background:{{ $statColor }}16;color:{{ $statColor }};">
                        {{ in_array($stat, ['present','late','absent']) ? ucfirst($stat) : 'Hadir' }}
                    </span>
                    <span class="text-[10px] font-bold text-[#8B86A5]">
                        Anak #{{ $att['id_anak'] ?? '-' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[12px] font-semibold text-[#4B5563]">
                        {{ $att['checkin_time'] ? \Carbon\Carbon::parse($att['checkin_time'])->format('d M H:i') : '--' }}
                    </span>
                    @if($att['checkout_time'])
                        <span class="text-[#C4B5FD]">→</span>
                        <span class="text-[12px] font-semibold text-[#4B5563]">{{ \Carbon\Carbon::parse($att['checkout_time'])->format('H:i') }}</span>
                    @endif
                    @foreach(($att['flags'] ?? []) as $flag)
                        <span class="text-[10px] font-bold text-[#DC2626] bg-[#FEE2E2] rounded-full px-2 py-0.5 uppercase">{{ $flag }}</span>
                    @endforeach
                </div>
                @if(!empty($att['notes']))
                <p class="text-[12px] text-[#8B86A5] font-medium mt-1.5">{{ $att['notes'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Pagination --}}
        @if(($meta['last_page'] ?? 1) > 1)
        <div class="flex items-center justify-center gap-2 anim delay-4 pt-2">
            @php
                $cp = (int) ($meta['current_page'] ?? 1);
                $lp = (int) ($meta['last_page'] ?? 1);
            @endphp
            @if($cp > 1)
            <a href="{{ route('nexus-supervision-nanny-attendance', ['idNanny' => $idNanny, 'page' => $cp - 1, 'from' => $from, 'to' => $to]) }}"
               class="w-9 h-9 rounded-full bg-white border-2 border-[#EAE6F5] flex items-center justify-center no-underline">
                <ion-icon name="chevron-back" style="color:#8B46D3;"></ion-icon>
            </a>
            @endif
            @for($p = max(1, $cp - 1); $p <= min($lp, $cp + 1); $p++)
            <a href="{{ route('nexus-supervision-nanny-attendance', ['idNanny' => $idNanny, 'page' => $p, 'from' => $from, 'to' => $to]) }}"
               class="w-9 h-9 rounded-full flex items-center justify-center no-underline text-[13px] font-extrabold {{ $p === $cp ? 'bg-[#8B46D3] text-white' : 'bg-white border-2 border-[#EAE6F5] text-[#8B86A5]' }}">
                {{ $p }}
            </a>
            @endfor
            @if($cp < $lp)
            <a href="{{ route('nexus-supervision-nanny-attendance', ['idNanny' => $idNanny, 'page' => $cp + 1, 'from' => $from, 'to' => $to]) }}"
               class="w-9 h-9 rounded-full bg-white border-2 border-[#EAE6F5] flex items-center justify-center no-underline">
                <ion-icon name="chevron-forward" style="color:#8B46D3;"></ion-icon>
            </a>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection