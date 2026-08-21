@extends('layouts.auth')

@section('title', 'Sign In - App')

@push('styles')
@include('components.styles')
<style>
    /* ── Pill button ── */
    .btn-signin {
        background: linear-gradient(135deg, #7B2FBE 0%, #9B46D3 100%);
        border-radius: 50px; color: #fff; font-weight: 800; font-size: 16px;
        padding: 17px; width: 100%;
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
        transition: background 0.15s, opacity 0.15s;
        position: relative;
    }
    .btn-google:active { background: #E4E2E2; }
    .btn-google:disabled { opacity: 0.7; cursor: not-allowed; }

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
    /* ── Spinner for Google button ── */
    @keyframes spin { to { transform: rotate(360deg); } }
    .google-spinner {
        width: 18px; height: 18px;
        border: 2.5px solid #ccc;
        border-top-color: #8B46D3;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: none;
    }
    .btn-google.loading .google-spinner { display: block; }
    .btn-google.loading .google-logo    { display: none; }
    .btn-google.loading .google-label   { color: #888; }
</style>
@endpush

@section('content')
<!-- HERO HEADER -->
<div class="hero-bg relative z-10 px-6 pt-[56px] pb-[90px]">
    <x-auth-hero
        title="Sign In"
        description="Enter your email and password to securely access your account and manage your services."
    />
</div>

<!-- FORM CARD -->
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
            <a href="{{ route('forgot.password') }}" class="text-sm font-bold text-[#8B46D3] hover:text-[#7C3AED] transition-colors">
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
        <button type="button" id="googleBtn" class="btn-google">
            <div class="google-spinner"></div>
            <svg class="google-logo w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.79-.07-1.54-.19-2.27h-11.3v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/>
                <path fill="#34A853" d="M12.255 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96h-3.98v3.09C3.515 21.3 7.615 24 12.255 24z"/>
                <path fill="#FBBC05" d="M5.525 14.29c-.25-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29V6.62h-3.98a11.86 11.86 0 000 10.76l3.98-3.09z"/>
                <path fill="#EA4335" d="M12.255 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C18.205 1.19 15.495 0 12.255 0c-4.64 0-8.74 2.7-10.71 6.62l3.98 3.09c.95-2.85 3.6-4.96 6.73-4.96z"/>
            </svg>
            <span class="google-label">Google</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
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

// Checkbox checked visual update
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

// Loading state (Sign In button)
function setLoading(loading) {
    const btn        = document.getElementById('submitBtn');
    const btnText    = document.getElementById('btnText');
    const btnArrow   = document.getElementById('btnArrow');
    const btnSpinner = document.getElementById('btnSpinner');
    btn.disabled = loading;
    btnText.textContent = loading ? 'Processing...' : 'Sign In';
    btnArrow.classList.toggle('hidden', loading);
    btnSpinner.classList.toggle('hidden', !loading);
    btn.style.opacity = loading ? '0.75' : '1';
}

// Form submit (email/password)
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!email || !password) { showToast('Email and password are required!'); return; }
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
            showToast(data.message || 'Invalid email or password.');
        }
    } catch (err) {
        showToast('Something went wrong. Please try again.');
    } finally {
        setLoading(false);
    }
});

// Google OAuth Login
document.getElementById('googleBtn').addEventListener('click', async () => {
    const btn = document.getElementById('googleBtn');
    if (btn.disabled) return;

    btn.disabled = true;
    btn.classList.add('loading');

    try {
        const res  = await fetch('https://api.alpha-kidz.com/api/auth/google/redirect', {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
        });

        if (!res.ok) throw new Error('Failed to reach the server.');

        const data = await res.json();

        if (data.status === 'success' && data.url) {
            window.location.href = data.url;
        } else {
            throw new Error('Redirect URL not found.');
        }
    } catch (err) {
        showToast('Google sign-in failed. Please try again.');
        btn.disabled = false;
        btn.classList.remove('loading');
    }
});
</script>

<script>
// ── Show server flash message (force-logout, etc) ──
(function() {
    const flashMsg = @json(session('auth_flash'));
    const flashStorage = sessionStorage.getItem('auth_flash');
    if (flashMsg) {
        showToast(flashMsg);
    } else if (flashStorage) {
        showToast(flashStorage);
        sessionStorage.removeItem('auth_flash');
    }
})();
</script>
@endpush
