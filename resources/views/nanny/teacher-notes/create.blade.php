@extends('layouts.app')

@section('title', 'Add Teacher Note')

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
        <a href="{{ route('nanny-teacher-notes-show', $idAnak) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Add Teacher Note</span>
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

    <form action="{{ route('nanny-teacher-notes-store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 anim delay-2">
        @csrf
        <input type="hidden" name="id_anak" value="{{ $idAnak }}">

        {{-- Child (readonly) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child</label>
            <input type="text" value="{{ $namaAnak }}" class="inp" disabled>
        </div>

        {{-- Subject (optional) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Subject <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <select name="subject_id" class="inp appearance-none">
                <option value="">— None —</option>
                @foreach($subjects as $s)
                <option value="{{ $s['id'] }}" {{ old('subject_id') == $s['id'] ? 'selected' : '' }}>{{ $s['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Teacher name (optional) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Teacher Name <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <input type="text" name="teacher_name" value="{{ old('teacher_name') }}" class="inp" placeholder="e.g. Bu Rina">
        </div>

        {{-- Note --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Note <span class="text-red-400">*</span></label>
            <textarea name="note" rows="5" placeholder="e.g. Pertemuan hari ini fokus membaca, anak aktif menjawab" class="inp resize-none">{{ old('note') }}</textarea>
        </div>

        {{-- Attachment (optional, image) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Attachment <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <div class="relative">
                <input type="file" name="attachment" id="attachmentInput" accept="image/*" class="inp hidden">
                <button type="button" onclick="document.getElementById('attachmentInput').click()"
                    class="w-full inp flex items-center justify-center gap-2 text-[#8B46D3]">
                    <ion-icon name="image-outline" style="font-size:18px;"></ion-icon>
                    <span id="attachmentLabel">Choose image</span>
                </button>
            </div>
            @if($errors->has('attachment'))
            <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('attachment') }}</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pb-2">
            <a href="{{ route('nanny-teacher-notes-show', $idAnak) }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Note</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const attachmentInput = document.getElementById('attachmentInput');
const attachmentLabel = document.getElementById('attachmentLabel');
if (attachmentInput) {
    attachmentInput.addEventListener('change', function () {
        attachmentLabel.textContent = this.files && this.files[0] ? this.files[0].name : 'Choose image';
    });
}
</script>
@endpush