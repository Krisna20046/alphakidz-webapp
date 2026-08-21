@extends('layouts.auth')

@section('title', 'Reset Password - App')

@push('styles')
@include('components.styles')
<style>
    .pill-input.error {
        background: rgba(255,230,230,0.7);
        box-shadow: 0 0 0 3px rgba(239,68,68,0.20);
    }

    /* Password strength bar */
    .strength-bar {
        height: 4px;
        border-radius: 4px;
        background: #EDE9FE;
        overflow: hidden;
        transition: all 0.3s;
    }
    .strength-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease, background 0.3s;
        width: 0%;
    }

    /* Success pop-in */
    @keyframes popIn {
        0%   { transform: scale(0.7); opacity: 0; }
        70%  { transform: scale(1.08); }
        100% { transform: scale(1); opacity: 1; }
    }
    .pop-in { animation: popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

    @keyframes checkDraw {
        from { stroke-dashoffset: 50; }
        to   { stroke-dashoffset: 0; }
    }
    .check-draw {
        stroke-dasharray: 50;
        stroke-dashoffset: 50;
        animation: checkDraw 0.4s ease 0.3s forwards;
    }
</style>
@endpush

@section('content')
<!-- HERO HEADER -->
<div class="hero-bg relative z-10 px-6 pt-[56px] pb-[90px]">
    <x-auth-hero
        title="Reset Password"
        description="Create a new secure password for your account. Make sure it's at least 8 characters."
    />
</div>

<!-- FORM CARD -->
<div class="relative z-20 -mt-[50px] bg-white rounded-t-[40px] px-6 pt-8 pb-10 min-h-[500px]">

    <!-- FORM STATE -->
    <div id="formState">

        <form id="resetForm" novalidate class="flex flex-col gap-4">
            @csrf
            {{-- Hidden fields populated from URL params --}}
            <input type="hidden" id="tokenField" name="token" value="{{ request()->query('token') }}">
            <input type="hidden" id="emailField" name="email" value="{{ request()->query('email') }}">

                <!-- New Password -->
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
                        placeholder="New Password"
                        autocomplete="new-password"
                        class="pill-input pr-14"
                    />
                    <button type="button" id="togglePassword1"
                            class="absolute right-[18px] top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#8B46D3] transition-colors">
                        <svg id="eye1" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg id="eyeOff1" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>

                <!-- Password Strength -->
                <div class="anim d3 px-1 -mt-2">
                    <div class="strength-bar">
                        <div id="strengthFill" class="strength-fill"></div>
                    </div>
                    <p id="strengthLabel" class="text-[11px] font-bold mt-1 text-[#B39DDB]"></p>
                </div>

                <!-- Confirm Password -->
                <div class="anim d4 relative">
                    <div class="absolute left-[18px] top-1/2 -translate-y-1/2 z-10">
                        <svg class="w-[18px] h-[18px] text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Confirm New Password"
                        autocomplete="new-password"
                        class="pill-input pr-14"
                    />
                    <button type="button" id="togglePassword2"
                            class="absolute right-[18px] top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#8B46D3] transition-colors">
                        <svg id="eye2" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg id="eyeOff2" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>

                <!-- Match indicator -->
                <div id="matchIndicator" class="anim d4 px-1 -mt-2 hidden">
                    <p id="matchLabel" class="text-[11px] font-bold"></p>
                </div>

            <!-- Reset Button -->
            <div class="anim d5 mt-3">
                <button type="submit" id="submitBtn" class="btn-signin flex items-center justify-center gap-2">
                    <span id="btnText">Reset Password</span>
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
        <div class="anim d6 mt-6 text-center">
            <p class="text-sm text-[#9CA3AF] font-semibold">
                Remember your password?
                <a href="{{ route('login') }}" class="font-extrabold text-[#8B46D3] hover:text-[#7C3AED] transition-colors ml-1">
                    Sign In
                </a>
            </p>
        </div>

    </div>
    <!-- end formState -->

    <!-- SUCCESS STATE -->
    <div id="successState" class="hidden flex flex-col items-center justify-center py-6 px-2">
        <div class="pop-in mb-6">
            <div class="w-24 h-24 rounded-[28px] bg-gradient-to-br from-[#D1FAE5] to-[#A7F3D0] flex items-center justify-center shadow-lg shadow-green-200/60">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 48 48">
                    <circle cx="24" cy="24" r="20" fill="#10B981" opacity="0.15"/>
                    <path class="check-draw" stroke="#10B981" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" d="M14 24l8 8 12-14"/>
                </svg>
            </div>
        </div>

        <h2 class="text-[#3D1F7A] text-[22px] font-extrabold text-center mb-2">Password Reset!</h2>
        <p class="text-[#6B7280] text-sm font-semibold text-center leading-relaxed max-w-[260px] mb-8">
            Your password has been successfully updated. You can now sign in with your new password.
        </p>

        <a href="{{ route('login') }}" class="btn-signin flex items-center justify-center gap-2 no-underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>Sign In Now</span>
        </a>
    </div>
    <!-- end successState -->

</div>
@endsection

@push('scripts')
<script>
// Read token & email from URL
(function() {
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');
    const email = params.get('email');
    if (token) document.getElementById('tokenField').value = token;
    if (email) document.getElementById('emailField').value = decodeURIComponent(email);
})();

// Toggle password visibility
function makeToggle(btnId, inputId, eyeId, eyeOffId) {
    document.getElementById(btnId).addEventListener('click', () => {
        const input = document.getElementById(inputId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        document.getElementById(eyeId).classList.toggle('hidden', isHidden);
        document.getElementById(eyeOffId).classList.toggle('hidden', !isHidden);
    });
}
makeToggle('togglePassword1', 'password', 'eye1', 'eyeOff1');
makeToggle('togglePassword2', 'password_confirmation', 'eye2', 'eyeOff2');

// Password strength
const strengthFill  = document.getElementById('strengthFill');
const strengthLabel = document.getElementById('strengthLabel');
const levels = [
    { color: '#EF4444', label: 'Too short',  width: '15%'  },
    { color: '#F59E0B', label: 'Weak',        width: '35%'  },
    { color: '#EAB308', label: 'Fair',        width: '60%'  },
    { color: '#22C55E', label: 'Strong',      width: '85%'  },
    { color: '#10B981', label: 'Very strong', width: '100%' },
];
function getStrength(pw) {
    if (pw.length < 6) return 0;
    let score = 1;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    return Math.min(score, 4);
}
document.getElementById('password').addEventListener('input', function() {
    const val = this.value;
    if (!val) { strengthFill.style.width = '0%'; strengthLabel.textContent = ''; return; }
    const idx = getStrength(val);
    const lvl = levels[idx];
    strengthFill.style.width = lvl.width;
    strengthFill.style.background = lvl.color;
    strengthLabel.textContent = lvl.label;
    strengthLabel.style.color = lvl.color;
    checkMatch();
});

// Password match indicator
function checkMatch() {
    const pw  = document.getElementById('password').value;
    const cpw = document.getElementById('password_confirmation').value;
    const ind = document.getElementById('matchIndicator');
    const lbl = document.getElementById('matchLabel');
    const cfEl = document.getElementById('password_confirmation');
    if (!cpw) { ind.classList.add('hidden'); cfEl.classList.remove('error'); return; }
    ind.classList.remove('hidden');
    if (pw === cpw) {
        lbl.textContent = '✓ Passwords match';
        lbl.style.color = '#10B981';
        cfEl.classList.remove('error');
    } else {
        lbl.textContent = '✗ Passwords do not match';
        lbl.style.color = '#EF4444';
        cfEl.classList.add('error');
    }
}
document.getElementById('password_confirmation').addEventListener('input', checkMatch);

// Loading state
function setLoading(loading) {
    const btn        = document.getElementById('submitBtn');
    const btnText    = document.getElementById('btnText');
    const btnArrow   = document.getElementById('btnArrow');
    const btnSpinner = document.getElementById('btnSpinner');
    btn.disabled = loading;
    btnText.textContent = loading ? 'Resetting...' : 'Reset Password';
    btnArrow.classList.toggle('hidden', loading);
    btnSpinner.classList.toggle('hidden', !loading);
}

// Form submit
document.getElementById('resetForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const token                = document.getElementById('tokenField').value;
    const email                = document.getElementById('emailField').value;
    const password             = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;

    if (!password)             { showToast('New password is required!'); return; }
    if (password.length < 8)   { showToast('Password must be at least 8 characters.'); return; }
    if (!password_confirmation) { showToast('Password confirmation is required!'); return; }
    if (password !== password_confirmation) { showToast('Passwords do not match.'); return; }
    if (!token || !email)       { showToast('Invalid reset link. Request a new reset email.'); return; }

    setLoading(true);

    try {
        const res = await fetch('https://api.alpha-kidz.com/api/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ token, email, password, password_confirmation }),
        });

        const data = await res.json();

        if (res.ok && (data.success || data.status === 'success' || data.message)) {
            document.getElementById('formState').classList.add('hidden');
            document.getElementById('successState').classList.remove('hidden');
        } else {
            showToast(data.message || 'Failed to reset password. Please try again.');
        }
    } catch (err) {
        showToast('Something went wrong. Please try again.');
    } finally {
        setLoading(false);
    }
});
</script>
@endpush
