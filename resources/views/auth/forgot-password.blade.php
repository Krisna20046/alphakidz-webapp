<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lupa Password - Aplikasi</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        .btn-primary {
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
        .btn-primary:active { transform: scale(0.97); }
        .btn-primary:disabled { opacity: 0.75; cursor: not-allowed; }

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

        /* ── Toast ── */
        #toast {
            transition: all 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }
        #toast.show { transform: translateY(0); opacity: 1; }

        /* ── Success state ── */
        @keyframes popIn {
            0%   { transform: scale(0.7); opacity: 0; }
            70%  { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }
        .pop-in { animation: popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

        /* ── Email icon envelope animation ── */
        @keyframes floatEnvelope {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-6px); }
        }
        .float-envelope { animation: floatEnvelope 2.4s ease-in-out infinite; }
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
        <div id="toast-inner" class="mx-4 mt-2 bg-red-500 text-white text-sm font-bold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
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
            <h1 class="text-white text-[32px] font-extrabold leading-tight mb-3">Forgot Password</h1>
            <p class="text-white/80 text-[14px] font-semibold leading-relaxed max-w-[270px]">
                Enter your registered email and we'll send you a link to reset your password.
            </p>
        </div>
    </div>

    <!-- ── FORM CARD ── -->
    <div class="relative z-20 -mt-[50px] bg-white rounded-t-[40px] px-6 pt-8 pb-10 min-h-[500px]">

        <!-- DEFAULT STATE: Form -->
        <div id="formState">

            <form id="forgotForm" novalidate class="flex flex-col gap-4">
                @csrf

                <!-- Email -->
                <div class="anim d3 relative">
                    <div class="absolute left-[18px] top-1/2 -translate-y-1/2 z-10">
                        <svg class="w-[18px] h-[18px] text-[#8B46D3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Email Address"
                        autocomplete="email"
                        class="pill-input"
                    />
                </div>

                <!-- Send Button -->
                <div class="anim d4 mt-3">
                    <button type="submit" id="submitBtn" class="btn-primary flex items-center justify-center gap-2">
                        <span id="btnText">Send Reset Link</span>
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

            <!-- Back to Sign In -->
            <div class="anim d5 mt-6 text-center">
                <p class="text-sm text-[#9CA3AF] font-semibold">
                    Remember your password?
                    <a href="{{ route('login') }}" class="font-extrabold text-[#8B46D3] hover:text-[#7C3AED] transition-colors ml-1">
                        Sign In
                    </a>
                </p>
            </div>

        </div>
        <!-- end formState -->

        <!-- SUCCESS STATE: Email sent confirmation -->
        <div id="successState" class="hidden flex flex-col items-center justify-center py-6 px-2">
            <div class="pop-in mb-6">
                <!-- Envelope illustration -->
                <div class="float-envelope w-24 h-24 rounded-[28px] bg-gradient-to-br from-[#EDE9FE] to-[#DDD6FE] flex items-center justify-center shadow-lg shadow-purple-200/60">
                    <svg class="w-12 h-12 text-[#8B46D3]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-[#3D1F7A] text-[22px] font-extrabold text-center mb-2 leading-tight">Check Your Email!</h2>
            <p class="text-[#6B7280] text-sm font-semibold text-center leading-relaxed max-w-[260px] mb-1">
                We've sent a password reset link to
            </p>
            <p id="sentEmailDisplay" class="text-[#8B46D3] text-sm font-extrabold text-center mb-6 break-all max-w-[260px]"></p>
            <p class="text-[#9CA3AF] text-xs font-semibold text-center max-w-[260px] mb-8">
                Didn't receive it? Check your spam folder or try again.
            </p>

            <!-- Resend button -->
            <button id="resendBtn" onclick="resetToForm()"
                    class="btn-primary flex items-center justify-center gap-2 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span>Resend Email</span>
            </button>

            <a href="{{ route('login') }}" class="text-sm font-bold text-[#8B46D3] hover:text-[#7C3AED] transition-colors">
                ← Back to Sign In
            </a>
        </div>
        <!-- end successState -->

    </div>
    <!-- end form card -->

</div>
</div>

<script>
// Toast
function showToast(msg, type = 'error') {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    document.getElementById('toast-msg').textContent = msg;
    inner.className = `mx-4 mt-2 text-white text-sm font-bold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2 ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
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
    btnText.textContent = loading ? 'Sending...' : 'Send Reset Link';
    btnArrow.classList.toggle('hidden', loading);
    btnSpinner.classList.toggle('hidden', !loading);
}

// Reset back to form (for resend)
function resetToForm() {
    document.getElementById('successState').classList.add('hidden');
    document.getElementById('formState').classList.remove('hidden');
}

// Form submit
document.getElementById('forgotForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value.trim();

    if (!email) { showToast('Email wajib diisi!'); return; }

    // Basic email format check
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) { showToast('Format email tidak valid.'); return; }

    setLoading(true);

    try {
        const res = await fetch('https://alphakidz.valove.id/api/forgot-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email }),
        });

        const data = await res.json();

        if (res.ok && (data.success || data.status === 'success' || data.message)) {
            // Show success state
            document.getElementById('sentEmailDisplay').textContent = email;
            document.getElementById('formState').classList.add('hidden');
            document.getElementById('successState').classList.remove('hidden');
        } else {
            showToast(data.message || 'Email tidak ditemukan. Coba lagi.');
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