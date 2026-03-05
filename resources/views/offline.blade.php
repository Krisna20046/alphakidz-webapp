{{-- resources/views/offline.blade.php --}}
{{-- Route: GET /offline (tanpa middleware auth) --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tidak Ada Koneksi - NannyApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex; align-items: center; justify-content: center;
                min-height: 100vh; padding: 32px 0;
                background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 60%, #e8d5ee 100%);
            }
            .phone-frame {
                width: 390px; min-height: 844px; border-radius: 44px;
                box-shadow: 0 40px 80px rgba(123,30,90,0.25), 0 0 0 8px #1a0d14, 0 0 0 10px #2d1020;
                overflow: hidden; position: relative;
                display: flex; align-items: center; justify-content: center;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; display:flex; align-items:center; justify-content:center; }
            .phone-frame { width:100%; }
        }

        @keyframes float {
            0%,100% { transform: translateY(0px); }
            50%      { transform: translateY(-12px); }
        }
        .float { animation: float 3s ease-in-out infinite; }

        @keyframes pulse-ring {
            0%   { transform: scale(1);   opacity: .6; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pulse-ring {
            position: absolute; inset: 0; border-radius: 50%;
            border: 2px solid #F3E6FA;
            animation: pulse-ring 2s ease-out infinite;
        }
        .pulse-ring-2 { animation-delay: .6s; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up     { animation: slideUp .4s ease forwards; }
        .d1 { animation-delay: .1s; opacity: 0; }
        .d2 { animation-delay: .2s; opacity: 0; }
        .d3 { animation-delay: .3s; opacity: 0; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale">

    <div class="flex flex-col items-center justify-center px-8 text-center py-16">

        <!-- Icon dengan pulse -->
        <div class="relative w-28 h-28 flex items-center justify-center mb-8 anim-up d1">
            <div class="pulse-ring"></div>
            <div class="pulse-ring pulse-ring-2"></div>
            <div class="float w-24 h-24 rounded-full bg-gradient-to-br from-[#F3E6FA] to-[#E8D5EE]
                        flex items-center justify-center shadow-xl shadow-plum/10 relative z-10">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                     stroke="#7B1E5A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="1" y1="1" x2="23" y2="23"/>
                    <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
                    <path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                    <line x1="12" y1="20" x2="12.01" y2="20"/>
                </svg>
            </div>
        </div>

        <!-- Text -->
        <h1 class="anim-up d1 text-2xl font-extrabold text-[#4A0E35] mb-3 leading-tight">
            Tidak Ada Koneksi
        </h1>
        <p class="anim-up d2 text-[#A2397B] text-sm leading-relaxed mb-8 max-w-xs">
            Halaman ini tidak tersedia secara offline. Periksa koneksi internet Anda dan coba lagi.
        </p>

        <!-- Status indicator -->
        <div id="statusCard" class="anim-up d2 w-full max-w-xs bg-white rounded-2xl border-2 border-[#F3E6FA] p-4 mb-6 flex items-center gap-3">
            <div id="statusDot" class="w-3 h-3 rounded-full bg-red-400 flex-shrink-0"></div>
            <div class="flex-1 text-left">
                <p id="statusTitle" class="text-[#4A0E35] font-bold text-sm">Offline</p>
                <p id="statusDesc" class="text-[#A2397B] text-xs mt-0.5">Tidak terhubung ke internet</p>
            </div>
        </div>

        <!-- Retry button -->
        <div class="anim-up d3 w-full max-w-xs space-y-3">
            <button id="retryBtn" onclick="retryConnection()"
                    class="w-full flex items-center justify-center gap-2
                           bg-[#7B1E5A] text-white font-bold py-4 rounded-2xl
                           shadow-lg shadow-plum/30 active:scale-95 transition-all text-sm">
                <svg id="retryIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                <span id="retryText">Coba Lagi</span>
            </button>

            <button onclick="history.back()"
                    class="w-full py-3.5 rounded-2xl border-2 border-[#F3E6FA] text-[#7B1E5A]
                           font-bold text-sm active:bg-[#F3E6FA] transition-all">
                Kembali
            </button>
        </div>

    </div>

</div>
</div>

<script>
let isRetrying = false;

// Monitor koneksi secara real-time
window.addEventListener('online', () => {
    document.getElementById('statusDot').className   = 'w-3 h-3 rounded-full bg-green-400 flex-shrink-0';
    document.getElementById('statusTitle').textContent = 'Terhubung!';
    document.getElementById('statusDesc').textContent  = 'Koneksi kembali tersedia, mengalihkan...';
    // Auto redirect setelah koneksi kembali
    setTimeout(() => { window.location.href = '/dashboard'; }, 1200);
});

window.addEventListener('offline', () => {
    document.getElementById('statusDot').className   = 'w-3 h-3 rounded-full bg-red-400 flex-shrink-0';
    document.getElementById('statusTitle').textContent = 'Offline';
    document.getElementById('statusDesc').textContent  = 'Tidak terhubung ke internet';
});

async function retryConnection() {
    if (isRetrying) return;
    isRetrying = true;

    const btn  = document.getElementById('retryBtn');
    const text = document.getElementById('retryText');
    const icon = document.getElementById('retryIcon');

    btn.disabled    = true;
    btn.style.opacity = '0.7';
    text.textContent = 'Memeriksa...';
    icon.style.animation = 'spin .7s linear infinite';

    // Tambahkan style spin
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);

    try {
        // Cek koneksi dengan fetch ke endpoint ringan
        const res = await fetch('/login', { method: 'HEAD', cache: 'no-store' });
        if (res.ok || res.status === 302 || res.status === 200) {
            text.textContent = 'Terhubung!';
            setTimeout(() => { window.location.href = '/dashboard'; }, 800);
            return;
        }
    } catch (_) {}

    // Gagal
    text.textContent  = 'Coba Lagi';
    icon.style.animation = '';
    btn.disabled       = false;
    btn.style.opacity  = '1';
    isRetrying         = false;
}
</script>

</body>
</html>