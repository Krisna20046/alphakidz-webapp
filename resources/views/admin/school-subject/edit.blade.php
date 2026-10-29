@extends('layouts.app')

@section('title', 'Edit School Subject')

@php
    $s = $subject;
    $color = $s['color'] ?? '#8B46D3';
@endphp

@push('styles')
<style>
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
    .inp.err   { border-color:#F44336; }

    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.96); }

    .overlay {
        position:absolute; inset:0; background:rgba(255,255,255,0.85);
        display:flex; flex-direction:column; align-items:center;
        justify-content:center; z-index:20; border-radius:44px;
    }
    @keyframes spin { to{transform:rotate(360deg);} }
    .spinner { width:38px;height:38px;border-radius:50%;border:4px solid #EDE9FE;border-top-color:#8B46D3;animation:spin .8s linear infinite; }

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
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('admin-school-subject') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Edit School Subject</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Update school subject data</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    <div id="loadingOverlay" class="overlay hidden" style="border-radius:0;">
        <div class="spinner mb-3"></div>
        <p class="text-sm font-bold text-[#8B46D3]">Saving...</p>
    </div>

    @if(session('error'))
    <div class="p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <form id="mainForm" action="{{ route('admin-school-subject.update', $s['id']) }}" method="POST"
          class="space-y-4 anim delay-2" onsubmit="handleSubmit(event)">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-lg mb-4">Subject Information</p>

            <div>
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">
                    Subject Name <span class="text-red-400">*</span>
                </label>
                <input type="text" name="name" id="fName"
                       value="{{ old('name', $s['name']) }}" placeholder="Example: Mathematics"
                       class="inp {{ $errors->has('name') ? 'err' : '' }}"
                       oninput="clearErr('name','fName'); updatePreview();">
                <p id="err-name" class="text-red-500 text-xs mt-1 {{ $errors->has('name') ? '' : 'hidden' }}">
                    {{ $errors->first('name') }}
                </p>
            </div>
        </div>

        {{-- Ikon --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-lg mb-1">Icon</p>
            <p class="text-[#8B86A5] text-xs mb-4">Choose an icon for the subject (optional)</p>
            <input type="hidden" name="icon" id="iconInput" value="{{ old('icon', $s['icon'] ?? '') }}">

            @php
                $currentIcon = old('icon', $s['icon'] ?? '');
                $icons = [
                    'book-outline','calculator-outline','color-palette-outline','musical-notes-outline',
                    'basketball-outline','flask-outline','globe-outline','language-outline',
                    'desktop-outline','ribbon-outline',
                ];
            @endphp

            <div class="flex gap-3 overflow-x-auto pb-1 no-scrollbar">
                <div class="flex gap-3">
                @foreach($icons as $iconName)
                    <button type="button"
                            onclick="selectIcon('{{ $iconName }}', this)"
                            class="icon-opt {{ $currentIcon === $iconName ? 'sel' : '' }}"
                            title="{{ $iconName }}">
                        <ion-icon name="{{ $iconName }}" style="font-size:24px;"></ion-icon>
                    </button>
                @endforeach
                </div>
            </div>
            <p class="text-[#8B46D3] text-xs mt-2 font-semibold">Saved icon: <span id="iconLabel">{{ $currentIcon ?: '-' }}</span></p>
        </div>

        {{-- Warna --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-lg mb-1">Color</p>
            <p class="text-[#8B86A5] text-xs mb-4">Choose an accent color for the subject (optional)</p>
            <input type="hidden" name="color" id="colorInput" value="{{ old('color', $color) }}">

            @php
                $colors = ['#8B46D3','#EC4899','#F59E0B','#22C55E','#3B82F6','#EF4444','#14B8A6','#6366F1'];
            @endphp

            <div class="flex gap-3 flex-wrap">
                @foreach($colors as $c)
                    <button type="button"
                            onclick="selectColor('{{ $c }}', this)"
                            class="color-opt {{ $color === $c ? 'sel' : '' }}"
                            style="background:{{ $c }};"></button>
                @endforeach
            </div>

            <div class="flex items-center gap-3 mt-5">
                <label class="block text-sm font-bold text-[#1E1B2E] flex-1">Or enter a color code</label>
                <div class="flex items-center gap-2">
                    <input type="color" id="colorCustom"
                           oninput="setCustomColor(this.value)"
                           class="w-9 h-9 rounded-lg border border-[#DDD6EF] bg-transparent cursor-pointer">
                    <input type="text" id="colorHexText" value="{{ old('color', $color) }}"
                           oninput="setCustomHex(this.value)"
                           class="inp w-28" maxlength="7">
                </div>
            </div>
        </div>

        {{-- Preview card --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-lg mb-4">Preview</p>
            <div class="flex items-center gap-3">
                <div id="previewIcon" class="w-12 h-12 rounded-2xl flex items-center justify-center text-white">
                    <ion-icon name="{{ $currentIcon ?: 'book-outline' }}" style="font-size:22px;"></ion-icon>
                </div>
                <div>
                    <p id="previewName" class="text-[#1E1B2E] font-bold text-sm">{{ old('name', $s['name']) }}</p>
                    <p id="previewColor" class="text-[#8B86A5] text-xs mt-0.5">{{ $color }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pb-2">
            <a href="{{ route('admin-school-subject') }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">
                Cancel
            </a>
            <button type="submit" id="submitBtn"
                    class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">
                Save Changes
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function selectIcon(iconName, btn) {
    document.getElementById('iconInput').value = iconName;
    document.querySelectorAll('.icon-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
    document.getElementById('iconLabel').textContent = iconName;
    document.getElementById('previewIcon').innerHTML = `<ion-icon name="${iconName}" style="font-size:22px;"></ion-icon>`;
}

function setColorHex(hex) {
    document.getElementById('colorInput').value = hex;
    document.getElementById('colorHexText').value = hex;
    document.getElementById('previewIcon').style.background = hex;
    document.getElementById('previewColor').textContent = hex;
}

function selectColor(hex, btn) {
    document.querySelectorAll('.color-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
    document.getElementById('colorCustom').value = hex;
    setColorHex(hex);
}

function setCustomColor(hex) {
    document.querySelectorAll('.color-opt').forEach(b => b.classList.remove('sel'));
    document.getElementById('colorCustom').value = hex;
    setColorHex(hex);
}

function setCustomHex(val) {
    let hex = val.trim();
    if (/^#?[0-9A-Fa-f]{6}$/.test(hex)) {
        if (!hex.startsWith('#')) hex = '#' + hex;
        document.querySelectorAll('.color-opt').forEach(b => b.classList.remove('sel'));
        document.getElementById('colorCustom').value = hex;
        setColorHex(hex);
    }
}

function updatePreview() {
    document.getElementById('previewName').textContent = document.getElementById('fName').value.trim() || 'Subject name';
}

function clearErr(errKey, fieldId) {
    document.getElementById('err-' + errKey)?.classList.add('hidden');
    document.getElementById(fieldId)?.classList.remove('err');
}

function handleSubmit(e) {
    let ok = true;
    function fail(errKey, fieldId, msg) {
        const ep = document.getElementById('err-' + errKey);
        const fp = document.getElementById(fieldId);
        if (ep) { ep.textContent = msg; ep.classList.remove('hidden'); }
        if (fp) fp.classList.add('err');
        ok = false;
    }

    const name = document.getElementById('fName').value.trim();
    if (!name) fail('name', 'fName', 'Subject name is required');

    if (!ok) { e.preventDefault(); return; }
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = true;
}

// ── Init ───────────────────────────────────────────────────────────────
(function init() {
    const color = document.getElementById('colorInput').value;
    document.getElementById('previewIcon').style.background = color;
    document.getElementById('colorCustom').value = color;
})();
</script>
@endpush