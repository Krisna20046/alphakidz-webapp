@extends('layouts.app')

@section('title', 'Assistant Notes - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'reader-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Catatan Asisten?',
         'body' => '<p>Halaman ini menampilkan <b>catatan harian</b> anak yang diisi nanny: mood, highlight, concern, dan rekomendasi.</p>'
                 . '<p>Halaman ini <b>read-only</b> — hanya untuk melihat.</p>'],
        ['icon' => 'happy-outline', 'color' => '#16A34A', 'title' => 'Mood',
         'body' => '<p>Suasana hati anak yang dicatat nanny: <b>Senang, Sedih, Marah,</b> atau <b>Biasa</b>.</p>'
                 . '<p>Mood antar-hari bisa membantu mendeteksi perubahan emosi anak.</p>'],
        ['icon' => 'sparkles-outline', 'color' => '#8B46D3', 'title' => 'Highlight, Concern & Rekomendasi',
         'body' => '<p><b>Highlight</b> — hal baik yang terjadi.</p>'
                 . '<p><b>Concern</b> — hal yang perlu diperhatikan.</p>'
                 . '<p><b>Rekomendasi</b> — saran tindak lanjut dari nanny.</p>'],
        ['icon' => 'briefcase-outline', 'color' => '#F59E0B', 'title' => 'Terkait Tugas',
         'body' => '<p>Catatan bisa dikaitkan ke <b>tugas akademik</b> tertentu, ditandai nama tugas di kartu.</p>'],
        ['icon' => 'document-text-outline', 'color' => '#0E9F6E', 'title' => 'Riwayat',
         'body' => '<p>Semua catatan tampil di riwayat, diurutkan dari yang terbaru.</p>'
                 . '<p>Gunakan tombol panah untuk berpindah halaman.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-notes') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Assistant Notes</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('majikan-notes-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
        <button type="button" onclick="anTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Notes history (read-only, via partial) --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Notes History</span>
            <span class="text-[10px] font-bold text-[#8B86A5]">read only</span>
        </div>
        @include('majikan.assistant-notes._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination])
    </div>

</div>

@include('assistant-notes._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
async function anGoToPage(page) {
    const url = "{{ route('majikan-notes-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('historyList').outerHTML = html;
}
</script>
@endpush
