<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Akun</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.29s; }

        @keyframes avatarIn {
            from { opacity: 0; transform: scale(0.82); }
            to   { opacity: 1; transform: scale(1); }
        }
        .avatar-in { animation: avatarIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.1s forwards; opacity: 0; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Input fields - disesuaikan dengan mockup */
        .inp {
            width: 100%;
            background: #F8F7FC;
            border: 1.5px solid #E5E2F0;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 600;
            color: #1E1B2E;
            font-family: 'Nunito', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .inp:focus { outline: none; border-color: #8B46D3; box-shadow: 0 0 0 3px rgba(139,70,211,0.12); }
        .inp::placeholder { color: #9CA3AF; font-weight: 500; }

        label.field-label { 
            display: block; 
            font-size: 13px; 
            font-weight: 700; 
            color: #374151; 
            margin-bottom: 8px; 
        }

        /* Password field with eye toggle */
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .inp {
            padding-right: 45px;
        }
        .eye-toggle {
            position: absolute;
            right: 14px;
            top: 60%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8B46D3;
            transition: opacity 0.2s;
        }
        .eye-toggle:hover {
            opacity: 0.7;
        }

        /* Toast notification */
        #toast {
            transition: all 0.3s ease;
        }

        /* Info banner - disesuaikan dengan mockup */
        .info-banner {
            background: #EDE9FE;
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .info-banner-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(139,70,211,0.1);
        }
        .info-banner-text {
            flex: 1;
            font-size: 12px;
            font-weight: 600;
            color: #8B46D3;
            line-height: 1.5;
        }

        /* Section divider */
        .section-divider {
            height: 1px;
            background: #E5E7EB;
            margin: 20px 0;
        }

        /* Header lock icon */
        .header-lock {
            width: 80px;
            height: 80px;
            background: #E1CCF4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            backdrop-filter: blur(10px);
        }

        {{-- ══════════════ SUCCESS MODAL STYLES ══════════════ --}}
        @keyframes modalBoxIn {
            from { opacity: 0; transform: translateY(40px) scale(0.94); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-box-in { animation: modalBoxIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards; }

        @keyframes badgePop {
            0%   { opacity: 0; transform: scale(0.3) rotate(-15deg); }
            65%  { transform: scale(1.15) rotate(4deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        .badge-pop { animation: badgePop 0.65s cubic-bezier(0.34,1.56,0.64,1) 0.25s both; }

        @keyframes floatDot {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-7px); }
        }
        .dot-r { animation: floatDot 2.2s ease-in-out infinite; }
        .dot-o { animation: floatDot 2.7s ease-in-out 0.4s infinite; }
        .dot-b { animation: floatDot 2.5s ease-in-out 0.7s infinite; }

        @keyframes spinSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .spin-slow { animation: spinSlow 9s linear infinite; }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; } 50% { opacity: 0.25; }
        }
        .pulse-dot { animation: pulseDot 1s ease-in-out infinite; }

        .txt-in-1 { animation: slideUp 0.45s ease 0.5s both; }
        .txt-in-2 { animation: slideUp 0.45s ease 0.65s both; }
        .btn-in   { animation: slideUp 0.45s ease 0.8s both; }
        .rdr-in   { animation: slideUp 0.45s ease 0.95s both; }

        #successModal { transition: opacity 0.25s ease; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
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
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PURPLE HEADER -->
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        
        <!-- Back button -->
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('profil.index') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0 hover:bg-white/30 transition-colors">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Edit Account</span>
        </div>

    </div>

    <!-- WHITE BODY -->
    <div class="flex-1 overflow-y-auto px-[24px] pt-[30px] pb-24 bg-gradient-to-b from-[#FAFAFF] via-[#F8F7FF] to-[#EDE9FE] rounded-t-[40px] -mt-[50px] relative z-20 flex flex-col gap-0 hide-scrollbar">

        <!-- Lock Icon Center -->
        <div class="header-lock avatar-in">
            <ion-icon name="lock-closed" style="font-size:32px;color:#8B46D3;"></ion-icon>
        </div>

        <!-- Edit Profile Title -->
        <div class="text-center mb-6 anim delay-1">
            <h2 class="text-[#8B46D3] text-xl font-extrabold mb-1">Edit Profile</h2>
            <p class="text-[#8B46D3] text-sm font-medium opacity-80">Update your account email and password</p>
        </div>

        <!-- Info banner -->
        <div class="anim delay-1 info-banner mb-6">
            <div class="info-banner-icon">
                <ion-icon name="information-circle" style="font-size:20px;color:#8B46D3;"></ion-icon>
            </div>
            <p class="info-banner-text">
                Change your account email or password. Leave the password blank if you don't want to change it.
            </p>
        </div>

        <form id="editAkunForm" novalidate class="flex flex-col gap-0">
            @csrf

            <!-- EMAIL SECTION -->
            <div class="anim delay-2 mb-4">
                <label class="field-label">Email</label>
                <input type="email" name="email" id="email"
                       value="{{ session('user')['email'] ?? 'sariwijaya@gmail.com' }}"
                       placeholder="Enter your email"
                       class="inp"/>
            </div>

            <!-- Divider -->
            <div class="section-divider anim delay-2"></div>

            <!-- PASSWORD SECTION -->
            <div class="anim delay-2 mb-5">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF] mb-2">Change Password</p>
                <p class="text-[#8B46D3] text-[12px] font-semibold">Optional — Leave Blank If You Don't Want To Change The Password</p>
            </div>

            @php
                $pwFields = [
                    ['id'=>'passwordLama',   'name'=>'password_lama',             'label'=>'Old Password',          'placeholder'=>'Enter old password'],
                    ['id'=>'passwordBaru',   'name'=>'password_baru',             'label'=>'New Password',          'placeholder'=>'Enter new password'],
                    ['id'=>'passwordConfirm','name'=>'password_baru_confirmation','label'=>'Confirm New Password', 'placeholder'=>'Confirm new password'],
                ];
            @endphp

            @foreach($pwFields as $f)
            <div class="anim delay-{{ $loop->index + 3 }} mb-4">
                <label class="field-label">{{ $f['label'] }}</label>
                <div class="password-wrapper">
                    <input type="password" name="{{ $f['name'] }}" id="{{ $f['id'] }}"
                           placeholder="{{ $f['placeholder'] }}"
                           class="inp"
                           autocomplete="off"/>
                    <button type="button" data-target="{{ $f['id'] }}"
                            class="eye-toggle">
                        <ion-icon class="eye-show" name="eye-off-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                        <ion-icon class="eye-hide hidden" name="eye-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                </div>
            </div>
            @endforeach

            <!-- Confirm match hint -->
            <div id="confirmHint" class="anim delay-4 text-xs font-semibold hidden mb-4"></div>

            <!-- BUTTONS -->
            <div class="anim delay-4 mt-6 flex flex-col gap-3">
                <!-- Save Button -->
                <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 bg-[#8B46D3] text-white font-bold py-[16px] rounded-[14px] text-[15px] tracking-wide transition-transform duration-150 active:scale-[0.97] shadow-[0_4px_16px_rgba(139,70,211,0.35)] hover:bg-[#7C3AED]">
                    <ion-icon name="save-outline" id="btnIcon" style="font-size:20px;"></ion-icon>
                    <span id="btnText">Save Change</span>
                    <svg id="btnSpinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>

                <!-- Cancel Button -->
                <a href="{{ route('profil.index') }}"
                   class="w-full flex items-center justify-center gap-2 bg-white border-[1.5px] border-[#FECACA] text-[#EF4444] font-bold py-[16px] rounded-[14px] text-[15px] tracking-wide transition-transform duration-150 active:scale-[0.97] hover:bg-red-50">
                    <ion-icon name="close-circle" style="font-size:20px;color:#EF4444;"></ion-icon>
                    <span>Cancel</span>
                </a>
            </div>

        </form>
        
        <div class="h-8"></div>

    </div>{{-- end white body --}}

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'profil'])

</div>
</div>

<!-- Toast notification -->
<div id="toast"
     class="fixed top-5 left-1/2 -translate-x-1/2 z-[60] flex items-center gap-3 bg-white rounded-2xl px-5 py-3.5 shadow-[0_8px_32px_rgba(0,0,0,0.15)] border border-[#F0EDFB] transition-all duration-300 opacity-0 -translate-y-2 pointer-events-none max-w-[340px] w-[90%]">
    <div id="toastIcon" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"></div>
    <p id="toastMsg" class="text-[#1E1B2E] text-[13px] font-bold flex-1"></p>
</div>

{{-- ══════════════ SUCCESS MODAL ══════════════ --}}
<div id="successModal"
     class="fixed inset-0 z-[70] flex items-end justify-center hidden opacity-0"
     style="background: rgba(15,10,35,0.55); backdrop-filter: blur(4px);">

    <div class="modal-box-in w-full sm:max-w-[390px] bg-gradient-to-b from-white via-white to-[#D4BAEF]/40
                rounded-t-[36px] pt-10 pb-12 px-8 flex flex-col items-center relative overflow-hidden min-h-[460px]">

        <!-- Decorative: spinning dashed circle (top-right) -->
        <div class="absolute top-6 right-8 pointer-events-none">
            <svg class="spin-slow w-[75px] h-[75px] text-[#D4BAEF]" viewBox="0 0 75 75" fill="none">
                <circle cx="37.5" cy="37.5" r="33" stroke="currentColor" stroke-width="2.5"
                        stroke-dasharray="7 5" stroke-linecap="round"/>
            </svg>
        </div>

        <!-- Floating dots -->
        <div class="dot-r absolute top-9 left-12 w-3.5 h-3.5 rounded-full bg-[#EF4444] pointer-events-none"></div>
        <div class="dot-o absolute top-[140px] left-8 w-3 h-3 rounded-full bg-[#F59E0B] pointer-events-none"></div>
        <div class="dot-b absolute top-[155px] right-10 w-3.5 h-3.5 rounded-full bg-[#3B82F6] pointer-events-none"></div>

        <!-- Corner shape (bottom-left) -->
        <div class="absolute bottom-16 left-6 pointer-events-none opacity-50">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <rect x="3" y="3" width="54" height="54" rx="14" stroke="#C4B5FD" stroke-width="2.5" fill="none"/>
            </svg>
        </div>

        <!-- Green badge -->
        <div class="badge-pop mb-7 z-10">
            <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                <path d="M60 7
                         L67 22 L83 18.5 L80.5 35 L95 43.5
                         L86 57 L95 70.5 L80.5 79 L83 95.5
                         L67 92 L60 107 L53 92 L37 95.5
                         L39.5 79 L25 70.5 L34 57 L25 43.5
                         L39.5 35 L37 18.5 L53 22 Z"
                      fill="#22C55E"/>
                <path d="M41 60 L54 73 L79 47"
                      stroke="white" stroke-width="5.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h2 class="txt-in-1 text-[#1E1B2E] text-[30px] font-extrabold mb-2 text-center z-10">
            Successful!
        </h2>

        <p class="txt-in-2 text-[#9CA3AF] text-[14px] font-semibold text-center leading-relaxed mb-9 max-w-[240px] z-10">
            Congratulations, your account has been successfully updated.
        </p>

        <div class="btn-in w-full z-10">
            <a href="{{ route('dashboard') }}"
               class="flex items-center justify-center gap-2 w-full bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-full text-[14px] tracking-wide shadow-[0_4px_20px_rgba(139,70,211,0.32)] transition-transform duration-150 active:scale-[0.97]">
                Browse Home <span class="text-base leading-none">→</span>
            </a>
        </div>

        <div class="rdr-in flex items-center gap-2 mt-4 z-10">
            <span class="pulse-dot w-2 h-2 rounded-full bg-[#22C55E] inline-block"></span>
            <span class="text-[#9CA3AF] text-[11px] font-bold uppercase tracking-wider">
                AUTO REDIRECT IN <span id="successCountdown">5</span>s
            </span>
        </div>

    </div>
</div>

<script>
// ── Clock ─────────────────────────────────────────────────────────────────────
(function () {
    const el = document.getElementById('statusTime');
    function tick() {
        const now = new Date();
        if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
    }
    tick(); setInterval(tick, 30000);
})();

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'error') {
    const toast  = document.getElementById('toast');
    const icon   = document.getElementById('toastIcon');
    const msgEl  = document.getElementById('toastMsg');
    const colors = { 
        error: { bg:'#FEE2E2', clr:'#EF4444', name:'close-circle' },
        success:{ bg:'#D1FAE5', clr:'#10B981', name:'checkmark-circle' } 
    };
    const c = colors[type] || colors.error;
    icon.style.background = c.bg;
    icon.innerHTML = `<ion-icon name="${c.name}" style="font-size:18px;color:${c.clr};"></ion-icon>`;
    msgEl.textContent = msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    toast.style.pointerEvents = 'auto';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(-8px)';
        toast.style.pointerEvents = 'none';
    }, type === 'success' ? 2000 : 3000);
}

// ── Eye Toggle ────────────────────────────────────────────────────────────────
document.querySelectorAll('.eye-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        btn.querySelector('.eye-show').classList.toggle('hidden', !isPass);
        btn.querySelector('.eye-hide').classList.toggle('hidden', isPass);
    });
});

// ── Confirm Password Match ────────────────────────────────────────────────────
const pwBaru    = document.getElementById('passwordBaru');
const pwConfirm = document.getElementById('passwordConfirm');
const hint      = document.getElementById('confirmHint');

function checkMatch() {
    if (!pwConfirm.value) { 
        hint.classList.add('hidden'); 
        return; 
    }
    hint.classList.remove('hidden');
    if (pwBaru.value === pwConfirm.value) {
        hint.textContent = '✓ Password cocok';
        hint.className = 'text-xs font-semibold text-green-600 anim delay-4 mb-4';
    } else {
        hint.textContent = '✗ Password tidak cocok';
        hint.className = 'text-xs font-semibold text-red-500 anim delay-4 mb-4';
    }
}

if (pwBaru && pwConfirm) {
    pwBaru.addEventListener('input', checkMatch);
    pwConfirm.addEventListener('input', checkMatch);
}

// ── Success Modal ─────────────────────────────────────────────────────────────
let _countdownIv = null;
function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => { modal.style.opacity = '1'; });
    let secs = 5;
    const el = document.getElementById('successCountdown');
    clearInterval(_countdownIv);
    _countdownIv = setInterval(() => {
        secs--;
        if (el) el.textContent = secs;
        if (secs <= 0) {
            clearInterval(_countdownIv);
            window.location.href = '{{ route("dashboard") }}';
        }
    }, 1000);
}

// ── Form Submit ───────────────────────────────────────────────────────────────
function setLoading(v) {
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const spinner = document.getElementById('btnSpinner');
    
    btn.disabled = v;
    btnText.textContent = v ? 'Menyimpan...' : 'Save Change';
    if (v) {
        btnIcon.style.display = 'none';
        spinner.classList.remove('hidden');
    } else {
        btnIcon.style.display = '';
        spinner.classList.add('hidden');
    }
}

const form = document.getElementById('editAkunForm');
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email   = document.getElementById('email')?.value.trim();
        const lama    = document.getElementById('passwordLama')?.value;
        const baru    = document.getElementById('passwordBaru')?.value;
        const confirm = document.getElementById('passwordConfirm')?.value;

        // Validasi
        if (!email) return showToast('Email wajib diisi.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return showToast('Format email tidak valid.');
        
        if (baru || confirm || lama) {
            if (!lama)            return showToast('Password lama wajib diisi untuk mengubah password.');
            if (!baru)            return showToast('Password baru wajib diisi.');
            if (baru.length < 6) return showToast('Password baru minimal 6 karakter.');
            if (baru !== confirm) return showToast('Konfirmasi password tidak cocok.');
        }

        setLoading(true);
        try {
            const fd = new FormData(form);
            const res = await fetch('{{ route("profil.update-akun") }}', {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: fd,
            });
            const data = await res.json();
            
            if (data.success) {
                // Tampilkan success modal alih-alih toast
                showSuccessModal();
            } else {
                const err = data.errors ? Object.values(data.errors)[0] : data.message;
                showToast(Array.isArray(err) ? err[0] : (err || 'Gagal menyimpan.'));
            }
        } catch(e) { 
            console.error(e);
            showToast('Terjadi kesalahan. Coba lagi.'); 
        } finally { 
            setLoading(false); 
        }
    });
}
</script>

@include('partials.auth-guard')
</body>
</html>