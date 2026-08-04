{{-- FRONTEND: resources/views/auth/google-callback.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Processing Login...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { screens: { sm: '1024px' } } } };
    </script>
    <script>
        // Force mobile layout on phones even when "Desktop Site" mode is active
        (function() {
            var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            var isPhoneScreen = window.screen.width <= 430 || window.screen.height <= 932;
            if (isTouchDevice && isPhoneScreen && window.innerWidth >= 1024) {
                var meta = document.querySelector('meta[name="viewport"]');
                if (meta) meta.content = 'width=430, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                var s = document.createElement('style');
                s.textContent = '.phone-wrapper{min-height:100vh!important;display:flex!important;align-items:center!important;justify-content:center!important;padding:0!important}.phone-frame{min-height:100vh!important;width:100%!important;border-radius:0!important;box-shadow:none!important}';
                document.head.appendChild(s);
            }
        })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; }
        @media (min-width: 1024px) {
            .phone-wrapper { display:flex;align-items:center;justify-content:center;min-height:100vh;background:#E5E2F5; }
            .phone-frame { width:390px;min-height:844px;border-radius:44px;box-shadow:0 40px 80px rgba(124,58,237,0.28),0 0 0 8px #1a1030,0 0 0 10px #2d1a50;overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center; }
        }
        @media (max-width: 1023px) {
            .phone-wrapper { min-height:100vh;display:flex;align-items:center;justify-content:center;background:#E5E2F5; }
            .phone-frame  { min-height:100vh;width:100%;display:flex;align-items:center;justify-content:center; }
        }
        @keyframes spin { to { transform:rotate(360deg); } }
        @keyframes pulse-ring { 0%{transform:scale(0.8);opacity:0.8}50%{transform:scale(1.15);opacity:0.2}100%{transform:scale(0.8);opacity:0.8} }
        @keyframes fadeIn { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)} }
        @keyframes checkDraw { from{stroke-dashoffset:50}to{stroke-dashoffset:0} }
        @keyframes shake { 0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-7px)}40%,80%{transform:translateX(7px)} }
        .spinner { width:56px;height:56px;border:4px solid rgba(139,70,211,0.15);border-top-color:#8B46D3;border-radius:50%;animation:spin 0.75s linear infinite; }
        .pulse-ring { width:90px;height:90px;border-radius:50%;background:rgba(139,70,211,0.10);animation:pulse-ring 1.8s ease-in-out infinite;position:absolute; }
        .content { animation:fadeIn 0.4s ease forwards; }
        .check-path { stroke-dasharray:50;stroke-dashoffset:50;animation:checkDraw 0.5s ease forwards 0.15s; }
        .shake { animation:shake 0.4s ease; }
    </style>
</head>
<body class="bg-[#E5E2F5]">
<div class="phone-wrapper">
<div class="phone-frame bg-white">
    <div class="content flex flex-col items-center justify-center gap-6 px-8 text-center w-full">

        <div class="relative flex items-center justify-center" id="iconWrapper">
            <div class="pulse-ring" id="pulseRing"></div>
            <div id="stateLoading"><div class="spinner"></div></div>
            <div id="stateSuccess" class="hidden">
                <div class="w-[72px] h-[72px] rounded-full bg-green-50 flex items-center justify-center">
                    <svg class="w-9 h-9" viewBox="0 0 36 36" fill="none">
                        <circle cx="18" cy="18" r="17" stroke="#22C55E" stroke-width="2"/>
                        <path class="check-path" d="M10 18l6 6 10-10" stroke="#22C55E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div id="stateError" class="hidden">
                <div class="w-[72px] h-[72px] rounded-full bg-red-50 flex items-center justify-center">
                    <svg class="w-9 h-9" viewBox="0 0 36 36" fill="none">
                        <circle cx="18" cy="18" r="17" stroke="#EF4444" stroke-width="2"/>
                        <path d="M12 12l12 12M24 12L12 24" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <div>
            <p id="titleText" class="text-[#3D1F7A] text-xl font-extrabold mb-1.5">Processing Login</p>
            <p id="subText" class="text-[#9CA3AF] text-sm font-semibold leading-relaxed">Connecting your Google account...</p>
        </div>

        <div id="dots" class="flex gap-2">
            <div class="w-2 h-2 rounded-full bg-[#8B46D3] animate-bounce" style="animation-delay:0s"></div>
            <div class="w-2 h-2 rounded-full bg-[#8B46D3] animate-bounce" style="animation-delay:0.15s"></div>
            <div class="w-2 h-2 rounded-full bg-[#8B46D3] animate-bounce" style="animation-delay:0.30s"></div>
        </div>

        <a id="btnBack" href="{{ route('login') }}"
           class="hidden px-8 py-3 rounded-full bg-[#8B46D3] text-white text-sm font-bold shadow-lg hover:bg-[#7C3AED] transition-colors">
            Back to Login
        </a>
    </div>
</div>
</div>

<script>
(async () => {
    const params = new URLSearchParams(window.location.search);
    const code   = params.get('code');

    if (!code) {
        showError('Authentication parameters not found.');
        return;
    }

    try {
        // ── STEP 1: Kirim code ke API backend untuk dapat token ──
        const apiUrl = 'https://api.alpha-kidz.com/api/auth/google/callback?' + params.toString();
        const apiRes = await fetch(apiUrl, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
        });
        const apiData = await apiRes.json();

        if (apiData.status !== 'success' || !apiData.token) {
            showError(apiData.message || 'Google sign-in failed. Please try again.');
            return;
        }

        // ── STEP 2: Simpan token ke Laravel session via store-token ──
        const storeRes = await fetch('{{ route("auth.store-token") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                token: apiData.token,
                user:  apiData.user,
            }),
        });
        const storeData = await storeRes.json();

        if (storeData.success || storeRes.ok) {
            // ── STEP 3: Redirect ke dashboard ──
            showSuccess(apiData.user?.name || 'Pengguna');
            setTimeout(() => {
                window.location.href = '{{ route("dashboard") }}';
            }, 1500);
        } else {
            showError('Failed to save the login session.');
        }

    } catch (err) {
        console.error(err);
        showError('Failed to connect to the server. Check your internet connection.');
    }
})();

function showSuccess(name) {
    document.getElementById('stateLoading').classList.add('hidden');
    document.getElementById('stateSuccess').classList.remove('hidden');
    document.getElementById('pulseRing').style.background = 'rgba(34,197,94,0.10)';
    document.getElementById('dots').classList.add('hidden');
    const t = document.getElementById('titleText');
    t.textContent = 'Login Successful! 🎉';
    t.className = 'text-green-600 text-xl font-extrabold mb-1.5';
    document.getElementById('subText').textContent = 'Hi, ' + name + '! Redirecting to the dashboard...';
}

function showError(msg) {
    document.getElementById('stateLoading').classList.add('hidden');
    document.getElementById('stateError').classList.remove('hidden');
    document.getElementById('pulseRing').style.background = 'rgba(239,68,68,0.08)';
    document.getElementById('dots').classList.add('hidden');
    const t = document.getElementById('titleText');
    t.textContent = 'Login Failed';
    t.className = 'text-red-500 text-xl font-extrabold mb-1.5';
    document.getElementById('subText').textContent = msg;
    document.getElementById('btnBack').classList.remove('hidden');
    const w = document.getElementById('iconWrapper');
    w.classList.add('shake');
    setTimeout(() => w.classList.remove('shake'), 400);
}
</script>
</body>
</html>
