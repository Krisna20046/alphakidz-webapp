@extends('layouts.app')

@section('title', 'Edit Task')

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
    .modal-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.55);
        display:flex; align-items:flex-end; justify-content:center;
        z-index:50; opacity:0; pointer-events:none; transition:opacity .25s ease;
    }
    .modal-backdrop.open { opacity:1; pointer-events:all; }
    .modal-sheet {
        background:#fff; width:100%; max-width:390px;
        border-radius:24px 24px 0 0; transform:translateY(100%);
        transition:transform .3s cubic-bezier(.4,0,.2,1);
        display:flex; flex-direction:column; max-height:85vh;
    }
    .modal-backdrop.open .modal-sheet { transform:translateY(0); }
    .handle-container { flex-shrink:0; background:white; border-radius:24px 24px 0 0; }
    .scrollable-content { overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch; }
    body.modal-open { overflow:hidden; }
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('academic-task.show', $task['id']) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Edit Task</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $task['title'] }}</p>
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

    <form action="{{ route('academic-task.update', $task['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-4 anim delay-2">
        @csrf

        {{-- Child (readonly: assignment tidak diubah) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child <span class="text-red-400">*</span></label>
            <select name="id_anak" id="idAnak" class="inp" required>
                <option value="" disabled>Select a child</option>
                @foreach($children as $c)
                <option value="{{ $c['id'] }}" {{ $task['id_anak'] == $c['id'] ? 'selected' : '' }}>{{ $c['nama'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Subject --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Subject <span class="text-red-400">*</span></label>
            <select name="subject_id" class="inp" required>
                <option value="" disabled>Select a subject</option>
                @foreach($subjects as $s)
                <option value="{{ $s['id'] }}" {{ $task['subject_id'] == $s['id'] ? 'selected' : '' }}>{{ $s['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Title & description --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $task['title']) }}" class="inp" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Description</label>
                <textarea name="description" rows="3" placeholder="Optional" class="inp resize-none">{{ old('description', $task['description']) }}</textarea>
            </div>
        </div>

        {{-- Type & status --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Type <span class="text-red-400">*</span></label>
                <select name="type" class="inp" required>
                    @foreach(['homework' => 'Homework', 'project' => 'Project', 'exam' => 'Exam'] as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $task['type']) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Status</label>
                <select name="status" class="inp">
                    @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled'] as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $task['status']) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Deadline, priority, score --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Deadline</label>
                <input type="datetime-local" name="deadline" class="inp"
                       value="{{ old('deadline', $task['deadline'] ? date('Y-m-d\TH:i', strtotime($task['deadline'])) : '') }}">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Priority</label>
                    <select name="priority" class="inp">
                        @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $key => $label)
                        <option value="{{ $key }}" {{ old('priority', $task['priority'] ?? 'medium') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Score (Nilai)</label>
                    <input type="number" name="score" min="0" max="100" step="0.01" class="inp"
                           value="{{ old('score', $task['score']) }}" placeholder="0-100">
                </div>
            </div>
        </div>

        {{-- Attachment --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Attachment (photo)</label>
            @if(!empty($task['attachment']))
            <img src="{{ $task['attachment'] }}" alt="attachment" class="mb-3 w-full rounded-2xl border border-[#EDE9FE] object-cover max-h-40">
            @endif
            <input type="file" name="attachment" accept="image/*" class="inp py-2.5">
            <p class="text-[10px] text-[#8B86A5] font-semibold mt-1.5">Optional · JPG/PNG · max 10MB · leave empty to keep current</p>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pb-2">
            <a href="{{ route('academic-task.show', $task['id']) }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Changes</button>
        </div>
    </form>

    {{-- Delete --}}
    <form method="POST" action="{{ route('academic-task.destroy', $task['id']) }}" onsubmit="return confirm('Delete this task? This cannot be undone.');" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 font-bold text-sm">
            <ion-icon name="trash-outline" style="font-size:18px;"></ion-icon>
            Delete Task
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush
