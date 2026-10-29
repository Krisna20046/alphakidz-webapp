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
                 . '<p>Menampilkan ringkasan AI, statistik PR & kehadiran 30 hari, timeline aktivitas, dan laporan pekanan terbaru.</p>'],
        ['icon' => 'sparkles-outline', 'color' => '#16A34A', 'title' => 'Ringkasan AI',
         'body' => '<p>Bagian atas berisi ringkasan harian, perilaku, dan insight belajar terbaru hasil analisis <b>AI</b> (read-only).</p>'],
        ['icon' => 'stats-chart-outline', 'color' => '#4F46E5', 'title' => 'Progres & Kehadiran',
         'body' => '<p>Bagian <b>Progres PR</b> dan <b>Kehadiran 30 hari</b> menampilkan statistik ringkas tugas dan kehadiran anak.</p>'],
        ['icon' => 'time-outline', 'color' => '#F59E0B', 'title' => 'Timeline & Laporan',
         'body' => '<p>Bagian <b>Aktivitas Terkini</b> memuat event terbaru 7 hari; <b>Laporan Pekanan</b> menampilkan report mingguan terakhir.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-dashboard-summary') }}"
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
                <option value="{{ route('majikan-dashboard-summary-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
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

        {{-- Daily summary (AI) --}}
        @if(!empty($dashboard['daily_summary']))
        @php $ds = $dashboard['daily_summary']; @endphp
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="sparkles-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Ringkasan Harian</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">
                        @if(!empty($ds['summary_date']))
                            {{ \Carbon\Carbon::parse($ds['summary_date'])->translatedFormat('d M Y') }}
                        @endif
                    </span>
                </div>
            </div>
            @if(!empty($ds['ai_summary']))
            <p class="ds-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">{{ $ds['ai_summary'] }}</p>
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5]">Belum ada ringkasan harian.</p>
            @endif
        </div>
        @endif

        {{-- Behavior summary (AI) --}}
        @if(!empty($dashboard['behavior_summary']))
        @php $bs = $dashboard['behavior_summary']; @endphp
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#FDF2F8] flex items-center justify-center shrink-0">
                    <ion-icon name="happy-outline" style="font-size:17px;color:#DB2777;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Ringkasan Perilaku</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">
                        @if(!empty($bs['summary_date']))
                            {{ \Carbon\Carbon::parse($bs['summary_date'])->translatedFormat('d M Y') }}
                        @endif
                    </span>
                </div>
            </div>
            @if(!empty($bs['behavior_summary']))
            <p class="ds-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">{{ $bs['behavior_summary'] }}</p>
            @endif
            @if(!empty($bs['recommendation']))
            <div class="mt-3 rounded-xl bg-[#F8F6FF] border border-[#EAE6F5] p-2.5">
                <span class="text-[10px] font-extrabold text-[#8B46D3] block mb-0.5 flex items-center gap-1">
                    <ion-icon name="bulb-outline" style="font-size:11px;"></ion-icon> Rekomendasi
                </span>
                <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed">{{ $bs['recommendation'] }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Learning insight (AI) --}}
        @if(!empty($dashboard['learning_insight']))
        @php $li = $dashboard['learning_insight']; @endphp
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center shrink-0">
                    <ion-icon name="eye-outline" style="font-size:17px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Insight Belajar</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Wawasan belajar terkini</span>
                </div>
            </div>
            @if(!empty($li['insight']))
            <p class="ds-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">{{ $li['insight'] }}</p>
            @endif
            @if(!empty($li['recommendation']))
            <div class="mt-3 rounded-xl bg-[#F0FDF4] border border-[#D1FAE5] p-2.5">
                <span class="text-[10px] font-extrabold text-[#16A34A] block mb-0.5 flex items-center gap-1">
                    <ion-icon name="bulb-outline" style="font-size:11px;"></ion-icon> Saran
                </span>
                <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed">{{ $li['recommendation'] }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Homework progress (30d) --}}
        @if(!empty($dashboard['homework_progress']))
        @php $hp = $dashboard['homework_progress']; @endphp
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                    <ion-icon name="stats-chart-outline" style="font-size:17px;color:#D97706;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Progres PR (30 Hari)</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Statistik tugas akademik</span>
                </div>
            </div>
            <div class="flex items-end gap-1 mb-3">
                @php
                    $rate = (int) ($hp['task_completion_rate'] ?? 0);
                @endphp
                <span class="text-[28px] font-extrabold text-[#1E1B2E] leading-none">{{ $rate }}%</span>
                <span class="text-[11px] font-bold text-[#8B86A5] mb-1">selesai</span>
            </div>
            <div class="w-full h-2 rounded-full bg-[#F0EDFB] overflow-hidden mb-3">
                <div class="h-full rounded-full transition-all" style="width:{{ $rate }}%;background:#D97706;"></div>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-xl bg-[#FAFAFF] border border-[#EAE6F5] py-2">
                    <p class="text-[16px] font-extrabold text-[#1E1B2E]">{{ (int)($hp['task_total'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-[#8B86A5]">Total</p>
                </div>
                <div class="rounded-xl bg-[#F0FDF4] border border-[#D1FAE5] py-2">
                    <p class="text-[16px] font-extrabold text-[#16A34A]">{{ (int)($hp['task_completed'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-[#8B86A5]">Selesai</p>
                </div>
                <div class="rounded-xl bg-[#FEF2F2] border border-[#FEE2E2] py-2">
                    <p class="text-[16px] font-extrabold text-[#DC2626]">{{ (int)($hp['task_overdue'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-[#8B86A5]">Overdue</p>
                </div>
            </div>
            @if(($hp['task_avg_score'] ?? null) !== null)
            <p class="text-[11px] font-bold text-[#8B86A5] mt-2">Nilai rata-rata: <span class="text-[#1E1B2E]">{{ $hp['task_avg_score'] }}</span></p>
            @endif
        </div>
        @endif

        {{-- Attendance stats (30d) --}}
        @if(!empty($dashboard['attendance_stats']))
        @php $as = $dashboard['attendance_stats']; @endphp
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#F0FDF4] flex items-center justify-center shrink-0">
                    <ion-icon name="finger-print-outline" style="font-size:17px;color:#16A34A;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Kehadiran (30 Hari)</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Statistik kehadiran asisten</span>
                </div>
            </div>
            @php $ar = (int) ($as['attendance_rate'] ?? 0); @endphp
            <div class="flex items-end gap-1 mb-3">
                <span class="text-[28px] font-extrabold text-[#1E1B2E] leading-none">{{ $ar }}%</span>
                <span class="text-[11px] font-bold text-[#8B86A5] mb-1">hadir</span>
            </div>
            <div class="w-full h-2 rounded-full bg-[#F0EDFB] overflow-hidden mb-3">
                <div class="h-full rounded-full transition-all" style="width:{{ $ar }}%;background:#16A34A;"></div>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-xl bg-[#F0FDF4] border border-[#D1FAE5] py-2">
                    <p class="text-[16px] font-extrabold text-[#16A34A]">{{ (int)($as['present_days'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-[#8B86A5]">Hadir</p>
                </div>
                <div class="rounded-xl bg-[#FEF3C7] border border-[#FDE68A] py-2">
                    <p class="text-[16px] font-extrabold text-[#D97706]">{{ (int)($as['late_days'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-[#8B86A5]">Terlambat</p>
                </div>
                <div class="rounded-xl bg-[#FEF2F2] border border-[#FEE2E2] py-2">
                    <p class="text-[16px] font-extrabold text-[#DC2626]">{{ (int)($as['absent_days'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-[#8B86A5]">Absen</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Activity timeline (7d) --}}
        @if(count($dashboard['activity_timeline'] ?? []) > 0)
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="time-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Aktivitas Terkini</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">Event 7 hari terakhir</span>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                @foreach(($dashboard['activity_timeline'] ?? []) as $ev)
                <div class="flex items-start gap-3 rounded-xl border border-[#EAE6F5] p-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="time-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[12px] font-extrabold text-[#1E1B2E] block">{{ $ev['title'] ?? 'Aktivitas' }}</span>
                        @if(!empty($ev['event_time']))
                        <span class="text-[10px] font-bold text-[#8B86A5]">{{ \Carbon\Carbon::parse($ev['event_time'])->translatedFormat('d M Y H:i') }}</span>
                        @endif
                        @if(!empty($ev['description']))
                        <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed mt-0.5 line-clamp-2">{{ $ev['description'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Weekly report --}}
        @if(!empty($dashboard['weekly_report']))
        @php $wr = $dashboard['weekly_report']; @endphp
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#E0F2FE] flex items-center justify-center shrink-0">
                    <ion-icon name="document-text-outline" style="font-size:17px;color:#0284C7;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Laporan Pekanan</span>
                    <span class="text-[11px] font-semibold text-[#8B86A5]">
                        @if(!empty($wr['week_start']) && !empty($wr['week_end']))
                            {{ \Carbon\Carbon::parse($wr['week_start'])->format('d M') }} – {{ \Carbon\Carbon::parse($wr['week_end'])->format('d M Y') }}
                        @endif
                    </span>
                </div>
            </div>
            @if(!empty($wr['summary']))
            <p class="ds-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">{{ $wr['summary'] }}</p>
            @else
            <p class="text-[12px] font-semibold text-[#8B86A5]">Belum ada summary laporan pekanan.</p>
            @endif
        </div>
        @endif

        {{-- Empty state --}}
        @if(empty($dashboard['daily_summary']) && empty($dashboard['behavior_summary']) && empty($dashboard['learning_insight'])
            && empty($dashboard['homework_progress']) && empty($dashboard['attendance_stats'])
            && count($dashboard['activity_timeline'] ?? []) === 0 && empty($dashboard['weekly_report']))
        <div class="flex flex-col items-center py-10">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
                <ion-icon name="grid-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">Belum ada data dasbor untuk tanggal ini.</p>
        </div>
        @endif
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
    const url = "{{ route('majikan-dashboard-summary-show', $idAnak) }}";
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