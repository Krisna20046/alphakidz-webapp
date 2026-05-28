{{-- resources/views/konsultan/tugaskan-nanny-ubah.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ubah Penugasan</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.29s; }
        .delay-5 { animation-delay: 0.37s; }
        .delay-6 { animation-delay: 0.45s; }
        @keyframes floatEmpty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }
        @keyframes spin2 { to { transform: rotate(360deg); } }
        .spin { animation: spin2 0.8s linear infinite; }
        @keyframes sheetUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .sheet-anim { animation: sheetUp 0.25s ease forwards; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .shell-card { background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,243,255,0.97) 100%); border-radius: 30px 30px 0 0; box-shadow: 0 -10px 30px rgba(139, 70, 211, 0.08); }
        .field-card { background: rgba(255,255,255,0.84); border: 1px solid #d8caef; border-radius: 10px; transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease; }
        .field-card:focus-within, .field-card.active { border-color: #8B46D3; box-shadow: 0 0 0 3px rgba(139, 70, 211, 0.10); }
        .action-card { transition: transform 0.15s ease; }
        .action-card:active { transform: scale(0.985); }
        .status-chip { border: 1.5px solid #dacbf1; background: rgba(255,255,255,0.78); border-radius: 12px; transition: all 0.15s ease; }
        .status-chip.active { border-color: #8B46D3; background: #f4ebff; box-shadow: 0 8px 18px rgba(139, 70, 211, 0.12); }
        .anak-row { transition: border-color 0.15s, background 0.15s; }
        .anak-row.selected { border-color: #8B46D3; background: #F8F5FF; }
        .modal-overlay { background: rgba(30,11,60,0.5); backdrop-filter: blur(4px); }
        input[type="date"] { appearance: none; -webkit-appearance: none; }
        .search-wrap:focus-within { border-color: #8B46D3 !important; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">
<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center"><div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]"><div class="bg-white rounded-[1.5px] h-full"></div></div></div>
        </div>
    </div>
    <div class="anim delay-1 relative z-10 overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.14),_transparent_34%),linear-gradient(135deg,_#A855F7_0%,_#8B46D3_40%,_#9F58F8_100%)] px-[20px] pt-[54px] pb-[102px]">
        <div class="absolute inset-0 opacity-25 bg-[url('/assets/bg-texture.png')] bg-cover bg-center"></div>
        <div class="relative z-10 flex items-start gap-3">
            <a href="{{ url('/konsultan/tugaskan-nanny') }}" class="mt-0.5 w-10 h-10 rounded-full bg-white/20 border border-white/25 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div class="min-w-0">
                <h1 class="text-white text-[18px] font-extrabold leading-tight">Edit Assignment</h1>
                <p class="text-white/70 text-[11px] font-semibold mt-0.5 leading-[1.35]">Change Nanny Assignment Data</p>
            </div>
        </div>
    </div>
    <div class="shell-card flex-1 overflow-y-auto px-[16px] pt-[18px] pb-28 -mt-[58px] relative z-20 hide-scrollbar">
        <div id="loadingSkeleton" class="space-y-4 anim delay-2">
            <div class="flex flex-col items-center pt-2 pb-3">
                <div class="w-[72px] h-[72px] rounded-full bg-[#EDE9FE] animate-pulse mb-3"></div>
                <div class="h-4 bg-[#EDE9FE] rounded-lg animate-pulse w-28 mb-2"></div>
                <div class="h-px w-full bg-[#E7DCF8] mt-4"></div>
            </div>
            <div class="field-card h-[62px] animate-pulse"></div>
            <div class="field-card h-[62px] animate-pulse"></div>
            <div class="grid grid-cols-4 gap-2">
                <div class="field-card h-[78px] animate-pulse"></div>
                <div class="field-card h-[78px] animate-pulse"></div>
                <div class="field-card h-[78px] animate-pulse"></div>
                <div class="field-card h-[78px] animate-pulse"></div>
            </div>
        </div>
        <div id="errorState" class="hidden flex-col items-center justify-center pt-14 px-6 text-center">
            <div class="float-anim w-24 h-24 rounded-full bg-[#FEE2E2] flex items-center justify-center mb-5"><ion-icon name="alert-circle" style="font-size:48px;color:#DC2626;"></ion-icon></div>
            <h3 class="text-[#1E1B2E] font-extrabold text-lg mb-2">Data tidak ditemukan</h3>
            <p class="text-[#8B86A5] text-sm mb-6">Data penugasan yang Anda cari tidak tersedia.</p>
            <a href="{{ url('/konsultan/tugaskan-nanny') }}" class="bg-[#8B46D3] text-white text-sm font-extrabold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">Kembali</a>
        </div>
        <div id="formSection" class="hidden space-y-4">
            <div id="nannyCard" class="anim delay-2 px-1"></div>
            <div class="anim delay-3 px-1 pt-1">
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5"><span class="text-[#1E1B2E] text-[12px] font-bold">Choose An Employer</span><span class="text-[#EF4444] text-[12px] font-black">*</span></div>
                        <button type="button" onclick="openMajikanModal()" class="action-card field-card w-full px-3 py-2.5 flex items-center justify-between text-left">
                            <div id="majikanDisplay" class="min-w-0"><span class="text-[#8B86A5] text-[12px] font-semibold">Pilih Majikan</span></div>
                            <ion-icon name="chevron-down" style="font-size:16px;color:#7C748F;"></ion-icon>
                        </button>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5"><span class="text-[#1E1B2E] text-[12px] font-bold">Choose A Child</span><span class="text-[#EF4444] text-[12px] font-black">*</span></div>
                        <button type="button" onclick="openAnakModal()" class="action-card field-card w-full px-3 py-2.5 flex items-center justify-between text-left">
                            <div id="anakDisplay" class="min-w-0"><span class="text-[#8B86A5] text-[12px] font-semibold">Pilih Anak</span></div>
                            <ion-icon name="chevron-down" style="font-size:16px;color:#7C748F;"></ion-icon>
                        </button>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 mb-2"><span class="text-[#1E1B2E] text-[12px] font-bold">Assignment Status</span><span class="text-[#EF4444] text-[12px] font-black">*</span></div>
                        <div id="statusOptions" class="grid grid-cols-4 gap-2"></div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5"><span class="text-[#1E1B2E] text-[12px] font-bold">Assignment Period</span><span class="text-[#EF4444] text-[12px] font-black">*</span></div>
                        <div class="space-y-2.5">
                            <label class="field-card block px-3 py-2.5 cursor-pointer">
                                <span class="block text-[9px] font-bold text-[#9C90B8] mb-1">Select Start Date</span>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <ion-icon name="calendar-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                                        <input type="date" id="tanggalMulai" class="w-full text-[#4B415F] font-extrabold text-[13px] bg-transparent border-none outline-none cursor-pointer" onchange="autoSelesai(); refreshDateFieldState();">
                                    </div>
                                    <ion-icon name="chevron-down" style="font-size:15px;color:#7C748F;"></ion-icon>
                                </div>
                            </label>
                            <label class="field-card block px-3 py-2.5 cursor-pointer">
                                <span class="block text-[9px] font-bold text-[#9C90B8] mb-1">Select End Date</span>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <ion-icon name="calendar-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                                        <input type="date" id="tanggalSelesai" class="w-full text-[#4B415F] font-extrabold text-[13px] bg-transparent border-none outline-none cursor-pointer" onchange="refreshDateFieldState();">
                                    </div>
                                    <ion-icon name="chevron-down" style="font-size:15px;color:#7C748F;"></ion-icon>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5"><span class="text-[#1E1B2E] text-[12px] font-bold">Notes</span><span class="text-[#8B86A5] text-[11px] font-semibold">(Optional)</span></div>
                        <div class="field-card overflow-hidden">
                            <textarea id="catatanInput" rows="4" placeholder="Enter your notes..." class="w-full px-3 py-3 text-[13px] text-[#1E1B2E] placeholder-[#B7AACC] bg-transparent resize-none focus:outline-none leading-relaxed font-semibold"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="anim delay-6 px-1 pt-2 pb-2">
                <button type="button" id="btnSubmit" onclick="openConfirmModal()" class="w-full flex items-center justify-center gap-2 h-[44px] rounded-[8px] font-extrabold text-[13px] bg-[#8B46D3] text-white shadow-[0_8px_18px_rgba(139,70,211,0.32)] active:scale-[0.98] transition-transform">
                    <ion-icon name="save-outline" style="font-size:16px;"></ion-icon>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
    </div>
    <div id="modalMajikan" class="hidden fixed inset-0 z-50 flex items-end justify-center sm:items-center">
        <div class="modal-overlay absolute inset-0" onclick="closeMajikanModal()"></div>
        <div class="sheet-anim relative bg-white w-full sm:w-[390px] rounded-t-[28px] sm:rounded-[24px] max-h-[85vh] flex flex-col shadow-2xl z-10 pb-16">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F0EDF8] shrink-0">
                <div class="flex items-center gap-2"><ion-icon name="briefcase-outline" style="font-size:16px;color:#8B46D3;"></ion-icon><span class="text-[#1E1B2E] font-extrabold text-[15px]">Pilih Majikan</span></div>
                <button onclick="closeMajikanModal()" class="w-8 h-8 rounded-full bg-[#EDE9FE] flex items-center justify-center"><ion-icon name="close" style="font-size:15px;color:#8B46D3;"></ion-icon></button>
            </div>
            <div class="px-4 py-3 border-b border-[#F0EDF8] shrink-0">
                <div class="search-wrap flex items-center bg-[#F8F8FB] rounded-xl border border-[#ECEAF4] px-3 gap-2">
                    <ion-icon name="search" style="font-size:15px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                    <input type="text" id="searchMajikan" class="flex-1 py-2.5 text-[13px] text-[#1E1B2E] bg-transparent focus:outline-none placeholder-[#C4B5FD] font-semibold" placeholder="Cari nama atau email..." oninput="filterMajikan(this.value)">
                </div>
            </div>
            <div id="majikanListModal" class="overflow-y-auto hide-scrollbar flex-1 px-4 py-3 space-y-2"></div>
        </div>
    </div>
    <div id="modalAnak" class="hidden fixed inset-0 z-50 flex items-end justify-center sm:items-center">
        <div class="modal-overlay absolute inset-0" onclick="closeAnakModal()"></div>
        <div class="sheet-anim relative bg-white w-full sm:w-[390px] rounded-t-[28px] sm:rounded-[24px] max-h-[85vh] flex flex-col shadow-2xl z-10 pb-16">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F0EDF8] shrink-0">
                <div class="flex items-center gap-2"><ion-icon name="happy-outline" style="font-size:16px;color:#8B46D3;"></ion-icon><span class="text-[#1E1B2E] font-extrabold text-[15px]">Pilih Anak</span></div>
                <button onclick="closeAnakModal()" class="w-8 h-8 rounded-full bg-[#EDE9FE] flex items-center justify-center"><ion-icon name="close" style="font-size:15px;color:#8B46D3;"></ion-icon></button>
            </div>
            <div id="anakListModal" class="overflow-y-auto hide-scrollbar flex-1 px-4 py-3 space-y-2"></div>
            <div class="px-4 py-4 border-t border-[#F0EDF8] bg-[#F8F7FF] shrink-0 flex items-center justify-between">
                <div class="flex items-center gap-1.5"><ion-icon name="checkmark-circle" style="font-size:15px;color:#8B46D3;"></ion-icon><span class="text-[#8B46D3] text-sm font-extrabold" id="anakCountLabel">0 anak terpilih</span></div>
                <button onclick="closeAnakModal()" class="bg-[#8B46D3] text-white text-[13px] font-extrabold px-5 py-2.5 rounded-xl shadow-[0_4px_12px_rgba(139,70,211,0.3)]">Konfirmasi</button>
            </div>
        </div>
    </div>
    <div id="modalConfirm" class="hidden fixed inset-0 z-50 flex items-end justify-center sm:items-center">
        <div class="modal-overlay absolute inset-0" onclick="closeConfirmModal()"></div>
        <div class="sheet-anim relative bg-white w-full sm:w-[390px] rounded-t-[28px] sm:rounded-[24px] shadow-2xl z-10 p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-[#EDE9FE] flex items-center justify-center mx-auto mb-4"><ion-icon name="help-circle" style="font-size:36px;color:#8B46D3;"></ion-icon></div>
            <h3 class="text-[#1E1B2E] font-extrabold text-lg mb-2">Konfirmasi Perubahan</h3>
            <p class="text-[#8B86A5] text-[13px] leading-relaxed mb-6">Apakah Anda yakin ingin menyimpan perubahan data penugasan ini?</p>
            <div class="flex gap-3">
                <button onclick="closeConfirmModal()" class="flex-1 h-[48px] rounded-2xl border border-[#ECEAF4] bg-[#F8F8FB] text-[#8B86A5] font-extrabold text-[13px] active:scale-[0.97] transition-transform">Batal</button>
                <button onclick="handleSubmit()" class="flex-1 h-[48px] rounded-2xl bg-[#8B46D3] text-white font-extrabold text-[13px] shadow-[0_4px_12px_rgba(139,70,211,0.35)] active:scale-[0.97] transition-transform">Ya, Simpan</button>
            </div>
        </div>
    </div>
    <div id="modalResult" class="hidden fixed inset-0 z-50 flex items-end justify-center sm:items-center">
        <div class="modal-overlay absolute inset-0"></div>
        <div class="sheet-anim relative bg-white w-full sm:w-[390px] rounded-t-[28px] sm:rounded-[24px] shadow-2xl z-10 p-6 text-center">
            <div id="resultIcon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"></div>
            <h3 id="resultTitle" class="text-[#1E1B2E] font-extrabold text-lg mb-2"></h3>
            <p id="resultMsg" class="text-[#8B86A5] text-[13px] leading-relaxed mb-6"></p>
            <button id="resultBtn" class="w-full h-[50px] bg-[#8B46D3] text-white font-extrabold rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]"></button>
        </div>
    </div>
    @include('partials.bottom-nav', ['active' => 'home'])
</div>
</div>
<script>
const API_BASE_URL  = '{{ env("API_BASE_URL") }}';
const API_TOKEN     = '{{ session("token") }}';
const parts        = location.pathname.split('/');
const assignmentId = parts[parts.indexOf('assignment') + 1];
const STATUS_OPTS = [
    { value:'active',    label:'Active',  icon:'time',             iconColor:'#4F46E5', iconBg:'#EAE8FF' },
    { value:'pending',   label:'Pending', icon:'hourglass',        iconColor:'#F59E0B', iconBg:'#FEF3C7' },
    { value:'completed', label:'Done',    icon:'checkmark-circle', iconColor:'#8B46D3', iconBg:'#EDE9FE' },
    { value:'cancelled', label:'Cancel',  icon:'ban',              iconColor:'#EF4444', iconBg:'#FEE2E2' },
];
let assignmentData = null;
let allMajikan = [];
let selectedMajikan = null;
let anakList = [];
let selectedAnak = {};
let currentStatus = 'active';
let nannyId = null;
async function loadAssignment() {
    try {
        const fd = new FormData();
        fd.append('id_assignment', assignmentId);
        const res = await fetch(`${API_BASE_URL}/nanny-assignment-detail`, { method:'POST', headers:{ 'Accept':'application/json', 'Authorization':`Bearer ${API_TOKEN}` }, body:fd });
        const json = await res.json();
        if (json.status === 'success' && json.assignment) { assignmentData = json.assignment; populateForm(assignmentData); }
        else showError();
    } catch (e) { showError(); }
}
function showError() {
    document.getElementById('loadingSkeleton').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorState').classList.add('flex');
}
function populateForm(a) {
    document.getElementById('loadingSkeleton').classList.add('hidden');
    document.getElementById('formSection').classList.remove('hidden');
    nannyId = a.id_nanny;
    const foto = a.nanny_foto || null;
    const avatar = foto ? `<img src="${foto}" class="w-[62px] h-[62px] rounded-full object-cover border-[3px] border-[#E8DDF9] shadow-[0_4px_10px_rgba(139,70,211,0.18)]">` : fallbackAvatarHtml();
    document.getElementById('nannyCard').innerHTML = `<div class="flex flex-col items-center text-center pt-1 pb-2"><div class="mb-3">${avatar}</div><h2 class="text-[#1E1B2E] font-extrabold text-[15px] leading-tight">${a.nanny_name || '-'}</h2><div class="h-px w-full bg-[#E7DCF8] mt-4"></div><div class="w-full text-left pt-4 space-y-3">${infoRow('document-text-outline', '#8B46D3', '#EDE9FE', 'Assignment ID', String(assignmentId || '-'))}${infoRow('mail-outline', '#EC4899', '#FDE7EF', 'Email', a.nanny_email || '-')}${infoRow('call-outline', '#4F46E5', '#E8ECFF', 'Phone Number', a.nanny_no_hp || a.no_hp || '-')}</div></div>`;
    selectedMajikan = { id: a.id_majikan, name: a.majikan_name || '-', email: a.majikan_email || '' };
    document.getElementById('majikanDisplay').innerHTML = selectedMajikan.id ? `<div class="min-w-0"><p class="text-[#A78BC6] text-[10px] font-bold truncate">${selectedMajikan.name}</p><p class="text-[#1E1B2E] text-[12px] font-extrabold truncate">${selectedMajikan.email || '-'}</p></div>` : `<span class="text-[#8B86A5] text-[12px] font-semibold">Pilih Majikan</span>`;
    document.getElementById('tanggalMulai').value = a.tanggal_mulai || '';
    document.getElementById('tanggalSelesai').value = a.tanggal_selesai || '';
    refreshDateFieldState();
    currentStatus = a.status || 'active';
    renderStatusOptions();
    document.getElementById('catatanInput').value = a.catatan || '';
    if (a.anak && Array.isArray(a.anak)) { a.anak.forEach(c => { selectedAnak[c.id_anak] = { id: c.id_anak, nama: c.anak_name || c.nama || '' }; }); }
    updateAnakDisplay();
    updateAnakCountLabel();
    loadAnak(a.id_majikan);
}
function fallbackAvatarHtml() { return `<div class="w-[80px] h-[80px] rounded-full bg-[#F3EEFC] border-[3px] border-[#E8DDF9] shadow-[0_4px_10px_rgba(139,70,211,0.12)] flex items-center justify-center"><ion-icon name="person" style="font-size:28px;color:#8B46D3;"></ion-icon></div>`; }
function infoRow(icon, color, bg, label, value) { return `<div class="flex items-start gap-2.5"><div class="w-8 h-8 rounded-[5px] flex items-center justify-center flex-shrink-0" style="background:${bg};"><ion-icon name="${icon}" style="font-size:15px;color:${color};"></ion-icon></div><div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">${label}</p><p class="text-[#1E1B2E] text-[12px] font-extrabold break-words">${value || '-'}</p></div></div>`; }
function autoSelesai() { const v = document.getElementById('tanggalMulai').value; const s = document.getElementById('tanggalSelesai').value; if (!v || s > v) return; const d = new Date(v); d.setFullYear(d.getFullYear() + 1); document.getElementById('tanggalSelesai').value = d.toISOString().split('T')[0]; }
function refreshDateFieldState() { ['tanggalMulai', 'tanggalSelesai'].forEach(id => { const input = document.getElementById(id); const wrapper = input.closest('.field-card'); if (wrapper) wrapper.classList.toggle('active', !!input.value); }); }
function openMajikanModal() { document.getElementById('modalMajikan').classList.remove('hidden'); loadMajikan(); }
function closeMajikanModal() { document.getElementById('modalMajikan').classList.add('hidden'); document.getElementById('searchMajikan').value = ''; }
async function loadMajikan() {
    const list = document.getElementById('majikanListModal');
    list.innerHTML = `<p class="text-center text-[#8B86A5] text-sm py-8 font-semibold">Memuat...</p>`;
    try {
        const fd = new FormData(); fd.append('search', '');
        const res = await fetch(`${API_BASE_URL}/user-majikan`, { method:'POST', headers:{ 'Accept':'application/json', 'Authorization':`Bearer ${API_TOKEN}` }, body:fd });
        const json = await res.json(); allMajikan = json.data || []; renderMajikan(allMajikan);
    } catch (e) { list.innerHTML = `<p class="text-center text-red-500 text-sm py-8 font-semibold">Gagal memuat</p>`; }
}
function filterMajikan(q) { const query = q.toLowerCase(); renderMajikan(allMajikan.filter(m => (m.name || '').toLowerCase().includes(query) || (m.email || '').toLowerCase().includes(query))); }
function renderMajikan(items) {
    const list = document.getElementById('majikanListModal');
    if (!items.length) { list.innerHTML = `<p class="text-center text-[#8B86A5] text-sm py-8 font-semibold">Tidak ditemukan</p>`; return; }
    list.innerHTML = items.map(m => `<button type="button" onclick="selectMajikan(${m.id}, '${esc(m.name)}', '${esc(m.email || '')}')" class="w-full flex items-center gap-3 p-3.5 rounded-2xl border border-[#ECEAF4] bg-[#F8F8FB] text-left active:bg-[#EDE9FE] transition-colors"><div class="w-9 h-9 rounded-full bg-[#EDE9FE] flex items-center justify-center flex-shrink-0"><ion-icon name="person-circle" style="font-size:20px;color:#8B46D3;"></ion-icon></div><div class="flex-1 min-w-0"><p class="text-[#1E1B2E] font-extrabold text-[13px] truncate">${m.name}</p><p class="text-[#8B86A5] text-[11px] font-semibold truncate">${m.email || ''}</p></div><div class="w-6 h-6 rounded-full bg-white border border-[#ECEAF4] flex items-center justify-center flex-shrink-0"><ion-icon name="chevron-forward" style="font-size:12px;color:#8B46D3;"></ion-icon></div></button>`).join('');
}
function selectMajikan(id, name, email) {
    selectedMajikan = { id, name, email };
    selectedAnak = {};
    document.getElementById('majikanDisplay').innerHTML = `<div class="min-w-0"><p class="text-[#A78BC6] text-[10px] font-bold truncate">${name}</p><p class="text-[#1E1B2E] text-[12px] font-extrabold truncate">${email || '-'}</p></div>`;
    updateAnakDisplay(); updateAnakCountLabel(); closeMajikanModal(); loadAnak(id);
}
function openAnakModal() { document.getElementById('modalAnak').classList.remove('hidden'); renderAnak(); }
function closeAnakModal() { document.getElementById('modalAnak').classList.add('hidden'); }
async function loadAnak(idMajikan) {
    try {
        const fd = new FormData(); fd.append('id_majikan', idMajikan);
        const res = await fetch(`${API_BASE_URL}/user-anak-for-konsultan`, { method:'POST', headers:{ 'Accept':'application/json', 'Authorization':`Bearer ${API_TOKEN}` }, body:fd });
        const json = await res.json(); anakList = json.data || [];
    } catch (e) { anakList = []; }
}
function renderAnak() {
    const list = document.getElementById('anakListModal');
    if (!anakList.length) { list.innerHTML = `<p class="text-center text-[#8B86A5] text-sm py-8 font-semibold">Belum ada data anak</p>`; return; }
    list.innerHTML = anakList.map(a => {
        const sel = !!selectedAnak[a.id];
        const today = new Date(); const b = new Date(a.tanggal_lahir); let age = today.getFullYear() - b.getFullYear(); if (today.getMonth() - b.getMonth() < 0 || (today.getMonth() === b.getMonth() && today.getDate() < b.getDate())) age--;
        return `<button type="button" onclick="toggleAnak(${a.id}, '${esc(a.nama || '')}')" id="anakRow_${a.id}" class="anak-row w-full flex items-center gap-3 p-3.5 rounded-2xl border-2 text-left ${sel ? 'selected border-[#8B46D3] bg-[#F8F5FF]' : 'border-[#ECEAF4] bg-[#F8F8FB]'}"><div id="cb_${a.id}" class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 ${sel ? 'bg-[#8B46D3] border-[#8B46D3]' : 'border-[#C4B5FD] bg-white'}">${sel ? '<ion-icon name="checkmark" style="font-size:11px;color:white;"></ion-icon>' : ''}</div><div class="flex-1 min-w-0"><p class="text-[#1E1B2E] font-extrabold text-[13px]">${a.nama || '-'}</p><div class="flex gap-3 mt-0.5"><span class="text-[#8B86A5] text-[11px] font-semibold">${a.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</span><span class="text-[#8B86A5] text-[11px] font-semibold">${age} tahun</span></div></div></button>`;
    }).join('');
}
function toggleAnak(id, nama) {
    if (selectedAnak[id]) delete selectedAnak[id]; else selectedAnak[id] = { id, nama };
    const sel = !!selectedAnak[id]; const row = document.getElementById(`anakRow_${id}`); const cb = document.getElementById(`cb_${id}`);
    row.className = `anak-row w-full flex items-center gap-3 p-3.5 rounded-2xl border-2 text-left ${sel ? 'selected border-[#8B46D3] bg-[#F8F5FF]' : 'border-[#ECEAF4] bg-[#F8F8FB]'}`;
    cb.className = `w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 ${sel ? 'bg-[#8B46D3] border-[#8B46D3]' : 'border-[#C4B5FD] bg-white'}`;
    cb.innerHTML = sel ? '<ion-icon name="checkmark" style="font-size:11px;color:white;"></ion-icon>' : '';
    updateAnakDisplay(); updateAnakCountLabel();
}
function updateAnakDisplay() {
    const count = Object.keys(selectedAnak).length; const names = Object.values(selectedAnak).map(a => a.nama).join(', ');
    document.getElementById('anakDisplay').innerHTML = count > 0 ? `<div class="min-w-0"><p class="text-[#A78BC6] text-[10px] font-bold truncate">${count} Selected Child${count > 1 ? 'ren' : ''}</p><p class="text-[#1E1B2E] text-[12px] font-extrabold truncate">${names}</p></div>` : `<span class="text-[#8B86A5] text-[12px] font-semibold">Pilih Anak</span>`;
}
function updateAnakCountLabel() { document.getElementById('anakCountLabel').textContent = `${Object.keys(selectedAnak).length} anak terpilih`; }
function renderStatusOptions() {
    document.getElementById('statusOptions').innerHTML = STATUS_OPTS.map(o => `<button type="button" onclick="selectStatus('${o.value}')" class="status-chip ${currentStatus === o.value ? 'active' : ''} flex flex-col items-center justify-center gap-1.5 h-[74px] px-1 text-center"><div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:${o.iconBg};"><ion-icon name="${o.icon}" style="font-size:16px;color:${o.iconColor};"></ion-icon></div><span class="text-[#5B4E73] text-[11px] font-bold leading-tight">${o.label}</span></button>`).join('');
}
function selectStatus(value) { currentStatus = value; renderStatusOptions(); }
function openConfirmModal() { document.getElementById('modalConfirm').classList.remove('hidden'); }
function closeConfirmModal() { document.getElementById('modalConfirm').classList.add('hidden'); }
async function handleSubmit() {
    closeConfirmModal();
    const mulai = document.getElementById('tanggalMulai').value;
    const selesai = document.getElementById('tanggalSelesai').value;
    const catatan = document.getElementById('catatanInput').value;
    if (!mulai || !selesai) { showResult(false, 'Data kurang', 'Isi tanggal mulai dan selesai.'); return; }
    if (mulai >= selesai) { showResult(false, 'Tanggal tidak valid', 'Tanggal selesai harus setelah tanggal mulai.'); return; }
    if (!selectedMajikan) { showResult(false, 'Data kurang', 'Pilih majikan terlebih dahulu.'); return; }
    if (!Object.keys(selectedAnak).length) { showResult(false, 'Data kurang', 'Pilih minimal satu anak.'); return; }
    const btn = document.getElementById('btnSubmit'); btn.innerHTML = `<ion-icon name="sync" style="font-size:16px;" class="spin"></ion-icon><span>Menyimpan...</span>`; btn.disabled = true;
    try {
        const fd = new FormData();
        fd.append('id_assignment', assignmentId); fd.append('id_nanny', nannyId); fd.append('id_majikan', selectedMajikan.id); Object.keys(selectedAnak).forEach(id => fd.append('id_anak[]', id)); fd.append('tanggal_mulai', mulai); fd.append('tanggal_selesai', selesai); fd.append('status', currentStatus); fd.append('catatan', catatan || 'Assignment By Konsultan');
        const res = await fetch(`${API_BASE_URL}/nanny-assignment-update`, { method:'POST', headers:{ 'Accept':'application/json', 'Authorization':`Bearer ${API_TOKEN}` }, body:fd });
        const json = await res.json();
        if (json.status === 'success') showResult(true, 'Berhasil!', 'Penugasan berhasil diubah.', () => { location.href = '{{ url("/konsultan/tugaskan-nanny") }}'; });
        else { showResult(false, 'Gagal', json.message || 'Gagal mengubah penugasan.'); resetBtn(); }
    } catch (e) { showResult(false, 'Kesalahan', 'Terjadi kesalahan jaringan.'); resetBtn(); }
}
function resetBtn() { const btn = document.getElementById('btnSubmit'); btn.disabled = false; btn.innerHTML = `<ion-icon name="save-outline" style="font-size:16px;"></ion-icon><span>Save Changes</span>`; }
function showResult(success, title, msg, onOk = null) {
    document.getElementById('resultIcon').className = `w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${success ? 'bg-[#DCFCE7]' : 'bg-[#FEE2E2]'}`;
    document.getElementById('resultIcon').innerHTML = `<ion-icon name="${success ? 'checkmark-circle' : 'alert-circle'}" style="font-size:36px;color:${success ? '#16A34A' : '#DC2626'};"></ion-icon>`;
    document.getElementById('resultTitle').textContent = title; document.getElementById('resultMsg').textContent = msg;
    const rbtn = document.getElementById('resultBtn'); rbtn.textContent = success ? 'Kembali ke Daftar' : 'Coba Lagi'; rbtn.onclick = onOk || (() => document.getElementById('modalResult').classList.add('hidden')); document.getElementById('modalResult').classList.remove('hidden');
}
function esc(s) { return String(s).replace(/'/g, "\\'").replace(/"/g, '&quot;'); }
(function () { const el = document.getElementById('statusTime'); function tick() { const n = new Date(); if (el) el.textContent = `${String(n.getHours()).padStart(2, '0')}:${String(n.getMinutes()).padStart(2, '0')}`; } tick(); setInterval(tick, 30000); })();
loadAssignment();
</script>
@include('partials.auth-guard')
</body>
</html>
