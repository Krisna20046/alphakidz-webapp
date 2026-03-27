<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Anak</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.29s; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .info-card {
            background: #FFFFFF;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        #deleteModal { transition:opacity .2s ease; }
        #deleteModalBox { transition:transform .3s cubic-bezier(0.34,1.56,0.64,1); }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">
<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('profil.data-anak') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Child Data Details</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5">Complete child data information</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">
        <div class="anim delay-2 flex flex-col items-center pt-1 pb-2">
            @if($anak['foto'] ?? null)
                <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}"
                     class="w-[94px] h-[94px] rounded-full object-cover border-4 border-[#EDE9FE] mb-3 shadow-[0_3px_10px_rgba(0,0,0,0.10)]"/>
            @else
                <div class="w-[94px] h-[94px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] flex items-center justify-center mb-3 shadow-[0_3px_10px_rgba(0,0,0,0.10)]">
                    <ion-icon name="happy-outline" style="font-size:42px;color:#8B46D3;"></ion-icon>
                </div>
            @endif
            <h2 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">{{ $anak['nama'] }}</h2>
            <div class="mt-2 bg-[#EFE9FB] px-4 py-1 rounded-full">
                <span class="text-[#8B46D3] text-[15px] font-bold">Child</span>
            </div>
        </div>

        <div class="anim delay-3 space-y-3">
            <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">Personal Information</p>

            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="calendar-outline" style="font-size:18px;color:#4F46E5;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Date Of Birth</p>
                    <p class="text-[#1E1B2E] text-[18px] font-extrabold leading-none mt-1">{{ $anak['tanggal_lahir'] }}</p>
                </div>
            </div>

            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                    <ion-icon name="{{ $anak['gender'] === 'L' ? 'male-outline' : 'female-outline' }}" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                    <p class="text-[#1E1B2E] text-[18px] font-extrabold leading-none mt-1">{{ $anak['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
            </div>
        </div>

        @php
            $hasMoreInformation = ($anak['catatan_khusus'] ?? null) || ($anak['alergi'] ?? null) || ($anak['hobi'] ?? null);
        @endphp
        @if($hasMoreInformation)
        <div class="anim delay-4 space-y-3">
            <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">More Information</p>

            @if($anak['catatan_khusus'] ?? null)
            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="create-outline" style="font-size:18px;color:#4F46E5;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Special Note</p>
                    <p class="text-[#1E1B2E] text-[18px] font-extrabold leading-none mt-1 break-words">{{ $anak['catatan_khusus'] }}</p>
                </div>
            </div>
            @endif

            @if($anak['alergi'] ?? null)
            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="warning-outline" style="font-size:18px;color:#F59E0B;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Allergies</p>
                    <p class="text-[#1E1B2E] text-[18px] font-extrabold leading-none mt-1 break-words">{{ $anak['alergi'] }}</p>
                </div>
            </div>
            @endif

            @if($anak['hobi'] ?? null)
            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="heart" style="font-size:18px;color:#EC4899;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Hobby</p>
                    <p class="text-[#1E1B2E] text-[18px] font-extrabold leading-none mt-1 break-words">{{ $anak['hobi'] }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="anim delay-4 space-y-3 pt-2">
            <a href="{{ route('profil.anak.ubah', $anak['id']) }}"
               class="w-full bg-gradient-to-r from-[#7C3AED] to-[#8B46D3] text-white font-extrabold py-4 rounded-[12px] shadow-[0_8px_24px_rgba(139,70,211,0.38)] flex items-center justify-center gap-2 text-[15px]">
                <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                <span class="leading-none">Update</span>
            </a>

            <button onclick="showDeleteModal()"
                    class="w-full bg-white text-[#D22F2F] font-extrabold py-4 rounded-[12px] flex items-center justify-center gap-2 text-[15px] shadow-[0_2px_10px_rgba(0,0,0,0.05)]">
                <ion-icon name="trash" style="font-size:18px;"></ion-icon>
                <span class="leading-none">Delete</span>
            </button>
        </div>
    </div>

    @include('partials.bottom-nav', ['active' => 'profil'])

</div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 hidden opacity-0 px-4 pb-8 sm:pb-0">
    <div id="deleteModalBox" class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl scale-90">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <ion-icon name="trash-outline" style="font-size:30px;color:#ef4444;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] text-lg font-extrabold mb-1">Hapus Data Anak?</h3>
            <p class="text-[#6B6589] text-sm leading-relaxed">Tindakan ini tidak dapat dibatalkan. Data anak <strong class="text-[#1E1B2E]">{{ $anak['nama'] }}</strong> akan dihapus permanen.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="hideDeleteModal()"
                    class="flex-1 py-3.5 rounded-2xl border-2 border-[#E9E3FB] text-[#8B46D3] font-bold text-sm">
                Batal
            </button>
            <form method="POST" action="{{ route('profil.anak.hapus', $anak['id']) }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" id="deleteSubmitBtn"
                        class="w-full py-3.5 rounded-2xl bg-red-500 text-white font-bold text-sm active:bg-red-600 transition-all flex items-center justify-center gap-2">
                    <span>Ya, Hapus</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function updateClock() {
    const el = document.getElementById('statusTime');
    if (el) {
        const n = new Date();
        el.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
    }
}
updateClock();
setInterval(updateClock, 30000);

const modal = document.getElementById('deleteModal');
const modalBox = document.getElementById('deleteModalBox');

function showDeleteModal() {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modalBox.style.transform = 'scale(1)';
    });
}

function hideDeleteModal() {
    modal.style.opacity = '0';
    modalBox.style.transform = 'scale(0.9)';
    setTimeout(() => modal.classList.add('hidden'), 200);
}
modal.addEventListener('click', (e) => {
    if (e.target === modal) hideDeleteModal();
});
</script>
</body>
</html>