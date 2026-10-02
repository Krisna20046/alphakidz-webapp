@extends('layouts.app')

@section('title', 'Behavior Summary - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .bs-text { white-space: pre-line; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ $idAnak ? route('nanny-behavior-summaries-show', $idAnak) : route('nanny-behavior-summaries') }}"
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

    {{-- Actions --}}
    @if(!empty($record['id']))
    <div class="anim delay-2 flex items-center gap-2 mb-4">
        <span class="flex-1 py-2 rounded-2xl border border-dashed border-[#D6CCEA] text-[#8B86A5] text-[12px] font-bold text-center">
            Generated by {{ !empty($record['generated_by']) ? $record['generated_by'] : 'AI' }}
        </span>
        <form method="POST" action="{{ route('nanny-behavior-summaries-regenerate', $record['id']) }}"
              class="shrink-0"
              onsubmit="event.preventDefault(); bsConfirmRegenerate('{{ route('nanny-behavior-summaries-regenerate', $record['id']) }}'); return false;">
            @csrf
            <input type="hidden" name="id_anak" value="{{ $idAnak }}">
            <button type="submit" aria-label="Regenerate"
                class="h-9 px-3 rounded-2xl bg-[#8B46D3] text-white text-[12px] font-extrabold flex items-center gap-1.5">
                <ion-icon name="refresh-outline" style="font-size:15px;"></ion-icon>
                Regenerate
            </button>
        </form>
    </div>
    @endif

</div>

{{-- Loader fullscreen (regenerate) --}}
<div id="bsLoading" class="hidden fixed inset-0 z-[90] flex flex-col items-center justify-center gap-4 px-6"
     style="background:rgba(30,27,46,0.68);backdrop-filter:blur(3px);">
    <div class="w-16 h-16 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center">
        <svg class="animate-spin" style="width:30px;height:30px;color:#fff" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
            <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
        </svg>
    </div>
    <p id="bsLoadingText" class="text-white text-[14px] font-extrabold">Memperbarui Behavior Summary…</p>
    <p class="text-white/60 text-[11px] font-semibold">Tunggu sebentar, proses mungkin berjalan beberapa detik.</p>
</div>

{{-- Modal konfirmasi regenerate --}}
<div id="bsRegenModal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-6">
    <div id="bsRegenBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm bg-white rounded-[28px] p-6 text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mx-auto mb-4">
            <ion-icon name="refresh-outline" style="font-size:26px;color:#8B46D3;"></ion-icon>
        </div>
        <p class="text-[16px] font-extrabold text-[#1E1B2E] mb-1">Regenerate summary ini?</p>
        <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed mb-5">
            Ringkasan AI akan diperbarui dengan data terbaru.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="bsRegenClose()"
                class="flex-1 py-3 rounded-2xl border border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold">Batal</button>
            <button type="button" onclick="bsRegenGo()"
                class="flex-1 py-3 rounded-2xl bg-[#8B46D3] text-white text-[13px] font-extrabold">Ya, Regenerate</button>
        </div>
    </div>
</div>

@include('behavior-summary._tutorial', ['steps' => [
    ['icon' => 'analytics-outline', 'color' => '#8B46D3', 'title' => 'Detail Ringkasan',
     'body' => '<p>Tampilan lengkap ringkasan perilaku anak hasil analisis <b>AI</b>.</p>'
              . '<p>Bagian <b>Pola Mood</b> dan <b>Pola Fokus</b> berisi analisis harian; <b>Rekomendasi</b> berisi saran pendampingan untuk majikan/nanny.</p>'],
    ['icon' => 'refresh-outline', 'color' => '#F59E0B', 'title' => 'Regenerate',
     'body' => '<p>Jika ada data baru, tekan tombol <b>Regenerate</b> untuk memperbarui ringkasan dengan data terbaru.</p>'],
]])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
const bsRegenModal    = document.getElementById('bsRegenModal');
let bsRegenTargetUrl  = '';
if (bsRegenModal) {
    document.getElementById('bsRegenBackdrop').addEventListener('click', bsRegenClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !bsRegenModal.classList.contains('hidden')) bsRegenClose();
    });
}
function bsConfirmRegenerate(url) {
    bsRegenTargetUrl = url;
    bsRegenModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function bsRegenClose() {
    bsRegenModal.classList.add('hidden');
    bsRegenTargetUrl = '';
    document.body.style.overflow = '';
}
function bsRegenGo() {
    if (!bsRegenTargetUrl) return bsRegenClose();
    const url = bsRegenTargetUrl;
    bsRegenClose();
    bsShowLoading('Memperbarui Behavior Summary…');
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}
</script>

<script>
function bsShowLoading(text) {
    const el = document.getElementById('bsLoading');
    if (!el) return;
    const txt = document.getElementById('bsLoadingText');
    if (txt && text) txt.textContent = text;
    el.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
</script>
@endpush