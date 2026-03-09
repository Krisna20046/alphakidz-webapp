{{-- resources/views/admin/diary-anak-list.blade.php --}}
{{-- Route: GET /admin/diary/{id_nanny}/anak  (bawa id_nanny via URL segment) --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pilih Anak – Diary Admin</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B', accent:'#B895C8' } },
                    fontFamily: { sans: ['Plus Jakarta Sans','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }
        @media (min-width: 640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; position:relative; }
        }
        @media (max-width: 639px) { .phone-wrapper{min-height:100vh;} .phone-frame{min-height:100vh;} }
        .header-bg { background: linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%); }
        @keyframes slideUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
        .anim-up    { animation:slideUp .35s ease forwards; }
        .anim-up.d1 { animation-delay:.05s; opacity:0; }
        .anim-up.d2 { animation-delay:.10s; opacity:0; }
        .anim-up.d3 { animation-delay:.15s; opacity:0; }
        @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
        .skeleton { background:linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }
        .anak-card { transition:transform .15s ease, opacity .15s ease; cursor:pointer; text-decoration:none; }
        .anak-card:hover  { opacity:.88; }
        .anak-card:active { transform:scale(0.97); opacity:.7; }
        .no-scrollbar::-webkit-scrollbar{display:none;} .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

        /* Gender pill */
        .pill-l { background:#dbeafe; color:#1d4ed8; }
        .pill-p { background:#fce7f3; color:#be185d; }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col" style="max-height:100vh;">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum flex-shrink-0">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg relative flex-shrink-0 overflow-hidden"
         style="padding:50px 20px 28px; border-bottom-left-radius:24px; border-bottom-right-radius:24px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <a href="{{ url('admin/diary') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px; left:20px; width:40px; height:40px; z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px; color:#fff;"></ion-icon>
        </a>

        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-3 shadow-lg" style="width:64px; height:64px;">
                <ion-icon name="people" style="font-size:30px; color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;">Pilih Anak</h1>
            <p id="nannyNameHeader" style="font-size:13px; color:#F3E6FA; font-weight:500;">Memuat data nanny…</p>
        </div>
    </div>

    <!-- LIST HEADER -->
    <div class="flex items-center justify-between flex-shrink-0 anim-up d2" style="padding:20px 20px 10px;">
        <span style="font-size:17px; font-weight:700; color:#4A0E35;">Anak yang Diasuh</span>
        <span id="anakCount" style="background:#F3E6FA; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700; color:#7B1E5A;">–</span>
    </div>

    <!-- LIST -->
    <div class="flex-1 overflow-y-auto no-scrollbar anim-up d3 pb-20" style="padding:0 20px;">

        <!-- Skeleton -->
        <div id="skeletonList">
            @for($i = 0; $i < 4; $i++)
            <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #F3E6FA; gap:12px;">
                <div class="skeleton flex-shrink-0" style="width:56px; height:56px; border-radius:28px;"></div>
                <div class="flex-1 flex flex-col gap-2">
                    <div class="skeleton" style="height:14px; width:60%;"></div>
                    <div class="skeleton" style="height:12px; width:45%;"></div>
                </div>
                <div class="skeleton flex-shrink-0" style="width:32px; height:32px; border-radius:16px;"></div>
            </div>
            @endfor
        </div>

        <div id="anakList" class="hidden"></div>

        <!-- Empty -->
        <div id="emptyState" class="hidden flex-col items-center justify-center" style="padding:60px 20px;">
            <div class="float-anim flex items-center justify-center" style="width:110px; height:110px; border-radius:55px; background:#F3E6FA; margin-bottom:20px;">
                <ion-icon name="body-outline" style="font-size:54px; color:#B895C8;"></ion-icon>
            </div>
            <p style="font-size:17px; font-weight:700; color:#4A0E35; margin-bottom:6px;">Belum ada anak</p>
            <p style="font-size:13px; color:#A2397B; text-align:center;">Nanny ini belum mengasuh anak apapun</p>
        </div>

        <!-- Error -->
        <div id="errorState" class="hidden flex flex-col items-center" style="padding:40px 20px; gap:12px;">
            <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#B895C8;"></ion-icon>
            <p style="font-size:15px; font-weight:700; color:#4A0E35;">Gagal memuat data</p>
            <button onclick="fetchAnak()" style="background:#7B1E5A; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Coba Lagi</button>
        </div>

        <div style="height:16px;"></div>
    </div>

    @include('partials.bottom-nav', ['active' => 'diary'])
</div>
</div>

<script>
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';

// Ambil id_nanny dari URL  /admin/diary/{id_nanny}/anak
const pathParts = window.location.pathname.split('/').filter(Boolean);
// path: admin / diary / {id_nanny} / anak
const ID_NANNY = pathParts[pathParts.length - 2] || null;

function pad(n){ return String(n).padStart(2,'0'); }
function updateClock(){
    const now = new Date(), el = document.getElementById('statusTime');
    if (el) el.textContent = pad(now.getHours())+':'+pad(now.getMinutes());
}
updateClock(); setInterval(updateClock, 30000);

async function fetchAnak() {
    if (!ID_NANNY) { showError(); return; }
    showSkeleton();
    try {
        const res = await fetch(`${API_BASE_URL}/nanny-assignments-anak-for-nanny?id_nanny=${ID_NANNY}`, {
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        
        // Extract all children from the nested structure
        const assignments = data.data || [];
        let allChildren = [];
        
        // Get nanny name from first assignment if available
        const nannyName = assignments[0]?.majikan_name || '';
        if (nannyName) {
            document.getElementById('nannyNameHeader').textContent = 'Nanny: ' + nannyName;
        } else {
            document.getElementById('nannyNameHeader').textContent = 'Daftar anak yang diasuh';
        }
        
        // Flatten the data structure - extract anak from each assignment
        assignments.forEach(assignment => {
            if (assignment.anak && Array.isArray(assignment.anak)) {
                assignment.anak.forEach(child => {
                    // Add assignment info to each child if needed
                    child.id_assignment = assignment.id_assignment;
                    child.tanggal_mulai = assignment.tanggal_mulai;
                    child.tanggal_selesai = assignment.tanggal_selesai;
                    allChildren.push(child);
                });
            }
        });

        renderAnak(allChildren);
    } catch(e) {
        console.error('Error fetching data:', e);
        showError();
    }
}

function showSkeleton(){
    document.getElementById('skeletonList').classList.remove('hidden');
    document.getElementById('anakList').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
}
function showError(){
    document.getElementById('skeletonList').classList.add('hidden');
    document.getElementById('anakList').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorState').classList.add('flex');
}

function renderAnak(list) {
    document.getElementById('skeletonList').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('anakCount').textContent = list.length;

    if (list.length === 0) {
        const empty = document.getElementById('emptyState');
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        document.getElementById('anakList').classList.add('hidden');
        return;
    }

    document.getElementById('emptyState').classList.add('hidden');
    const container = document.getElementById('anakList');
    container.classList.remove('hidden');

    container.innerHTML = list.map((anak, i) => {
        const namaAnak = anak.nama || anak.name || 'Anak';
        const idAnak   = anak.id;
        const foto     = anak.foto || '';
        const gender   = (anak.gender || '').toUpperCase();
        const tglLahir = anak.tanggal_lahir || '';
        const umur     = calculateAge(tglLahir);

        const isMale = gender === 'L' || gender === 'LAKI' || gender === 'MALE';
        const genderLabel = isMale ? 'Laki-laki' : 'Perempuan';
        const genderIcon  = isMale ? 'male-outline' : 'female-outline';

        const avatarHtml = foto
            ? `<img src="${foto}" alt="${namaAnak}"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    style="width:56px;height:56px;border-radius:28px;border:3px solid #F3E6FA;object-fit:cover;flex-shrink:0;">
               <div class="items-center justify-center hidden"
                    style="width:56px;height:56px;border-radius:28px;background:#F3E6FA;border:3px solid #F3E6FA;flex-shrink:0;">
                   <ion-icon name="body-outline" style="font-size:24px;color:#7B1E5A;"></ion-icon>
               </div>`
            : `<div class="flex items-center justify-center"
                    style="width:56px;height:56px;border-radius:28px;background:#F3E6FA;border:3px solid #F3E6FA;flex-shrink:0;">
                   <ion-icon name="body-outline" style="font-size:24px;color:#7B1E5A;"></ion-icon>
               </div>`;

        // Link menuju halaman diary: /admin/diary/{id_nanny}/anak/{id_anak}
        const href = `/admin/diary/${ID_NANNY}/anak/${idAnak}`;

        return `<a href="${href}" class="anak-card flex items-center bg-white"
                   style="border-radius:16px;margin-bottom:12px;border:2px solid #F3E6FA;padding:16px;gap:12px;
                          animation:slideUp .3s ease ${i*.06}s both;opacity:0;">
            <div style="flex-shrink:0;">${avatarHtml}</div>
            <div class="flex-1 min-w-0">
                <p class="line-clamp-1" style="font-size:15px;font-weight:700;color:#4A0E35;margin-bottom:6px;">${namaAnak}</p>
                <div class="flex items-center flex-wrap" style="gap:8px;">
                    <div class="flex items-center" style="gap:4px;">
                        <ion-icon name="${genderIcon}" style="font-size:13px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                        <span style="font-size:12px;color:#7B1E5A;font-weight:500;">${genderLabel}</span>
                    </div>
                    ${umur ? `<div class="flex items-center" style="gap:4px;">
                        <ion-icon name="calendar-outline" style="font-size:12px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                        <span style="font-size:12px;color:#7B1E5A;font-weight:500;">${umur}</span>
                    </div>` : ''}
                </div>
            </div>
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;">
                <ion-icon name="chevron-forward-outline" style="font-size:20px;color:#B895C8;"></ion-icon>
            </div>
        </a>`;
    }).join('');
}

function calculateAge(tgl) {
    if (!tgl) return '';
    const birth = new Date(tgl);
    const now   = new Date();
    let years  = now.getFullYear() - birth.getFullYear();
    let months = now.getMonth()    - birth.getMonth();
    if (months < 0) { years--; months += 12; }
    if (years > 0) return years + ' tahun';
    if (months > 0) return months + ' bulan';
    return '< 1 bulan';
}

fetchAnak();
</script>

@include('partials.auth-guard')
</body>
</html>