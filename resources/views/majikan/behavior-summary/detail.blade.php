@extends('layouts.app')

@section('title', 'Behavior Summary - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .bs-text { white-space: pre-line; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'analytics-outline', 'color' => '#8B46D3', 'title' => 'Detail Ringkasan',
         'body' => '<p>Tampilan lengkap ringkasan perilaku anak hasil analisis <b>AI</b>.</p>'
                 . '<p>Bagian <b>Pola Mood</b> dan <b>Pola Fokus</b> berisi analisis harian; <b>Rekomendasi</b> berisi saran pendampingan untuk majikan/nanny.</p>'],
        ['icon' => 'eye-outline', 'color' => '#F59E0B', 'title' => 'Baca Saja',
         'body' => '<p>Halaman ini <b>read-only</b> — hanya untuk melihat detail ringkasan. Nanny yang bisa generate/memperbarui ringkasan.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ $idAnak ? route('majikan-behavior-summaries-show', $idAnak) : route('majikan-behavior-summaries') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Behavior Summary</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        <button type="button" onclick="bsTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Summary card --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="analytics-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Behavior Summary</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">
                    @if(!empty($record['summary_date']))
                        {{ \Carbon\Carbon::parse($record['summary_date'])->translatedFormat('d M Y') }}
                    @endif
                    @if(!empty($record['updated_at']))
                        · diperbarui {{ \Carbon\Carbon::parse($record['updated_at'])->translatedFormat('d M Y H:i') }}
                    @endif
                </span>
            </div>
        </div>

        @if(!empty($record['behavior_summary']))
        <p class="bs-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">
            {{ $record['behavior_summary'] }}
        </p>
        @else
        <p class="text-[13px] font-semibold text-[#8B86A5] leading-relaxed">
            Belum ada ringkasan perilaku untuk anak ini.
        </p>
        @endif
    </div>

    {{-- Mood pattern card --}}
    @if(!empty($record['mood_pattern']))
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-[#FDF2F8] flex items-center justify-center shrink-0">
                <ion-icon name="happy-outline" style="font-size:17px;color:#DB2777;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Pola Mood</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">Dominan & urutan emosi sepanjang hari</span>
            </div>
        </div>
        <p class="bs-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">
            {{ $record['mood_pattern'] }}
        </p>
    </div>
    @endif

    {{-- Focus pattern card --}}
    @if(!empty($record['focus_pattern']))
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center shrink-0">
                <ion-icon name="eye-outline" style="font-size:17px;color:#4F46E5;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Pola Fokus</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">Skor fokus & trend pada hari itu</span>
            </div>
        </div>
        <p class="bs-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">
            {{ $record['focus_pattern'] }}
        </p>
    </div>
    @endif

    {{-- Recommendation card --}}
    @if(!empty($record['recommendation']))
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <ion-icon name="bulb-outline" style="font-size:17px;color:#D97706;"></ion-icon>
            </div>
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Rekomendasi</span>
        </div>
        <p class="bs-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">
            {{ $record['recommendation'] }}
        </p>
    </div>
    @endif

    @if(!empty($record['updated_at']))
    <div class="anim delay-2 py-2 rounded-2xl border border-dashed border-[#D6CCEA] text-[#8B86A5] text-[12px] font-bold text-center mb-4">
        Diperbarui {{ \Carbon\Carbon::parse($record['updated_at'])->translatedFormat('d M Y H:i') }}
    </div>
    @endif

</div>

@include('behavior-summary._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush