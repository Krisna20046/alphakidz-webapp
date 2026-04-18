{{-- resources/views/konsultan/nanny-anda-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Nanny — {{ $nanny['name'] ?? 'Nanny' }}</title>
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
        .delay-7      { animation-delay: 0.53s; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        @keyframes avatarPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(139,70,211,0.3); }
            50%     { box-shadow: 0 0 0 8px rgba(139,70,211,0); }
        }
        .avatar-pulse { animation: avatarPulse 2.5s ease-in-out 0.5s infinite; }

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

        /* Modal */
        .modal-overlay { background: rgba(30,11,60,0.5); backdrop-filter: blur(4px); }
        .modal-card    { animation: slideUp 0.25s ease forwards; }
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
            <a href="{{ route('konsultan-nanny-anda') }}"
               class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Detail Nanny</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Informasi lengkap profil<br>dan manajemen akun nanny</p>
            </div>
        </div>
    </div>

    @if(!isset($nanny))
    {{-- NOT FOUND --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
        <div class="flex flex-col items-center justify-center pt-20 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon name="person-circle-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
            </div>
            <h2 class="text-[#1E1B2E] font-bold text-xl mb-2">Data tidak ditemukan</h2>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed mb-6">Data yang Anda cari tidak tersedia</p>
            <a href="{{ route('konsultan-nanny-anda') }}"
               class="bg-[#8B46D3] text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    @else
    @php
        $isActive = (int)($nanny['is_active'] ?? 1) === 1;
        $idUser   = (int)($nanny['id_user']   ?? 0);
        $isMale   = ($nanny['gender'] ?? '') === 'L';
    @endphp

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
        <div id="flash-success"
             class="bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-2xl flex items-center gap-2">
            <ion-icon name="checkmark-circle" style="font-size:16px;color:#16A34A;flex-shrink:0;"></ion-icon>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div id="flash-error"
             class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-4 py-3 rounded-2xl flex items-center gap-2">
            <ion-icon name="alert-circle" style="font-size:16px;color:#DC2626;flex-shrink:0;"></ion-icon>
            {{ session('error') }}
        </div>
        @endif

        {{-- ── PROFILE CARD ── --}}
        <div class="section-card anim delay-2 p-5">
            <div class="flex flex-col items-center">
                @if(!empty($nanny['foto']))
                <img src="{{ $nanny['foto'] }}" alt="{{ $nanny['name'] }}"
                     class="avatar-pulse w-[88px] h-[88px] rounded-full object-cover border-4 border-[#EDE9FE] shadow-[0_3px_10px_rgba(0,0,0,0.12)]"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="avatar-pulse w-[88px] h-[88px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] items-center justify-center hidden">
                    <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                </div>
                @else
                <div class="avatar-pulse w-[88px] h-[88px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                </div>
                @endif

                <h2 class="text-[#1E1B2E] text-[22px] font-extrabold mt-3 mb-2">{{ $nanny['name'] }}</h2>

                {{-- Posisi badge --}}
                <div class="flex items-center gap-1.5 bg-[#EDE9FE] px-3 py-1.5 rounded-full mb-2">
                    <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                    <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">{{ $nanny['posisi'] ?? 'Nanny' }}</span>
                </div>

                {{-- Status badge --}}
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $isActive ? 'bg-[#DCFCE7]' : 'bg-[#FEE2E2]' }}">
                    <ion-icon name="ellipse" style="font-size:8px;color:{{ $isActive ? '#166534' : '#991B1B' }};"></ion-icon>
                    <span class="text-[10px] font-extrabold tracking-wide uppercase {{ $isActive ? 'text-[#166534]' : 'text-[#991B1B]' }}">
                        {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
            </div>

            @if(!empty($nanny['bio']))
            <div class="h-px bg-[#E5E1F0] my-4"></div>
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="information-circle-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Bio</p>
                    <p class="text-[#1E1B2E] text-[12px] font-semibold leading-relaxed">{{ $nanny['bio'] }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- ── CONTACT INFORMATION CARD ── --}}
        <div class="section-card anim delay-3 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="call" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Kontak</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                {{-- Email --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                        <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $nanny['email'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- No HP --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="call-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Nomor HP</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $nanny['no_hp'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PERSONAL INFORMATION CARD ── --}}
        <div class="section-card anim delay-4 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="person-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Pribadi</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                {{-- Tanggal Lahir --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Tanggal Lahir</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $nanny['tanggal_lahir'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- Gender --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $isMale ? 'male-outline' : 'female-outline' }}"
                                  style="font-size:16px;color:{{ $isMale ? '#4F46E5' : '#EC4899' }};"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            @php $g = $nanny['gender'] ?? ''; echo $g === 'L' ? 'Laki-laki' : ($g === 'P' ? 'Perempuan' : '-'); @endphp
                        </p>
                    </div>
                </div>

                {{-- Lokasi --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                        <ion-icon name="location-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Lokasi</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            @if(!empty($nanny['kota']) && !empty($nanny['provinsi']))
                                {{ $nanny['kota'] }}, {{ $nanny['provinsi'] }}
                            @else -
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Alamat (kondisional) --}}
                @if(!empty($nanny['alamat']))
                <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0 mt-0.5">
                        <ion-icon name="home-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Alamat</p>
                        <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['alamat'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── PROFESSIONAL INFORMATION CARD ── --}}
        @if(!empty($nanny['skill']) || !empty($nanny['pengalaman']) || !empty($nanny['sertifikasi']))
        <div class="section-card anim delay-5 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="briefcase" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Profesional</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                @if(!empty($nanny['skill']))
                <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0 mt-0.5">
                        <ion-icon name="star-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Skill</p>
                        <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['skill'] }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($nanny['pengalaman']))
                <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0 mt-0.5">
                        <ion-icon name="time-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Pengalaman</p>
                        <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['pengalaman'] }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($nanny['sertifikasi']))
                <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#DCFCE7] flex items-center justify-center shrink-0 mt-0.5">
                        <ion-icon name="ribbon-outline" style="font-size:16px;color:#166534;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Sertifikasi</p>
                        <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['sertifikasi'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── ACTION BUTTONS ── --}}
        <div class="anim delay-6 space-y-3 pt-1">

            {{-- Hubungi Nanny --}}
            <a href="{{ route('chat.room', $idUser) }}"
               class="bg-white border border-[#E7E3F5] text-[#8B46D3] rounded-2xl font-extrabold
                      flex items-center justify-center gap-2 h-[52px]
                      shadow-[0_2px_10px_rgba(0,0,0,0.06)] active:scale-[0.97] transition-transform">
                <ion-icon name="chatbubble-ellipses-outline" style="font-size:18px;"></ion-icon>
                <span>Hubungi Nanny</span>
            </a>

            {{-- Toggle Status Akun --}}
            @if($isActive)
            <button onclick="openStatusModal()"
                    class="w-full flex items-center justify-center gap-2 h-[52px] rounded-2xl font-extrabold text-[14px]
                           bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626]
                           active:scale-[0.97] transition-transform">
                <ion-icon name="close-circle-outline" style="font-size:18px;"></ion-icon>
                Nonaktifkan Akun
            </button>
            @else
            <button onclick="openStatusModal()"
                    class="w-full flex items-center justify-center gap-2 h-[52px] rounded-2xl font-extrabold text-[14px]
                           bg-[#F0FDF4] border border-[#BBF7D0] text-[#16A34A]
                           active:scale-[0.97] transition-transform">
                <ion-icon name="checkmark-circle-outline" style="font-size:18px;"></ion-icon>
                Aktifkan Akun
            </button>
            @endif
        </div>

    </div>

    {{-- ── STATUS MODAL ── --}}
    <div id="statusModal"
         class="hidden fixed inset-0 z-50 flex items-end justify-center sm:items-center"
         style="padding-bottom: 80px;">
        <div class="modal-overlay absolute inset-0" onclick="closeStatusModal()"></div>
        <div class="modal-card relative bg-white rounded-[24px] mx-5 p-6 w-full max-w-sm shadow-[0_20px_60px_rgba(0,0,0,0.2)] z-10">
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-[68px] h-[68px] rounded-full flex items-center justify-center mb-4
                     {{ $isActive ? 'bg-[#FEF2F2]' : 'bg-[#F0FDF4]' }}">
                    <ion-icon name="{{ $isActive ? 'close-circle' : 'checkmark-circle' }}"
                              style="font-size:36px;color:{{ $isActive ? '#DC2626' : '#16A34A' }};"></ion-icon>
                </div>
                <h3 class="text-[#1E1B2E] font-extrabold text-[18px] mb-2">
                    {{ $isActive ? 'Nonaktifkan Akun?' : 'Aktifkan Akun?' }}
                </h3>
                <p class="text-[#8B86A5] text-[13px] leading-relaxed">
                    Anda akan
                    <span class="font-extrabold {{ $isActive ? 'text-[#DC2626]' : 'text-[#16A34A]' }}">
                        {{ $isActive ? 'menonaktifkan' : 'mengaktifkan' }}
                    </span>
                    akun nanny <span class="font-extrabold text-[#1E1B2E]">{{ $nanny['name'] }}</span>.
                    {{ $isActive ? 'Nanny tidak dapat login setelah dinonaktifkan.' : 'Nanny dapat kembali login setelah diaktifkan.' }}
                </p>
            </div>

            <div class="flex gap-3">
                <button onclick="closeStatusModal()"
                        class="flex-1 h-[48px] rounded-2xl border border-[#ECEAF4] bg-[#F8F8FB]
                               text-[#8B86A5] font-extrabold text-[13px]
                               active:scale-[0.97] transition-transform">
                    Batal
                </button>
                <form action="{{ route('konsultan-nanny-update-status') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="id"         value="{{ $idUser }}">
                    <input type="hidden" name="is_active"  value="{{ $isActive ? 0 : 1 }}">
                    <input type="hidden" name="redirect_id" value="{{ $nanny['id_user'] }}">
                    <button type="submit"
                            class="w-full h-[48px] rounded-2xl text-white font-extrabold text-[13px]
                                   active:scale-[0.97] transition-transform
                                   {{ $isActive
                                       ? 'bg-[#DC2626] shadow-[0_4px_12px_rgba(220,38,38,0.35)]'
                                       : 'bg-[#16A34A] shadow-[0_4px_12px_rgba(22,163,74,0.35)]' }}">
                        Ya, {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @endif

    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
    (function () {
        const el = document.getElementById('statusTime');
        function tick() {
            const now = new Date();
            if (el) el.textContent =
                `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        tick();
        setInterval(tick, 30000);
    })();

    function openStatusModal() {
        const m = document.getElementById('statusModal');
        m.classList.remove('hidden');
    }
    function closeStatusModal() {
        const m = document.getElementById('statusModal');
        m.classList.add('hidden');
    }

    setTimeout(function () {
        ['flash-success', 'flash-error'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }, 4000);
</script>
@include('partials.auth-guard')
</body>
</html>