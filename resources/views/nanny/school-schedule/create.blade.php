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

    .icon-opt {
        width:46px; height:46px; border-radius:12px; background:#F0EDFB;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
        cursor:pointer; border:2px solid transparent; transition:all .15s;
        color:#8B46D3;
    }
    .icon-opt.sel { background:#8B46D3; color:#fff; border-color:#6D28D9; }
    .icon-opt:active { transform:scale(.92); }
    .color-opt {
        width:38px; height:38px; border-radius:50%; cursor:pointer;
        border:3px solid transparent; transition:all .15s; flex-shrink:0;
    }
    .color-opt.sel { border-color:#1E1B2E; transform:scale(1.08); }
    .color-opt:active { transform:scale(.92); }
    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@php
    $subjectIcons = [
        'book-outline'    => 'Book',
        'calculator-outline' => 'Calculator',
        'color-palette-outline' => 'Art',
        'musical-notes-outline' => 'Music',
        'basketball-outline' => 'Sports',
        'flask-outline'   => 'Science',
        'globe-outline'   => 'Geography',
        'language-outline'  => 'Language',
        'desktop-outline' => 'ICT',
        'ribbon-outline'  => 'Religion',
    ];
    $subjectColors = ['#8B46D3','#EC4899','#F59E0B','#22C55E','#3B82F6','#EF4444','#14B8A6','#6366F1'];
@endphp

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
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-bold text-[#1E1B2E]">Subject <span class="text-red-400">*</span></label>
                <button type="button" onclick="openSubjectModal()"
                    class="flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#EDE9FE] text-[#8B46D3] text-xs font-bold">
                    <ion-icon name="add" style="font-size:14px;"></ion-icon>
                    New
                </button>
            </div>
            @if(count($subjects) === 0)
                <button type="button" onclick="openSubjectModal()" class="text-sm font-semibold text-[#F59E0B] underline">
                    No subjects yet. Click here to add one.
                </button>
            @else
            <select name="subject_id" id="subjectSelect" class="inp" required>
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

@push('modals')
{{-- NEW SUBJECT MODAL --}}
<div id="subjectModal" class="modal-backdrop">
    <div class="modal-sheet">
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1.5 rounded-full bg-gray-200"></div></div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EDE9FE]">
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">New Subject</h2>
                <button onclick="closeSubjectModal()" class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </button>
            </div>
        </div>
        <div class="scrollable-content">
            <form id="newSubjectForm" onsubmit="submitNewSubject(event)" class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Subject Name <span class="text-red-400">*</span></label>
                    <input type="text" id="nsName" placeholder="Example: Mathematics" class="inp">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-1">Icon</label>
                    <p class="text-[#8B86A5] text-xs mb-2">Optional</p>
                    <input type="hidden" id="nsIcon">
                    <div class="flex gap-3 overflow-x-auto pb-1 no-scrollbar">
                        <div class="flex gap-3">
                        @foreach($subjectIcons as $iconName => $iconLabel)
                            <button type="button" onclick="selectNsIcon('{{ $iconName }}', this)"
                                    class="icon-opt" title="{{ $iconLabel }}">
                                <ion-icon name="{{ $iconName }}" style="font-size:24px;"></ion-icon>
                            </button>
                        @endforeach
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#1E1B2E] mb-1">Color</label>
                    <p class="text-[#8B86A5] text-xs mb-2">Optional</p>
                    <input type="hidden" id="nsColor" value="#8B46D3">
                    <div class="flex gap-2 flex-wrap">
                        @foreach($subjectColors as $c)
                            <button type="button" onclick="selectNsColor('{{ $c }}', this)"
                                    class="color-opt {{ $c === '#8B46D3' ? 'sel' : '' }}" style="background:{{ $c }};"></button>
                        @endforeach
                    </div>
                </div>
                <p id="nsErr" class="hidden text-red-500 text-xs font-bold"></p>
                <div class="flex gap-3 pb-2 pt-1">
                    <button type="button" onclick="closeSubjectModal()"
                            class="act-btn flex-1 py-3.5 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold">Cancel</button>
                    <button type="submit" id="nsSubmitBtn"
                            class="act-btn flex-1 py-3.5 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

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

// ── New Subject modal ──────────────────────────────────────────────────
function openSubjectModal() {
    document.getElementById('nsName').value = '';
    document.getElementById('nsIcon').value = '';
    document.getElementById('nsErr').classList.add('hidden');
    document.getElementById('subjectModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function closeSubjectModal() {
    document.getElementById('subjectModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
document.getElementById('subjectModal').addEventListener('click', function(e) {
    if (e.target === this) closeSubjectModal();
});

function selectNsIcon(iconName, btn) {
    document.getElementById('nsIcon').value = iconName;
    document.querySelectorAll('.icon-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
}
function selectNsColor(hex, btn) {
    document.getElementById('nsColor').value = hex;
    document.querySelectorAll('.color-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
}

async function submitNewSubject(e) {
    e.preventDefault();
    const name = document.getElementById('nsName').value.trim();
    const errEl = document.getElementById('nsErr');
    if (!name) {
        errEl.textContent = 'Subject name is required';
        errEl.classList.remove('hidden');
        return;
    }
    errEl.classList.add('hidden');
    const btn = document.getElementById('nsSubmitBtn');
    btn.disabled = true;

    const form = document.getElementById('newSubjectForm');
    const body = new FormData(form);
    body.append('_token', '{{ csrf_token() }}');
    body.set('name', name);
    body.set('icon', document.getElementById('nsIcon').value);
    body.set('color', document.getElementById('nsColor').value);

    try {
        const res = await fetch('{{ route('admin-school-subject.store') }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: body,
        });
        // The proxy store endpoint always returns a redirect (HTML), never JSON —
        // so the only safe success signal is a non-network failure. Reload to show
        // the freshly-created subject in the select.
        if (res.ok || res.status < 500) {
            window.location.reload();
            return;
        }
        throw new Error('Failed to save subject.');
    } catch (err) {
        document.getElementById('nsErr').textContent = err.message;
        document.getElementById('nsErr').classList.remove('hidden');
    } finally {
        btn.disabled = false;
    }
}
</script>
@endpush
