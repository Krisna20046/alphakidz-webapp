@extends('layouts.app')

@section('title', 'Nanny Detail')

@push('styles')
<style>
    .info-card { border-radius:16px; border:2px solid #EAE6F5; padding:16px; background:#fff; }
    .section-title { font-size:14px; font-weight:800; color:#1E1B2E; margin-bottom:12px; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nexus-supervision') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide block truncate">{{ $nanny['name'] ?? 'Nanny' }}</span>
            <p class="text-white/60 text-xs font-medium mt-0.5 truncate">{{ $nanny['email'] ?? '' }}</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <div class="flex flex-col gap-4">

        {{-- Today attendance --}}
        <div class="info-card anim delay-2">
            <div class="flex items-center justify-between mb-3">
                <span class="section-title" style="margin-bottom:0;">Kehadiran Hari Ini</span>
                <a href="{{ route('nexus-supervision-nanny-attendance', $idNanny) }}"
                   class="text-[#8B46D3] text-xs font-extrabold no-underline">Riwayat</a>
            </div>
            @if(count($todayAttendance) === 0)
                <p class="text-[13px] text-[#9CA3AF] font-semibold">Belum ada kehadiran tercatat hari ini.</p>
            @else
                <div class="flex flex-col gap-2">
                    @foreach($todayAttendance as $att)
                    <div class="flex items-center gap-2 bg-[#F8F7FF] border border-[#ECEAF4] rounded-[12px] px-3 py-2.5">
                        @php $stat = $att['status'] ?? 'present'; $statColor = $stat === 'absent' ? '#DC2626' : ($stat === 'late' ? '#D97706' : '#16A34A'); @endphp
                        <span class="text-[11px] font-bold rounded-full px-2.5 py-0.5" style="background:{{ $statColor }}16;color:{{ $statColor }};">
                            {{ in_array($stat, ['present','late','absent']) ? ucfirst($stat) : 'Hadir' }}
                        </span>
                        <span class="text-[12px] font-semibold text-[#4B5563]">
                            {{ $att['checkin_time'] ? \Carbon\Carbon::parse($att['checkin_time'])->format('H:i') : '--:--' }}
                            @if($att['checkout_time'])
                                → {{ \Carbon\Carbon::parse($att['checkout_time'])->format('H:i') }}
                            @endif
                        </span>
                        @foreach(($att['flags'] ?? []) as $flag)
                        <span class="text-[10px] font-bold text-[#DC2626] bg-[#FEE2E2] rounded-full px-2 py-0.5 uppercase">{{ $flag }}</span>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Stats: assignments + children --}}
        <div class="grid grid-cols-2 gap-3 anim delay-3">
            <div class="info-card flex items-center gap-3" style="padding:14px;">
                <div class="w-9 h-9 rounded-[10px] bg-[#EDE9FE] flex items-center justify-center flex-shrink-0">
                    <ion-icon name="briefcase-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p class="text-[18px] font-extrabold text-[#1E1B2E] leading-none">{{ count($assignments) }}</p>
                    <p class="text-[11px] font-bold text-[#8B86A5] mt-1">Assignment</p>
                </div>
            </div>
            <div class="info-card flex items-center gap-3" style="padding:14px;">
                <div class="w-9 h-9 rounded-[10px] bg-[#EDE9FE] flex items-center justify-center flex-shrink-0">
                    <ion-icon name="body-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p class="text-[18px] font-extrabold text-[#1E1B2E] leading-none">{{ count($children) }}</p>
                    <p class="text-[11px] font-bold text-[#8B86A5] mt-1">Anak</p>
                </div>
            </div>
        </div>

        {{-- Assignments --}}
        <div class="info-card anim delay-3">
            <span class="section-title">Assignments</span>
            @if(count($assignments) === 0)
                <p class="text-[13px] text-[#9CA3AF] font-semibold">Tidak ada assignment.</p>
            @else
                <div class="flex flex-col gap-2">
                    @foreach($assignments as $a)
                    <div class="flex items-center justify-between bg-[#F8F7FF] border border-[#ECEAF4] rounded-[12px] px-3 py-2.5">
                        <div class="min-w-0">
                            <p class="text-[12px] font-bold text-[#1E1B2E]">
                                Majikan #{{ $a['id_majikan'] ?? '-' }}
                                <span class="ml-1 text-[10px] font-bold uppercase {{ ($a['status'] ?? '') === 'active' ? 'text-[#16A34A]' : 'text-[#9CA3AF]' }}">
                                    {{ $a['status'] ?? '' }}
                                </span>
                            </p>
                            <p class="text-[11px] font-semibold text-[#8B86A5] mt-0.5">
                                {{ $a['tanggal_mulai'] ?? '?' }}{{ !empty($a['tanggal_selesai']) ? ' → ' . $a['tanggal_selesai'] : '' }}
                            </p>
                        </div>
                        @if(!empty($a['catatan']))
                        <span class="text-[10px] text-[#A8A2C2] ml-2 flex-shrink-0 truncate max-w-[90px]">{{ $a['catatan'] }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Children --}}
        <div class="info-card anim delay-4">
            <span class="section-title">Anak di Supervisi</span>
            @if(count($children) === 0)
                <p class="text-[13px] text-[#9CA3AF] font-semibold">Tidak ada anak ter-assign.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($children as $c)
                    <span class="inline-flex items-center gap-1.5 text-[12px] font-bold text-[#4B5563] bg-[#F3F0FD] rounded-full px-3 py-1.5">
                        <ion-icon name="{{ ($c['gender'] ?? '') === 'L' ? 'male-outline' : 'female-outline' }}" style="font-size:13px;color:#8B46D3;"></ion-icon>
                        {{ $c['nama'] ?? '?' }}
                    </span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent timeline (7 hari) --}}
        <div class="info-card anim delay-4">
            <span class="section-title">Aktivitas Terbaru (7 hari)</span>
            @if(count($recentTimeline) === 0)
                <p class="text-[13px] text-[#9CA3AF] font-semibold">Belum ada aktivitas tercatat.</p>
            @else
                <div class="flex flex-col">
                    @foreach($recentTimeline as $t)
                    <div class="flex gap-3 py-2 {{ !$loop->last ? 'border-b border-[#F0EDF9]' : '' }}">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $t['source_type'] === 'diary' ? 'bg-[#FDE8F0]' : 'bg-[#EDE9FE]' }}">
                                <ion-icon name="{{ $t['source_type'] === 'diary' ? 'book-outline' : 'finger-print-outline' }}"
                                          style="font-size:15px;color:{{ $t['source_type'] === 'diary' ? '#EC4899' : '#8B46D3' }};"></ion-icon>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[13px] font-bold text-[#1E1B2E] truncate">{{ $t['title'] ?? '' }}</p>
                                <span class="text-[10px] font-semibold text-[#A8A2C2] whitespace-nowrap flex-shrink-0">
                                    {{ \Carbon\Carbon::parse($t['event_time'] ?? null)->diffForHumans() }}
                                </span>
                            </div>
                            @if(!empty($t['description']))
                            <p class="text-[12px] text-[#8B86A5] font-medium mt-0.5 line-clamp-2">{{ $t['description'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Open tickets --}}
        <div class="info-card anim delay-5">
            <span class="section-title">Tiket Terbuka</span>
            @if(count($openTickets) === 0)
                <p class="text-[13px] text-[#9CA3AF] font-semibold">Tidak ada tiket terbuka.</p>
            @else
                <div class="flex flex-col gap-2">
                    @foreach($openTickets as $q)
                    <div class="bg-[#F8F7FF] border border-[#ECEAF4] rounded-[12px] px-3 py-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[12px] font-bold text-[#1E1B2E] truncate">{{ $q['judul'] ?? '' }}</p>
                            <span class="text-[10px] font-bold text-[#D97706] bg-[#FEF3C7] rounded-full px-2 py-0.5 uppercase flex-shrink-0">{{ $q['status'] ?? 'open' }}</span>
                        </div>
                        @if(!empty($q['kategori']))
                        <span class="text-[10px] font-bold text-[#8B46D3] bg-[#EDE9FE] rounded-full px-2 py-0.5 mt-1 inline-block">{{ $q['kategori'] }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection