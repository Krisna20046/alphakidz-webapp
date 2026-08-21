@extends('layouts.auth')

@section('title', 'Forgot Password - App')

@push('styles')
@include('components.styles')
<style>
    /* Success state */
    @keyframes popIn {
        0%   { transform: scale(0.7); opacity: 0; }
        70%  { transform: scale(1.08); }
        100% { transform: scale(1); opacity: 1; }
    }
    .pop-in { animation: popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

    /* Email icon envelope animation */
    @keyframes floatEnvelope {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-6px); }
    }
    .float-envelope { animation: floatEnvelope 2.4s ease-in-out infinite; }
</style>
@endpush

@section('content')
<!-- HERO HEADER -->
<div class="hero-bg relative z-10 px-6 pt-[56px] pb-[90px]">
    <x-auth-hero
        title="Forgot Password"
        description="Enter your registered email and we'll send you a link to reset your password."
    />
</div>

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
                <button type="submit" id="submitBtn" class="btn-signin flex items-center justify-center gap-2">
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
                class="btn-signin flex items-center justify-center gap-2 mb-4">
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
@endsection

@push('scripts')
<script>
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

    if (!email) { showToast('Email is required!'); return; }

    // Basic email format check
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) { showToast('Invalid email format.'); return; }

    setLoading(true);

    try {
        const res = await fetch('https://api.alpha-kidz.com/api/forgot-password', {
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
            showToast(data.message || 'Email not found. Please try again.');
        }
    } catch (err) {
        showToast('Something went wrong. Please try again.');
    } finally {
        setLoading(false);
    }
});
</script>
@endpush
