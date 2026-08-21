@extends('layouts.app')

@section('title', 'Select Child – Diary Admin')

@push('styles')
<style>
    @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
    .skeleton { background:linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }
    .anak-card { transition:transform .15s ease, opacity .15s ease; cursor:pointer; text-decoration:none; }
    .anak-card:hover  { opacity:.88; }
    .anak-card:active { transform:scale(0.97); opacity:.7; }
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    .pill-l { background:#dbeafe; color:#1d4ed8; }
    .pill-p { background:#fce7f3; color:#be185d; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex flex-col items-center relative z-10">
        <a href="{{ url('admin/diary') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:0; left:0; width:40px; height:40px;">
            <ion-icon name="arrow-back" style="font-size:20px; color:#fff;"></ion-icon>
        </a>
        <div class="flex items-center justify-center bg-white rounded-full mb-3 shadow-lg" style="width:64px; height:64px;">
            <ion-icon name="people" style="font-size:30px; color:#8B46D3;"></ion-icon>
        </div>
        <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;">Select Child</h1>
        <p id="nannyNameHeader" style="font-size:13px; color:#E5DEFF; font-weight:500;">Loading nanny data...</p>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    <div class="flex items-center justify-between anim delay-2">
        <span style="font-size:17px; font-weight:700; color:#1E1B2E;">Children in Care</span>
        <span id="anakCount" style="background:#EDE9FE; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700; color:#8B46D3;">–</span>
    </div>

    <!-- Skeleton -->
    <div id="skeletonList">
        @for($i = 0; $i < 4; $i++)
        <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #EDE9FE; gap:12px;">
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
        <div class="float-anim flex items-center justify-center" style="width:110px; height:110px; border-radius:55px; background:#EDE9FE; margin-bottom:20px;">
            <ion-icon name="body-outline" style="font-size:54px; color:#C4B5FD;"></ion-icon>
        </div>
        <p style="font-size:17px; font-weight:700; color:#1E1B2E; margin-bottom:6px;">No children yet</p>
        <p style="font-size:13px; color:#8B86A5; text-align:center;">This nanny is not caring for any children yet</p>
    </div>

    <!-- Error -->
    <div id="errorState" class="hidden flex flex-col items-center" style="padding:40px 20px; gap:12px;">
        <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#C4B5FD;"></ion-icon>
        <p style="font-size:15px; font-weight:700; color:#1E1B2E;">Failed to load data</p>
        <button onclick="fetchAnak()" style="background:#8B46D3; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Try Again</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';

const pathParts = window.location.pathname.split('/').filter(Boolean);
const ID_NANNY = pathParts[pathParts.length - 2] || null;

async function fetchAnak() {
    if (!ID_NANNY) { showError(); return; }
    showSkeleton();
    try {
        const res = await fetch(`${API_BASE_URL}/nanny-assignments-anak-for-nanny?id_nanny=${ID_NANNY}`, {
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();

        const assignments = data.data || [];
        let allChildren = [];

        const nannyName = assignments[0]?.majikan_name || '';
        if (nannyName) {
            document.getElementById('nannyNameHeader').textContent = 'Nanny: ' + nannyName;
        } else {
            document.getElementById('nannyNameHeader').textContent = 'List of children in care';
        }

        assignments.forEach(assignment => {
            if (assignment.anak && Array.isArray(assignment.anak)) {
                assignment.anak.forEach(child => {
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
        const namaAnak = anak.nama || anak.name || 'Child';
        const idAnak   = anak.id;
        const foto     = anak.foto || '';
        const gender   = (anak.gender || '').toUpperCase();
        const tglLahir = anak.tanggal_lahir || '';
        const umur     = calculateAge(tglLahir);

        const isMale = gender === 'L' || gender === 'LAKI' || gender === 'MALE';
        const genderLabel = isMale ? 'Male' : 'Female';
        const genderIcon  = isMale ? 'male-outline' : 'female-outline';

        const avatarHtml = foto
            ? `<img src="${foto}" alt="${namaAnak}"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    style="width:56px;height:56px;border-radius:28px;border:3px solid #EDE9FE;object-fit:cover;flex-shrink:0;">
               <div class="items-center justify-center hidden"
                    style="width:56px;height:56px;border-radius:28px;background:#EDE9FE;border:3px solid #EDE9FE;flex-shrink:0;">
                   <ion-icon name="body-outline" style="font-size:24px;color:#8B46D3;"></ion-icon>
               </div>`
            : `<div class="flex items-center justify-center"
                    style="width:56px;height:56px;border-radius:28px;background:#EDE9FE;border:3px solid #EDE9FE;flex-shrink:0;">
                   <ion-icon name="body-outline" style="font-size:24px;color:#8B46D3;"></ion-icon>
               </div>`;

        const href = `/admin/diary/${ID_NANNY}/anak/${idAnak}`;

        return `<a href="${href}" class="anak-card flex items-center bg-white"
                   style="border-radius:16px;margin-bottom:12px;border:2px solid #EDE9FE;padding:16px;gap:12px;
                          animation:slideUp .3s ease ${i*.06}s both;opacity:0;">
            <div style="flex-shrink:0;">${avatarHtml}</div>
            <div class="flex-1 min-w-0">
                <p class="line-clamp-1" style="font-size:15px;font-weight:700;color:#1E1B2E;margin-bottom:6px;">${namaAnak}</p>
                <div class="flex items-center flex-wrap" style="gap:8px;">
                    <div class="flex items-center" style="gap:4px;">
                        <ion-icon name="${genderIcon}" style="font-size:13px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span style="font-size:12px;color:#8B46D3;font-weight:500;">${genderLabel}</span>
                    </div>
                    ${umur ? `<div class="flex items-center" style="gap:4px;">
                        <ion-icon name="calendar-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span style="font-size:12px;color:#8B46D3;font-weight:500;">${umur}</span>
                    </div>` : ''}
                </div>
            </div>
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#EDE9FE;">
                <ion-icon name="chevron-forward-outline" style="font-size:20px;color:#C4B5FD;"></ion-icon>
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
    if (years > 0) return years + ' years';
    if (months > 0) return months + ' months';
    return '< 1 month';
}

fetchAnak();
</script>
@endpush
