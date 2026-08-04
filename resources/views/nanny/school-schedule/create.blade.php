@extends('layouts.app')

@section('title', 'Add Schedule')

@push('styles')
<style>
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
    .inp.err { border-color:#F44336; }
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.96); }
    .overlay {
        position:absolute; inset:0; background:rgba(255,255,255,0.85);
        display:flex; flex-direction:column; align-items:center;
        justify-content:center; z-index:20; border-radius:44px;
    }
    @keyframes spin { to{transform:rotate(360deg);} }
    .spinner { width:38px;height:38px;border-radius:50%;border:4px solid #EDE9FE;border-top-color:#8B46D3;animation:spin .8s linear infinite; }
</style>
@endpush

@php
    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $daysId = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('school-schedule.index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Add Schedule</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Create a new school schedule</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    <div id="loadingOverlay" class="overlay hidden">
        <div class="spinner mb-3"></div>
        <p class="text-sm font-bold text-[#8B46D3]">Saving...</p>
    </div>

    @if(session('error'))
    <div class="p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2 mb-4">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('school-schedule.store') }}" method="POST" class="space-y-4 anim delay-2" onsubmit="handleSubmit(event)">
        @csrf

        {{-- Anak --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child <span class="text-red-400">*</span></label>
            @if(count($children) === 0)
                <p class="text-sm font-semibold text-[#F59E0B]">No children are assigned to you right now.</p>
            @else
            <select name="id_anak" class="inp" required>
                <option value="" disabled {{ old('id_anak') ? '' : 'selected' }}>Select a child</option>
                @foreach($children as $c)
                <option value="{{ $c['id'] }}" {{ old('id_anak') == $c['id'] ? 'selected' : '' }}>{{ $c['nama'] }}</option>
                @endforeach
            </select>
            @endif
        </div>

        {{-- Mata Pelajaran --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Subject <span class="text-red-400">*</span></label>
            @if(count($subjects) === 0)
                <p class="text-sm font-semibold text-[#F59E0B]">No subjects yet. Add one first from the Subjects menu.</p>
            @else
            <select name="subject_id" class="inp" required>
                <option value="" disabled {{ old('subject_id') ? '' : 'selected' }}>Select a subject</option>
                @foreach($subjects as $subj)
                <option value="{{ $subj['id'] }}" {{ old('subject_id') == $subj['id'] ? 'selected' : '' }}>{{ $subj['name'] }}</option>
                @endforeach
            </select>
            @endif
        </div>

        {{-- Hari --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Day <span class="text-red-400">*</span></label>
            <select name="day_of_week" class="inp" required>
                <option value="" disabled {{ old('day_of_week') ? '' : 'selected' }}>Select a day</option>
                @foreach($days as $i => $d)
                <option value="{{ $d }}" {{ old('day_of_week') === $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>

        {{-- Jam --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-sm mb-4">Class Time</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#8B86A5] mb-2">Start <span class="text-red-400">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="inp" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#8B86A5] mb-2">End <span class="text-red-400">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="inp" required>
                </div>
            </div>
        </div>

        {{-- Guru & Catatan --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Teacher Name</label>
                <input type="text" name="teacher_name" value="{{ old('teacher_name') }}" placeholder="e.g. Ms. Rina" class="inp">
            </div>
            <div>
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional" class="inp resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pb-2">
            <a href="{{ route('school-schedule.index') }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save</button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function handleSubmit(form) {
    const st = form.start_time.value, en = form.end_time.value;
    const ok = (st && en) && en > st;
    $('#errorMsg')?.remove();
    if (!ok) {
        const d = document.createElement('div');
        d.id = 'errorMsg';
        d.className = 'p-3 rounded-2xl bg-red-50 border border-red-200 text-sm text-red-700 font-bold';
        d.textContent = 'End time must be after start time.';
        form.start_time.closest('.grid').parentElement.after(d);
        return false;
    }
    document.getElementById('loadingOverlay').classList.remove('hidden');
    return true;
}
</script>
@endpush
