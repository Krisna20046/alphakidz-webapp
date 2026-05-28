{{-- resources/views/konsultan/tugaskan-nanny.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tugaskan Nanny</title>
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
        .anim         { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1      { animation-delay: 0.05s; }
        .delay-2      { animation-delay: 0.13s; }
        .delay-3      { animation-delay: 0.21s; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        @keyframes spin2 { to { transform: rotate(360deg); } }
        .spin { animation: spin2 1s linear infinite; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .section-card {
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
        }
        .nanny-card {
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
            transition: transform 0.15s ease, box-shadow 0.15s, opacity 0.15s;
        }
        .nanny-card:active { transform: scale(0.98); opacity: 0.75; }
        .detail-item {
            background: #F8F8FB;
            border: 1px solid #ECEAF4;
            border-radius: 10px;
        }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    {{-- STATUS BAR --}}
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
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

    {{-- HEADER --}}
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-start gap-3 relative z-10">
            <a href="{{ route('dashboard') }}"
               class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Tugaskan Nanny</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Kelola penugasan nanny<br>di bawah pengawasan Anda</p>
            </div>
        </div>
    </div>

    {{-- SCROLLABLE BODY --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

        {{-- Loading State --}}
        <div id="loadingState" class="flex flex-col items-center justify-center pt-20">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4 spin">
                <ion-icon name="sync" style="font-size:26px;color:#8B46D3;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-sm font-semibold">Memuat data nanny...</p>
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden flex-col items-center pt-16 pb-10 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon name="people-outline" style="font-size:52px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-extrabold text-lg mb-2">Belum ada nanny</h3>
            <p class="text-[#8B86A5] text-sm text-center leading-relaxed">Anda belum memiliki nanny yang terdaftar</p>
        </div>

        {{-- List State --}}
        <div id="listState" class="hidden space-y-4">
            <div class="anim delay-2 flex items-center justify-between">
                <h2 class="text-[#1E1B2E] font-extrabold text-[15px]">Daftar Nanny</h2>
                <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                    <span class="text-[#8B46D3] text-xs font-extrabold" id="nannyCount">0</span>
                </div>
            </div>
            <div id="nannyCards" class="space-y-3 anim delay-3"></div>
        </div>

        <div class="h-6"></div>
    </div>

    @include('partials.bottom-nav', ['active' => 'home'])
</div>
</div>

<script>
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';

const STATUS_MAP = {
    active:    { label:'Aktif Bertugas',      icon:'time',           bg:'bg-blue-100',   text:'text-blue-600' },
    pending:   { label:'Menunggu Konfirmasi', icon:'hourglass',      bg:'bg-yellow-100', text:'text-yellow-700' },
    completed: { label:'Tugas Selesai',       icon:'checkmark-done', bg:'bg-gray-100',   text:'text-gray-600' },
    cancelled: { label:'Dibatalkan',          icon:'close-circle',   bg:'bg-red-100',    text:'text-red-600' },
    tersedia:  { label:'Tersedia',            icon:'checkmark-circle',bg:'bg-green-100', text:'text-green-700' },
};

function avatarHtml(foto) {
    if (foto) return `<img src="${foto}" class="w-[52px] h-[52px] rounded-full object-cover border-[3px] border-[#EDE9FE] flex-shrink-0" onerror="this.outerHTML='<div class=\\'w-[52px] h-[52px] rounded-full bg-[#F3F0FD] border-[3px] border-[#EDE9FE] flex items-center justify-center flex-shrink-0\\'><ion-icon name=\\'person\\' style=\\'font-size:22px;color:#8B46D3;\\'></ion-icon></div>'">`;
    return `<div class="w-[52px] h-[52px] rounded-full bg-[#F3F0FD] border-[3px] border-[#EDE9FE] flex items-center justify-center flex-shrink-0"><ion-icon name="person" style="font-size:22px;color:#8B46D3;"></ion-icon></div>`;
}

function cardHtml(item, i) {
    const s    = item.is_assigned ? (STATUS_MAP[item.assignment_status] || STATUS_MAP.tersedia) : STATUS_MAP.tersedia;
    const href = !item.is_assigned
        ? `{{ url('/konsultan/tugaskan-nanny') }}/${item.id}/tambah`
        : `{{ url('/konsultan/tugaskan-nanny/assignment') }}/${item.id_assignment}/ubah`;

    return `
    <a href="${href}" class="nanny-card block" style="animation:slideUp .3s ease ${i * .06}s both;opacity:0;">
        {{-- Top: avatar + name + status --}}
        <div class="flex items-center gap-3 p-4 border-b border-[#F0EDF8]">
            ${avatarHtml(item.foto)}
            <div class="flex-1 min-w-0">
                <p class="text-[#1E1B2E] font-extrabold text-[14px] truncate mb-1.5">${item.name || '-'}</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold ${s.bg} ${s.text}">
                    <ion-icon name="${s.icon}" style="font-size:11px;"></ion-icon>${s.label}
                </span>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#EDE9FE] flex items-center justify-center flex-shrink-0">
                <ion-icon name="chevron-forward" style="font-size:14px;color:#8B46D3;"></ion-icon>
            </div>
        </div>
        {{-- Bottom: detail rows --}}
        <div class="px-4 py-3 space-y-2">
            <div class="detail-item px-3 py-2 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-[7px] bg-[#EFE9FB] flex items-center justify-center flex-shrink-0">
                    <ion-icon name="person-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">Gender</p>
                    <p class="text-[#1E1B2E] text-[12px] font-extrabold">${item.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-[7px] bg-[#FDE8EF] flex items-center justify-center flex-shrink-0">
                    <ion-icon name="mail-outline" style="font-size:13px;color:#EC4899;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">Email</p>
                    <p class="text-[#1E1B2E] text-[12px] font-extrabold truncate">${item.email || '-'}</p>
                </div>
            </div>
            ${item.id_assignment ? `
            <div class="detail-item px-3 py-2 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-[7px] bg-[#EDE9FE] flex items-center justify-center flex-shrink-0">
                    <ion-icon name="document-text-outline" style="font-size:13px;color:#4F46E5;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">Assignment ID</p>
                    <p class="text-[#1E1B2E] text-[12px] font-extrabold">#${item.id_assignment}</p>
                </div>
            </div>` : ''}
            ${item.catatan ? `
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2 flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-[7px] bg-[#FEF3E2] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <ion-icon name="document-text" style="font-size:13px;color:#F59E0B;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px] mb-0.5">Catatan</p>
                    <p class="text-[#8B86A5] text-[11px] font-semibold italic">"${item.catatan}"</p>
                </div>
            </div>` : ''}
        </div>
        <div class="px-4 py-2.5 border-t border-[#F0EDF8] flex items-center justify-end">
            <span class="text-[#8B86A5] text-[11px] font-semibold italic">${!item.is_assigned ? 'Tap untuk tugaskan' : 'Tap untuk lihat detail'}</span>
        </div>
    </a>`;
}

async function loadNannies() {
    try {
        const res  = await fetch(`${API_BASE_URL}/konsultan-nanny`, { headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${API_TOKEN}` } });
        const json = await res.json();
        const data = json.status === 'success' && Array.isArray(json.data) ? json.data : [];
        document.getElementById('loadingState').classList.add('hidden');
        if (!data.length) {
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('flex');
            return;
        }
        document.getElementById('nannyCount').textContent = data.length;
        document.getElementById('nannyCards').innerHTML   = data.map((item, i) => cardHtml(item, i)).join('');
        document.getElementById('listState').classList.remove('hidden');
    } catch (e) {
        document.getElementById('loadingState').innerHTML = `
        <div class="text-center px-8">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <ion-icon name="alert-circle" style="font-size:32px;color:#DC2626;"></ion-icon>
            </div>
            <p class="text-red-600 font-bold mb-3 text-sm">Gagal memuat data</p>
            <button onclick="loadNannies()" class="bg-[#8B46D3] text-white text-sm font-bold px-6 py-2.5 rounded-2xl shadow-[0_4px_12px_rgba(139,70,211,0.35)]">Coba lagi</button>
        </div>`;
    }
}

(function () {
    const el = document.getElementById('statusTime');
    function tick() { const n = new Date(); if (el) el.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`; }
    tick(); setInterval(tick, 30000);
})();

loadNannies();
</script>
@include('partials.auth-guard')
</body>
</html>