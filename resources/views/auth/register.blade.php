<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar - Aplikasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: {
                            DEFAULT: '#7B1E5A',
                            light: '#9B2E72',
                            pale: '#FFF9FB',
                            soft: '#F5E6EF',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 50%, #e8d5ee 100%);
            }
            .phone-frame {
                width: 390px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(123,30,90,0.25), 0 0 0 8px #1a0d14, 0 0 0 10px #2d1020;
                overflow: hidden;
                position: relative;
            }
        }

        .input-field:focus {
            outline: none;
            border-color: #7B1E5A;
            box-shadow: 0 0 0 3px rgba(123,30,90,0.12);
        }

        .btn-primary:active { transform: scale(0.97); }

        /* Password strength bar */
        .strength-bar {
            height: 3px;
            border-radius: 99px;
            transition: width 0.4s ease, background-color 0.4s ease;
        }

        /* Validation hint items */
        .hint-item { transition: color 0.2s ease; }
        .hint-item.valid { color: #16a34a; }
        .hint-item.invalid { color: #dc2626; }
        .hint-item.neutral { color: #9ca3af; }

        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .logo-shimmer {
            background: linear-gradient(90deg, #7B1E5A, #c45e9a, #7B1E5A);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slideUp 0.45s ease forwards; }
        .delay-1 { animation-delay: 0.05s; opacity: 0; }
        .delay-2 { animation-delay: 0.12s; opacity: 0; }
        .delay-3 { animation-delay: 0.19s; opacity: 0; }
        .delay-4 { animation-delay: 0.26s; opacity: 0; }
        .delay-5 { animation-delay: 0.33s; opacity: 0; }
        .delay-6 { animation-delay: 0.40s; opacity: 0; }

        #toast {
            transition: all 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }
        #toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        #successBanner {
            transition: all 0.4s ease;
            transform: translateY(-100%);
            opacity: 0;
        }
        #successBanner.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-plum-pale">

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale">

    <!-- Status Bar -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1">
        <span class="text-xs font-semibold text-gray-700">9:41</span>
        <div class="flex gap-1 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="#374151"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="#374151"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center gap-0.5">
                <div class="w-6 h-3 border border-gray-700 rounded-sm p-px flex items-stretch"><div class="bg-gray-700 rounded-xs flex-1"></div></div>
            </div>
        </div>
    </div>

    <!-- Toast Error -->
    <div id="toast" class="fixed sm:absolute top-0 left-0 right-0 z-50 mx-auto max-w-sm">
        <div id="toastInner" class="mx-4 mt-2 bg-red-500 text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span id="toast-message"></span>
        </div>
    </div>

    <!-- Success Banner -->
    <div id="successBanner" class="fixed sm:absolute top-0 left-0 right-0 z-50 mx-auto max-w-sm">
        <div class="mx-4 mt-2 bg-green-500 text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span id="successMessage"></span>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto px-6 pt-6 pb-12" style="max-height: calc(100vh - 48px)">

        <!-- Header -->
        <div class="animate-slide-up delay-1 flex flex-col items-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-plum to-plum-light flex items-center justify-center shadow-lg shadow-plum/30 mb-3">
                <span class="text-3xl">🌈</span>
            </div>
            <h1 class="text-2xl font-extrabold logo-shimmer">Daftar Akun Baru</h1>
            <p class="text-sm text-gray-400 mt-1">Bergabung sekarang, gratis!</p>
        </div>

        <form id="registerForm" class="flex flex-col gap-4" novalidate>
            @csrf

            <!-- Nama Lengkap -->
            <div class="animate-slide-up delay-2">
                <label class="block text-xs font-semibold text-plum mb-1.5 ml-1">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Nama kamu"
                        autocomplete="name"
                        class="input-field w-full bg-white border-2 border-plum-soft rounded-2xl pl-11 pr-4 py-4 text-sm font-medium text-gray-800 placeholder-gray-300 transition-all duration-200"
                    />
                </div>
            </div>

            <!-- Email -->
            <div class="animate-slide-up delay-2">
                <label class="block text-xs font-semibold text-plum mb-1.5 ml-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="contoh@email.com"
                        autocomplete="email"
                        class="input-field w-full bg-white border-2 border-plum-soft rounded-2xl pl-11 pr-4 py-4 text-sm font-medium text-gray-800 placeholder-gray-300 transition-all duration-200"
                    />
                </div>
            </div>

            <!-- Password -->
            <div class="animate-slide-up delay-3">
                <label class="block text-xs font-semibold text-plum mb-1.5 ml-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        class="input-field w-full bg-white border-2 border-plum-soft rounded-2xl pl-11 pr-12 py-4 text-sm font-medium text-gray-800 placeholder-gray-300 transition-all duration-200"
                    />
                    <button type="button" class="eye-toggle absolute inset-y-0 right-4 flex items-center text-plum/60 hover:text-plum transition-colors" data-target="password">
                        <svg class="eye-icon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-off-icon w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>

                <!-- Password strength hints -->
                <div id="passwordHints" class="mt-2 pl-1 hidden">
                    <!-- Strength bar -->
                    <div class="flex gap-1 mb-2">
                        <div class="flex-1 h-1 rounded-full bg-plum-soft overflow-hidden"><div id="bar1" class="strength-bar w-0 bg-red-400"></div></div>
                        <div class="flex-1 h-1 rounded-full bg-plum-soft overflow-hidden"><div id="bar2" class="strength-bar w-0 bg-yellow-400"></div></div>
                        <div class="flex-1 h-1 rounded-full bg-plum-soft overflow-hidden"><div id="bar3" class="strength-bar w-0 bg-green-400"></div></div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-0.5">
                        <p class="hint-item neutral text-xs flex items-center gap-1" id="hint-min">
                            <span class="hint-dot">●</span> Min. 8 karakter
                        </p>
                        <p class="hint-item neutral text-xs flex items-center gap-1" id="hint-cap">
                            <span class="hint-dot">●</span> Huruf kapital
                        </p>
                        <p class="hint-item neutral text-xs flex items-center gap-1" id="hint-num">
                            <span class="hint-dot">●</span> Mengandung angka
                        </p>
                    </div>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="animate-slide-up delay-4">
                <label class="block text-xs font-semibold text-plum mb-1.5 ml-1">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <input
                        type="password"
                        id="confirm"
                        name="password_confirmation"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        class="input-field w-full bg-white border-2 border-plum-soft rounded-2xl pl-11 pr-12 py-4 text-sm font-medium text-gray-800 placeholder-gray-300 transition-all duration-200"
                    />
                    <button type="button" class="eye-toggle absolute inset-y-0 right-4 flex items-center text-plum/60 hover:text-plum transition-colors" data-target="confirm">
                        <svg class="eye-icon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-off-icon w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <p id="confirmMatch" class="text-xs mt-1.5 ml-1 hidden"></p>
            </div>

            <!-- Submit Button -->
            <div class="animate-slide-up delay-5 mt-2">
                <button
                    type="submit"
                    id="submitBtn"
                    class="btn-primary w-full bg-gradient-to-r from-plum to-plum-light text-white font-bold py-4 rounded-2xl shadow-lg shadow-plum/40 transition-all duration-200 flex items-center justify-center gap-2 text-sm"
                >
                    <span id="btnText">Daftar Sekarang</span>
                    <svg id="btnArrow" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-plum-soft"></div>
            <span class="text-xs text-gray-400 font-medium">atau</span>
            <div class="flex-1 h-px bg-plum-soft"></div>
        </div>

        <!-- Login Link -->
        <p class="animate-slide-up delay-6 text-center text-sm text-gray-500 pb-4">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold text-plum hover:text-plum-light transition-colors ml-1">
                Masuk di sini
            </a>
        </p>
    </div>

</div>
</div>

<script>
// ─── Toggle Password ───────────────────────────────────────────────────────────
document.querySelectorAll('.eye-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const input    = document.getElementById(targetId);
        const isPass   = input.type === 'password';
        input.type     = isPass ? 'text' : 'password';
        btn.querySelector('.eye-icon').classList.toggle('hidden', isPass);
        btn.querySelector('.eye-off-icon').classList.toggle('hidden', !isPass);
    });
});

// ─── Password Strength & Hints ────────────────────────────────────────────────
const passwordEl = document.getElementById('password');
const hintsBox   = document.getElementById('passwordHints');
const hintMin    = document.getElementById('hint-min');
const hintCap    = document.getElementById('hint-cap');
const hintNum    = document.getElementById('hint-num');

function setHint(el, valid) {
    el.classList.remove('valid', 'invalid', 'neutral');
    el.classList.add(valid ? 'valid' : 'invalid');
}

passwordEl.addEventListener('input', () => {
    const val = passwordEl.value;
    if (!val) { hintsBox.classList.add('hidden'); return; }
    hintsBox.classList.remove('hidden');

    const hasMin = val.length >= 8;
    const hasCap = /[A-Z]/.test(val);
    const hasNum = /[0-9]/.test(val);
    const score  = [hasMin, hasCap, hasNum].filter(Boolean).length;

    setHint(hintMin, hasMin);
    setHint(hintCap, hasCap);
    setHint(hintNum, hasNum);

    // Strength bars
    ['bar1', 'bar2', 'bar3'].forEach((id, i) => {
        const bar = document.getElementById(id);
        bar.style.width = score > i ? '100%' : '0%';
    });

    checkConfirmMatch();
});

// ─── Confirm Match ────────────────────────────────────────────────────────────
const confirmEl    = document.getElementById('confirm');
const confirmMatch = document.getElementById('confirmMatch');

function checkConfirmMatch() {
    const pass = passwordEl.value;
    const conf = confirmEl.value;
    if (!conf) { confirmMatch.classList.add('hidden'); return; }
    confirmMatch.classList.remove('hidden');
    if (pass === conf) {
        confirmMatch.textContent = '✓ Password cocok';
        confirmMatch.className = 'text-xs mt-1.5 ml-1 text-green-600 font-semibold';
    } else {
        confirmMatch.textContent = '✗ Password tidak cocok';
        confirmMatch.className = 'text-xs mt-1.5 ml-1 text-red-500 font-semibold';
    }
}

confirmEl.addEventListener('input', checkConfirmMatch);

// ─── Toast ────────────────────────────────────────────────────────────────────
function showToast(message, type = 'error') {
    const toast  = document.getElementById('toast');
    const inner  = document.getElementById('toastInner');
    document.getElementById('toast-message').textContent = message;
    inner.className = `mx-4 mt-2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// ─── Loading ──────────────────────────────────────────────────────────────────
function setLoading(loading) {
    const btn    = document.getElementById('submitBtn');
    const text   = document.getElementById('btnText');
    const arrow  = document.getElementById('btnArrow');
    const spin   = document.getElementById('btnSpinner');
    btn.disabled = loading;
    text.textContent = loading ? 'Mendaftar...' : 'Daftar Sekarang';
    arrow.classList.toggle('hidden', loading);
    spin.classList.toggle('hidden', !loading);
    btn.classList.toggle('opacity-75', loading);
}

// ─── Validation Helpers ───────────────────────────────────────────────────────
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ─── Form Submit ──────────────────────────────────────────────────────────────
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const name     = document.getElementById('name').value.trim();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm  = document.getElementById('confirm').value;

    // Client-side validation (matches React Native logic)
    if (!name || !email || !password || !confirm) {
        return showToast('Semua field wajib diisi!');
    }
    if (!validateEmail(email)) {
        return showToast('Email tidak valid!');
    }
    if (password.length < 8) {
        return showToast('Password minimal 8 karakter!');
    }
    if (!/[A-Z]/.test(password)) {
        return showToast('Password harus mengandung minimal 1 huruf kapital!');
    }
    if (!/[0-9]/.test(password)) {
        return showToast('Password harus mengandung minimal 1 angka!');
    }
    if (password !== confirm) {
        return showToast('Konfirmasi password tidak cocok!');
    }

    setLoading(true);

    try {
        const response = await fetch('{{ route("register.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name,
                email,
                password,
                password_confirmation: confirm,
            }),
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message || 'Registrasi berhasil!', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1800);
        } else {
            // Handle validation errors dari Laravel
            const errors = data.errors;
            if (errors) {
                const firstError = Object.values(errors)[0];
                showToast(Array.isArray(firstError) ? firstError[0] : firstError);
            } else {
                showToast(data.message || 'Gagal registrasi.');
            }
        }
    } catch (err) {
        showToast('Terjadi kesalahan. Coba lagi.');
    } finally {
        setLoading(false);
    }
});
</script>
</body>
</html>