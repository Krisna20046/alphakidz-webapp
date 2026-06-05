@extends('layouts.app')

@section('title', 'Ubah Data Akun')

@php
    $u = $user;
    $roleToId = ['admin'=>1, 'majikan'=>2, 'nanny'=>3, 'konsultan'=>4];
    $currentRoleId = $roleToId[strtolower($u['role'] ?? 'konsultan')] ?? 4;
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

    .role-row {
        display:flex; align-items:center; padding:16px;
        border-radius:12px; background:#F0EDFB;
        border:2px solid transparent; cursor:pointer;
        transition: background .15s, border-color .15s;
    }
    .role-row.sel { background:#EDE9FE; border-color:#8B46D3; }
    .role-ring {
        width:20px; height:20px; border-radius:50%;
        border:2px solid #8B46D3; margin-right:12px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
    }
    .role-dot {
        width:10px; height:10px; border-radius:50%;
        background:#8B46D3; display:none;
    }
    .role-row.sel .role-dot { display:block; }
    .role-name { font-size:15px; color:#1E1B2E; font-weight:600; }
    .role-row.sel .role-name { font-weight:800; color:#8B46D3; }

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

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('admin-kelola-akun') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Ubah Data Akun</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Ubah data akun pengguna</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    <div id="loadingOverlay" class="overlay hidden" style="border-radius:0;">
        <div class="spinner mb-3"></div>
        <p class="text-sm font-bold text-[#8B46D3]">Sedang menyimpan...</p>
    </div>

    @if(session('error'))
    <div class="p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <form id="mainForm" action="{{ route('admin-kelola-akun.update', $u['id_user']) }}" method="POST"
          class="space-y-4 anim delay-2" onsubmit="handleSubmit(event)">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-lg mb-4">Informasi Akun</p>

            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">
                    Nama Lengkap <span class="text-red-400">*</span>
                </label>
                <input type="text" name="name" id="fName"
                       value="{{ old('name', $u['name']) }}"
                       placeholder="Masukkan nama lengkap"
                       class="inp {{ $errors->has('name') ? 'err' : '' }}"
                       oninput="clearErr('name','fName')">
                <p id="err-name" class="text-red-500 text-xs mt-1 {{ $errors->has('name') ? '' : 'hidden' }}">
                    {{ $errors->first('name') }}
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">
                    Email <span class="text-red-400">*</span>
                </label>
                <input type="email" name="email" id="fEmail"
                       value="{{ old('email', $u['email']) }}"
                       placeholder="Masukkan email"
                       class="inp {{ $errors->has('email') ? 'err' : '' }}"
                       oninput="clearErr('email','fEmail')">
                <p id="err-email" class="text-red-500 text-xs mt-1 {{ $errors->has('email') ? '' : 'hidden' }}">
                    {{ $errors->first('email') }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password" id="fPassword"
                           placeholder="Kosongkan jika tidak ingin mengubah password"
                           class="inp pr-12 {{ $errors->has('password') ? 'err' : '' }}"
                           oninput="clearErr('password','fPassword')">
                    <button type="button" onclick="togglePwd('fPassword','eyeIcon1')"
                            class="absolute right-3 top-1/2 -translate-y-1/2">
                        <ion-icon id="eyeIcon1" name="eye-off-outline"
                                  style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                </div>
                <p id="err-password" class="text-red-500 text-xs mt-1 {{ $errors->has('password') ? '' : 'hidden' }}">
                    {{ $errors->first('password') }}
                </p>
                <p class="text-[#8B46D3] text-xs mt-1.5 italic font-medium">
                    Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <p class="text-[#1E1B2E] font-extrabold text-lg mb-4">
                Peran Pengguna <span class="text-red-400">*</span>
            </p>
            <input type="hidden" name="id_role" id="idRoleInput"
                   value="{{ old('id_role', $currentRoleId) }}">

            <div class="space-y-3">
                @foreach([1=>'Admin', 2=>'Majikan', 3=>'Nanny', 4=>'Konsultan'] as $rId => $rLabel)
                <button type="button" id="role-btn-{{ $rId }}"
                        onclick="selectRole({{ $rId }})"
                        class="role-row w-full {{ (int)old('id_role', $currentRoleId) === $rId ? 'sel' : '' }}">
                    <div class="role-ring"><div class="role-dot"></div></div>
                    <span class="role-name">{{ $rLabel }}</span>
                </button>
                @endforeach
            </div>
            <p id="err-id_role" class="text-red-500 text-xs mt-2 {{ $errors->has('id_role') ? '' : 'hidden' }}">
                {{ $errors->first('id_role', 'Role harus dipilih') }}
            </p>
        </div>

        <div class="flex gap-3 pb-2">
            <a href="{{ route('admin-kelola-akun') }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">
                Batal
            </a>
            <button type="submit" id="submitBtn"
                    class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    if (f.type === 'password') { f.type = 'text';     i.setAttribute('name','eye-outline'); }
    else                       { f.type = 'password'; i.setAttribute('name','eye-off-outline'); }
}

function selectRole(id) {
    document.getElementById('idRoleInput').value = id;
    document.querySelectorAll('.role-row').forEach(b => b.classList.remove('sel'));
    document.getElementById('role-btn-' + id).classList.add('sel');
    document.getElementById('err-id_role').classList.add('hidden');
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

    const name     = document.getElementById('fName').value.trim();
    const email    = document.getElementById('fEmail').value.trim();
    const password = document.getElementById('fPassword').value;
    const idRole   = document.getElementById('idRoleInput').value;

    if (!name)                             fail('name',     'fName',     'Nama harus diisi');
    if (!email)                            fail('email',    'fEmail',    'Email harus diisi');
    else if (!/\S+@\S+\.\S+/.test(email)) fail('email',    'fEmail',    'Format email tidak valid');
    if (password && password.length < 6)  fail('password', 'fPassword', 'Password minimal 6 karakter');
    if (!idRole) {
        const ep = document.getElementById('err-id_role');
        if (ep) { ep.textContent = 'Role harus dipilih'; ep.classList.remove('hidden'); }
        ok = false;
    }

    if (!ok) { e.preventDefault(); return; }
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = true;
}
</script>
@endpush
