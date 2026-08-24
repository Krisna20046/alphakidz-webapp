@extends('layouts.app')

@section('title', 'Emergency Contacts - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'call-outline', 'color' => '#DC2626', 'title' => 'Apa itu Kontakti Kurgensi?',
         'body' => '<p>Halaman ini untuk mengelola <b>kontakti kurgensi</b> anak — keluarga / dokter / kontakti importanti.</p>'
                 . '<p>Unik anak lah bisa tambah, ubah, hapus utk kontakti benari.</p>'],
        ['icon' => 'list-outline', 'color' => '#8B46D3', 'title' => 'Urutan (Priority)',
         'body' => '<p>Maksiku iju tampil suayen di-urutken na <b>priority_order</b> (1 = most urgent).</p>'
                 . '<p>Quick call button di-cepat primero non jaman di-hubungi pertama malibel diklik.<p>'],
        ['icon' => 'add-circle-outline', 'color' => '#0E9F6E', 'title' => 'Cara Menambah',
         'body' => '<p>Tekan tombol <b>+</b> (kanan bawah) untung tambah kontak benari.</p>'
                 . '<p>Isi <b>name</b>, <b>relationship</b>, <b>phone</b>, pas <b>priority</b>, lalu simpan.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-emergency-contacts') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        @if(count($anakList) > 1)
        <div class="relative">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('nanny-emergency-contacts-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @else
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Emergency Contacts</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @endif
        <button type="button" onclick="ecTutorialOpen()"
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

    {{-- Contact list --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Emergency Contacts</span>
            <a href="{{ route('nanny-emergency-contacts-create', $idAnak) }}"
               class="text-[10px] font-bold text-[#8B46D3]">+ Add contact</a>
        </div>
        @include('nanny.emergency-contact._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination, 'canEdit' => true])
    </div>

</div>

{{-- FAB --}}
<div class="fixed bottom-[80px] right-[20px] sm:right-[calc(50%-175px)] z-30">
    <a href="{{ route('nanny-emergency-contacts-create', $idAnak) }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

{{-- Modal konfirmasi hapus kontak (in-app) --}}
<div id="ecDeleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6">
    <div id="ecDeleteBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm bg-white rounded-[28px] p-6 text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <ion-icon name="trash-outline" style="font-size:26px;color:#DC2626;"></ion-icon>
        </div>
        <p class="text-[16px] font-extrabold text-[#1E1B2E] mb-1">Hapus kontak ini?</p>
        <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed mb-5">
            Kontak tidak bisa dikembalikan setelah dihapus.
        </p>
        <form id="ecDeleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="ecDeleteClose()"
                    class="flex-1 py-3 rounded-2xl border border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold">Batal</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#DC2626] text-white text-[13px] font-extrabold">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@include('emergency-contact._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
const ecDeleteModal = document.getElementById('ecDeleteModal');
const ecDeleteForm  = document.getElementById('ecDeleteForm');
if (ecDeleteModal) {
    document.getElementById('ecDeleteBackdrop').addEventListener('click', ecDeleteClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !ecDeleteModal.classList.contains('hidden')) ecDeleteClose();
    });
}
function ecDeleteConfirm(url) {
    if (!ecDeleteModal) return false;
    ecDeleteForm.action = url;
    ecDeleteModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    return false;
}
function ecDeleteClose() {
    ecDeleteModal.classList.add('hidden');
    document.body.style.overflow = '';
}
</script>

<script>
async function ecGoToPage(page) {
    const url = "{{ route('nanny-emergency-contacts-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('contactList').outerHTML = html;
}
</script>
@endpush