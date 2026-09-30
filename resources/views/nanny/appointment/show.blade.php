@extends('layouts.app')

@section('title', 'Appointments - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .upcoming-card { transition: transform .15s ease; }
    .upcoming-card:active { transform: scale(0.98); }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'calendar-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Appointment?',
         'body' => '<p>Halaman ini untuk mengelola <b>jadwal kegiatan</b> anak — ekstrakurikuler, kontrol dokter, terapi, dan event sekolah.</p>'
                 . '<p>Setiap jadwal bisa diberi pengingat (reminder) sebelum waktunya.</p>'],
        ['icon' => 'time-outline', 'color' => '#8B46D3', 'title' => 'Jadwal Mendatang',
         'body' => '<p>Bagian <b>Upcoming</b> menampilkan jadwal yang waktunya masih di depan (belum lewat).</p>'
                 . '<p>Bagian <b>All Appointments</b> berisi seluruh riwayat jadwal anak.</p>'],
        ['icon' => 'add-circle-outline', 'color' => '#0E9F6E', 'title' => 'Cara Menambah',
         'body' => '<p>Tekan tombol <b>+</b> (kanan bawah) untuk menambah appointment benari.</p>'
                 . '<p>Pilih <b>type</b>, isi <b>title</b>, <b>waktu</b>, <b>lokasi</b> (opsional), dan <b>reminder</b> lalu simpan.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-appointment') }}"
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
                <option value="{{ route('nanny-appointment-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @else
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Appointments</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @endif
        <button type="button" onclick="apTutorialOpen()"
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

    {{-- Upcoming appointments --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Upcoming</span>
            <span class="text-[10px] font-bold text-[#8B46D3] bg-[#EDE9FE] rounded-full px-2.5 py-1">{{ count($upcoming) }} scheduled</span>
        </div>

        @if(count($upcoming) === 0)
        <div class="flex flex-col items-center py-6">
            <div class="w-12 h-12 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-2">
                <ion-icon name="calendar-outline" style="font-size:22px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">Tidak ada jadwal mendatang.</p>
        </div>
        @else
        <div class="flex flex-col gap-2">
            @foreach($upcoming as $u)
            @php
                $um = match ($u['type'] ?? '') {
                    'extracurricular' => ['Aktivitas Ekstra', '#8B46D3', '#EDE9FE', 'trophy-outline'],
                    'doctor'          => ['Konsultasi Dokter', '#DC2626', '#FEE2E2', 'medical-outline'],
                    'therapy'         => ['Terapi', '#0E9F6E', '#D1FAE5', 'fitness-outline'],
                    'school_event'    => ['Event Sekolah', '#D97706', '#FEF3C7', 'school-outline'],
                    default           => ['Appointment', '#8B46D3', '#EDE9FE', 'calendar-outline'],
                };
            @endphp
            <div class="upcoming-card rounded-2xl border border-[#EDE9FE] bg-[#FAF6FF] p-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $um[2] }};">
                        <ion-icon name="{{ $um[3] }}" style="font-size:16px;color:{{ $um[1] }};"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $um[2] }};color:{{ $um[1] }};">
                                {{ $um[0] }}
                            </span>
                            @if(!empty($u['reminder_before_minutes']))
                            <span class="text-[10px] font-bold text-[#8B86A5]">reminder {{ $u['reminder_before_minutes'] }} mnt</span>
                            @endif
                        </div>
                        <p class="text-[14px] font-extrabold text-[#1E1B2E] mt-1.5">{{ $u['title'] }}</p>
                        <div class="mt-1 flex items-center gap-1.5">
                            <ion-icon name="time-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                            <span class="text-[12px] font-bold text-[#4B4763]">{{ $u['schedule_at'] }}</span>
                        </div>
                        @if(!empty($u['location']))
                        <div class="mt-1 flex items-center gap-1.5">
                            <ion-icon name="location-outline" style="font-size:12px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                            <span class="text-[12px] font-semibold text-[#8B86A5]">{{ $u['location'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- All appointments (history) --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">All Appointments</span>
            <a href="{{ route('nanny-appointment-create', $idAnak) }}"
               class="text-[10px] font-bold text-[#8B46D3]">+ Add</a>
        </div>
        @include('nanny.appointment._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination, 'canEdit' => true])
    </div>

</div>

{{-- FAB --}}
<div class="fixed bottom-[80px] right-[20px] sm:right-[calc(50%-175px)] z-30">
    <a href="{{ route('nanny-appointment-create', $idAnak) }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

{{-- Modal konfirmasi hapus appointment (in-app) --}}
<div id="apDeleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6">
    <div id="apDeleteBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm bg-white rounded-[28px] p-6 text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <ion-icon name="trash-outline" style="font-size:26px;color:#DC2626;"></ion-icon>
        </div>
        <p class="text-[16px] font-extrabold text-[#1E1B2E] mb-1">Hapus appointment ini?</p>
        <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed mb-5">
            Jadwal tidak bisa dikembalikan setelah dihapus.
        </p>
        <form id="apDeleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="apDeleteClose()"
                    class="flex-1 py-3 rounded-2xl border border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold">Batal</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#DC2626] text-white text-[13px] font-extrabold">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@include('appointment._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
const apDeleteModal = document.getElementById('apDeleteModal');
const apDeleteForm  = document.getElementById('apDeleteForm');
if (apDeleteModal) {
    document.getElementById('apDeleteBackdrop').addEventListener('click', apDeleteClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !apDeleteModal.classList.contains('hidden')) apDeleteClose();
    });
}
function apDeleteConfirm(url) {
    if (!apDeleteModal) return false;
    apDeleteForm.action = url;
    apDeleteModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    return false;
}
function apDeleteClose() {
    apDeleteModal.classList.add('hidden');
    document.body.style.overflow = '';
}
</script>

<script>
async function apGoToPage(page) {
    const url = "{{ route('nanny-appointment-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('appointmentList').outerHTML = html;
}
</script>
@endpush