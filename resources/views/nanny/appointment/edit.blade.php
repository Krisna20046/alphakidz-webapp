@extends('layouts.app')

@section('title', 'Edit Appointment')

@push('styles')
<style>
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
    .type-chip { transition: all .15s ease; }
    .type-chip.selected { border-color:#8B46D3; background:#EDE9FE; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-appointment-show', $idAnak) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Edit Appointment</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('error') || $errors->any())
    <div class="p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2 mb-4">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-bold">{{ $errors->first() ?? session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('nanny-appointment-update', $record['id']) }}" method="POST" class="space-y-4 anim delay-2">
        @csrf
        <input type="hidden" name="id_anak" value="{{ $idAnak }}">

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child</label>
            <input type="text" value="{{ $namaAnak }}" class="inp" disabled>
        </div>

        @php
            $currentType = $record['type'] ?? '';
            $types = [
                'extracurricular' => ['Aktivitas Ekstra', 'trophy-outline'],
                'doctor'          => ['Konsultasi Dokter', 'medical-outline'],
                'therapy'         => ['Terapi', 'fitness-outline'],
                'school_event'    => ['Event Sekolah', 'school-outline'],
            ];

            $dt = $record['schedule_at'] ?? '';
            if ($dt) {
                // Normalize "Y-m-d H:i:s" → "Y-m-dTH:i" untuk input datetime-local
                $dt = str_replace(' ', 'T', substr($dt, 0, 16));
            }
        @endphp

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Type <span class="text-red-400">*</span></label>
            <input type="hidden" name="type" id="typeInput" value="{{ $currentType }}">
            <div class="grid grid-cols-2 gap-2">
                @foreach($types as $val => [$label, $icon])
                <button type="button" data-type="{{ $val }}"
                    class="type-chip flex items-center gap-2 rounded-xl border-2 border-[#DDD6EF] bg-[#F8F7FF] px-3 py-2.5 text-[11px] font-extrabold text-[#4B4763] {{ $currentType === $val ? 'selected' : '' }}">
                    <ion-icon name="{{ $icon }}" style="font-size:14px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                    <span class="leading-tight">{{ $label }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Title <span class="text-red-400">*</span></label>
            <input type="text" name="title" value="{{ $record['title'] ?? '' }}" class="inp" placeholder="e.g. Kelas Piano">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Schedule Date & Time <span class="text-red-400">*</span></label>
            <input type="datetime-local" name="schedule_at" value="{{ $dt }}" class="inp">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Location <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <input type="text" name="location" value="{{ $record['location'] ?? '' }}" class="inp" placeholder="e.g. Ruang Musik">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Reminder Before (minutes)</label>
            <select name="reminder_before_minutes" class="inp appearance-none">
                @foreach([15, 30, 60, 120, 1440] as $opt)
                <option value="{{ $opt }}" {{ (int)($record['reminder_before_minutes'] ?? 60) == $opt ? 'selected' : '' }}>{{ $opt }} menit{{ $opt == 1440 ? ' (1 hari)' : '' }}</option>
                @endforeach
            </select>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Notes <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <textarea name="notes" class="inp" rows="3" placeholder="Catatan tambahan...">{{ $record['notes'] ?? '' }}</textarea>
        </div>

        <div class="flex gap-3 pb-2">
            <a href="{{ route('nanny-appointment-show', $idAnak) }}"
               class="flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.type-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.type-chip').forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');
        document.getElementById('typeInput').value = chip.dataset.type;
    });
});
</script>
@endpush