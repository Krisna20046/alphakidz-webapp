{{-- resources/views/konsultan/majikan-nanny.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Majikan Anda</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href = "https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap"
        rel = "stylesheet">
    <script script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim {
            animation: slideUp 0.4s ease forwards;
            opacity: 0;
        }

        .delay-1 {
            animation-delay: 0.05s;
        }

        .delay-2 {
            animation-delay: 0.13s;
        }

        .delay-3 {
            animation-delay: 0.21s;
        }

        @keyframes floatEmpty {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        .float-anim {
            animation: floatEmpty 3s ease-in-out infinite;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .majikan-card {
            transition: transform .15s ease;
        }

        .majikan-card:active {
            transform: scale(0.98);
        }

        .badge-aktif {
            background: #DCFCE7;
            color: #166534;
        }

        .badge-pending {
            background: #FEF9C3;
            color: #854D0E;
        }

        .badge-inactive {
            background: #FEE2E2;
            color: #991B1B;
        }
    </style>
</head>

<body class="font-['Nunito'] bg-[#E5E2F5]">
    <div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
        <div
            class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

            <div
                class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
                <span id="statusTime">9:41</span>
                <div class="flex items-center gap-1.5">
                    <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                        <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5" />
                        <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white"
                            opacity="0.7" />
                        <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white" />
                    </svg>
                    <div class="flex items-center">
                        <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                            <div class="bg-white rounded-[1.5px] h-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
                <div class="flex items-center gap-3 relative z-10">
                    <a href="{{ route('dashboard') }}"
                        class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                        <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
                    </a>
                    <div>
                        <span class="text-white text-[17px] font-extrabold tracking-wide">Majikan Anda</span>
                        <p class="text-white/60 text-xs font-medium mt-0.5">{{ count($assignments ?? []) }} majikan
                            dalam pengawasan</p>
                    </div>
                </div>
            </div>

            <div
                class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">
                @if (session('success'))
                    <div id="flash-success"
                        class="anim delay-2 bg-[#DCFCE7] border border-[#BBF7D0] text-[#166534] text-xs font-bold px-4 py-3 rounded-[14px] flex items-center gap-2">
                        <ion-icon name="checkmark-circle"
                            style="font-size:16px;color:#16A34A;flex-shrink:0;"></ion-icon>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="anim delay-3">
                    @if (isset($assignments) && count($assignments) > 0)
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-[#5A556E] text-[18px] font-extrabold">Daftar Majikan</h2>
                            <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                                <span class="text-[#8B46D3] text-xs font-bold">{{ count($assignments) }} Majikan</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 pb-6">
                            @foreach ($assignments as $i => $item)
                                @php
                                    $isMale = ($item['majikan_gender'] ?? '') === 'L';
                                    $status = strtolower($item['status'] ?? '');
                                    if ($status === 'aktif') {
                                        $badgeClass = 'badge-aktif';
                                    } elseif ($status === 'nonaktif') {
                                        $badgeClass = 'badge-inactive';
                                    } else {
                                        $badgeClass = 'badge-pending';
                                    }
                                    $statusLabel = !empty($item['status']) ? ucfirst($item['status']) : 'Bertugas';
                                    $nannyName = $item['nanny_name'] ?? null;
                                    $subtitle = $nannyName
                                        ? 'Nanny: ' . $nannyName
                                        : $item['majikan_email'] ?? 'Detail majikan dalam pengawasan Anda';
                                @endphp
                                <a href="{{ route('konsultan-majikan-nanny-detail', $item['id_majikan']) }}"
                                    class="majikan-card block bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]"
                                    style="animation: slideUp 0.35s ease {{ $i * 0.05 }}s both; opacity:0;">
                                    <div class="flex items-center gap-3">
                                        @if (!empty($item['majikan_foto']))
                                            <img src="{{ $item['majikan_foto'] }}" alt="{{ $item['majikan_name'] }}"
                                                class="w-[50px] h-[50px] rounded-[8px] object-cover bg-[#F3F0FD]"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div
                                                class="w-[50px] h-[50px] rounded-[8px] items-center justify-center hidden bg-[#F3F0FD]">
                                                <ion-icon name="person"
                                                    style="font-size:24px;color:#8B46D3;"></ion-icon>
                                            </div>
                                        @else
                                            <div
                                                class="w-[50px] h-[50px] rounded-[8px] flex items-center justify-center bg-[#F3F0FD]">
                                                <ion-icon name="person"
                                                    style="font-size:24px;color:#8B46D3;"></ion-icon>
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">
                                                    {{ $item['majikan_name'] }}</p>
                                                <span
                                                    class="{{ $badgeClass }} text-[10px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1 mb-0.5">
                                                    <ion-icon name="{{ $isMale ? 'male-outline' : 'female-outline' }}"
                                                        style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                                                    <span
                                                        class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $isMale ? 'Laki-laki' : 'Perempuan' }}</span>
                                                </div>
                                                @if (!empty($item['majikan_email']))
                                                    <div class="flex items-center gap-1 mb-0.5">
                                                        <ion-icon name="mail-outline"
                                                            style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                                                        <span
                                                            class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $item['majikan_email'] ?? '-' }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <p class="text-[#8B86A5] text-[11px] italic font-semibold mt-0.5 truncate">
                                                "{{ $subtitle }}"
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center pt-16 pb-10 px-8">
                            <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                                <ion-icon name="people-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                            </div>
                            <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Belum ada majikan</h3>
                            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                                Anda belum memiliki majikan yang terdaftar di bawah pengawasan Anda
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @include('partials.bottom-nav', ['active' => 'home'])

        </div>
    </div>

    <script>
        (function() {
            const el = document.getElementById('statusTime');

            function tick() {
                const now = new Date();
                if (el) el.textContent =
                    `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
            }
            tick();
            setInterval(tick, 30000);
        })();
    </script>
    @include('partials.auth-guard')
</body>

</html>
