{{-- resources/views/majikan/nanny-anda-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Penugasan — {{ $assignment['nanny_name'] ?? 'Nanny' }}</title>
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

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
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
            background: #F8F8FB;
            border: 1px solid #ECEAF4;
            border-radius: 12px;
        }
        .btn-contact {
            background: #FFFFFF;
            border: 1px solid #E7E3F5;
            color: #8B46D3;
            border-radius: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 48px;
        }
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
            <a href="{{ route('majikan-nanny') }}"
               class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Nanny Assignment Details</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Complete Information on Nanny<br>Assignments</p>
            </div>
        </div>
    </div>

    @if(!isset($assignment))
    {{-- NOT FOUND --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
        <div class="flex flex-col items-center justify-center pt-20 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon name="document-text-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
            </div>
            <h2 class="text-[#1E1B2E] font-bold text-xl mb-2">Data tidak ditemukan</h2>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed mb-6">Data yang Anda cari tidak tersedia</p>
            <a href="{{ route('majikan-nanny') }}"
               class="bg-[#8B46D3] text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    @else
    @php
        // Hitung bulan kerja
        $tglMulai   = $assignment['tanggal_mulai'] ?? null;
        $tglSelesai = $assignment['tanggal_selesai'] ?? null;
        $bulanKerja = $assignment['bulan_kerja'] ?? null;
        if (!$bulanKerja && $tglMulai) {
            try {
                $start      = new \DateTime($tglMulai);
                $end        = $tglSelesai ? new \DateTime($tglSelesai) : new \DateTime();
                $diff       = $start->diff($end);
                $bulanKerja = ($diff->y * 12) + $diff->m;
                if ($bulanKerja < 1) $bulanKerja = 1;
            } catch (\Exception $e) { $bulanKerja = null; }
        }
        $monthLabel = $bulanKerja
            ? $bulanKerja . ' ' . ($bulanKerja == 1 ? 'Month' : 'Months') . ' Of Work'
            : 'On Duty';
        $periodLabel = ($tglMulai && $tglSelesai) ? $tglMulai . ' - ' . $tglSelesai : ($tglMulai ?? '-');
    @endphp

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">

        {{-- ── PROFILE CARD ── --}}
        <div class="section-card anim delay-2 p-5">
            {{-- Foto + Nama + Badge --}}
            <div class="flex flex-col items-center">
                @if(!empty($assignment['nanny_foto']))
                <img src="{{ $assignment['nanny_foto'] }}" alt="{{ $assignment['nanny_name'] }}"
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

                <h2 class="text-[#1E1B2E] text-[22px] font-extrabold mt-3 mb-2">{{ $assignment['nanny_name'] }}</h2>

                {{-- Badge bulan kerja (hijau, dengan icon clock) --}}
                <div class="flex items-center gap-1.5 bg-[#DCFCE7] px-3 py-1.5 rounded-full">
                    <ion-icon name="time-outline" style="font-size:12px;color:#166534;"></ion-icon>
                    <span class="text-[#166534] text-[10px] font-extrabold tracking-wide uppercase">{{ $monthLabel }}</span>
                </div>
            </div>

            <div class="h-px bg-[#E5E1F0] my-4"></div>

            {{-- Info rows: Email, Assignment Period, Phone --}}
            <div class="space-y-2">
                {{-- Email --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                        <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $assignment['nanny_email'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- Assignment Period --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Assignment Period</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $periodLabel }}</p>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="call-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Phone Number</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $assignment['nanny_no_hp'] ?? $assignment['no_hp'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ASSIGNMENT INFORMATION CARD ── --}}
        <div class="section-card anim delay-3 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="briefcase" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Assignment Information</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                {{-- Start Date --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Start Date</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $assignment['tanggal_mulai'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- End Date --}}
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">End Date</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $assignment['tanggal_selesai'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- Notes (kondisional) --}}
                @if(!empty($assignment['catatan']))
                <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0 mt-0.5">
                        <ion-icon name="document-text-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Notes</p>
                        <p class="text-[#8B86A5] text-[12px] font-semibold italic">"{{ $assignment['catatan'] }}"</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── CHILD DATA CARD ── --}}
        @if(!empty($assignment['anak']) && count($assignment['anak']) > 0)
        <div class="section-card anim delay-4 p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="happy" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Child Data</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-3">
                @foreach($assignment['anak'] as $child)
                @php
                    $childMale = ($child['gender'] ?? '') === 'L';
                    // Hitung umur anak
                    $umurTeks = null;
                    if (!empty($child['tanggal_lahir'])) {
                        try {
                            $born = new \DateTime($child['tanggal_lahir']);
                            $now  = new \DateTime();
                            $age  = $born->diff($now);
                            if ($age->y > 0) $umurTeks = $age->y . ' ' . ($age->y == 1 ? 'year' : 'years');
                            elseif ($age->m > 0) $umurTeks = $age->m . ' month' . ($age->m > 1 ? 's' : '');
                            else $umurTeks = $age->d . ' day' . ($age->d != 1 ? 's' : '');
                        } catch (\Exception $e) { $umurTeks = $child['tanggal_lahir']; }
                    }
                @endphp
                <div class="child-card flex items-start gap-3 p-3">
                    {{-- Foto anak --}}
                    @if(!empty($child['foto']))
                    <img src="{{ $child['foto'] }}" alt="{{ $child['nama'] }}"
                         class="w-[60px] h-[60px] rounded-[10px] object-cover bg-[#F3F0FD] shrink-0"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-[60px] h-[60px] rounded-[10px] items-center justify-center hidden bg-[#F3F0FD] shrink-0">
                        <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                    </div>
                    @else
                    <div class="w-[60px] h-[60px] rounded-[10px] flex items-center justify-center bg-[#F3F0FD] shrink-0">
                        <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                    </div>
                    @endif

                    {{-- Info anak --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] text-[14px] font-extrabold mb-1.5">{{ $child['nama'] }}</p>

                        {{-- Umur --}}
                        @if($umurTeks)
                        <div class="flex items-center gap-1.5 mb-1">
                            <ion-icon name="calendar-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold">{{ $umurTeks }}</span>
                        </div>
                        @endif

                        {{-- Gender --}}
                        <div class="flex items-center gap-1.5 mb-1">
                            <ion-icon name="{{ $childMale ? 'male' : 'female' }}" style="font-size:12px;color:{{ $childMale ? '#4F46E5' : '#EC4899' }};flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold">{{ $childMale ? 'Male' : 'Female' }}</span>
                        </div>

                        {{-- Alergi --}}
                        @if(!empty($child['alergi']))
                        <div class="flex items-center gap-1.5 mb-1">
                            <ion-icon name="alert-circle-outline" style="font-size:12px;color:#F59E0B;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold">{{ $child['alergi'] }}</span>
                        </div>
                        @endif

                        {{-- Catatan Khusus --}}
                        @if(!empty($child['catatan_khusus']))
                        <div class="flex items-center gap-1.5 mb-1">
                            <ion-icon name="document-text-outline" style="font-size:12px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold">{{ $child['catatan_khusus'] }}</span>
                        </div>
                        @endif

                        {{-- Hobi --}}
                        @if(!empty($child['hobi']))
                        <div class="flex items-center gap-1.5">
                            <ion-icon name="heart-outline" style="font-size:12px;color:#EC4899;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold">{{ $child['hobi'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── CONTACT BUTTON ── --}}
        <div class="anim delay-5 pt-1">
            <a href="{{ route('chat.room', [$assignment['id_nanny'], 'nama' =>($assignment['nanny_name'])]) }}"
               class="btn-contact shadow-[0_2px_10px_rgba(0,0,0,0.06)] w-full">
                <ion-icon name="chatbubble-ellipses-outline" style="font-size:16px;"></ion-icon>
                <span>Contact a Nanny</span>
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
            if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        tick();
        setInterval(tick, 30000);
    })();
</script>
@include('partials.auth-guard')
</body>
</html>