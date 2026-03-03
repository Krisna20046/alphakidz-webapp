{{-- resources/views/nanny/konsultan.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Konsultan Anda</title>
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
        @keyframes slideUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
        .anim-up{animation:slideUp .35s ease forwards;}
        .anim-up.d1{animation-delay:.05s;opacity:0;}.anim-up.d2{animation-delay:.10s;opacity:0;}
        .anim-up.d3{animation-delay:.15s;opacity:0;}.anim-up.d4{animation-delay:.20s;opacity:0;}
        .anim-up.d5{animation-delay:.25s;opacity:0;}
        @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .float-anim{animation:floatEmpty 3s ease-in-out infinite;}
        .no-scrollbar::-webkit-scrollbar{display:none;}.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        .sec{background:#fff;border-radius:20px;padding:20px;margin-bottom:16px;border:2px solid #F3E6FA;}
        .sec-hd{display:flex;align-items:center;gap:8px;margin-bottom:16px;}
        .sec-hd span{font-size:16px;font-weight:700;color:#4A0E35;}
        .info-row{display:flex;align-items:flex-start;margin-bottom:16px;}
        .info-row:last-child{margin-bottom:0;}
        .info-ico{width:40px;height:40px;border-radius:12px;background:#F3E6FA;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;}
        .chat-btn{display:flex;align-items:center;justify-content:center;gap:10px;background:#7B1E5A;color:#fff;border-radius:16px;padding:16px;font-size:16px;font-weight:700;letter-spacing:.5px;cursor:pointer;border:none;width:100%;transition:opacity .15s,transform .12s;}
        .chat-btn:hover{opacity:.9;}.chat-btn:active{transform:scale(0.98);}
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
         style="padding:50px 20px 24px;border-bottom-left-radius:24px;border-bottom-right-radius:24px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>
        <a href="{{ route('dashboard') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg" style="width:64px;height:64px;">
                <ion-icon name="person" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Konsultan Anda</h1>
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">Informasi konsultan pribadi</p>
        </div>
    </div>

    @if($data)
    <!-- SCROLLABLE -->
    <div class="flex-1 overflow-y-auto no-scrollbar pb-16" style="padding-right:20px; padding-left:20px; padding-top:20px;">

        {{-- Profile card --}}
        <div class="sec anim-up d2" style="text-align:center;padding:28px 20px;">
            @if(!empty($data['foto']))
            <img src="{{ $data['foto'] }}" alt="{{ $data['name'] }}"
                 style="width:120px;height:120px;border-radius:60px;object-fit:cover;border:4px solid #F3E6FA;margin:0 auto 16px;"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="items-center justify-center hidden"
                 style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;border:4px solid #F3E6FA;margin:0 auto 16px;">
                <ion-icon name="person" style="font-size:50px;color:#7B1E5A;"></ion-icon>
            </div>
            @else
            <div class="flex items-center justify-center mx-auto"
                 style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;border:4px solid #F3E6FA;margin-bottom:16px;">
                <ion-icon name="person" style="font-size:50px;color:#7B1E5A;"></ion-icon>
            </div>
            @endif
            <p style="font-size:22px;font-weight:700;color:#4A0E35;margin-bottom:8px;">{{ $data['name'] }}</p>
            @if(!empty($data['spesialis']))
            <div class="inline-flex items-center" style="background:#F3E6FA;padding:8px 16px;border-radius:16px;gap:6px;">
                <ion-icon name="school" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                <span style="font-size:13px;color:#7B1E5A;font-weight:600;">{{ $data['spesialis'] }}</span>
            </div>
            @endif
        </div>

        {{-- Bio --}}
        @if(!empty($data['bio']))
        <div class="sec anim-up d3">
            <div class="sec-hd">
                <ion-icon name="information-circle" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Bio</span>
            </div>
            <p style="font-size:15px;color:#4A0E35;line-height:24px;">{{ $data['bio'] }}</p>
        </div>
        @endif

        {{-- Kontak --}}
        <div class="sec anim-up d3">
            <div class="sec-hd">
                <ion-icon name="call" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Informasi Kontak</span>
            </div>
            <div class="info-row">
                <div class="info-ico"><ion-icon name="mail" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Email</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;">{{ $data['email'] ?? '-' }}</p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-ico"><ion-icon name="call" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Nomor HP</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;">{{ $data['no_hp'] ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Pribadi --}}
        <div class="sec anim-up d4">
            <div class="sec-hd">
                <ion-icon name="person-circle" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Informasi Pribadi</span>
            </div>
            <div class="info-row">
                <div class="info-ico"><ion-icon name="calendar" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Tanggal Lahir</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;">{{ $data['tanggal_lahir'] ?? '-' }}</p>
                </div>
            </div>
            <div class="info-row">
                @php $genderL = ($data['gender'] ?? '') === 'L'; @endphp
                <div class="info-ico"><ion-icon name="{{ $genderL ? 'male' : 'female' }}" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Gender</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;">
                        {{ ($data['gender'] ?? '') === 'L' ? 'Laki-laki' : (($data['gender'] ?? '') === 'P' ? 'Perempuan' : '-') }}
                    </p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-ico"><ion-icon name="location" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Lokasi</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;">
                        @if(!empty($data['kota']) && !empty($data['provinsi']))
                            {{ $data['kota'] }}, {{ $data['provinsi'] }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
            @if(!empty($data['alamat']))
            <div class="info-row">
                <div class="info-ico"><ion-icon name="home" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Alamat</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:22px;">{{ $data['alamat'] }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Profesional --}}
        @if(!empty($data['skill']) || !empty($data['pengalaman']) || !empty($data['sertifikasi']))
        <div class="sec anim-up d5">
            <div class="sec-hd">
                <ion-icon name="briefcase" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Informasi Profesional</span>
            </div>
            @if(!empty($data['skill']))
            <div class="info-row">
                <div class="info-ico"><ion-icon name="star" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Skill</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:22px;">{{ $data['skill'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($data['pengalaman']))
            <div class="info-row">
                <div class="info-ico"><ion-icon name="time" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Pengalaman</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:22px;">{{ $data['pengalaman'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($data['sertifikasi']))
            <div class="info-row">
                <div class="info-ico"><ion-icon name="ribbon" style="font-size:18px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Sertifikasi</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:22px;">{{ $data['sertifikasi'] }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Chat button --}}
        <div style="margin-top:8px;margin-bottom:16px;">
            <a href="{{ route('chat.room', ['id' => $data['id_user'] ?? 0]) }}" class="chat-btn">
                <ion-icon name="chatbubble-ellipses" style="font-size:22px;color:#fff;"></ion-icon>
                Hubungi Konsultan
            </a>
        </div>

        <div style="height:20px;"></div>
    </div>

    @else
    {{-- Empty --}}
    <div class="flex-1 flex flex-col items-center justify-center" style="padding:40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
            <ion-icon name="person-circle-outline" style="font-size:64px;color:#B895C8;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">Data konsultan tidak ditemukan</p>
        <p class="text-center" style="font-size:14px;color:#A2397B;">Belum ada konsultan yang ditugaskan untuk Anda</p>
    </div>
    @endif

    @include('partials.bottom-nav', ['active' => ''])
</div>
</div>
<script>
    function updateClock(){const n=new Date(),e=document.getElementById('statusTime');if(e)e.textContent=String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0');}
    updateClock();setInterval(updateClock,30000);
</script>
</body>
</html>