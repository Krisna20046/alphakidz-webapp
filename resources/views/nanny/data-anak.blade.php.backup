{{-- resources/views/nanny/data-anak.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Anak</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

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

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .anak-card { transition: transform .15s ease; }
        .anak-card:active { transform: scale(0.98); }
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
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('dashboard') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Child Data</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">Child Information on Assignment</p>
            </div>
        </div>
    </div>

    @php $anak = $assignmentData['anak'] ?? []; @endphp

    @if($assignmentData && count($anak) > 0)

    {{-- SCROLLABLE CONTENT --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

        {{-- Assignment info card --}}
        <div class="anim delay-2">
            <div class="bg-white rounded-[14px] px-3 py-3 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5] flex items-center gap-3">
                {{-- Employer photo --}}
                <div class="w-[56px] h-[56px] rounded-[10px] overflow-hidden bg-[#F3F0FD] shrink-0 flex items-center justify-center">
                    @if(!empty($assignmentData['majikan_foto']))
                    <img src="{{ $assignmentData['majikan_foto'] }}" alt="{{ $assignmentData['majikan_name'] }}"
                         class="w-full h-full object-cover"
                         onerror="this.style.display='none';this.parentElement.innerHTML='<ion-icon name=\'person\' style=\'font-size:26px;color:#8B46D3;\'></ion-icon>'">
                    @else
                    <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                    @endif
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-extrabold text-[#757575] uppercase tracking-wider mb-0.5">Assignment With</p>
                    <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate mb-1">{{ $assignmentData['majikan_name'] ?? '-' }}</p>
                    <div class="flex gap-1">
                        <ion-icon name="calendar-outline" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[#8B46D3] text-[11px] font-semibold">
                            {{ $assignmentData['tanggal_mulai'] ?? '' }} – {{ $assignmentData['tanggal_selesai'] ?? '' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- List header --}}
        <div class="anim delay-3 flex items-center justify-between">
            <span class="text-[#1E1B2E] font-extrabold text-[18px]">Child Data</span>
            <span class="text-[9px] font-extrabold px-3 py-1 rounded-full"
                  style="background:#EDE9FE;color:#6D28D9;">
                {{ count($anak) }} {{ count($anak) == 1 ? 'CHILD' : 'CHILDREN' }}
            </span>
        </div>

        {{-- Cards --}}
        <div class="flex flex-col gap-3 pb-6">
            @foreach($anak as $i => $child)
            @php
                $lahir  = new DateTime($child['tanggal_lahir'] ?? 'now');
                $diff   = $lahir->diff(new DateTime());
                $umur   = ($diff->y > 0 ? $diff->y . ' tahun ' : '') . $diff->m . ' bulan';
                $isMale = ($child['gender'] ?? '') === 'L';
                $hasDetail = !empty($child['catatan_khusus']) || !empty($child['alergi']) || !empty($child['hobi']);
            @endphp

            <div class="anak-card bg-white rounded-[14px] shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5] overflow-hidden"
                 style="animation: slideUp 0.35s ease {{ $i * 0.07 }}s both; opacity:0;">

                {{-- Card top --}}
                <div class="flex items-center gap-3 px-3 py-3">
                    {{-- Avatar --}}
                    <div class="w-[56px] h-[56px] rounded-[10px] overflow-hidden bg-[#F3F0FD] shrink-0 flex items-center justify-center">
                        @if(!empty($child['foto']))
                        <img src="{{ $child['foto'] }}" alt="{{ $child['nama'] }}"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none';this.parentElement.innerHTML='<ion-icon name=\'happy\' style=\'font-size:26px;color:#8B46D3;\'></ion-icon>'">
                        @else
                        <ion-icon name="happy" style="font-size:26px;color:#8B46D3;"></ion-icon>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $child['nama'] }}</p>
                        </div>
                        <div class="flex gap-1">
                            <span class="iconify" data-icon="mdi-birthday-cake" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></span>
                            <span class="text-[#8B46D3] text-[11px] font-semibold">{{ trim($umur) }}</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="iconify" data-icon="{{ $isMale ? 'mdi-gender-male' : 'mdi-gender-female' }}" 
                                style="font-size:14px;color:{{ $isMale ? '#2635DA' : '#F41E56' }};flex-shrink:0;"></span>
                            <span class="text-[11px] font-semibold" style="color:{{ $isMale ? '#2635DA' : '#F41E56' }}">{{ $isMale ? 'Male' : 'Female' }}</span>
                        </div>
                    </div>
                </div>

                @if($hasDetail)
                {{-- Divider --}}
                <div class="h-px bg-[#F0ECF8] mx-3"></div>

                {{-- Detail rows --}}
                <div class="px-3 py-3 flex flex-col gap-3">

                    @if(!empty($child['catatan_khusus']))
                    <div class="flex items-start gap-3">
                        <div class="w-[32px] h-[32px] rounded-[8px] bg-[#FEF3C7] flex items-center justify-center shrink-0">
                            <span class="iconify" data-icon="mdi:file-document-outline" style="font-size:15px;color:#D97706;"></span>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-extrabold text-[#8B86A5] uppercase tracking-wider mb-0.5">Special Notes</p>
                            <p class="text-[#1E1B2E] text-[13px] font-semibold leading-[19px]">{{ $child['catatan_khusus'] }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($child['alergi']))
                    <div class="flex items-start gap-3">
                        <div class="w-[32px] h-[32px] rounded-[8px] bg-[#FEE2E2] flex items-center justify-center shrink-0">
                            <span class="iconify" data-icon="mdi:alert-outline" style="font-size:15px;color:#EF4444;"></span>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-extrabold text-[#8B86A5] uppercase tracking-wider mb-0.5">Allergies</p>
                            <p class="text-[#1E1B2E] text-[13px] font-semibold leading-[19px]">{{ $child['alergi'] }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($child['hobi']))
                    <div class="flex items-start gap-3">
                        <div class="w-[32px] h-[32px] rounded-[8px] bg-[#D6D8F3] flex items-center justify-center shrink-0">
                            <span class="iconify" data-icon="mdi:heart-outline" style="font-size:15px;color:#2635DA;"></span>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-extrabold text-[#8B86A5] uppercase tracking-wider mb-0.5">Hobby</p>
                            <p class="text-[#1E1B2E] text-[13px] font-semibold leading-[19px]">{{ $child['hobi'] }}</p>
                        </div>
                    </div>
                    @endif

                </div>
                @endif

            </div>
            @endforeach
        </div>

        <div style="height:30px;"></div>
    </div>

    @else
    {{-- EMPTY STATE --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col items-center justify-center">
        <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
            <ion-icon name="happy-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
        </div>
        <h3 class="text-[#1E1B2E] font-extrabold text-lg mb-2">Belum ada data anak</h3>
        <p class="text-[#8B86A5] text-sm text-center leading-relaxed px-8">
            @if($assignmentData)
                Penugasan dengan <span class="font-bold text-[#8B46D3]">{{ $assignmentData['majikan_name'] ?? '' }}</span><br>
                {{ $assignmentData['tanggal_mulai'] ?? '' }} – {{ $assignmentData['tanggal_selesai'] ?? '' }}
            @else
                Tidak ada penugasan aktif saat ini
            @endif
        </p>
    </div>
    @endif

    @include('partials.bottom-nav', ['active' => ''])
</div>
</div>

<script>
    function updateClock(){const n=new Date(),e=document.getElementById('statusTime');if(e)e.textContent=String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0');}
    updateClock();setInterval(updateClock,30000);
</script>
@include('partials.auth-guard')
</body>
</html>