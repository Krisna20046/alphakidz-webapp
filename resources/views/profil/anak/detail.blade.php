<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Child Details</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { screens: { sm: '1024px' } } } };
    </script>
    <script>
        // Force mobile layout on phones even when "Desktop Site" mode is active
        (function() {
            var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            var isPhoneScreen = window.screen.width <= 430 || window.screen.height <= 932;
            if (isTouchDevice && isPhoneScreen && window.innerWidth >= 1024) {
                var meta = document.querySelector('meta[name="viewport"]');
                if (meta) meta.content = 'width=430, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                var s = document.createElement('style');
                s.textContent = '.phone-wrapper{min-height:100vh!important;display:block!important;padding:0!important;background:#F0EDFB!important}.phone-frame{min-height:100vh!important;width:100%!important;border-radius:0!important;box-shadow:none!important}';
                document.head.appendChild(s);
            }
        })();
    </script>
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

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .info-card {
            background: #FFFFFF;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        #deleteModal { transition:opacity .2s ease; }
        #deleteModalBox { transition:transform .3s cubic-bezier(0.34,1.56,0.64,1); }

        /* ── Medical Info Tabs ─────────────────────────────── */
        .med-wrap {
            background: #FFFFFF;
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .tab-nav {
            display:flex; gap:6px; margin-bottom:14px; background:#F3F0FD;
            padding:4px; border-radius:14px;
        }
        .med-tab {
            flex:1; padding:9px 10px; border-radius:10px; border:none; font-weight:800; font-size:12.5px;
            background:transparent; color:#8B86A5; white-space:nowrap; cursor:pointer;
            transition:all .18s; font-family:'Nunito',sans-serif;
            display:flex; align-items:center; justify-content:center; gap:5px;
        }
        .med-tab ion-icon { font-size:15px; }
        .med-tab.active { background:#8B46D3; color:white; box-shadow:0 4px 10px rgba(139,70,211,0.35); }
        .tab-content { display:none; }
        .tab-content.active { display:block; animation: slideUp .25s ease forwards; }

        .med-count {
            font-size:11px; font-weight:800; color:#8B46D3; background:#EDE9FE;
            padding:2px 9px; border-radius:20px; margin-left:auto;
        }

        .med-card {
            background:#FBFAFF; border-radius:14px; padding:13px 14px; margin-bottom:8px;
            border:1.5px solid #F0ECF9; display:flex; gap:12px; align-items:flex-start;
        }
        .med-icon {
            width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center;
            flex-shrink:0; margin-top:1px;
        }
        .med-body { flex:1; min-width:0; }
        .med-card .name { font-weight:800; font-size:14px; color:#1E1B2E; line-height:1.25; }
        .med-card .badge {
            display:inline-block; font-size:10.5px; font-weight:800; color:#8B46D3; background:#EDE9FE;
            padding:2px 8px; border-radius:20px; margin-top:5px;
        }
        .med-row {
            display:flex; align-items:flex-start; gap:6px; margin-top:6px; font-size:12.5px; color:#1E1B2E; font-weight:600;
        }
        .med-row ion-icon { font-size:14px; color:#A79BC7; flex-shrink:0; margin-top:1px; }
        .med-row .lbl { color:#8B86A5; font-weight:700; }
        .med-note {
            margin-top:6px; font-size:12px; color:#8B86A5; font-weight:600; font-style:italic;
            background:#F5F3FB; padding:6px 10px; border-radius:8px; line-height:1.4;
        }
        .med-note .lbl { font-style:normal; font-weight:800; color:#6B6589; }
        .med-empty {
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:36px 20px; text-align:center;
        }
        .med-empty ion-icon { font-size:38px; color:#D9D0F0; margin-bottom:10px; }
        .med-empty p { font-size:13px; font-weight:700; color:#A79BC7; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">
<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
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

    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('profil.data-anak') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Child Data Details</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5">Complete child data information</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">
        <div class="anim delay-2 flex flex-col items-center pt-1 pb-2">
            @if($anak['foto'] ?? null)
                <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}"
                     class="w-[94px] h-[94px] rounded-full object-cover border-4 border-[#EDE9FE] mb-3 shadow-[0_3px_10px_rgba(0,0,0,0.10)]"/>
            @else
                <div class="w-[94px] h-[94px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] flex items-center justify-center mb-3 shadow-[0_3px_10px_rgba(0,0,0,0.10)]">
                    <ion-icon name="happy-outline" style="font-size:42px;color:#8B46D3;"></ion-icon>
                </div>
            @endif
            <h2 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">{{ $anak['nama'] }}</h2>
            <div class="mt-2 bg-[#EFE9FB] px-4 py-1 rounded-full">
                <span class="text-[#8B46D3] text-[15px] font-bold">Child</span>
            </div>
        </div>

        <div class="anim delay-3 space-y-3">
            <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">Personal Information</p>

            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="calendar-outline" style="font-size:18px;color:#4F46E5;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Date Of Birth</p>
                    <p class="text-[#1E1B2E] text-[14px] font-semibold leading-relaxed mt-1">{{ $anak['tanggal_lahir'] ? \Illuminate\Support\Str::substr($anak['tanggal_lahir'], 0, 10) : '-' }}</p>
                </div>
            </div>

            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                    <ion-icon name="{{ $anak['gender'] === 'L' ? 'male-outline' : 'female-outline' }}" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                    <p class="text-[#1E1B2E] text-[18px] font-semibold leading-relaxed mt-1">{{ $anak['gender'] === 'L' ? 'Male' : 'Female' }}</p>
                </div>
            </div>

            @if($anak['tempat_lahir'] ?? null)
            <div class="info-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#E0F2FE] flex items-center justify-center shrink-0">
                    <ion-icon name="location-outline" style="font-size:18px;color:#0284C7;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Place Of Birth</p>
                    <p class="text-[#1E1B2E] text-[14px] font-semibold leading-relaxed mt-1">{{ $anak['tempat_lahir'] }}</p>
                </div>
            </div>
            @endif
        </div>

        @php
            $hasMoreInformation = ($anak['catatan_khusus'] ?? null) || ($anak['alergi'] ?? null) || ($anak['hobi'] ?? null);
        @endphp
        @if($hasMoreInformation)
        <div class="anim delay-4 space-y-3">
            <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">More Information</p>

            @if($anak['catatan_khusus'] ?? null)
            <div class="info-card p-4 flex items-start gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="create-outline" style="font-size:18px;color:#4F46E5;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Special Note</p>
                    <p class="text-[#1E1B2E] text-[14px] font-semibold leading-relaxed mt-1 whitespace-pre-line break-words">{{ $anak['catatan_khusus'] }}</p>
                </div>
            </div>
            @endif

            @if($anak['alergi'] ?? null)
            <div class="info-card p-4 flex items-start gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="warning-outline" style="font-size:18px;color:#F59E0B;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Allergies</p>
                    <p class="text-[#1E1B2E] text-[14px] font-semibold leading-relaxed mt-1 whitespace-pre-line break-words">{{ $anak['alergi'] }}</p>
                </div>
            </div>
            @endif

            @if($anak['hobi'] ?? null)
            <div class="info-card p-4 flex items-start gap-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="heart" style="font-size:18px;color:#EC4899;"></ion-icon>
                </div>
                <div>
                    <p class="text-[#8B86A5] text-[13px] font-extrabold uppercase tracking-[1.8px]">Hobby</p>
                    <p class="text-[#1E1B2E] text-[14px] font-semibold leading-relaxed mt-1 whitespace-pre-line break-words">{{ $anak['hobi'] }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════════
             MEDICAL INFORMATION (RS, Dokter, Vaksin)
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="anim delay-4 space-y-3">
            <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">Medical Information</p>

            <div class="med-wrap">
                {{-- Tab Nav --}}
                <div class="tab-nav">
                    <button class="med-tab active" data-tab="rs">
                        <ion-icon name="business-outline"></ion-icon> Hospital
                    </button>
                    <button class="med-tab" data-tab="dokter">
                        <ion-icon name="medkit-outline"></ion-icon> Doctor
                    </button>
                    <button class="med-tab" data-tab="vaksin">
                        <ion-icon name="shield-checkmark-outline"></ion-icon> Vaccine
                    </button>
                </div>

                {{-- Tab: Rumah Sakit --}}
                <div class="tab-content active" id="tab-rs">
                    <div id="rsList">
                        @forelse($rumahSakit as $rs)
                        <div class="med-card">
                            <div class="med-icon" style="background:#E0F2FE;">
                                <ion-icon name="business-outline" style="font-size:18px;color:#0284C7;"></ion-icon>
                            </div>
                            <div class="med-body">
                                <div class="med-row" style="margin-top:0;">
                                    <ion-icon name="business-outline"></ion-icon>
                                    <span><span class="lbl">Hospital Name:</span> <strong style="color:#1E1B2E;">{{ $rs['nama_rs'] }}</strong></span>
                                </div>
                                <div class="med-row">
                                    <ion-icon name="pricetag-outline"></ion-icon>
                                    <span><span class="lbl">Category:</span> {{ ['rs' => 'Hospital', 'klinik' => 'Clinic', 'puskesmas' => 'Health Center'][$rs['kategori'] ?? 'rs'] ?? ucfirst($rs['kategori']) }}</span>
                                </div>
                                @if($rs['alamat'] ?? null)
                                <div class="med-row">
                                    <ion-icon name="location-outline"></ion-icon>
                                    <span><span class="lbl">Address:</span> {{ $rs['alamat'] }}</span>
                                </div>
                                @endif
                                @if($rs['no_telp'] ?? null)
                                <div class="med-row">
                                    <ion-icon name="call-outline"></ion-icon>
                                    <span><span class="lbl">Phone Number:</span> {{ $rs['no_telp'] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="med-empty">
                            <ion-icon name="business-outline"></ion-icon>
                            <p>No hospital data yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Tab: Dokter --}}
                <div class="tab-content" id="tab-dokter">
                    <div id="dokterList">
                        @forelse($dokter as $d)
                        <div class="med-card">
                            <div class="med-icon" style="background:#EDE9FE;">
                                <ion-icon name="medkit-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                            </div>
                            <div class="med-body">
                                <div class="med-row" style="margin-top:0;">
                                    <ion-icon name="person-outline"></ion-icon>
                                    <span><span class="lbl">Doctor Name:</span> <strong style="color:#1E1B2E;">{{ $d['nama_dokter'] }}</strong></span>
                                </div>
                                <div class="med-row">
                                    <ion-icon name="pricetag-outline"></ion-icon>
                                    <span><span class="lbl">Specialization:</span> {{ $d['spesialisasi'] ?? 'General' }}</span>
                                </div>
                                @if($d['no_telp'] ?? null)
                                <div class="med-row">
                                    <ion-icon name="call-outline"></ion-icon>
                                    <span><span class="lbl">Phone Number:</span> {{ $d['no_telp'] }}</span>
                                </div>
                                @endif
                                @if($d['alamat_praktek'] ?? null)
                                <div class="med-row">
                                    <ion-icon name="location-outline"></ion-icon>
                                    <span><span class="lbl">Practice Address:</span> {{ $d['alamat_praktek'] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="med-empty">
                            <ion-icon name="medkit-outline"></ion-icon>
                            <p>No doctor data yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Tab: Vaksin --}}
                <div class="tab-content" id="tab-vaksin">
                    <div id="vaksinList">
                        @forelse($vaksin as $v)
                        <div class="med-card">
                            <div class="med-icon" style="background:#FDE8EF;">
                                <ion-icon name="shield-checkmark-outline" style="font-size:18px;color:#EC4899;"></ion-icon>
                            </div>
                            <div class="med-body">
                                <div class="med-row" style="margin-top:0;">
                                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                                    <span><span class="lbl">Vaccine Name:</span> <strong style="color:#1E1B2E;">{{ $v['nama_vaksin'] }}</strong></span>
                                </div>
                                <div class="med-row">
                                    <ion-icon name="calendar-outline"></ion-icon>
                                    <span><span class="lbl">Date:</span> {{ $v['tanggal_vaksin'] ? \Illuminate\Support\Str::substr($v['tanggal_vaksin'], 0, 10) : '-' }}</span>
                                </div>
                                @if($v['tempat_vaksin'] ?? null)
                                <div class="med-row">
                                    <ion-icon name="location-outline"></ion-icon>
                                    <span><span class="lbl">Location:</span> {{ $v['tempat_vaksin'] }}</span>
                                </div>
                                @endif
                                @if($v['dokter_pemberi'] ?? null)
                                <div class="med-row">
                                    <ion-icon name="person-outline"></ion-icon>
                                    <span><span class="lbl">Administering Doctor:</span> {{ $v['dokter_pemberi'] }}</span>
                                </div>
                                @endif
                                @if($v['catatan'] ?? null)
                                <div class="med-note"><span class="lbl">Notes:</span> {{ $v['catatan'] }}</div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="med-empty">
                            <ion-icon name="shield-checkmark-outline"></ion-icon>
                            <p>No vaccine data yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="anim delay-4 space-y-3 pt-2">
            <a href="{{ route('profil.anak.ubah', $anak['id']) }}"
               class="w-full bg-gradient-to-r from-[#7C3AED] to-[#8B46D3] text-white font-extrabold py-4 rounded-[12px] shadow-[0_8px_24px_rgba(139,70,211,0.38)] flex items-center justify-center gap-2 text-[15px]">
                <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                <span class="leading-none">Update</span>
            </a>

            <button onclick="showDeleteModal()"
                    class="w-full bg-white text-[#D22F2F] font-extrabold py-4 rounded-[12px] flex items-center justify-center gap-2 text-[15px] shadow-[0_2px_10px_rgba(0,0,0,0.05)]">
                <ion-icon name="trash" style="font-size:18px;"></ion-icon>
                <span class="leading-none">Delete</span>
            </button>
        </div>
    </div>

    @include('partials.bottom-nav', ['active' => 'profil'])

</div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 hidden opacity-0 px-4 pb-8 sm:pb-0">
    <div id="deleteModalBox" class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl scale-90">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <ion-icon name="trash-outline" style="font-size:30px;color:#ef4444;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] text-lg font-extrabold mb-1">Delete Child Data?</h3>
            <p class="text-[#6B6589] text-sm leading-relaxed">This action cannot be undone. The child's data <strong class="text-[#1E1B2E]">{{ $anak['nama'] }}</strong> will be permanently deleted.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="hideDeleteModal()"
                    class="flex-1 py-3.5 rounded-2xl border-2 border-[#E9E3FB] text-[#8B46D3] font-bold text-sm">
                Cancel
            </button>
            <form method="POST" action="{{ route('profil.anak.hapus', $anak['id']) }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" id="deleteSubmitBtn"
                        class="w-full py-3.5 rounded-2xl bg-red-500 text-white font-bold text-sm active:bg-red-600 transition-all flex items-center justify-center gap-2">
                    <span>Yes, Delete</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function updateClock() {
    const el = document.getElementById('statusTime');
    if (el) {
        const n = new Date();
        el.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
    }
}
updateClock();
setInterval(updateClock, 30000);

const modal = document.getElementById('deleteModal');
const modalBox = document.getElementById('deleteModalBox');

function showDeleteModal() {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modalBox.style.transform = 'scale(1)';
    });
}

function hideDeleteModal() {
    modal.style.opacity = '0';
    modalBox.style.transform = 'scale(0.9)';
    setTimeout(() => modal.classList.add('hidden'), 200);
}
modal.addEventListener('click', (e) => {
    if (e.target === modal) hideDeleteModal();
});

// ── Medical Tab Navigation ─────────────────────────────────
document.querySelectorAll('.med-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.med-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});
</script>
</body>
</html>
