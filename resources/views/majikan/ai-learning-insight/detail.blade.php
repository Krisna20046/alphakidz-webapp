@extends('layouts.app')

@section('title', 'AI Learning Insight - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .insight-text { white-space: pre-line; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'document-text-outline', 'color' => '#8B46D3', 'title' => 'Detail Insight',
         'body' => '<p>Tampilan lengkap insight belajar anak hasil analisis <b>AI</b>.</p>'
                 . '<p>Bagian <b>Insight</b> adalah narasi perkembangan belajar, bagian <b>Rekomendasi</b> berisi saran pendampingan untuk majikan/nanny.</p>'],
        ['icon' => 'eye-outline', 'color' => '#F59E0B', 'title' => 'Baca Saja',
         'body' => '<p>Halaman ini <b>read-only</b> — hanya untuk melihat detail insight. Nanny yang bisa generate/memperbarui insight.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ $idAnak ? route('majikan-ai-learning-insights-show', $idAnak) : route('majikan-ai-learning-insights') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">AI Learning Insight</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        <button type="button" onclick="aiTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Insight card --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="sparkles-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Insight</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">
                    @if(!empty($record['generated_at']))
                        {{ \Carbon\Carbon::parse($record['generated_at'])->translatedFormat('d M Y H:i') }}
                    @endif
                </span>
            </div>
        </div>

        @if(!empty($record['insight']))
        <p class="insight-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">
            {{ $record['insight'] }}
        </p>
        @else
        <p class="text-[13px] font-semibold text-[#8B86A5] leading-relaxed">
            Belum ada insight untuk anak ini.
        </p>
        @endif
    </div>

    {{-- Recommendation card --}}
    @if(!empty($record['recommendation']))
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <ion-icon name="bulb-outline" style="font-size:17px;color:#D97706;"></ion-icon>
            </div>
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Rekomendasi</span>
        </div>
        <p class="insight-text text-[13px] font-semibold text-[#4B4763] leading-relaxed">
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

@include('ai-learning-insight._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush