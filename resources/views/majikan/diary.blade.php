{{-- resources/views/majikan/diary.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Diary Anak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config={theme:{extend:{colors:{plum:{DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B',accent:'#B895C8'}},fontFamily:{sans:['Plus Jakarta Sans','sans-serif']}}}}
    </script>
    <style>
        *{-webkit-tap-highlight-color:transparent;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:#FFF9FB;}
        @media(min-width:640px){
            .phone-wrapper{display:flex;align-items:flex-start;justify-content:center;min-height:100vh;padding:32px 0;background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%);}
            .phone-frame{width:390px;min-height:844px;border-radius:44px;box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020;overflow:hidden;position:relative;}
        }
        @media(max-width:639px){.phone-wrapper{min-height:100vh;}.phone-frame{min-height:100vh;}}
        .header-bg{background:linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%);}
        @keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .anim-up{animation:slideUp .35s ease forwards;}
        .anim-up.d1{animation-delay:.05s;opacity:0;}
        .anim-up.d2{animation-delay:.10s;opacity:0;}
        .anim-up.d3{animation-delay:.15s;opacity:0;}
        .anim-up.d4{animation-delay:.20s;opacity:0;}
        .akt-card{transition:opacity .15s,transform .15s;cursor:pointer;}
        .akt-card:hover{opacity:.85;}
        .akt-card:active{transform:scale(0.98);opacity:.7;}
        .filter-chip{cursor:pointer;transition:background .15s,color .15s;white-space:nowrap;}
        .filter-chip:active{transform:scale(0.95);}
        .date-arrow{cursor:pointer;border:none;background:transparent;transition:background .15s;border-radius:10px;padding:8px;display:flex;align-items:center;justify-content:center;}
        .date-arrow:hover{background:#F3E6FA;}
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;padding:20px;z-index:50;opacity:0;pointer-events:none;transition:opacity .2s ease;}
        .modal-overlay.open{opacity:1;pointer-events:auto;}
        .modal-box{background:#fff;border-radius:24px;width:100%;max-height:82vh;overflow:hidden;transform:translateY(20px);transition:transform .25s ease;display:flex;flex-direction:column;max-width:390px;}
        .modal-overlay.open .modal-box{transform:translateY(0);}
        .picker-scroll{height:200px;overflow-y:auto;border-radius:12px;background:#FFF9FB;}
        .picker-scroll::-webkit-scrollbar{display:none;}.picker-scroll{-ms-overflow-style:none;scrollbar-width:none;}
        .picker-item{padding:10px 12px;text-align:center;border-radius:8px;cursor:pointer;margin:2px 4px;font-size:14px;color:#4A0E35;font-weight:500;transition:background .12s;}
        .picker-item.active{background:#7B1E5A;color:#fff;font-weight:700;}
        .picker-item:hover:not(.active){background:#F3E6FA;}
        .no-scrollbar::-webkit-scrollbar{display:none;}.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .float-anim{animation:floatEmpty 3s ease-in-out infinite;}
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
    <div class="header-bg relative flex-shrink-0"
         style="padding:50px 20px 24px;border-bottom-left-radius:24px;border-bottom-right-radius:24px;margin-bottom:16px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>
        <a href="{{ route('majikan-diary-choose') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg" style="width:64px;height:64px;">
                <ion-icon name="book" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Diary Anak</h1>
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">{{ $namaAnak ?? '' }}</p>
        </div>
    </div>

    <!-- FILTER KATEGORI -->
    <div class="flex-shrink-0 anim-up d2" style="margin-bottom:16px;">
        <div class="no-scrollbar flex" style="overflow-x:auto;padding:0 20px;gap:10px;">
            @php
                $kats = [['value'=>'','label'=>'Semua'],['value'=>'makan','label'=>'Makan'],['value'=>'tidur','label'=>'Tidur'],['value'=>'main','label'=>'Main'],['value'=>'belajar','label'=>'Belajar'],['value'=>'mandi','label'=>'Mandi']];
                $activeKat = request('kategori','');
            @endphp
            @foreach($kats as $kat)
            @php $isActive = $activeKat === $kat['value']; @endphp
            <a href="{{ route('majikan-diary', array_merge(['id'=>$idAnak??0], $kat['value']?['kategori'=>$kat['value']]:[], ['tanggal'=>$tanggal??date('Y-m-d')])) }}"
               class="filter-chip flex-shrink-0"
               style="padding:8px 16px;border-radius:20px;font-size:14px;border:2px solid {{ $isActive?'#7B1E5A':'#F3E6FA' }};background:{{ $isActive?'#7B1E5A':'#FFFFFF' }};font-weight:{{ $isActive?'700':'600' }};color:{{ $isActive?'#FFFFFF':'#7B1E5A' }};">
                {{ $kat['label'] }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- DATE SELECTOR -->
    <div class="flex-shrink-0 anim-up d3" style="padding:0 20px;margin-bottom:16px;">
        <div class="flex items-center justify-between bg-white" style="padding:16px 20px;border-radius:16px;border:2px solid #F3E6FA;">
            <button class="date-arrow" id="btnPrev">
                <ion-icon name="chevron-back" style="font-size:28px;color:#7B1E5A;"></ion-icon>
            </button>
            <button id="btnDatePicker" class="flex flex-col items-center flex-1" style="background:transparent;cursor:pointer;border:none;">
                <span style="font-size:16px;font-weight:700;color:#4A0E35;">{{ $tanggalIndo ?? date('d F Y') }}</span>
                <span style="font-size:12px;color:#A2397B;margin-top:4px;font-weight:500;">
                    {{ isset($diaryData) ? ($diaryData['total_aktivitas']??0).' aktivitas' : '0 aktivitas' }}
                </span>
            </button>
            <button class="date-arrow" id="btnNext">
                <ion-icon name="chevron-forward" style="font-size:28px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 overflow-y-auto no-scrollbar anim-up d4" style="padding:0 20px;">
        @if(isset($aktivitas) && count($aktivitas) > 0)
        @foreach($aktivitas as $i => $item)
        @php
            $bgs  = ['makan'=>'#FF6B6B','tidur'=>'#4ECDC4','main'=>'#FFD93D','belajar'=>'#6BCB77','mandi'=>'#95B8D1'];
            $icos = ['makan'=>'restaurant','tidur'=>'bed','main'=>'football','belajar'=>'school','mandi'=>'water'];
            $bg   = $bgs[$item['kategori']]  ?? '#B895C8';
            $ico  = $icos[$item['kategori']] ?? 'calendar';
        @endphp
        <div class="akt-card flex items-center justify-between bg-white"
             style="padding:16px;border-radius:16px;margin-bottom:12px;border:2px solid #F3E6FA;border-left:4px solid {{ $bg }};animation:slideUp .25s ease {{ $i*.05 }}s both;opacity:0;"
             onclick='openDetail(@json($item))'>
            <div class="flex items-center flex-1 min-w-0">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:50px;height:50px;border-radius:25px;background:{{ $bg }}20;margin-right:12px;">
                    <ion-icon name="{{ $ico }}" style="font-size:24px;color:{{ $bg }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p style="font-size:16px;font-weight:700;color:#4A0E35;margin-bottom:4px;">{{ ucfirst($item['kategori']) }}</p>
                    <p style="font-size:14px;color:#7B1E5A;font-weight:500;margin-bottom:2px;">{{ $item['jam_mulai_fmt'] }} - {{ $item['jam_selesai_fmt'] }}</p>
                    <p style="font-size:12px;color:#A2397B;font-weight:500;">Durasi: {{ $item['durasi_fmt'] }}</p>
                </div>
            </div>
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;margin-left:8px;">
                <ion-icon name="chevron-forward" style="font-size:20px;color:#7B1E5A;"></ion-icon>
            </div>
        </div>
        @endforeach
        @else
        <div class="flex flex-col items-center justify-center" style="padding:60px 20px 100px;">
            <div class="float-anim flex items-center justify-center" style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
                <ion-icon name="calendar-clear-outline" style="font-size:60px;color:#B895C8;"></ion-icon>
            </div>
            <p class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">Tidak ada aktivitas</p>
            <p class="text-center" style="font-size:14px;color:#A2397B;">pada tanggal ini</p>
        </div>
        @endif
        <div style="height:30px;"></div>
    </div>

    @include('partials.bottom-nav', ['active'=>'diary'])
</div>
</div>

<!-- MODAL DATE PICKER -->
<div class="modal-overlay" id="modalDatePicker">
    <div class="modal-box">
        <div class="flex items-center justify-between flex-shrink-0" style="padding:20px;border-bottom:2px solid #F3E6FA;">
            <span style="font-size:20px;font-weight:700;color:#4A0E35;">Pilih Tanggal</span>
            <button onclick="closeDatePicker()" style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;">
                <ion-icon name="close" style="font-size:20px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div class="flex flex-1 overflow-hidden" style="padding:10px 20px 0;gap:0;">
            <div class="flex flex-col flex-1" style="margin:0 5px;">
                <p style="font-size:14px;font-weight:700;color:#7B1E5A;text-align:center;margin-bottom:10px;">Tahun</p>
                <div class="picker-scroll" id="pickerYear"></div>
            </div>
            <div class="flex flex-col flex-1" style="margin:0 5px;">
                <p style="font-size:14px;font-weight:700;color:#7B1E5A;text-align:center;margin-bottom:10px;">Bulan</p>
                <div class="picker-scroll" id="pickerMonth"></div>
            </div>
            <div class="flex flex-col flex-1" style="margin:0 5px;">
                <p style="font-size:14px;font-weight:700;color:#7B1E5A;text-align:center;margin-bottom:10px;">Tanggal</p>
                <div class="picker-scroll" id="pickerDay"></div>
            </div>
        </div>
        <div class="flex flex-shrink-0" style="padding:10px 20px 20px;gap:10px;">
            <button onclick="closeDatePicker()" style="flex:1;padding:14px;border-radius:12px;background:#F3E6FA;font-size:16px;font-weight:600;color:#7B1E5A;cursor:pointer;border:none;">Batal</button>
            <button onclick="confirmDatePicker()" style="flex:1;padding:14px;border-radius:12px;background:#7B1E5A;font-size:16px;font-weight:700;color:#fff;cursor:pointer;border:none;letter-spacing:.3px;">Pilih</button>
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div class="flex items-center justify-between flex-shrink-0" style="padding:20px;border-bottom:2px solid #F3E6FA;">
            <span style="font-size:20px;font-weight:700;color:#4A0E35;">Detail Aktivitas</span>
            <button onclick="closeDetail()" style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;">
                <ion-icon name="close" style="font-size:20px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto no-scrollbar" style="padding:20px;" id="detailBody"></div>
        <div class="flex-shrink-0" style="padding:0 20px 20px;">
            <button onclick="closeDetail()" style="width:100%;background:#7B1E5A;padding:16px;border-radius:16px;font-size:16px;font-weight:700;color:#fff;letter-spacing:.5px;cursor:pointer;border:none;">Tutup</button>
        </div>
    </div>
</div>

<script>
const MONTHS_ID=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const ID_ANAK={{ $idAnak??'null' }};
const KATEGORI="{{ $activeKat??request('kategori','') }}";
let currentDate=new Date("{{ $tanggal??date('Y-m-d') }}");
let tY,tM,tD;

function pad(n){return String(n).padStart(2,'0');}
function fmt(d){return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate());}

function updateClock(){const n=new Date(),e=document.getElementById('statusTime');if(e)e.textContent=pad(n.getHours())+':'+pad(n.getMinutes());}
updateClock();setInterval(updateClock,30000);

function reload(){
    const p=new URLSearchParams({tanggal:fmt(currentDate)});
    if(KATEGORI)p.set('kategori',KATEGORI);
    window.location.href='{{ route("majikan-diary", $idAnak??0) }}?'+p.toString();
}

document.getElementById('btnPrev').addEventListener('click',()=>{currentDate.setDate(currentDate.getDate()-1);reload();});
document.getElementById('btnNext').addEventListener('click',()=>{currentDate.setDate(currentDate.getDate()+1);reload();});

// --- date picker ---
function daysInMonth(y,m){return new Date(y,m+1,0).getDate();}

function buildPicker(id,items,active,cb){
    const el=document.getElementById(id);
    el.innerHTML='';
    items.forEach(item=>{
        const d=document.createElement('div');
        d.className='picker-item'+(item.v===active?' active':'');
        d.textContent=item.l;
        d.onclick=()=>{
            el.querySelectorAll('.picker-item').forEach(x=>x.classList.remove('active'));
            d.classList.add('active');
            cb(item.v);
            d.scrollIntoView({block:'nearest',behavior:'smooth'});
        };
        el.appendChild(d);
    });
    const a=el.querySelector('.active');
    if(a)setTimeout(()=>a.scrollIntoView({block:'center'}),60);
}

function buildDayPicker(){
    const total=daysInMonth(tY,tM);
    buildPicker('pickerDay',Array.from({length:total},(_,i)=>({v:i+1,l:String(i+1)})),tD,v=>{tD=v;});
}

function openDatePicker(){
    tY=currentDate.getFullYear();tM=currentDate.getMonth();tD=currentDate.getDate();
    const curY=new Date().getFullYear();
    buildPicker('pickerYear',Array.from({length:7},(_,i)=>({v:curY-5+i,l:String(curY-5+i)})),tY,v=>{tY=v;buildDayPicker();});
    buildPicker('pickerMonth',MONTHS_ID.map((m,i)=>({v:i,l:m})),tM,v=>{tM=v;if(tD>daysInMonth(tY,tM))tD=daysInMonth(tY,tM);buildDayPicker();});
    buildDayPicker();
    document.getElementById('modalDatePicker').classList.add('open');
}
function closeDatePicker(){document.getElementById('modalDatePicker').classList.remove('open');}
function confirmDatePicker(){currentDate=new Date(tY,tM,tD);closeDatePicker();reload();}

document.getElementById('btnDatePicker').addEventListener('click',openDatePicker);
document.getElementById('modalDatePicker').addEventListener('click',e=>{if(e.target.id==='modalDatePicker')closeDatePicker();});

// --- detail modal ---
function getKatColor(k){return{makan:'#FF6B6B',tidur:'#4ECDC4',main:'#FFD93D',belajar:'#6BCB77',mandi:'#95B8D1'}[k]||'#B895C8';}
function getKatIcon(k) {return{makan:'restaurant',tidur:'bed',main:'football',belajar:'school',mandi:'water'}[k]||'calendar';}
function getMood(m)    {return{senang:'😊',sedih:'😢',marah:'😠',biasa:'😐'}[m]||'😊';}

function detailRow(iconName,label,value,isLast){
    return `<div style="display:flex;align-items:flex-start;${isLast?'':'margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #F3E6FA;'}">
        <div style="width:36px;height:36px;border-radius:10px;background:#F3E6FA;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
            <ion-icon name="${iconName}" style="font-size:18px;color:#7B1E5A;"></ion-icon>
        </div>
        <div style="flex:1;">
            <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">${label}</p>
            <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">${value||'-'}</p>
        </div>
    </div>`;
}

function openDetail(item){
    const bg=getKatColor(item.kategori),ic=getKatIcon(item.kategori);
    const rows=[
        {icon:'time-outline',label:'Waktu Mulai',value:item.jam_mulai_fmt},
        {icon:'time-outline',label:'Waktu Selesai',value:item.jam_selesai_fmt},
        {icon:'hourglass',label:'Durasi',value:item.durasi_fmt},
    ];
    if(item.mood) rows.push({emoji:getMood(item.mood),label:'Mood',value:item.mood.charAt(0).toUpperCase()+item.mood.slice(1)});
    if(item.deskripsi) rows.push({icon:'document-text',label:'Deskripsi',value:item.deskripsi});
    if(item.nanny_name) rows.push({icon:'person',label:'Dicatat oleh',value:item.nanny_name});

    let html=`<div style="display:flex;flex-direction:column;align-items:center;padding:20px;border-radius:16px;margin-bottom:20px;background:${bg}20;">
        <ion-icon name="${ic}" style="font-size:40px;color:${bg};"></ion-icon>
        <p style="font-size:22px;font-weight:700;color:#4A0E35;margin-top:10px;">${item.kategori.charAt(0).toUpperCase()+item.kategori.slice(1)}</p>
    </div>`;

    rows.forEach((r,idx)=>{
        const last=idx===rows.length-1&&!item.foto_url;
        if(r.emoji){
            html+=`<div style="display:flex;align-items:flex-start;${last?'':'margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #F3E6FA;'}">
                <div style="width:36px;height:36px;border-radius:10px;background:#F3E6FA;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <span style="font-size:20px;">${r.emoji}</span>
                </div>
                <div style="flex:1;"><p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">${r.label}</p><p style="font-size:15px;color:#4A0E35;font-weight:500;">${r.value||'-'}</p></div>
            </div>`;
        } else {
            html+=detailRow(r.icon,r.label,r.value,last);
        }
    });

    if(item.foto_url){
        html+=`<div style="margin-top:10px;">
            <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:8px;">Foto</p>
            <img src="${item.foto_url}" style="width:100%;height:200px;border-radius:16px;object-fit:cover;background:#F3E6FA;border:2px solid #F3E6FA;">
        </div>`;
    }

    document.getElementById('detailBody').innerHTML=html;
    document.getElementById('modalDetail').classList.add('open');
}
function closeDetail(){document.getElementById('modalDetail').classList.remove('open');}
document.getElementById('modalDetail').addEventListener('click',e=>{if(e.target.id==='modalDetail')closeDetail();});
</script>
</body>
</html>