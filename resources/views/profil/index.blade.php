<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Profil</title>
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
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex; align-items: flex-start; justify-content: center;
                min-height: 100vh; padding: 32px 0;
                background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 60%, #e8d5ee 100%);
            }
            .phone-frame {
                width: 390px; min-height: 844px; border-radius: 44px;
                box-shadow: 0 40px 80px rgba(123,30,90,0.25), 0 0 0 8px #1a0d14, 0 0 0 10px #2d1020;
                overflow: hidden; position: relative;
            }
        }

        .header-bg { background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%); }
        .header-wave { clip-path: ellipse(110% 100% at 50% 0%); }

        .menu-item { transition: transform 0.15s ease, background 0.15s ease; }
        .menu-item:active { transform: scale(0.97); background: #F3E6FA; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up { animation: slideUp 0.4s ease forwards; }
        .delay-1 { animation-delay: 0.05s; opacity: 0; }
        .delay-2 { animation-delay: 0.12s; opacity: 0; }
        .delay-3 { animation-delay: 0.19s; opacity: 0; }

        @keyframes avatarIn {
            from { opacity: 0; transform: scale(0.7); }
            to   { opacity: 1; transform: scale(1); }
        }
        .avatar-in { animation: avatarIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.1s forwards; opacity: 0; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }

        /* Confirm modal */
        #logoutModal { transition: opacity 0.2s ease; }
        #logoutModalBox { transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg header-wave px-5 pt-10 pb-14 relative shrink-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white/5 translate-y-8 -translate-x-6"></div>

        <div class="relative flex flex-col items-center">
            <!-- Avatar -->
            <div class="avatar-in relative mb-4">
                @if(session('user')['foto_url'] ?? null)
                    <img src="{{ session('user')['foto_url'] }}" alt="foto"
                         class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-xl"/>
                @else
                    <div class="w-24 h-24 rounded-full border-4 border-white bg-white flex items-center justify-center shadow-xl">
                        <ion-icon name="person" style="font-size:48px;color:#7B1E5A;"></ion-icon>
                    </div>
                @endif
            </div>

            <h1 class="anim-up delay-1 text-white text-2xl font-extrabold tracking-wide">
                {{ session('user')['name'] ?? 'Pengguna' }}
            </h1>
            <p class="anim-up delay-1 text-white/60 text-sm mt-0.5">
                {{ session('user')['email'] ?? '' }}
            </p>
            <div class="anim-up delay-2 mt-3 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full">
                <span class="text-white text-xs font-bold tracking-wide">
                    {{ session('user')['role'] ?? '' }}
                </span>
            </div>
        </div>
    </div>

    <!-- SCROLLABLE BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar -mt-6 px-4 pb-16">

        <!-- Pengaturan Card -->
        <div class="anim-up delay-2 bg-white rounded-3xl shadow-sm shadow-plum/10 overflow-hidden mb-4">
            <div class="px-5 pt-4 pb-2">
                <p class="text-plum-dark text-sm font-extrabold uppercase tracking-wider">Pengaturan</p>
            </div>

            <!-- Edit Profil -->
            <a href="{{ route('profil.detail') }}"
               class="menu-item flex items-center gap-4 px-5 py-4 border-b border-plum-soft/50">
                <div class="w-11 h-11 rounded-2xl bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="create-outline" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                </div>
                <span class="flex-1 text-plum-dark font-semibold text-sm">Edit Profil</span>
                <ion-icon name="chevron-forward" style="font-size:18px;color:#B895C8;"></ion-icon>
            </a>

            <!-- Edit Akun -->
            <a href="{{ route('profil.edit-akun') }}"
               class="menu-item flex items-center gap-4 px-5 py-4 border-b border-plum-soft/50">
                <div class="w-11 h-11 rounded-2xl bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="settings-outline" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                </div>
                <span class="flex-1 text-plum-dark font-semibold text-sm">Edit Akun</span>
                <ion-icon name="chevron-forward" style="font-size:18px;color:#B895C8;"></ion-icon>
            </a>

            {{-- Data Anak hanya untuk Majikan (id_role 2) --}}
            @if((session('user')['role'] ?? '') === 'Majikan')
            <a href="{{ route('profil.data-anak') }}"
               class="menu-item flex items-center gap-4 px-5 py-4">
                <div class="w-11 h-11 rounded-2xl bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="happy-outline" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                </div>
                <span class="flex-1 text-plum-dark font-semibold text-sm">Data Anak</span>
                <ion-icon name="chevron-forward" style="font-size:18px;color:#B895C8;"></ion-icon>
            </a>
            @endif
        </div>

        <!-- Info Akun Card -->
        <div class="anim-up delay-3 bg-white rounded-3xl shadow-sm shadow-plum/10 overflow-hidden mb-4">
            <div class="px-5 pt-4 pb-2">
                <p class="text-plum-dark text-sm font-extrabold uppercase tracking-wider">Info Akun</p>
            </div>
            <div class="px-5 py-4 flex items-center gap-3 border-b border-plum-soft/50">
                <div class="w-9 h-9 rounded-xl bg-plum-soft/60 flex items-center justify-center shrink-0">
                    <ion-icon name="mail-outline" style="font-size:17px;color:#A2397B;"></ion-icon>
                </div>
                <div>
                    <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">Email</p>
                    <p class="text-plum-dark text-sm font-medium">{{ session('user')['email'] ?? '-' }}</p>
                </div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-plum-soft/60 flex items-center justify-center shrink-0">
                    <ion-icon name="call-outline" style="font-size:17px;color:#A2397B;"></ion-icon>
                </div>
                <div>
                    <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">No. HP</p>
                    <p class="text-plum-dark text-sm font-medium">{{ session('user')['no_hp'] ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="anim-up delay-3 mb-4">
            <button onclick="showLogoutModal()"
                    class="w-full flex items-center justify-center gap-3 bg-red-500 hover:bg-red-600 active:scale-95 text-white font-bold py-4 rounded-2xl shadow-md shadow-red-200 transition-all">
                <ion-icon name="log-out-outline" style="font-size:20px;"></ion-icon>
                <span class="text-sm tracking-wide">Logout</span>
            </button>
        </div>

        <div class="h-2"></div>
    </div>

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'profil'])

</div>
</div>

<!-- LOGOUT CONFIRM MODAL -->
<div id="logoutModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 hidden opacity-0 px-4 pb-8 sm:pb-0">
    <div id="logoutModalBox" class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl scale-90">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <ion-icon name="log-out-outline" style="font-size:32px;color:#ef4444;"></ion-icon>
            </div>
            <h3 class="text-plum-dark text-lg font-extrabold mb-1">Keluar dari Akun?</h3>
            <p class="text-plum-muted text-sm">Kamu perlu login ulang untuk mengakses aplikasi.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="hideLogoutModal()"
                    class="flex-1 py-3.5 rounded-2xl border-2 border-plum-soft text-plum font-bold text-sm active:bg-plum-soft transition-all">
                Batal
            </button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full py-3.5 rounded-2xl bg-red-500 text-white font-bold text-sm active:bg-red-600 transition-all">
                    Ya, Keluar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Clock
function updateClock() {
    const now = new Date();
    const el = document.getElementById('statusTime');
    if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
}
updateClock(); setInterval(updateClock, 30000);

// Logout modal
const modal    = document.getElementById('logoutModal');
const modalBox = document.getElementById('logoutModalBox');

function showLogoutModal() {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modalBox.style.transform = 'scale(1)';
    });
}
function hideLogoutModal() {
    modal.style.opacity = '0';
    modalBox.style.transform = 'scale(0.9)';
    setTimeout(() => modal.classList.add('hidden'), 200);
}
modal.addEventListener('click', (e) => { if (e.target === modal) hideLogoutModal(); });
</script>

@include('partials.auth-guard')

</body>
</html>