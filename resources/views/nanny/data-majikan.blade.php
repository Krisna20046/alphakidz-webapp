{{-- resources/views/nanny/majikan.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Majikan Anda</title>
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
        .delay-4      { animation-delay: 0.29s; }
        .delay-5      { animation-delay: 0.37s; }
        .delay-6      { animation-delay: 0.45s; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .section-card {
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
        }
        .detail-item {
            background: #F8F8FB;
            border: 1px solid #ECEAF4;
            border-radius: 10px;
        }
        .child-card {
            background: #FFFFFF;
            border-radius: 14px;
            border: 1px solid #ECEAF4;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .child-detail-item {
            background: #F8F8FB;
            border: 1px solid #ECEAF4;
            border-radius: 10px;
        }
        .btn-chat {
            background: #8B46D3;
            color: #FFFFFF;
            border-radius: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 52px;
            font-size: 15px;
            width: 100%;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(139,70,211,0.35);
            transition: opacity .15s, transform .12s;
            text-decoration: none;
        }
        .btn-chat:hover  { opacity: .9; }
        .btn-chat:active { transform: scale(0.98); }
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
                <rect x="0"   y="4"   width="3" height="7"    rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5"  rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9"   y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
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
                <span class="text-white text-[17px] font-extrabold tracking-wide">Employer Data</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Complete Employer Profile Information</p>
            </div>
        </div>
    </div>

    @if($data)
    {{-- ── SCROLLABLE CONTENT ── --}}
    @php
        $genderText = ($data['majikan_gender'] ?? '') === 'L' ? 'Laki-laki' : (($data['majikan_gender'] ?? '') === 'P' ? 'Perempuan' : '-');
        $isMale     = ($data['majikan_gender'] ?? '') === 'L';
        $locationText = (!empty($data['majikan_kota']) && !empty($data['majikan_provinsi']))
                        ? $data['majikan_kota'].', '.$data['majikan_provinsi']
                        : ($data['majikan_kota'] ?? $data['majikan_provinsi'] ?? '-');
    @endphp

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
                bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
                rounded-t-[50px] -mt-[50px] relative z-20
                hide-scrollbar space-y-4">

        {{-- ── PROFILE CARD ── --}}
        <div class="section-card anim delay-2 p-5">
            <div class="flex flex-col items-center">
                @if(!empty($data['majikan_foto']))
                <img src="{{ $data['majikan_foto'] }}" alt="{{ $data['majikan_name'] }}"
                     class="w-[88px] h-[88px] rounded-full object-cover border-4 border-[#EDE9FE] shadow-[0_3px_10px_rgba(0,0,0,0.12)]"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-[88px] h-[88px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] items-center justify-center hidden">
                    <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                </div>
                @else
                <div class="w-[88px] h-[88px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                </div>
                @endif

                <h2 class="text-[#1E1B2E] text-[25px] font-extrabold mt-3">{{ $data['majikan_name'] }}</h2>

                <div class="mt-2 bg-[#EFE9FB] px-3 py-1 rounded-full flex items-center gap-1.5">
                    <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                    <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">Majikan</span>
                </div>
            </div>
        </div>

        {{-- ── INFORMASI KONTAK ── --}}
        <div class="section-card anim delay-3 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="call" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Contact Information</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                        <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $data['majikan_email'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="call-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Nomor HP</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $data['majikan_no_hp'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── INFORMASI PRIBADI ── --}}
        <div class="section-card anim delay-4 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="person-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Personal Information</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Date of Birth</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $data['majikan_tanggal_lahir'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $isMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $genderText }}</p>
                    </div>
                </div>
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="location-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Lokasi</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $locationText }}</p>
                    </div>
                </div>
                @if(!empty($data['majikan_alamat']))
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                        <ion-icon name="home-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Alamat</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold leading-snug">{{ $data['majikan_alamat'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── DATA ANAK ── --}}
        @if(count($children) > 0)
        <div class="anim delay-5">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <ion-icon name="people" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Child Data</h3>
                </div>
                <div class="bg-[#EFE9FB] px-3 py-1 rounded-full">
                    <span class="text-[#8B46D3] text-[11px] font-extrabold">{{ count($children) }} anak</span>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($children as $i => $child)
                @php $childMale = ($child['gender'] ?? '') === 'L'; @endphp
                <div class="child-card p-4" style="animation: slideUp 0.4s ease {{ 0.45 + $i * 0.08 }}s both; opacity: 0;">

                    {{-- Child header --}}
                    <div class="flex items-center gap-3 mb-3">
                        @if(!empty($child['foto']))
                        <img src="{{ $child['foto'] }}" alt="{{ $child['nama'] }}"
                             class="w-12 h-12 rounded-full object-cover border-2 border-[#EDE9FE] shrink-0"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-12 h-12 rounded-full bg-[#F3F0FD] border-2 border-[#EDE9FE] items-center justify-center hidden shrink-0">
                            <ion-icon name="happy-outline" style="font-size:22px;color:#8B46D3;"></ion-icon>
                        </div>
                        @else
                        <div class="w-12 h-12 rounded-full bg-[#F3F0FD] border-2 border-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="happy-outline" style="font-size:22px;color:#8B46D3;"></ion-icon>
                        </div>
                        @endif
                        <div>
                            <p class="text-[#1E1B2E] text-[15px] font-extrabold">{{ $child['nama'] }}</p>
                            <div class="flex items-center gap-1 mt-0.5">
                                <ion-icon name="{{ $childMale ? 'male-outline' : 'female-outline' }}" style="font-size:11px;color:#8B46D3;"></ion-icon>
                                <span class="text-[#8B46D3] text-[11px] font-bold">{{ $childMale ? 'Laki-laki' : 'Perempuan' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-[#E5E1F0] mb-3"></div>

                    <div class="space-y-2">
                        <div class="child-detail-item px-3 py-2 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-[7px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                                <ion-icon name="calendar-outline" style="font-size:13px;color:#4F46E5;"></ion-icon>
                            </div>
                            <div class="flex-1">
                                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">date of birth</p>
                                <p class="text-[#1E1B2E] text-[12px] font-extrabold">{{ $child['tanggal_lahir'] ?? '-' }}</p>
                            </div>
                        </div>

                        @if(!empty($child['catatan_khusus']))
                        <div class="child-detail-item px-3 py-2 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-[7px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                                <ion-icon name="document-text-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                            </div>
                            <div class="flex-1">
                                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">Special notes</p>
                                <p class="text-[#1E1B2E] text-[12px] font-extrabold leading-snug">{{ $child['catatan_khusus'] }}</p>
                            </div>
                        </div>
                        @endif

                        @if(!empty($child['alergi']))
                        <div class="child-detail-item px-3 py-2 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-[7px] bg-[#FEE2E2] flex items-center justify-center shrink-0">
                                <ion-icon name="warning-outline" style="font-size:13px;color:#EF4444;"></ion-icon>
                            </div>
                            <div class="flex-1">
                                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">Alergi</p>
                                <p class="text-[#1E1B2E] text-[12px] font-extrabold leading-snug">{{ $child['alergi'] }}</p>
                            </div>
                        </div>
                        @endif

                        @if(!empty($child['hobi']))
                        <div class="child-detail-item px-3 py-2 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-[7px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                                <ion-icon name="heart-outline" style="font-size:13px;color:#EC4899;"></ion-icon>
                            </div>
                            <div class="flex-1">
                                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.5px]">Hobi</p>
                                <p class="text-[#1E1B2E] text-[12px] font-extrabold leading-snug">{{ $child['hobi'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @else
        <div class="section-card anim delay-5 p-5">
            <div class="flex flex-col items-center py-4">
                <div class="w-16 h-16 rounded-full bg-[#F3F0FD] flex items-center justify-center mb-3">
                    <ion-icon name="people-outline" style="font-size:32px;color:#C4B5FD;"></ion-icon>
                </div>
                <p class="text-[#1E1B2E] text-[14px] font-bold">There is no child data</p>
                <p class="text-[#9CA3AF] text-[12px] font-semibold mt-1">You have registered child information</p>
            </div>
        </div>
        @endif

        {{-- ── TOMBOL HUBUNGI ── --}}
        <div class="anim delay-6 pt-1 pb-4">
            <a href="{{ route('chat.room', ['id' => $data['id_majikan'] ?? 0]) }}" class="btn-chat">
                <ion-icon name="chatbubble-ellipses-outline" style="font-size:18px;"></ion-icon>
                Hubungi Majikan
            </a>
        </div>

    </div>

    @else
    {{-- ── EMPTY STATE ── --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
                bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
                rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
        <div class="flex flex-col items-center justify-center pt-20 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon name="person-circle-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
            </div>
            <h2 class="text-[#1E1B2E] font-bold text-xl mb-2 text-center">Data majikan tidak ditemukan</h2>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">Belum ada penugasan aktif saat ini</p>
        </div>
    </div>
    @endif

    @include('partials.bottom-nav', ['active' => ''])

</div>
</div>

<script>
    (function () {
        const el = document.getElementById('statusTime');
        function tick() {
            const now = new Date();
            if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        tick();
        setInterval(tick, 30000);
    })();
</script>
@include('partials.auth-guard')
</body>
</html>