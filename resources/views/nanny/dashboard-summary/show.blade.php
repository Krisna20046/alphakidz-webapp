@extends('layouts.app')

@section('title', 'Dashboard Summary - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .ds-text { white-space: pre-line; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'grid-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Dashboard Summary?',
         'body' => '<p>Ringkasan harian anak yang dikumpulkan dari berbagai modul dalam <b>satu layar</b>.</p>'
                 . '<p>Menampilkan jadwal hari ini, kehadiran asisten, reminder tugas, checklist rutin, dan PR yang belum selesai.</p>'],
        ['icon' => 'calendar-outline', 'color' => '#F59E0B', 'title' => 'Per Tanggal',
         'body' => '<p>Panel merujuk pada <b>satu tanggal</b> tertentu (default hari ini).</p>'
                 . '<p>Atur tanggal lalu tekan terapkan untuk melihat ringkasan hari tersebut.</p>'],
        ['icon' => 'time-outline', 'color' => '#16A34A', 'title' => 'Jadwal & Aktivitas',
         'body' => '<p>Bagian <b>Jadwal Hari Ini</b> memuat jadwal sekolah; <b>Checklist</b> menunjukkan progres rutinitas harian/pekanan.</p>'],
        ['icon' => 'book-outline', 'color' => '#DB2777', 'title' => 'PR & Reminder',
         'body' => '<p>Bagian <b>PR Belum Selesai</b> dan <b>Reminder</b> membantu memantau tugas yang perlu dikerjakan hari ini.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-dashboard-summary') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Dashboard Summary</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('nanny-dashboard-summary-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
        <button type="button" onclick="dsTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Date filter --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="calendar-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Tanggal Ringkasan</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">Ringkasan per tanggal (default hari ini)</span>
            </div>
        </div>
        <select id="dsTanggalSelect" class="mt-3 w-full appearance-none rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-sm font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
            <option value="today">Hari ini</option>
            @for($d = 1; $d <= 7; $d++)
            @php $dt = \Carbon\Carbon::today()->subDays($d); @endphp
            <option value="{{ $dt->format('Y-m-d') }}" @if($tanggal === $dt->format('Y-m-d')) selected @endif>
                {{ $dt->translatedFormat('l, d M Y') }}
            </option>
            @endfor
        </select>
        <button id="dsApplyBtn" type="button"
            class="mt-3 w-full py-3 rounded-2xl bg-[#8B46D3] text-white text-[13px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.99] transition-transform">
            <ion-icon name="checkmark-circle-outline" style="font-size:16px;"></ion-icon>
            Terapkan
        </button>
    </div>

    {{-- Loading placeholders --}}
    <div class="anim delay-2">
        <div id="dsLoading" class="hidden mb-4">
            <div class="flex items-center gap-3 rounded-2xl border border-[#DDD6EF] bg-white p-4 mb-4">
                <svg class="animate-spin" style="width:20px;height:20px;color:#8B46D3" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
                    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                </svg>
                <p class="text-[12px] font-bold text-[#8B86A5]">Memuat ringkasan dasbor…</p>
            </div>
        </div>

        {{-- Schedule today --}}
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center shrink-0">
                    <ion-icon name="school-outline" style="font-size:17px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Jadwal Hari Ini</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Jadwal sekolah</span>
                </div>
            </div>
            @if(count($dashboard['today_schedule'] ?? []) > 0)
            <div class="flex flex-col gap-2">
                @foreach(($dashboard['today_schedule'] ?? []) as $sched)
                <div class="flex items-start gap-3 rounded-xl border border-[#EAE6F5] p-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#EEF2FF] flex items-center justify-center shrink-0">
                        <ion-icon name="time-outline" style="font-size:14px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[12px] font-extrabold text-[#1E1B2E] block">{{ $sched['nama_mapel'] ?? 'Mapel' }}</span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">{{ $sched['start_time'] ?? '--' }} - {{ $sched['end_time'] ?? '--' }}</span>
                        @if(!empty($sched['teacher_name']))
                        <span class="text-[10px] font-semibold text-[#8B86A5] block mt-0.5">Guru: {{ $sched['teacher_name'] }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed">Belum ada jadwal untuk hari ini.</p>
            @endif
        </div>

        {{-- Attendance today --}}
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#F0FDF4] flex items-center justify-center shrink-0">
                    <ion-icon name="finger-print-outline" style="font-size:17px;color:#16A34A;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Kehadiran Asisten</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Status kehadiran hari ini</span>
                </div>
            </div>
            @if(!empty($dashboard['today_attendance']))
            @php $att = $dashboard['today_attendance']; $ok = in_array(($att['status'] ?? ''), ['present','late']); @endphp
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ok ? 'bg-[#F0FDF4] text-[#16A34A]' : 'bg-[#FEF2F2] text-[#DC2626]' }}">
                    <ion-icon name="{{ $ok ? 'checkmark-circle' : 'close-circle' }}" style="font-size:11px;"></ion-icon>
                    Hadir @if(!empty($att['status'])){{ $att['status'] }}@endif
                </span>
                @if(!empty($att['nama_nanny']))
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EDE9FE] text-[#8B46D3]">
                    <ion-icon name="person-outline" style="font-size:11px;"></ion-icon>
                    {{ $att['nama_nanny'] }}
                </span>
                @endif
            </div>
            @if(!empty($att['checkin_time']) || !empty($att['checkout_time']))
            <div class="flex items-center gap-3 text-[11px] font-bold text-[#8B86A5]">
                @if(!empty($att['checkin_time']))
                <span class="inline-flex items-center gap-1"><ion-icon name="log-in-outline" style="font-size:12px;"></ion-icon> Masuk {{ $att['checkin_time'] }}</span>
                @endif
                @if(!empty($att['checkout_time']))
                <span class="inline-flex items-center gap-1"><ion-icon name="log-out-outline" style="font-size:12px;"></ion-icon> Keluar {{ $att['checkout_time'] }}</span>
                @endif
            </div>
            @endif
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed">Belum ada kehadiran asisten untuk hari ini.</p>
            @endif
        </div>

        {{-- Reminders today --}}
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                    <ion-icon name="notifications-outline" style="font-size:17px;color:#D97706;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Reminder Hari Ini</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Reminder tugas</span>
                </div>
            </div>
            @if(count($dashboard['today_reminders'] ?? []) > 0)
            <div class="flex flex-col gap-2">
                @foreach(($dashboard['today_reminders'] ?? []) as $rem)
                <div class="flex items-start gap-3 rounded-xl border border-[#EAE6F5] p-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#FEF3C7] flex items-center justify-center shrink-0">
                        <ion-icon name="alarm-outline" style="font-size:14px;color:#D97706;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[12px] font-extrabold text-[#1E1B2E] block">{{ $rem['task_title'] ?? 'Tugas' }}</span>
                        @if(!empty($rem['type']))
                        <span class="text-[10px] font-bold text-[#8B86A5]">{{ strtoupper($rem['type']) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed">Tidak ada reminder untuk hari ini.</p>
            @endif
        </div>

        {{-- Checklist today --}}
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#FDF2F8] flex items-center justify-center shrink-0">
                    <ion-icon name="checkmark-done-outline" style="font-size:17px;color:#DB2777;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Checklist Hari Ini</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Progres rutinitas</span>
                </div>
            </div>
            @if(!empty($dashboard['today_checklist']))
            @php $ch = $dashboard['today_checklist']; @endphp
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-[#1E1B2E] truncate">
                    <ion-icon name="list-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                    {{ $ch['title'] ?? 'Checklist' }}
                </span>
                <span class="ml-auto shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                    {{ ($ch['is_completed'] ?? false) ? 'bg-[#F0FDF4] text-[#16A34A]' : 'bg-[#FEF3C7] text-[#D97706]' }}">
                    {{ $ch['completed_items'] ?? 0 }}/{{ $ch['total_items'] ?? 0 }}
                </span>
            </div>
            @php $pct = (int) ($ch['progress_percent'] ?? 0); @endphp
            <div class="w-full h-2 rounded-full bg-[#F0EDFB] overflow-hidden">
                <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%;background:#8B46D3;"></div>
            </div>
            <p class="text-[10px] font-bold text-[#8B86A5] mt-1.5">{{ $pct }}% selesai</p>
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed">Belum ada checklist untuk hari ini.</p>
            @endif
        </div>

        {{-- Pending homework --}}
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center shrink-0">
                    <ion-icon name="book-outline" style="font-size:17px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">PR Belum Selesai</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Tugas terdekat deadline</span>
                </div>
            </div>
            @if(count($dashboard['pending_homework'] ?? []) > 0)
            <div class="flex flex-col gap-2">
                @foreach(($dashboard['pending_homework'] ?? []) as $t)
                @php $overdue = ($t['status'] ?? '') === 'overdue'; @endphp
                <div class="flex items-start gap-3 rounded-xl border border-[#EAE6F5] p-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#EEF2FF] flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $overdue ? 'alert-circle-outline' : 'time-outline' }}" style="font-size:14px;color:{{ $overdue ? '#DC2626' : '#4F46E5' }}"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[12px] font-extrabold text-[#1E1B2E] block">{{ $t['title'] ?? 'Tugas' }}</span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            @if(!empty($t['nama_mapel'])){{ $t['nama_mapel'] }} · @endif
                            {{ !empty($t['deadline']) ? \Carbon\Carbon::parse($t['deadline'])->format('d M Y H:i') : 'Tanpa deadline' }}
                        </span>
                    </div>
                    @if($overdue)
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#DC2626]">Overdue</span>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed">Tidak ada PR yang belum selesai. 🎉</p>
            @endif
        </div>
    </div>
</div>

@include('dashboard-summary._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
function dsShowLoading(show) {
    const el = document.getElementById('dsLoading');
    if (el) el.classList.toggle('hidden', !show);
}
function dsLoad() {
    const sel = document.getElementById('dsTanggalSelect');
    const tgl = (sel && sel.value !== 'today') ? sel.value : '';
    const url = "{{ route('nanny-dashboard-summary-show', $idAnak) }}";
    dsShowLoading(true);
    const u = new URL(url, window.location.origin);
    if (tgl) u.searchParams.set('tanggal', tgl);
    window.location.href = u.toString();
}
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('dsTanggalSelect');
    const btn = document.getElementById('dsApplyBtn');
    if (sel && btn) btn.addEventListener('click', dsLoad);
    if (sel) sel.addEventListener('change', dsLoad);
});
</script>
@endpush