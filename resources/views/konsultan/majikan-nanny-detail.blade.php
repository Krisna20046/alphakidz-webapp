{{-- resources/views/konsultan/majikan-nanny-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Penugasan - {{ $assignment['majikan_name'] ?? 'Majikan' }}</title>
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
        .delay-5 { animation-delay: 0.37s; }
        .delay-6 { animation-delay: 0.45s; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        @keyframes avatarPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(139,70,211,0.3); }
            50% { box-shadow: 0 0 0 8px rgba(139,70,211,0); }
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
        .connector-line {
            width: 2px;
            height: 18px;
            background: linear-gradient(to bottom, #DDD6EF, #8B46D3);
            border-radius: 2px;
        }
        .connector-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #8B46D3;
            border: 2px solid #EDE9FE;
        }
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
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-start gap-3 relative z-10">
            <a href="{{ route('konsultan-majikan-nanny') }}"
               class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Detail Penugasan</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Informasi lengkap majikan<br>dan nanny yang ditugaskan</p>
            </div>
        </div>
    </div>

    @if(!isset($assignment))
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
        <div class="flex flex-col items-center justify-center pt-20 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon name="document-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
            </div>
            <h2 class="text-[#1E1B2E] font-bold text-xl mb-2">Data tidak ditemukan</h2>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed mb-6">Data penugasan yang Anda cari tidak tersedia</p>
            <a href="{{ route('konsultan-majikan-nanny') }}"
               class="bg-[#8B46D3] text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    @else
    @php
        $a = $assignment;
        $status = strtolower($a['status'] ?? '');
        $isActive = $status === 'active' || $status === 'aktif';
        $majMale = ($a['majikan_gender'] ?? '') === 'L';
        $nannyMale = ($a['nanny_gender'] ?? '') === 'L';
        $formatDate = function ($date) {
            if (empty($date)) return '-';
            try {
                return (new \DateTime($date))->format('d M Y');
            } catch (\Throwable $e) {
                return $date;
            }
        };
        $calcAge = function ($date) {
            if (empty($date)) return '-';
            try {
                return (new \DateTime())->diff(new \DateTime($date))->y . ' tahun';
            } catch (\Throwable $e) {
                return '-';
            }
        };
    @endphp

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">

        <div class="section-card anim delay-2 p-5">
            <div class="flex flex-col items-center">
                @if(!empty($a['majikan_foto']))
                <img src="{{ $a['majikan_foto'] }}" alt="{{ $a['majikan_name'] }}"
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

                <h2 class="text-[#1E1B2E] text-[22px] font-extrabold mt-3 mb-2">{{ $a['majikan_name'] ?? '-' }}</h2>

                <div class="flex items-center gap-1.5 bg-[#EDE9FE] px-3 py-1.5 rounded-full mb-2">
                    <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                    <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">Majikan</span>
                </div>

                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $isActive ? 'bg-[#DCFCE7]' : 'bg-[#FEE2E2]' }}">
                    <ion-icon name="ellipse" style="font-size:8px;color:{{ $isActive ? '#166534' : '#991B1B' }};"></ion-icon>
                    <span class="text-[10px] font-extrabold tracking-wide uppercase {{ $isActive ? 'text-[#166534]' : 'text-[#991B1B]' }}">
                        {{ $isActive ? 'AKTIF' : 'TIDAK AKTIF' }}
                    </span>
                </div>
            </div>

            @if(!empty($a['catatan']))
            <div class="h-px bg-[#E5E1F0] my-4"></div>
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="document-text-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Catatan</p>
                    <p class="text-[#1E1B2E] text-[12px] font-semibold leading-relaxed">{{ $a['catatan'] }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="section-card anim delay-3 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="clipboard" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Penugasan</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Periode Penugasan</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            {{ $formatDate($a['tanggal_mulai'] ?? null) }} - {{ $formatDate($a['tanggal_selesai'] ?? null) }}
                        </p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="time-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Status Penugasan</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $isActive ? 'Aktif' : 'Tidak Aktif' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="anim delay-4 space-y-2">
            <p class="text-[#8B86A5] text-[10px] font-extrabold uppercase tracking-[1.8px] text-center">Hubungan Penugasan</p>

            <div class="section-card p-5">
                <div class="flex items-center gap-2">
                    <ion-icon name="person-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Majikan</h3>
                </div>
                <div class="h-px bg-[#E5E1F0] my-4"></div>

                <div class="space-y-2">
                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                            <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $a['majikan_email'] ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="{{ $majMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:{{ $majMale ? '#4F46E5' : '#EC4899' }};"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $majMale ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>

                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                            <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Tanggal Lahir</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                                {{ $formatDate($a['majikan_tanggal_lahir'] ?? null) }}
                                @if(!empty($a['majikan_tanggal_lahir']))
                                <span class="text-[#8B86A5] font-semibold">({{ $calcAge($a['majikan_tanggal_lahir']) }})</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                            <ion-icon name="location-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Lokasi</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                                @if(!empty($a['majikan_kota']) && !empty($a['majikan_provinsi']))
                                    {{ $a['majikan_kota'] }}, {{ $a['majikan_provinsi'] }}
                                @else - @endif
                            </p>
                        </div>
                    </div>

                    @if(!empty($a['majikan_alamat']))
                    <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0 mt-0.5">
                            <ion-icon name="home-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Alamat</p>
                            <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $a['majikan_alamat'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col items-center py-1">
                <div class="connector-line"></div>
                <div class="connector-dot"></div>
                <div class="connector-line"></div>
            </div>

            <div class="section-card p-5">
                <div class="flex items-center gap-2">
                    <ion-icon name="heart" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Nanny</h3>
                </div>
                <div class="h-px bg-[#E5E1F0] my-4"></div>

                <div class="flex items-center gap-3 mb-3">
                    @if(!empty($a['nanny_foto']))
                    <img src="{{ $a['nanny_foto'] }}"
                         alt="{{ $a['nanny_name'] }}"
                         class="w-[56px] h-[56px] rounded-[10px] object-cover bg-[#F3F0FD] shrink-0"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-[56px] h-[56px] rounded-[10px] items-center justify-center hidden bg-[#F3F0FD] shrink-0">
                        <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                    </div>
                    @else
                    <div class="w-[56px] h-[56px] rounded-[10px] flex items-center justify-center bg-[#F3F0FD] shrink-0">
                        <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $a['nanny_name'] ?? '-' }}</p>
                        <div class="inline-flex items-center gap-1 bg-[#EDE9FE] px-2.5 py-1 rounded-full mt-1">
                            <ion-icon name="briefcase-outline" style="font-size:11px;color:#8B46D3;"></ion-icon>
                            <span class="text-[#8B46D3] text-[10px] font-extrabold uppercase">Nanny</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                            <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $a['nanny_email'] ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="{{ $nannyMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:{{ $nannyMale ? '#4F46E5' : '#EC4899' }};"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $nannyMale ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>

                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                            <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Tanggal Lahir</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                                {{ $formatDate($a['nanny_tanggal_lahir'] ?? null) }}
                                @if(!empty($a['nanny_tanggal_lahir']))
                                <span class="text-[#8B86A5] font-semibold">({{ $calcAge($a['nanny_tanggal_lahir']) }})</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                            <ion-icon name="location-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Lokasi</p>
                            <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                                @if(!empty($a['nanny_kota']) && !empty($a['nanny_provinsi']))
                                    {{ $a['nanny_kota'] }}, {{ $a['nanny_provinsi'] }}
                                @else - @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="anim delay-6 space-y-3 pt-1">
            <a href="{{ route('chat.room', $a['id_majikan'] ?? 0) }}?nama={{ urlencode($a['majikan_name'] ?? '') }}"
               class="bg-white border border-[#E7E3F5] text-[#8B46D3] rounded-2xl font-extrabold flex items-center justify-center gap-2 h-[52px] shadow-[0_2px_10px_rgba(0,0,0,0.06)] active:scale-[0.97] transition-transform">
                <ion-icon name="chatbubble-ellipses-outline" style="font-size:18px;"></ion-icon>
                <span>Chat dengan Majikan</span>
            </a>
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
</script>
@include('partials.auth-guard')
</body>
</html>
