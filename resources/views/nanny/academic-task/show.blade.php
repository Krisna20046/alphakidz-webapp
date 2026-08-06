@extends('layouts.app')

@section('title', 'Task Detail')

@push('styles')
<style>
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.96); }
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
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

@php
    $statusMeta = [
        'pending'     => ['label' => 'Pending',     'bg' => 'bg-[#FFF7ED]', 'text' => 'text-[#EA580C]'],
        'in_progress' => ['label' => 'In Progress', 'bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#4F46E5]'],
        'completed'   => ['label' => 'Completed',   'bg' => 'bg-[#F0FDF4]', 'text' => 'text-[#16A34A]'],
        'overdue'     => ['label' => 'Overdue',     'bg' => 'bg-[#FEF2F2]', 'text' => 'text-[#DC2626]'],
        'cancelled'   => ['label' => 'Cancelled',   'bg' => 'bg-[#F3F4F6]', 'text' => 'text-[#6B7280]'],
    ];
    $typeLabels = ['homework' => 'Homework', 'project' => 'Project', 'exam' => 'Exam'];
    $st = $statusMeta[$task['status']] ?? $statusMeta['pending'];
    $childName = $childNames[$task['id_anak']] ?? 'Child';
    $lastProgress = collect($task['progress'] ?? [])->sortByDesc('created_at')->first();
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('academic-task.index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="block text-white text-[17px] font-extrabold tracking-wide truncate">{{ $task['title'] }}</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $childName }}</p>
        </div>
        <a href="{{ route('academic-task.edit', $task['id']) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="create-outline" class="text-white" style="font-size:17px;"></ion-icon>
        </a>
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

    {{-- Rejection banner (Modul 11, Opsi B: task di-reopen oleh majikan) --}}
    @if(!empty($rejection))
    <div class="rounded-2xl p-4 bg-red-50 border border-red-200 flex items-start gap-3 mb-4 anim delay-1">
        <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
            <ion-icon name="alert-circle" style="font-size:20px;color:#DC2626;"></ion-icon>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[#DC2626] text-sm font-extrabold">Tugas Ditolak oleh Majikan</p>
            @if(!empty($rejection['comment']))
            <p class="text-[#7F1D1D] text-xs font-semibold mt-0.5">{{ $rejection['comment'] }}</p>
            @endif
            <p class="text-[#9F1239] text-[10px] font-bold mt-1">
                Perbaiki tugas ini, lalu update progress & tandai selesai kembali.
            </p>
        </div>
    </div>
    @endif

    {{-- Info card --}}
    <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF] anim delay-2">
        <div class="flex items-center justify-between mb-3">
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $st['bg'] }} {{ $st['text'] }}">{{ $st['label'] }}</span>
            <span class="text-xs font-extrabold text-[#F59E0B]">{!! $task['score'] !== null ? '⭐ ' . $task['score'] : '' !!}</span>
        </div>
        <h2 class="text-[#1E1B2E] font-extrabold text-lg mb-1">{{ $task['title'] }}</h2>
        <p class="text-[#8B86A5] text-xs font-semibold mb-3">
            {{ $task['subject']['name'] ?? 'Subject' }} · {{ $typeLabels[$task['type']] ?? $task['type'] }} · {{ ucfirst($task['priority'] ?? 'medium') }}
        </p>
        @if(!empty($task['description']))
        <p class="text-sm text-[#1E1B2E] font-semibold leading-relaxed bg-[#F8F7FF] border border-[#EDE9FE] rounded-2xl p-4">{{ $task['description'] }}</p>
        @endif
        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-[#EDE9FE]">
            <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="calendar-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
            </div>
            <div>
                <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Deadline</p>
                <p class="text-[#1E1B2E] text-sm font-bold">{{ $task['deadline'] ? date('d M Y, H:i', strtotime($task['deadline'])) : 'No deadline' }}</p>
            </div>
        </div>
        @if(!empty($task['attachment']))
        <img src="{{ $task['attachment'] }}" alt="attachment" class="mt-3 w-full rounded-2xl border border-[#EDE9FE] object-cover max-h-56">
        @endif
    </div>

    {{-- Actions --}}
    <div class="grid grid-cols-2 gap-3 anim delay-2 mt-4">
        <button type="button" onclick="openProgress()"
            class="act-btn flex items-center justify-center gap-2 py-3.5 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">
            <ion-icon name="camera-outline" style="font-size:18px;"></ion-icon>
            Update Progress
        </button>
        @if($task['status'] !== 'completed')
        <form method="POST" action="{{ route('academic-task.complete', $task['id']) }}">
            @csrf
            <button type="submit"
                class="act-btn flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-[#22C55E] text-white text-sm font-bold shadow-lg shadow-[#22C55E]/30">
                <ion-icon name="checkmark-done-outline" style="font-size:18px;"></ion-icon>
                Mark Complete
            </button>
        </form>
        @else
        <div class="flex items-center justify-center gap-2 py-3.5 rounded-2xl bg-[#F0FDF4] text-[#16A34A] text-sm font-bold">
            <ion-icon name="checkmark-circle" style="font-size:18px;"></ion-icon>
            Completed
        </div>
        @endif
    </div>

    {{-- Progress --}}
    <div class="anim delay-3 mt-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">Progress</span>
            <span class="text-xs font-extrabold text-[#8B46D3]">{{ $lastProgress['progress_percentage'] ?? 0 }}%</span>
        </div>

        <div class="w-full h-3 bg-[#EDE9FE] rounded-full overflow-hidden mb-4">
            <div class="h-full rounded-full transition-all" style="width:{{ $lastProgress['progress_percentage'] ?? 0 }}%;background:linear-gradient(90deg,#8B46D3,#C084FC);"></div>
        </div>

        @if(empty($task['progress']))
        <p class="text-[#8B86A5] text-xs text-center py-4">No progress recorded yet.</p>
        @else
        <div class="space-y-3">
            @foreach($task['progress'] as $p)
            <div class="bg-white rounded-2xl p-4 border border-[#DDD6EF] flex items-start gap-3">
                @if(!empty($p['photo']))
                <img src="{{ $p['photo'] }}" alt="progress" class="w-16 h-16 rounded-xl object-cover shrink-0 border border-[#EDE9FE]">
                @else
                <div class="w-16 h-16 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="image-outline" style="font-size:22px;color:#C4B5FD;"></ion-icon>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] text-sm font-extrabold">{{ $p['progress_percentage'] }}%</p>
                    @if(!empty($p['note']))
                    <p class="text-[#8B86A5] text-xs font-semibold mt-0.5">{{ $p['note'] }}</p>
                    @endif
                    <p class="text-[10px] text-[#C4B5FD] font-bold mt-1">
                        {{ $p['created_at'] ? date('d M Y, H:i', strtotime($p['created_at'])) : '' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Progress modal --}}
<div id="progressModal" class="modal-backdrop">
    <div class="modal-sheet">
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1.5 rounded-full bg-gray-200"></div></div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EDE9FE]">
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Update Progress</h2>
                <button onclick="closeProgress()" class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </button>
            </div>
        </div>
        <div class="scrollable-content pb-16">
            <form action="{{ route('academic-task.progress', $task['id']) }}" method="POST" enctype="multipart/form-data" class="px-5 py-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Percentage <span class="text-red-400">*</span></label>
                    <input type="number" name="progress_percentage" id="pctInput" min="0" max="100"
                           value="{{ $lastProgress['progress_percentage'] ?? 0 }}" class="inp" required>
                    <div class="mt-2">
                        <input type="range" id="pctRange" min="0" max="100" step="10" value="{{ $lastProgress['progress_percentage'] ?? 0 }}"
                               class="w-full accent-[#8B46D3]" oninput="document.getElementById('pctInput').value=this.value">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Note</label>
                    <textarea name="note" rows="3" placeholder="Optional" class="inp resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Photo</label>
                    <input type="file" name="photo" accept="image/*" class="inp py-2.5">
                    <p class="text-[10px] text-[#8B86A5] font-semibold mt-1.5">Optional · JPG/PNG · max 10MB</p>
                </div>
                <p class="text-[10px] text-[#8B86A5] font-semibold">Reaching 100% will auto-complete this task.</p>
                <div class="flex gap-3 pb-2 pt-1">
                    <button type="button" onclick="closeProgress()"
                            class="act-btn flex-1 py-3.5 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold">Cancel</button>
                    <button type="submit"
                            class="act-btn flex-1 py-3.5 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sync range <-> number input
const pctInput = document.getElementById('pctInput');
const pctRange = document.getElementById('pctRange');
if (pctInput && pctRange) {
    pctInput.addEventListener('input', () => {
        let v = Math.max(0, Math.min(100, parseInt(pctInput.value || '0', 10) || 0));
        pctRange.value = v;
    });
}

function openProgress() {
    document.getElementById('progressModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function closeProgress() {
    document.getElementById('progressModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
document.getElementById('progressModal').addEventListener('click', function(e) {
    if (e.target === this) closeProgress();
});

const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush
