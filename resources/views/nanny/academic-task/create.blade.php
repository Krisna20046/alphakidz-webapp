@extends('layouts.app')

@section('title', 'Add Task')

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
    .type-opt {
        flex:1; padding:14px 10px; border-radius:14px; background:#F8F7FF;
        border:2px solid #DDD6EF; display:flex; flex-direction:column;
        align-items:center; gap:6px; cursor:pointer; transition:all .15s;
        color:#8B86A5;
    }
    .type-opt.sel { border-color:#8B46D3; background:#EDE9FE; color:#8B46D3; }
    .type-opt:active { transform:scale(.96); }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('academic-task.index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Add Task</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Track homework, project or exam</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('error'))
    <div class="p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2 mb-4">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('academic-task.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 anim delay-2">
        @csrf

        {{-- Child + hidden assignment --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child <span class="text-red-400">*</span></label>
            @if(count($children) === 0)
                <p class="text-sm font-semibold text-[#F59E0B]">No children are assigned to you right now.</p>
            @else
            <input type="hidden" name="id_assignment" id="idAssignment">
            <select name="id_anak" id="idAnak" class="inp" required>
                <option value="" disabled {{ old('id_anak') ? '' : 'selected' }}>Select a child</option>
                @foreach($children as $c)
                <option value="{{ $c['id'] }}" data-assignment="{{ $c['id_assignment'] }}"
                    {{ old('id_anak') == $c['id'] ? 'selected' : '' }}>{{ $c['nama'] }}</option>
                @endforeach
            </select>
            @endif
        </div>

        {{-- Type --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-3">Type <span class="text-red-400">*</span></label>
            <input type="hidden" name="type" id="taskType" value="{{ old('type', 'homework') }}">
            <div class="flex gap-3">
                @foreach(['homework' => ['label' => 'Homework', 'icon' => 'book-outline'], 'project' => ['label' => 'Project', 'icon' => 'construct-outline'], 'exam' => ['label' => 'Exam', 'icon' => 'document-text-outline']] as $key => $opt)
                <button type="button" onclick="selectType('{{ $key }}', this)"
                    class="type-opt {{ old('type', 'homework') === $key ? 'sel' : '' }}">
                    <ion-icon name="{{ $opt['icon'] }}" style="font-size:22px;"></ion-icon>
                    <span class="text-xs font-extrabold">{{ $opt['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Subject --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Subject <span class="text-red-400">*</span></label>
            <select name="subject_id" class="inp" required>
                <option value="" disabled {{ old('subject_id') ? '' : 'selected' }}>Select a subject</option>
                @foreach($subjects as $s)
                <option value="{{ $s['id'] }}" {{ old('subject_id') == $s['id'] ? 'selected' : '' }}>{{ $s['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Title & description --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Membuat miniatur rumah" class="inp" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Description</label>
                <textarea name="description" rows="3" placeholder="Optional" class="inp resize-none">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Deadline & priority --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Deadline <span class="text-red-400">*</span></label>
                <input type="datetime-local" name="deadline" value="{{ old('deadline') ? date('Y-m-d\TH:i', strtotime(old('deadline'))) : '' }}" class="inp">
            </div>
            <div>
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Priority</label>
                <select name="priority" class="inp">
                    <option value="low"   {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"  {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>

        {{-- Attachment --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Attachment (photo)</label>
            <input type="file" name="attachment" accept="image/*" class="inp py-2.5">
            <p class="text-[10px] text-[#8B86A5] font-semibold mt-1.5">Optional · JPG/PNG · max 10MB</p>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pb-2">
            <a href="{{ route('academic-task.index') }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Task</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectType(key, btn) {
    document.getElementById('taskType').value = key;
    document.querySelectorAll('.type-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
}

// Sinkron hidden id_assignment berdasarkan child yang dipilih
(function syncAssignment() {
    const select = document.getElementById('idAnak');
    const hidden = document.getElementById('idAssignment');
    if (!select || !hidden) return;
    function update() {
        const opt = select.options[select.selectedIndex];
        hidden.value = opt ? (opt.dataset.assignment || '') : '';
    }
    update();
    select.addEventListener('change', update);
})();
</script>
@endpush
