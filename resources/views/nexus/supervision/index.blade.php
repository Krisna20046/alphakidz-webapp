@extends('layouts.app')

@section('title', 'Nexus Supervision')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    .nanny-card { transition: opacity .15s ease, transform .15s ease; }
    .nanny-card:hover { opacity: .85; }
    .nanny-card:active { transform: scale(0.98); opacity: .7; }
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
            <span class="text-white text-[17px] font-extrabold tracking-wide">Nexus Supervision</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $date }}</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 anim delay-2 mb-5">
        <div class="bg-white rounded-[16px] p-4 shadow-[0_2px_12px_rgba(0,0,0,0.08)]">
            <div class="w-9 h-9 rounded-[10px] bg-[#EDE9FE] flex items-center justify-center mb-2">
                <ion-icon name="people-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
            </div>
            <p class="text-[22px] font-extrabold text-[#1E1B2E] leading-none">{{ $totalNannies }}</p>
            <p class="text-[11px] font-bold text-[#8B86A5] mt-1">Nannies Aktif</p>
        </div>
        <div class="bg-white rounded-[16px] p-4 shadow-[0_2px_12px_rgba(0,0,0,0.08)]">
            <div class="w-9 h-9 rounded-[10px] bg-[#FDE8F0] flex items-center justify-center mb-2">
                <ion-icon name="chatbubble-ellipses-outline" style="font-size:18px;color:#EC4899;"></ion-icon>
            </div>
            <p class="text-[22px] font-extrabold text-[#1E1B2E] leading-none">{{ $totalOpenTickets }}</p>
            <p class="text-[11px] font-bold text-[#8B86A5] mt-1">Open Tickets</p>
        </div>
    </div>

    <div class="flex items-center justify-between anim delay-3 mb-3">
        <span style="font-size:18px;font-weight:700;color:#1E1B2E;">Nanny</span>
        <span style="background:#EDE9FE;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700;color:#8B46D3;">
            {{ count($nannies) }}
        </span>
    </div>

    @if(count($nannies) > 0)
    <div class="flex flex-col gap-3">
        @foreach($nannies as $i => $nanny)
        <a href="{{ route('nexus-supervision-nanny', $nanny['id']) }}"
           class="nanny-card bg-white flex items-start"
           style="border-radius:16px;border:2px solid #EAE6F5;padding:16px;
                  animation:slideUp 0.3s ease {{ $i*0.06 }}s both;opacity:0;">
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:44px;height:44px;border-radius:14px;background:#F3F0FD;margin-right:12px;">
                <span style="font-size:16px;font-weight:800;color:#8B46D3;">{{ strtoupper(substr($nanny['name'] ?? '?',0,1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <p class="truncate" style="font-size:15px;font-weight:700;color:#1E1B2E;">{{ $nanny['name'] ?? '-' }}</p>
                    <ion-icon name="chevron-forward" style="font-size:18px;color:#C4B5FD;flex-shrink:0;margin-left:8px;"></ion-icon>
                </div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="text-[11px] font-bold text-[#8B86A5] bg-[#F4F4F4] rounded-full px-2.5 py-0.5">
                        <ion-icon name="briefcase-outline" style="font-size:10px;vertical-align:-1px;"></ion-icon>
                        {{ $nanny['assignment_count'] ?? 0 }} assignment
                    </span>
                    <span class="text-[11px] font-bold text-[#8B86A5] bg-[#F4F4F4] rounded-full px-2.5 py-0.5">
                        <ion-icon name="body-outline" style="font-size:10px;vertical-align:-1px;"></ion-icon>
                        {{ $nanny['child_count'] ?? 0 }} anak
                    </span>
                </div>
                {{-- Today attendance flags --}}
                @php $today = $nanny['today_attendance'] ?? []; @endphp
                @if(count($today) === 0)
                    <span class="text-[11px] font-semibold text-[#A8A2C2]">Belum ada kehadiran hari ini</span>
                @else
                    <div class="flex flex-col gap-1">
                        @foreach($today as $att)
                        <div class="flex items-center gap-2 flex-wrap">
                            @php
                                $stat = $att['status'] ?? '';
                                $statLbl = in_array($stat, ['present','late','absent']) ? ucfirst($stat) : 'Hadir';
                                $statColor = $stat === 'absent' ? '#DC2626' : ($stat === 'late' ? '#D97706' : '#16A34A');
                            @endphp
                            <span class="text-[11px] font-bold rounded-full px-2.5 py-0.5" style="background:{{ $statColor }}16;color:{{ $statColor }};">
                                {{ $statLbl }}
                            </span>
                            <span class="text-[11px] font-semibold text-[#8B86A5]">
                                {{ $att['checkin_time'] ? \Carbon\Carbon::parse($att['checkin_time'])->format('H:i') : '--:--' }}
                                @if($att['checkout_time'])
                                    → {{ \Carbon\Carbon::parse($att['checkout_time'])->format('H:i') }}
                                @endif
                            </span>
                            @foreach(($att['flags'] ?? []) as $flag)
                                <span class="text-[10px] font-bold text-[#DC2626] bg-[#FEE2E2] rounded-full px-2 py-0.5 uppercase tracking wide">
                                    {{ $flag }}
                                </span>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center" style="padding:60px 40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#EDE9FE;margin-bottom:24px;">
            <ion-icon name="people-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#1E1B2E;margin-bottom:8px;">No nanny to supervise</p>
        <p class="text-center" style="font-size:14px;color:#9CA3AF;line-height:20px;">Tidak ada nanny aktif dengan assignment saat ini.</p>
    </div>
    @endif
</div>
@endsection