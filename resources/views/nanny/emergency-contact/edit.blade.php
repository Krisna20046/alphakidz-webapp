@extends('layouts.app')

@section('title', 'Edit Emergency Contact')

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
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-emergency-contacts-show', $idAnak) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Edit Contact</span>
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

    <form action="{{ route('nanny-emergency-contacts-update', $contact['id']) }}" method="POST" class="space-y-4 anim delay-2">
        @csrf
        <input type="hidden" name="id_anak" value="{{ $idAnak }}">

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child</label>
            <input type="text" value="{{ $namaAnak }}" class="inp" disabled>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Name <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $contact['name']) }}" class="inp">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Relationship <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <input type="text" name="relationship" value="{{ old('relationship', $contact['relationship'] ?? '') }}" class="inp">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Phone <span class="text-red-400">*</span></label>
            <input type="tel" name="phone" value="{{ old('phone', $contact['phone']) }}" class="inp">
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Priority Order</label>
            <select name="priority_order" class="inp appearance-none">
                @foreach($can as $opt)
                <option value="{{ $opt['value'] }}" {{ old('priority_order', (string)($contact['priority_order'] ?? '')) == (string)($opt['value'] ?? '') ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3 pb-2">
            <a href="{{ route('nanny-emergency-contacts-show', $idAnak) }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Changes</button>
        </div>
    </form>
</div>
@endsection