@extends('layouts.app')

@section('title', 'AI Learning Insight - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .insight-text { white-space: pre-line; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ $idAnak ? route('nanny-ai-learning-insights-show', $idAnak) : route('nanny-ai-learning-insights') }}"
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

    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3 mb-4
        {{ session('success') ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center
            {{ session('success') ? 'bg-green-100' : 'bg-red-100' }}">
            <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'close-circle' }}"
                style="font-size:18px;color:{{ session('success') ? '#4CAF50' : '#F44336' }};"></ion-icon>
        </div>
        <p class="text-sm font-bold {{ session('success') ? 'text-green-800' : 'text-red-800' }} flex-1">
            {{ session('success') ?? session('error') }}
        </p>
        <button onclick="document.getElementById('toast').remove()">
            <ion-icon name="close" style="font-size:16px;color:#999;"></ion-icon>
        </button>
    </div>
    @endif

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
            Belum ada insight. Generate untuk membuat ringkasan AI perkembangan belajar anak.
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

    {{-- Actions --}}
    @if(!empty($record['id']))
    <div class="anim delay-2 flex items-center gap-2 mb-4">
        <span class="flex-1 py-2 rounded-2xl border border-dashed border-[#D6CCEA] text-[#8B86A5] text-[12px] font-bold text-center">
            Diperbarui {{ !empty($record['updated_at']) ? \Carbon\Carbon::parse($record['updated_at'])->translatedFormat('d M Y H:i') : '' }}
        </span>
        <form method="POST" action="{{ route('nanny-ai-learning-insights-regenerate', $record['id']) }}"
              class="shrink-0"
              onsubmit="event.preventDefault(); aiConfirmRegenerate('{{ route('nanny-ai-learning-insights-regenerate', $record['id']) }}'); return false;">
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
<div id="aiLoading" class="hidden fixed inset-0 z-[90] flex flex-col items-center justify-center gap-4 px-6"
     style="background:rgba(30,27,46,0.68);backdrop-filter:blur(3px);">
    <div class="w-16 h-16 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center">
        <svg class="animate-spin" style="width:30px;height:30px;color:#fff" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
            <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
        </svg>
    </div>
    <p id="aiLoadingText" class="text-white text-[14px] font-extrabold">Memperbarui AI Learning Insight…</p>
    <p class="text-white/60 text-[11px] font-semibold">Tunggu sebentar, proses mungkin berjalan beberapa detik.</p>
</div>

{{-- Modal konfirmasi regenerate --}}
<div id="aiRegenModal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-6">
    <div id="aiRegenBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm bg-white rounded-[28px] p-6 text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mx-auto mb-4">
            <ion-icon name="refresh-outline" style="font-size:26px;color:#8B46D3;"></ion-icon>
        </div>
        <p class="text-[16px] font-extrabold text-[#1E1B2E] mb-1">Regenerate insight ini?</p>
        <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed mb-5">
            Ringkasan AI akan diperbarui dengan data terbaru.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="aiRegenClose()"
                class="flex-1 py-3 rounded-2xl border border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold">Batal</button>
            <button type="button" onclick="aiRegenGo()"
                class="flex-1 py-3 rounded-2xl bg-[#8B46D3] text-white text-[13px] font-extrabold">Ya, Regenerate</button>
        </div>
    </div>
</div>

@include('ai-learning-insight._tutorial', ['steps' => [
    ['icon' => 'document-text-outline', 'color' => '#8B46D3', 'title' => 'Detail Insight',
     'body' => '<p>Tampilan lengkap insight belajar anak hasil analisis <b>AI</b>.</p>'
              . '<p>Bagian <b>Insight</b> adalah narasi perkembangan belajar, bagian <b>Rekomendasi</b> berisi saran pendampingan untuk majikan/nanny.</p>'],
    ['icon' => 'refresh-outline', 'color' => '#F59E0B', 'title' => 'Regenerate',
     'body' => '<p>Jika ada data baru, tekan tombol <b>Regenerate</b> untuk memperbarui insight dengan data terbaru.</p>'],
]])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
const aiRegenModal    = document.getElementById('aiRegenModal');
let aiRegenTargetUrl  = '';
if (aiRegenModal) {
    document.getElementById('aiRegenBackdrop').addEventListener('click', aiRegenClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !aiRegenModal.classList.contains('hidden')) aiRegenClose();
    });
}
function aiConfirmRegenerate(url) {
    aiRegenTargetUrl = url;
    aiRegenModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function aiRegenClose() {
    aiRegenModal.classList.add('hidden');
    aiRegenTargetUrl = '';
    document.body.style.overflow = '';
}
function aiRegenGo() {
    if (!aiRegenTargetUrl) return aiRegenClose();
    const url = aiRegenTargetUrl;
    aiRegenClose();
    aiShowLoading('Memperbarui AI Learning Insight…');
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
function aiShowLoading(text) {
    const el = document.getElementById('aiLoading');
    if (!el) return;
    const txt = document.getElementById('aiLoadingText');
    if (txt && text) txt.textContent = text;
    el.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
</script>
@endpush