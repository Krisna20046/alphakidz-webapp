{{-- resources/views/admin/kelola-akun/create.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tambah Akun</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }
        @media (min-width:640px) {
            .phone-wrapper { display:flex;align-items:flex-start;justify-content:center;min-height:100vh;padding:32px 0;background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%); }
            .phone-frame   { width:420px;min-height:844px;border-radius:44px;box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020;overflow:hidden;position:relative; }
        }
        @media (max-width:639px) { .phone-wrapper{min-height:100vh;} .phone-frame{min-height:100vh;} }
        .header-bg   { background: linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%); }
        .header-wave { border-radius: 0 0 30px 30px; }
        @keyframes slideUp { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        .anim-up { animation:slideUp .35s ease forwards; }
        .delay-1 { animation-delay:.05s;opacity:0; }
        .delay-2 { animation-delay:.12s;opacity:0; }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none;scrollbar-width:none; }

        /* Input */
        .inp {
            width:100%; background:#FFF9FB; border:1px solid #F0E6F5;
            border-radius:12px; padding:12px 16px; font-size:14px;
            color:#4A0E35; outline:none; transition:border-color .2s;
            font-family:'Plus Jakarta Sans',sans-serif;
        }
        .inp:focus { border-color:#7B1E5A; }
        .inp.err   { border-color:#F44336; }

        /* Role radio row — identical to RN roleOption */
        .role-row {
            display:flex; align-items:center; padding:16px;
            border-radius:12px; background:#F8F0F5;
            border:2px solid transparent; cursor:pointer;
            transition: background .15s, border-color .15s;
        }
        .role-row.sel { background:#F3E5F5; border-color:#7B1E5A; }
        .role-ring {
            width:20px; height:20px; border-radius:50%;
            border:2px solid #7B1E5A; margin-right:12px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
        }
        .role-dot {
            width:10px; height:10px; border-radius:50%;
            background:#7B1E5A; display:none;
        }
        .role-row.sel .role-dot { display:block; }
        .role-name { font-size:15px; color:#4A0E35; font-weight:500; }
        .role-row.sel .role-name { font-weight:600; color:#7B1E5A; }

        .act-btn { transition:transform .1s ease; }
        .act-btn:active { transform:scale(0.96); }

        /* Loading overlay */
        .overlay {
            position:absolute; inset:0; background:rgba(255,255,255,0.85);
            display:flex; flex-direction:column; align-items:center;
            justify-content:center; z-index:20; border-radius:44px;
        }
        @keyframes spin { to{transform:rotate(360deg);} }
        .spinner { width:38px;height:38px;border-radius:50%;border:4px solid #F3E6FA;border-top-color:#7B1E5A;animation:spin .8s linear infinite; }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col relative">

    {{-- Loading overlay --}}
    <div id="loadingOverlay" class="overlay hidden">
        <div class="spinner mb-3"></div>
        <p class="text-sm font-semibold text-plum">Sedang membuat akun...</p>
    </div>

    {{-- Status bar --}}
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    {{-- Header --}}
    <div class="header-bg header-wave px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10"></div>
        <div class="relative flex items-center gap-3 anim-up delay-1">
            <a href="{{ route('admin-kelola-akun') }}"
               class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
            </a>
            <h1 class="text-white text-xl font-extrabold">Tambah Akun</h1>
        </div>
    </div>

    {{-- Server error flash --}}
    @if(session('error'))
    <div class="mx-4 mt-4 p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-semibold">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Form --}}
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 pt-5 pb-10">
        <form id="mainForm" action="{{ route('admin-kelola-akun.store') }}" method="POST"
              class="space-y-4 anim-up delay-2" onsubmit="handleSubmit(event)">
            @csrf

            {{-- ── Informasi Akun ── --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid rgba(243,230,250,.6);">
                <p class="text-plum-dark font-bold text-lg mb-4">Informasi Akun</p>

                {{-- Nama --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-plum-dark mb-2">
                        Nama Lengkap <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" id="fName"
                           value="{{ old('name') }}" placeholder="Masukkan nama lengkap"
                           class="inp {{ $errors->has('name') ? 'err' : '' }}"
                           oninput="clearErr('name','fName')">
                    <p id="err-name" class="text-red-500 text-xs mt-1 {{ $errors->has('name') ? '' : 'hidden' }}">
                        {{ $errors->first('name') }}
                    </p>
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-plum-dark mb-2">
                        Email <span class="text-red-400">*</span>
                    </label>
                    <input type="email" name="email" id="fEmail"
                           value="{{ old('email') }}" placeholder="Masukkan email"
                           class="inp {{ $errors->has('email') ? 'err' : '' }}"
                           oninput="clearErr('email','fEmail')">
                    <p id="err-email" class="text-red-500 text-xs mt-1 {{ $errors->has('email') ? '' : 'hidden' }}">
                        {{ $errors->first('email') }}
                    </p>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-plum-dark mb-2">
                        Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="fPassword"
                               placeholder="Masukkan password"
                               class="inp pr-12 {{ $errors->has('password') ? 'err' : '' }}"
                               oninput="clearErr('password','fPassword')">
                        <button type="button" onclick="togglePwd('fPassword','eyeIcon1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2">
                            <ion-icon id="eyeIcon1" name="eye-off-outline"
                                      style="font-size:18px;color:#7B1E5A;"></ion-icon>
                        </button>
                    </div>
                    <p id="err-password" class="text-red-500 text-xs mt-1 {{ $errors->has('password') ? '' : 'hidden' }}">
                        {{ $errors->first('password') }}
                    </p>
                    <p class="text-plum text-xs mt-1.5 italic">Password minimal 6 karakter</p>
                </div>
            </div>

            {{-- ── Peran Pengguna ── --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid rgba(243,230,250,.6);">
                <p class="text-plum-dark font-bold text-lg mb-4">
                    Peran Pengguna <span class="text-red-400">*</span>
                </p>
                <input type="hidden" name="id_role" id="idRoleInput" value="{{ old('id_role', 4) }}">

                <div class="space-y-3">
                    @foreach([1=>'Admin', 2=>'Majikan', 3=>'Nanny', 4=>'Konsultan'] as $rId => $rLabel)
                    <button type="button" id="role-btn-{{ $rId }}"
                            onclick="selectRole({{ $rId }})"
                            class="role-row w-full {{ (int)old('id_role', 4) === $rId ? 'sel' : '' }}">
                        <div class="role-ring"><div class="role-dot"></div></div>
                        <span class="role-name">{{ $rLabel }}</span>
                    </button>
                    @endforeach
                </div>
                <p id="err-id_role" class="text-red-500 text-xs mt-2 {{ $errors->has('id_role') ? '' : 'hidden' }}">
                    {{ $errors->first('id_role', 'Role harus dipilih') }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 pb-2">
                <a href="{{ route('admin-kelola-akun') }}"
                   class="act-btn flex-1 py-4 rounded-2xl bg-plum-soft text-plum text-sm font-bold text-center">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                        class="act-btn flex-1 py-4 rounded-2xl bg-plum text-white text-sm font-bold shadow-lg shadow-plum/30">
                    Buat Akun
                </button>
            </div>

        </form>
    </div>

</div>
</div>

<script>
// Clock
(function() {
    const el = document.getElementById('statusTime');
    function tick() {
        if (!el) return;
        const n = new Date();
        el.textContent = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0');
    }
    tick(); setInterval(tick, 30000);
})();

// Toggle password visibility
function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    if (f.type === 'password') { f.type = 'text';     i.setAttribute('name','eye-outline'); }
    else                       { f.type = 'password'; i.setAttribute('name','eye-off-outline'); }
}

// Select role
function selectRole(id) {
    document.getElementById('idRoleInput').value = id;
    document.querySelectorAll('.role-row').forEach(b => b.classList.remove('sel'));
    document.getElementById('role-btn-' + id).classList.add('sel');
    document.getElementById('err-id_role').classList.add('hidden');
}

// Clear inline error
function clearErr(errKey, fieldId) {
    document.getElementById('err-' + errKey)?.classList.add('hidden');
    document.getElementById(fieldId)?.classList.remove('err');
}

// Client-side validation
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

    if (!name)                              fail('name',     'fName',     'Nama harus diisi');
    if (!email)                             fail('email',    'fEmail',    'Email harus diisi');
    else if (!/\S+@\S+\.\S+/.test(email))  fail('email',    'fEmail',    'Format email tidak valid');
    if (!password)                          fail('password', 'fPassword', 'Password harus diisi');
    else if (password.length < 6)          fail('password', 'fPassword', 'Password minimal 6 karakter');
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
@include('partials.auth-guard')
</body>
</html>