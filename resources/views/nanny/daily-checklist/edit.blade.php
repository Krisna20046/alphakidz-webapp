@extends('layouts.app')

@section('title', 'Edit Daily Checklist')

@push('styles')
<style>
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.96); }
    .item-row { animation:slideUp .25s ease both; }
</style>
@endpush

@php
    $items     = $checklist['items'] ?? [];
    $dayOfWeek = (int) ($checklist['day_of_week'] ?? 0);
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-daily-checklists-show', $idAnak) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Edit Daily Rutin</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }} • {{ $dayNames[(int)($checklist['day_of_week'] ?? 1)] ?? '' }}</p>
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

    <form action="{{ route('nanny-daily-checklists-update', $checklist['id']) }}" method="POST" class="space-y-4 anim delay-2">
        @csrf
        <input type="hidden" name="id_anak" value="{{ $idAnak }}">

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child</label>
            <input type="text" value="{{ $namaAnak }}" class="inp" disabled>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Judul <span class="text-red-400">*</span></label>
            <input type="text" name="title" value="{{ $checklist['title'] ?? '' }}" class="inp" placeholder="e.g. Rutinitas Pagi / Sore">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Hari <span class="text-red-400">*</span></label>
            <p class="text-[11px] font-semibold text-[#8B86A5] mb-2">Pindahkan rutinitas ini ke hari lain (berulang tiap minggu).</p>
            <select name="day_of_week" class="inp appearance-none" required>
                @foreach($dayNames as $num => $nama)
                <option value="{{ $num }}" {{ $dayOfWeek === $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-bold text-[#1E1B2E]">Item Aktivitas</label>
                <span class="text-[10px] font-bold text-[#8B86A5]" id="dcItemCount">{{ count($items) }}</span>
            </div>
            <p class="text-[11px] font-semibold text-[#8B86A5] mb-3">Ceklis yang dikerjakan anak.</p>

            <div id="dcItems" class="space-y-2 mb-3">
                @foreach($items as $index => $item)
                <div class="item-row flex items-center gap-2">
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">
                    <input type="text" name="items[{{ $index }}][item_name]" value="{{ $item['item_name'] }}" placeholder="Nama aktivitas" class="inp">
                    <button type="button" onclick="this.closest('.item-row').remove();dcCount();"
                        class="w-10 h-10 rounded-xl bg-[#FEF2F2] flex items-center justify-center shrink-0">
                        <ion-icon name="trash-outline" style="font-size:16px;color:#DC2626;"></ion-icon>
                    </button>
                </div>
                @endforeach
            </div>

            <button type="button" onclick="dcAddItem()"
                class="w-full py-3 rounded-xl border-2 border-dashed border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold">
                + Tambah Item
            </button>
        </div>

        <div class="flex gap-3 pb-2">
            <a href="{{ route('nanny-daily-checklists-show', ['id_anak' => $idAnak, 'day' => $dayOfWeek]) }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Batal</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Simpan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function dcCount() {
    const rows = document.querySelectorAll('#dcItems .item-row');
    document.getElementById('dcItemCount').textContent = rows.length;
}
function dcAddItem() {
    const box = document.getElementById('dcItems');
    const idx = box.querySelectorAll('.item-row').length;
    const row = document.createElement('div');
    row.className = 'item-row flex items-center gap-2';
    row.innerHTML =
        '<input type="hidden" name="items[' + idx + '][id]" value="">' +
        '<input type="text" name="items[' + idx + '][item_name]" placeholder="Nama aktivitas" class="inp">' +
        '<button type="button" onclick="this.closest(\'.item-row\').remove();dcCount();"' +
        '   class="w-10 h-10 rounded-xl bg-[#FEF2F2] flex items-center justify-center shrink-0">' +
        '   <ion-icon name="trash-outline" style="font-size:16px;color:#DC2626;"></ion-icon>' +
        '</button>';
    box.appendChild(row);
    row.querySelector('input').focus();
    dcCount();
}
</script>
@endpush
@endsection
