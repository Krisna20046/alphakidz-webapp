@extends('layouts.app')

@section('title', 'Weekly Report - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'document-text-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Weekly Report?',
         'body' => '<p>Ringkasan mingguan anak dalam bentuk <b>PDF</b> yang dibuat nanny dari data aktivitas diary, tugas akademik, dan perkembangan belajar selama satu minggu.</p>'
                 . '<p>Halaman ini <b>read-only</b> — hanya untuk melihat dan mengunduh.</p>'],
        ['icon' => 'sparkles-outline', 'color' => '#16A34A', 'title' => 'Ringkasan AI',
         'body' => '<p>Setiap laporan berisi ringkasan naratif hasil <b>AI</b>: ringkasan minggu, pola & observasi, perhatian khusus, dan rekomendasi.</p>'
                 . '<p>Bila belum ada data aktivitas pada minggu tersebut, laporan menampilkan catatan bahwa data kosong.</p>'],
        ['icon' => 'download-outline', 'color' => '#0E9F6E', 'title' => 'Unduh PDF',
         'body' => '<p>Tekan tombol <b>Download PDF</b> untuk mengunduh laporan minggu terpilih.</p>'
                 . '<p>Gunakan tombol panah untuk berpindah halaman riwayat minggu-minggu sebelumnya.</p>'],
        ['icon' => 'time-outline', 'color' => '#F59E0B', 'title' => 'Status Laporan',
         'body' => '<p>Status <b>Ready · PDF tersedia</b> berarti laporan siap diunduh.</p>'
                 . '<p>Status <b>No data</b> berarti tidak ada aktivitas tercatat pada minggu tersebut, sehingga PDF belum dibuat.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-weekly-report') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Weekly Report</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('majikan-weekly-report-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
        <button type="button" onclick="wrTutorialOpen()"
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

    {{-- History (read-only, via partial) --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Report History</span>
            <span class="text-[10px] font-bold text-[#8B86A5]">read only</span>
        </div>
        @include('majikan.weekly-report._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination])
    </div>

</div>

{{-- Modal preview PDF (liat dalam aplikasi) --}}
<div id="wrPdfModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div id="wrPdfBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-2xl h-[85vh] bg-white rounded-[24px] overflow-hidden flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#EAE6F5] bg-white">
            <span class="text-[14px] font-extrabold text-[#1E1B2E] flex items-center gap-2">
                <ion-icon name="document-text-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                Weekly Report
            </span>
            <button type="button" onclick="wrPdfClose()"
                class="w-8 h-8 rounded-full bg-[#F3F0FD] flex items-center justify-center shrink-0">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="flex-1 bg-[#F3F0FD] relative overflow-hidden">
            <div id="wrPdfLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3"
                 style="background:rgba(243,240,253,0.6);">
                <div class="w-12 h-12 rounded-2xl bg-[#8B46D3] flex items-center justify-center">
                    <svg class="animate-spin" style="width:22px;height:22px;color:#fff" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
                        <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                    </svg>
                </div>
                <p class="text-[#8B86A5] text-[12px] font-bold">Memuat PDF…</p>
            </div>
            <iframe id="wrPdfFrame" src="about:blank" class="w-full h-full border-0" title="Preview Weekly Report"></iframe>
        </div>
    </div>
</div>

@include('weekly-report._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
async function wrGoToPage(page) {
    const url = "{{ route('majikan-weekly-report-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('historyList').outerHTML = html;
}
</script>

<script>
const wrPdfModal = document.getElementById('wrPdfModal');
const wrPdfFrame = document.getElementById('wrPdfFrame');
if (wrPdfModal) {
    document.getElementById('wrPdfBackdrop').addEventListener('click', wrPdfClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !wrPdfModal.classList.contains('hidden')) wrPdfClose();
    });
}
const wrPdfLoading = document.getElementById('wrPdfLoading');
function wrPdfOpen(url) {
    if (!wrPdfModal) return;
    if (wrPdfLoading) wrPdfLoading.style.display = 'flex';
    wrPdfFrame.src = url;
    wrPdfModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function wrPdfReady() {
    if (wrPdfLoading) wrPdfLoading.style.display = 'none';
}
function wrPdfClose() {
    wrPdfModal.classList.add('hidden');
    wrPdfFrame.src = 'about:blank';
    document.body.style.overflow = '';
    if (wrPdfLoading) wrPdfLoading.style.display = 'flex';
}
wrPdfFrame.addEventListener('load', wrPdfReady);
</script>
@endpush