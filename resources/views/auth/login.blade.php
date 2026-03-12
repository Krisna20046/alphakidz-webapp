<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk - Aplikasi</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Nunito', sans-serif; }

        /* ── Desktop phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex;
                align-items: flex-start;
                justify-content: center;
                min-height: 100vh;
                padding: 32px 0 60px;
                background: #E5E2F5;
            }
            .phone-frame {
                width: 390px;
                min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(124,58,237,0.28), 0 0 0 8px #1a1030, 0 0 0 10px #2d1a50;
                overflow: hidden;
                position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame  { min-height: 100vh; }
        }

        /* ── Marble / fluid purple background ── */
        .hero-bg {
            background-color: #8B46D3;
            background-image: url('/assets/bg-texture-login.png');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-color: #8B46D3;
            opacity: 0.60;
            pointer-events: none;
        }

        /* ── Pill input ── */
        .pill-input {
            background: rgba(237,230,255,0.55);
            border: none;
            border-radius: 50px;
            padding: 16px 20px 16px 52px;
            font-size: 14px;
            font-weight: 600;
            color: #3D1F7A;
            width: 100%;
            outline: none;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .pill-input::placeholder { color: #B39DDB; font-weight: 500; }
        .pill-input:focus {
            background: rgba(237,230,255,0.9);
            box-shadow: 0 0 0 3px rgba(139,70,211,0.20);
        }

        /* ── Pill button ── */
        .btn-signin {
            background: linear-gradient(135deg, #7B2FBE 0%, #9B46D3 100%);
            border-radius: 50px;
            color: #fff;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 0.02em;
            padding: 17px;
            width: 100%;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 8px 24px rgba(123,47,190,0.40);
        }
        .btn-signin:active { transform: scale(0.97); }

        /* ── Google button ── */
        .btn-google {
            background: #F0EFEF;
            border-radius: 50px;
            color: #444;
            font-weight: 700;
            font-size: 15px;
            padding: 16px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.15s;
        }
        .btn-google:active { background: #E4E2E2; }

        /* ── Slide-up animation ── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.45s ease forwards; opacity: 0; }
        .d1 { animation-delay: 0.05s; }
        .d2 { animation-delay: 0.14s; }
        .d3 { animation-delay: 0.23s; }
        .d4 { animation-delay: 0.32s; }
        .d5 { animation-delay: 0.41s; }
        .d6 { animation-delay: 0.50s; }

        /* ── Toast ── */
        #toast {
            transition: all 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }
        #toast.show { transform: translateY(0); opacity: 1; }

        /* ── Checkbox custom ── */
        .custom-checkbox {
            width: 18px; height: 18px;
            border: 2px solid #C4B5D8;
            border-radius: 4px;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        #rememberCheck:checked + .custom-checkbox {
            background: #8B46D3;
            border-color: #8B46D3;
        }
    </style>
</head>
<body class="bg-[#E5E2F5]">

<div class="phone-wrapper">
<div class="phone-frame bg-white">

    <!-- STATUS BAR (desktop) -->
    <div class="hidden sm:flex items-center justify-between hero-bg px-6 pt-[14px] text-white text-xs font-bold relative z-10">
        <span>9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
                <rect x="13.5" y="0" width="3" height="11" rx="0.6" fill="white" opacity="0.25"/>
            </svg>
            <svg width="16" height="12" viewBox="0 0 16 12" fill="white">
                <path d="M8 3C5.5 3 3.3 4 1.7 5.6L0 3.8C2.1 1.7 5 0.5 8 0.5s5.9 1.2 8 3.3L14.3 5.6C12.7 4 10.5 3 8 3z" opacity="0.5"/>
                <path d="M8 6.5c-1.5 0-2.8.6-3.8 1.5L2.5 6.2C3.9 4.8 5.9 4 8 4s4.1.8 5.5 2.2L11.8 8C10.8 7.1 9.5 6.5 8 6.5z" opacity="0.75"/>
                <circle cx="8" cy="10.5" r="2"/>
            </svg>
            <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                <div class="bg-white rounded-[1.5px] h-full"></div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed sm:absolute top-0 left-0 right-0 z-50 mx-auto max-w-sm">
        <div class="mx-4 mt-2 bg-red-500 text-white text-sm font-bold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span id="toast-msg"></span>
        </div>
    </div>

    <!-- ── HERO HEADER ── -->
    <div class="hero-bg relative z-10 px-6 pt-[56px] pb-[90px]">
        <!-- Back button -->
        <div class="anim d1 mb-8">
            <button onclick="history.back()"
                    class="w-10 h-10 rounded-full bg-white/20 border border-white/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <div class="anim d2">
            <h1 class="text-white text-[32px] font-extrabold leading-tight mb-3">Sign In</h1>
            <p class="text-white/80 text-[14px] font-semibold leading-relaxed max-w-[270px]">
                Enter your email and password to securely access your account and manage your services.
            </p>
        </div>
    </div>

    <!-- ── FORM CARD ── -->
    <div class="relative z-20 -mt-[50px] bg-white rounded-t-[40px] px-6 pt-8 pb-10 min-h-[500px]">

        <form id="loginForm" novalidate class="flex flex-col gap-4">
            @csrf

            <!-- Username / Email -->
            <div class="anim d3 relative">
                <div class="absolute left-[18px] top-1/2 -translate-y-1/2 z-10">
                    <svg class="w-[18px] h-[18px] text-[#8B46D3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Username"
                    autocomplete="email"
                    class="pill-input"
                />
            </div>

            <!-- Password -->
            <div class="anim d3 relative">
                <div class="absolute left-[18px] top-1/2 -translate-y-1/2 z-10">
                    <svg class="w-[18px] h-[18px] text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Password"
                    autocomplete="current-password"
                    class="pill-input pr-14"
                />
                <button type="button" id="togglePassword"
                        class="absolute right-[18px] top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#8B46D3] transition-colors">
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                    <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="anim d4 flex items-center justify-between px-1 mt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none" for="rememberCheck">
                    <input type="checkbox" id="rememberCheck" name="remember" class="sr-only peer">
                    <div class="w-[18px] h-[18px] rounded-[4px] border-2 border-[#C4B5D8] peer-checked:bg-[#8B46D3] peer-checked:border-[#8B46D3] flex items-center justify-center transition-all duration-150 flex-shrink-0">
                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-[#6B7280]">Remember Me</span>
                </label>
                <a href="#" class="text-sm font-bold text-[#8B46D3] hover:text-[#7C3AED] transition-colors">
                    Forgot Password
                </a>
            </div>

            <!-- Sign In Button -->
            <div class="anim d5 mt-3">
                <button type="submit" id="submitBtn" class="btn-signin flex items-center justify-center gap-2">
                    <span id="btnText">Sign In</span>
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

        <!-- Sign Up link -->
        <div class="anim d5 mt-5 text-center">
            <p class="text-sm text-[#9CA3AF] font-semibold">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-extrabold text-[#8B46D3] hover:text-[#7C3AED] transition-colors ml-1">
                    Sign Up Here
                </a>
            </p>
        </div>

        <!-- Divider -->
        <div class="anim d6 flex items-center gap-3 my-5">
            <div class="flex-1 h-px bg-[#EDE9FE]"></div>
            <span class="text-xs font-bold text-[#9CA3AF]">Or Continue With Account</span>
            <div class="flex-1 h-px bg-[#EDE9FE]"></div>
        </div>

        <!-- Google Button -->
        <div class="anim d6">
            <button type="button" class="btn-google">
                <!-- Google G logo -->
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.79-.07-1.54-.19-2.27h-11.3v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/>
                    <path fill="#34A853" d="M12.255 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96h-3.98v3.09C3.515 21.3 7.615 24 12.255 24z"/>
                    <path fill="#FBBC05" d="M5.525 14.29c-.25-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29V6.62h-3.98a11.86 11.86 0 000 10.76l3.98-3.09z"/>
                    <path fill="#EA4335" d="M12.255 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C18.205 1.19 15.495 0 12.255 0c-4.64 0-8.74 2.7-10.71 6.62l3.98 3.09c.95-2.85 3.6-4.96 6.73-4.96z"/>
                </svg>
                <span>Google</span>
            </button>
        </div>

    </div>
    <!-- end form card -->

</div>
</div>

<script>
// Toggle password visibility
const toggleBtn  = document.getElementById('togglePassword');
const passwordEl = document.getElementById('password');
const eyeIcon    = document.getElementById('eyeIcon');
const eyeOffIcon = document.getElementById('eyeOffIcon');

toggleBtn.addEventListener('click', () => {
    const isHidden = passwordEl.type === 'password';
    passwordEl.type = isHidden ? 'text' : 'password';
    eyeIcon.classList.toggle('hidden', isHidden);
    eyeOffIcon.classList.toggle('hidden', !isHidden);
});

// Checkbox checked visual update (needed because peer- doesn't update dynamically without JS in some cases)
const chk = document.getElementById('rememberCheck');
chk.addEventListener('change', () => {
    const box = chk.nextElementSibling;
    const tick = box.querySelector('svg');
    if (chk.checked) {
        box.classList.add('bg-[#8B46D3]', 'border-[#8B46D3]');
        tick && tick.classList.remove('hidden');
    } else {
        box.classList.remove('bg-[#8B46D3]', 'border-[#8B46D3]');
        tick && tick.classList.add('hidden');
    }
});

// Toast
function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// Loading state
function setLoading(loading) {
    const btn        = document.getElementById('submitBtn');
    const btnText    = document.getElementById('btnText');
    const btnArrow   = document.getElementById('btnArrow');
    const btnSpinner = document.getElementById('btnSpinner');
    btn.disabled = loading;
    btnText.textContent = loading ? 'Memproses...' : 'Sign In';
    btnArrow.classList.toggle('hidden', loading);
    btnSpinner.classList.toggle('hidden', !loading);
    btn.style.opacity = loading ? '0.75' : '1';
}

// Form submit
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!email || !password) { showToast('Email dan password wajib diisi!'); return; }
    setLoading(true);

    try {
        const res  = await fetch('{{ route("login.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password }),
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect || '{{ route("dashboard") }}';
        } else {
            showToast(data.message || 'Email atau password salah.');
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