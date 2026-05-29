<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $isEditing ? 'Edit Profil' : 'Detail Profil' }}</title>
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
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.29s; }
        .delay-5 { animation-delay: 0.37s; }

        @keyframes avatarIn {
            from { opacity: 0; transform: scale(0.82); }
            to   { opacity: 1; transform: scale(1); }
        }
        .avatar-in { animation: avatarIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.1s forwards; opacity: 0; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Input fields */
        .inp {
            width: 100%;
            background: #F5F4FB;
            border: 1.5px solid #E8E4F5;
            border-radius: 12px;
            padding: 13px 16px;
            font-size: 14px;
            font-weight: 600;
            color: #1E1B2E;
            font-family: 'Nunito', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .inp:focus { outline: none; border-color: #8B46D3; box-shadow: 0 0 0 3px rgba(139,70,211,0.12); }
        .inp::placeholder { color: #B0A8CC; font-weight: 500; }

        select.inp { appearance: none; -webkit-appearance: none; cursor: pointer;
                     background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238B46D3' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                     background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; }

        label.field-label { display: block; font-size: 13px; font-weight: 700; color: #1E1B2E; margin-bottom: 7px; }
        label.field-label .req { color: #EF4444; margin-left: 2px; }

        /* Bottom sheet */
        .sheet { transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); transform: translateY(100%); }
        .sheet.open { transform: translateY(0); }
        .sheet-backdrop { transition: opacity 0.3s ease; }

        /* Info card row */
        .info-row { display: flex; align-items: flex-start; gap: 14px; padding: 13px 16px;
                    background: #F8F7FF; border-radius: 14px; }
        .info-icon { width: 40px; height: 40px; border-radius: 12px; display: flex;
                     align-items: center; justify-content: center; flex-shrink: 0; }

        /* ── Success Modal ───────────────────────────── */
        #successModal { transition: opacity 0.25s ease; }

        @keyframes modalBoxIn {
            from { opacity: 0; transform: translateY(40px) scale(0.94); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-box-in { animation: modalBoxIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards; }

        @keyframes badgePop {
            0%   { opacity: 0; transform: scale(0.3) rotate(-15deg); }
            65%  { transform: scale(1.15) rotate(4deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        .badge-pop { animation: badgePop 0.65s cubic-bezier(0.34,1.56,0.64,1) 0.25s both; }

        @keyframes floatDot {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-7px); }
        }
        .dot-r { animation: floatDot 2.2s ease-in-out infinite; }
        .dot-o { animation: floatDot 2.7s ease-in-out 0.4s infinite; }
        .dot-b { animation: floatDot 2.5s ease-in-out 0.7s infinite; }

        @keyframes spinSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .spin-slow { animation: spinSlow 9s linear infinite; }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; } 50% { opacity: 0.25; }
        }
        .pulse-dot { animation: pulseDot 1s ease-in-out infinite; }

        .txt-in-1 { animation: slideUp 0.45s ease 0.5s both; }
        .txt-in-2 { animation: slideUp 0.45s ease 0.65s both; }
        .btn-in   { animation: slideUp 0.45s ease 0.8s both; }
        .rdr-in   { animation: slideUp 0.45s ease 0.95s both; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
                <rect x="13.5" y="0" width="3" height="11" rx="0.6" fill="white" opacity="0.25"/>
            </svg>
            <svg width="16" height="12" viewBox="0 0 16 12" fill="white">
                <path d="M8 3C5.5 3 3.3 4 1.7 5.6L0 3.8C2.1 1.7 5 0.5 8 0.5s5.9 1.2 8 3.3L14.3 5.6C12.7 4 10.5 3 8 3z" opacity="0.5"/>
                <path d="M8 6.5c-1.5 0-2.8.6-3.8 1.5L2.5 6.2C3.9 4.8 5.9 4 8 4s4.1.8 5.5 2.2L11.8 8C10.8 7.1 9.5 6.5 8 6.5z" opacity="0.75"/>
                <circle cx="8" cy="10.5" r="2"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PURPLE HEADER -->
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('profil.index') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <span class="text-white text-[17px] font-extrabold tracking-wide">My Profile</span>
        </div>
    </div>

    <!-- WHITE BODY -->
    <div class="flex-1 overflow-y-auto px-[30px] pt-[30px] pb-20 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 flex flex-col gap-5 hide-scrollbar">

    @if($isEditing)
    {{-- ══════════════════════ EDIT / FORM MODE ══════════════════════ --}}

        <!-- Avatar section -->
        <div class="flex flex-col items-center pt-[28px] pb-[20px]">
            <div class="avatar-in relative mb-2">
                <div class="w-[88px] h-[88px] rounded-full p-[3px]"
                     style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                    {{--
                        BUG FIX: Struktur avatar sekarang konsisten di kedua kondisi.
                        #avatarIcon dan #avatarPreview selalu ada di DOM agar JS
                        tidak error "Cannot set properties of null".
                    --}}
                    @if($user['foto_url'] ?? null)
                        {{-- Sudah ada foto: tampilkan img, icon disembunyikan --}}
                        <ion-icon id="avatarIcon" name="person"
                                  style="display:none; font-size:42px; color:#8B46D3;"></ion-icon>
                        <img id="avatarPreview"
                             src="{{ $user['foto_url'] }}"
                             alt="foto"
                             class="w-full h-full rounded-full object-cover border-2 border-white"/>
                    @else
                        {{-- Belum ada foto: tampilkan placeholder + icon, img disembunyikan --}}
                        <div id="avatarPlaceholder"
                             class="w-full h-full rounded-full bg-[#F0EDFB] border-2 border-white flex items-center justify-center">
                            <ion-icon id="avatarIcon" name="person"
                                      style="font-size:42px; color:#8B46D3;"></ion-icon>
                        </div>
                        <img id="avatarPreview"
                             src=""
                             alt="foto"
                             class="hidden"
                             style="position:absolute; inset:3px; width:calc(100% - 6px); height:calc(100% - 6px); border-radius:9999px; object-fit:cover; border:2px solid white;"/>
                    @endif
                </div>

                <!-- Camera button -->
                <label for="fotoInput"
                       class="absolute -bottom-1 -right-1 w-9 h-9 rounded-full bg-[#8B46D3] border-[2.5px] border-white flex items-center justify-center cursor-pointer shadow-md">
                    <ion-icon name="camera" style="font-size:16px;color:white;"></ion-icon>
                </label>
                <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden">
            </div>
            <p class="text-[#8B46D3] text-[13px] font-bold mt-1">Change Profil Picture</p>
        </div>

        <form id="profileForm" enctype="multipart/form-data" novalidate class="flex flex-col gap-0">
            @csrf

            <!-- Divider -->
            <div class="h-px bg-[#F0EDFB] mb-5"></div>

            <!-- PERSONAL INFORMATION label -->
            <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF] mb-4">Personal Information</p>

            <!-- Full Name -->
            <div class="anim delay-2 mb-4">
                <label class="field-label">Full Name <span class="req">*</span></label>
                <input type="text" name="name" id="name"
                       value="{{ $user['name'] ?? '' }}"
                       placeholder="Nama lengkap"
                       class="inp"/>
            </div>

            <!-- Phone Number -->
            <div class="anim delay-2 mb-4">
                <label class="field-label">Phone Number <span class="req">*</span></label>
                <input type="tel" name="no_hp" id="noHp"
                       value="{{ $user['no_hp'] ?? '' }}"
                       placeholder="+62 8xxxxxxxxx"
                       class="inp"/>
            </div>

            <!-- Date of Birth + Gender (side by side) -->
            <div class="anim delay-2 flex gap-3 mb-4">
                <div class="flex-1">
                    <label class="field-label">Date Of Birth <span class="req">*</span></label>
                    <div class="relative">
                        <input type="date" name="tanggal_lahir" id="tanggalLahir"
                               value="{{ $user['tanggal_lahir'] ?? '' }}"
                               class="inp pr-10"/>
                        <ion-icon name="calendar-outline"
                                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:17px;color:#8B46D3;pointer-events:none;"></ion-icon>
                    </div>
                </div>
                <div class="flex-1">
                    <label class="field-label">Gender <span class="req">*</span></label>
                    <select name="gender" id="genderSelect" class="inp">
                        <option value="">Pilih</option>
                        <option value="L" {{ ($user['gender'] ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ ($user['gender'] ?? '') === 'P' ? 'selected' : '' }}>Wanita</option>
                    </select>
                </div>
            </div>

            <!-- Province -->
            <div class="anim delay-3 mb-4">
                <label class="field-label">Province <span class="req">*</span></label>
                <button type="button" onclick="openSheet('provinsi')"
                        class="inp flex items-center justify-between text-left">
                    <span id="provinsiLabel" class="{{ ($user['provinsi'] ?? '') ? 'text-[#1E1B2E]' : 'text-[#B0A8CC]' }} font-[600]">
                        {{ $user['provinsi'] ?? 'Select Province' }}
                    </span>
                    <ion-icon name="chevron-down-outline" style="font-size:17px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                </button>
                <input type="hidden" name="id_provinsi" id="idProvinsi" value="{{ $user['id_provinsi'] ?? '' }}">
            </div>

            <!-- City / District -->
            <div class="anim delay-3 mb-4">
                <label class="field-label">City / District <span class="req">*</span></label>
                <button type="button" id="kotaBtn" onclick="openSheet('kota')"
                        class="inp flex items-center justify-between text-left {{ !($user['id_provinsi'] ?? '') ? 'opacity-50' : '' }}">
                    <span id="kotaLabel" class="{{ ($user['kota'] ?? '') ? 'text-[#1E1B2E]' : 'text-[#B0A8CC]' }} font-[600]">
                        {{ $user['kota'] ?? 'Select City' }}
                    </span>
                    <ion-icon name="chevron-down-outline" style="font-size:17px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                </button>
                <input type="hidden" name="id_kota" id="idKota" value="{{ $user['id_kota'] ?? '' }}">
            </div>

            <!-- Address -->
            <div class="anim delay-3 mb-5">
                <label class="field-label">Address <span class="req">*</span></label>
                <textarea name="alamat" id="alamat" rows="3"
                          placeholder="Masukkan alamat lengkap"
                          class="inp resize-none">{{ $user['alamat'] ?? '' }}</textarea>
            </div>

            <!-- Divider -->
            <div class="h-px bg-[#F0EDFB] mb-5"></div>

            <!-- MORE INFORMATION -->
            <div class="anim delay-4 mb-4">
                <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF]">More Information</p>
                <p class="text-[#8B46D3] text-[12px] font-semibold mt-[3px]">Optional - Improve Your Profile</p>
            </div>

            <!-- Bio -->
            <div class="anim delay-4 mb-4">
                <label class="field-label">Bio</label>
                <textarea name="bio" rows="3"
                          placeholder="Tell me about yourself"
                          class="inp resize-none">{{ $user['bio'] ?? '' }}</textarea>
            </div>

            {{-- Skill/Pengalaman/Sertifikasi — non-Majikan only --}}
            @if(($user['id_role'] ?? 0) != 2)
            <div class="anim delay-4 mb-4">
                <label class="field-label">Skill</label>
                <textarea name="skill" rows="2"
                          placeholder="Contoh: Masakan Nusantara, Asuh Anak"
                          class="inp resize-none">{{ $user['skill'] ?? '' }}</textarea>
            </div>
            <div class="anim delay-4 mb-4">
                <label class="field-label">Pengalaman (tahun)</label>
                <input type="number" name="pengalaman"
                       value="{{ $user['pengalaman'] ?? '' }}"
                       placeholder="Contoh: 3" min="0"
                       class="inp"/>
            </div>
            <div class="anim delay-5 mb-4">
                <label class="field-label">Sertifikasi</label>
                <textarea name="sertifikasi" rows="2"
                          placeholder="Contoh: CPR, First Aid, PAUD"
                          class="inp resize-none">{{ $user['sertifikasi'] ?? '' }}</textarea>
            </div>
            @endif

            <!-- SAVE CHANGES button -->
            <div class="anim delay-5 mt-2 flex flex-col gap-3">
                <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-[14px] text-[14px] tracking-wide transition-transform duration-150 active:scale-[0.97] shadow-[0_4px_16px_rgba(139,70,211,0.35)]">
                    <ion-icon name="save-outline" id="btnIcon" style="font-size:18px;"></ion-icon>
                    <span id="btnText">Save Changes</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>

                @if($user['is_filled'] == 1)
                <a href="{{ route('profil.detail') }}"
                   class="w-full flex items-center justify-center gap-2 bg-white border-[1.5px] border-[#FECACA] text-[#EF4444] font-extrabold py-[15px] rounded-[14px] text-[14px] tracking-wide transition-transform duration-150 active:scale-[0.97]">
                    <ion-icon name="close-circle" style="font-size:18px;color:#EF4444;"></ion-icon>
                    Cancel
                </a>
                @endif
            </div>

        </form>
        <div class="h-6"></div>

    @else
    {{-- ══════════════════════ VIEW MODE ══════════════════════ --}}

        <!-- Avatar + Name -->
        <div class="flex flex-col items-center pt-[28px] pb-[24px]">
            <div class="avatar-in mb-4">
                @if($user['foto_url'] ?? null)
                    <div class="w-[88px] h-[88px] rounded-full p-[3px]"
                         style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                        <img src="{{ $user['foto_url'] }}" alt="foto"
                             class="w-full h-full rounded-full object-cover border-2 border-white"/>
                    </div>
                @else
                    <div class="w-[88px] h-[88px] rounded-full p-[3px]"
                         style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                        <div class="w-full h-full rounded-full bg-[#F0EDFB] border-2 border-white flex items-center justify-center">
                            <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                        </div>
                    </div>
                @endif
            </div>
            <h1 class="anim delay-2 text-[#1E1B2E] text-[22px] font-extrabold leading-tight mb-3">
                {{ $user['name'] ?? 'Pengguna' }}
            </h1>
            <div class="anim delay-2">
                <span class="inline-block px-5 py-[6px] rounded-full bg-[#EDE9FE] text-[#8B46D3] text-[12px] font-bold">
                    {{ $user['role'] ?? '' }}
                </span>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="flex flex-col gap-[10px] pb-5">

            <!-- Section label -->
            <div class="anim delay-2">
                <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF] mb-[6px]">Personal Information</p>
            </div>

            @php
                $infoRows = [
                    ['icon' => 'mail-outline',     'iconBg' => '#EDE9FE', 'iconClr' => '#8B46D3',
                     'label' => 'EMAIL',           'value' => $user['email'] ?? '-'],
                    ['icon' => 'call-outline',      'iconBg' => '#FCE7F3', 'iconClr' => '#EC4899',
                     'label' => 'PHONE NUMBER',    'value' => $user['no_hp'] ?? '-'],
                    ['icon' => 'calendar-outline',  'iconBg' => '#E0E7FF', 'iconClr' => '#6366F1',
                     'label' => 'DATE OF BIRTH',   'value' => $user['tanggal_lahir'] ?? '-'],
                    ['icon' => 'transgender-outline','iconBg' => '#FEF3C7','iconClr' => '#F59E0B',
                     'label' => 'GENDER',
                     'value' => ($user['gender'] ?? '') === 'L' ? 'Laki-laki' : (($user['gender'] ?? '') === 'P' ? 'Perempuan' : '-')],
                    ['icon' => 'location-outline',  'iconBg' => '#EDE9FE', 'iconClr' => '#8B46D3',
                     'label' => 'LOCATION',
                     'value' => ($user['kota'] ?? '-')],
                    ['icon' => 'home-outline',      'iconBg' => '#FCE7F3', 'iconClr' => '#EC4899',
                     'label' => 'ADDRESS',         'value' => $user['alamat'] ?? '-'],
                ];
                $rowDelay = 200;
            @endphp

            @foreach($infoRows as $row)
            @php $rowDelay += 60; @endphp
            <div class="info-row shadow-[0_2px_12px_rgba(0,0,0,0.07)]" style="animation: slideUp 0.4s ease {{ $rowDelay }}ms both; opacity:0;">
                <div class="info-icon" style="background:{{ $row['iconBg'] }};">
                    <ion-icon name="{{ $row['icon'] }}" style="font-size:18px;color:{{ $row['iconClr'] }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">{{ $row['label'] }}</p>
                    <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px] break-words">{{ $row['value'] }}</p>
                </div>
            </div>
            @endforeach

            {{-- Bio --}}
            @if($user['bio'] ?? null)
            <div class="info-row" style="animation: slideUp 0.4s ease {{ $rowDelay + 60 }}ms both; opacity:0;">
                <div class="info-icon bg-[#F0EDFB]">
                    <ion-icon name="document-text-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">BIO</p>
                    <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px]">{{ $user['bio'] }}</p>
                </div>
            </div>
            @endif

            {{-- Professional info — non-Majikan --}}
            @if(($user['id_role'] ?? 0) != 2)
                @if($user['skill'] ?? null)
                <div class="info-row" style="animation: slideUp 0.4s ease {{ $rowDelay + 80 }}ms both; opacity:0;">
                    <div class="info-icon bg-[#DBEAFE]">
                        <ion-icon name="star-outline" style="font-size:18px;color:#3B82F6;"></ion-icon>
                    </div>
                    <div><p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">SKILL</p>
                    <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px]">{{ $user['skill'] }}</p></div>
                </div>
                @endif
                @if($user['pengalaman'] ?? null)
                <div class="info-row" style="animation: slideUp 0.4s ease {{ $rowDelay + 100 }}ms both; opacity:0;">
                    <div class="info-icon bg-[#D1FAE5]">
                        <ion-icon name="briefcase-outline" style="font-size:18px;color:#10B981;"></ion-icon>
                    </div>
                    <div><p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">PENGALAMAN</p>
                    <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px]">{{ $user['pengalaman'] }} tahun</p></div>
                </div>
                @endif
            @endif

            <!-- UPDATE PROFILE button -->
            <div style="animation: slideUp 0.4s ease {{ $rowDelay + 120 }}ms both; opacity:0; margin-top: 8px;">
                <a href="{{ route('profil.detail', ['edit' => 1]) }}"
                   class="w-full flex items-center justify-center gap-2 bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-[14px] text-[14px] tracking-wide shadow-[0_4px_16px_rgba(139,70,211,0.35)] transition-transform duration-150 active:scale-[0.97]">
                    <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                    Update Profile
                </a>
            </div>

        </div>

    @endif
    </div>{{-- end white body --}}

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'profil'])

</div>
</div>

{{-- ══════════════ BOTTOM SHEETS ══════════════ --}}
<!-- Backdrop -->
<div id="sheetBackdrop"
     class="sheet-backdrop fixed inset-0 bg-black/50 z-40 hidden opacity-0"
     onclick="closeSheet()"></div>

<!-- Provinsi Sheet -->
<div id="provinsiSheet"
     class="sheet fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-[28px] max-h-[75vh] flex flex-col sm:max-w-[390px] sm:mx-auto">
    <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#F0EDFB] shrink-0">
        <h3 class="text-[#1E1B2E] font-extrabold text-[16px]">Pilih Provinsi</h3>
        <button onclick="closeSheet()"
                class="w-8 h-8 rounded-full bg-[#F0EDFB] flex items-center justify-center">
            <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
        </button>
    </div>
    <div class="px-4 py-3 shrink-0">
        <div class="flex items-center gap-2 bg-[#F5F4FB] rounded-[12px] px-4 py-2.5">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <input type="text" id="provinsiSearch" placeholder="Cari provinsi..."
                   oninput="filterList('provinsi', this.value)"
                   class="flex-1 bg-transparent text-sm text-[#1E1B2E] placeholder-[#B0A8CC] outline-none font-semibold"/>
        </div>
    </div>
    <div id="provinsiList" class="overflow-y-auto flex-1 pb-4">
        <div class="flex justify-center py-8">
            <svg class="w-6 h-6 animate-spin" style="color:#8B46D3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Kota Sheet -->
<div id="kotaSheet"
     class="sheet fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-[28px] max-h-[75vh] flex flex-col sm:max-w-[390px] sm:mx-auto">
    <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#F0EDFB] shrink-0">
        <h3 class="text-[#1E1B2E] font-extrabold text-[16px]">Pilih Kota</h3>
        <button onclick="closeSheet()"
                class="w-8 h-8 rounded-full bg-[#F0EDFB] flex items-center justify-center">
            <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
        </button>
    </div>
    <div class="px-4 py-3 shrink-0">
        <div class="flex items-center gap-2 bg-[#F5F4FB] rounded-[12px] px-4 py-2.5">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <input type="text" id="kotaSearch" placeholder="Cari kota..."
                   oninput="filterList('kota', this.value)"
                   class="flex-1 bg-transparent text-sm text-[#1E1B2E] placeholder-[#B0A8CC] outline-none font-semibold"/>
        </div>
    </div>
    <div id="kotaList" class="overflow-y-auto flex-1 pb-4">
        <div class="flex justify-center py-8">
            <svg class="w-6 h-6 animate-spin" style="color:#8B46D3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="toast"
     class="fixed top-5 left-1/2 -translate-x-1/2 z-[60] flex items-center gap-3 bg-white rounded-2xl px-5 py-3.5 shadow-[0_8px_32px_rgba(0,0,0,0.15)] border border-[#F0EDFB] transition-all duration-300 opacity-0 -translate-y-2 pointer-events-none max-w-[340px] w-[90%]">
    <div id="toastIcon" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"></div>
    <p id="toastMsg" class="text-[#1E1B2E] text-[13px] font-bold flex-1"></p>
</div>

{{-- ══════════════ SUCCESS MODAL ══════════════ --}}
<div id="successModal"
     class="fixed inset-0 z-[70] flex items-end justify-center hidden opacity-0"
     style="background: rgba(15,10,35,0.55); backdrop-filter: blur(4px);">

    <div class="modal-box-in w-full sm:max-w-[390px] bg-gradient-to-b from-white via-white to-[#D4BAEF]/40
                rounded-t-[36px] pt-10 pb-12 px-8 flex flex-col items-center relative overflow-hidden min-h-[460px]">

        <!-- Decorative: spinning dashed circle (top-right) -->
        <div class="absolute top-6 right-8 pointer-events-none">
            <svg class="spin-slow w-[75px] h-[75px] text-[#D4BAEF]" viewBox="0 0 75 75" fill="none">
                <circle cx="37.5" cy="37.5" r="33" stroke="currentColor" stroke-width="2.5"
                        stroke-dasharray="7 5" stroke-linecap="round"/>
            </svg>
        </div>

        <!-- Floating dots -->
        <div class="dot-r absolute top-9 left-12 w-3.5 h-3.5 rounded-full bg-[#EF4444] pointer-events-none"></div>
        <div class="dot-o absolute top-[140px] left-8 w-3 h-3 rounded-full bg-[#F59E0B] pointer-events-none"></div>
        <div class="dot-b absolute top-[155px] right-10 w-3.5 h-3.5 rounded-full bg-[#3B82F6] pointer-events-none"></div>

        <!-- Corner shape (bottom-left) -->
        <div class="absolute bottom-16 left-6 pointer-events-none opacity-50">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <rect x="3" y="3" width="54" height="54" rx="14" stroke="#C4B5FD" stroke-width="2.5" fill="none"/>
            </svg>
        </div>

        <!-- Green badge -->
        <div class="badge-pop mb-7 z-10">
            <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                <path d="M60 7
                         L67 22 L83 18.5 L80.5 35 L95 43.5
                         L86 57 L95 70.5 L80.5 79 L83 95.5
                         L67 92 L60 107 L53 92 L37 95.5
                         L39.5 79 L25 70.5 L34 57 L25 43.5
                         L39.5 35 L37 18.5 L53 22 Z"
                      fill="#22C55E"/>
                <path d="M41 60 L54 73 L79 47"
                      stroke="white" stroke-width="5.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h2 class="txt-in-1 text-[#1E1B2E] text-[30px] font-extrabold mb-2 text-center z-10">
            Successful!
        </h2>

        <p class="txt-in-2 text-[#9CA3AF] text-[14px] font-semibold text-center leading-relaxed mb-9 max-w-[240px] z-10">
            Congratulations, your data has been successfully updated.
        </p>

        <div class="btn-in w-full z-10">
            <a href="{{ route('dashboard') }}"
               class="flex items-center justify-center gap-2 w-full bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-full text-[14px] tracking-wide shadow-[0_4px_20px_rgba(139,70,211,0.32)] transition-transform duration-150 active:scale-[0.97]">
                Browse Home <span class="text-base leading-none">→</span>
            </a>
        </div>

        <div class="rdr-in flex items-center gap-2 mt-4 z-10">
            <span class="pulse-dot w-2 h-2 rounded-full bg-[#22C55E] inline-block"></span>
            <span class="text-[#9CA3AF] text-[11px] font-bold uppercase tracking-wider">
                AUTO REDIRECT IN <span id="successCountdown">5</span>s
            </span>
        </div>

    </div>
</div>

<script>
// Clock
(function () {
    const el = document.getElementById('statusTime');
    function tick() {
        const now = new Date();
        if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
    }
    tick(); setInterval(tick, 30000);
})();

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'error') {
    const toast  = document.getElementById('toast');
    const icon   = document.getElementById('toastIcon');
    const msgEl  = document.getElementById('toastMsg');
    const colors = { error: { bg:'#FEE2E2', clr:'#EF4444', name:'close-circle' },
                     success:{ bg:'#D1FAE5', clr:'#10B981', name:'checkmark-circle' } };
    const c = colors[type] || colors.error;
    icon.style.background = c.bg;
    icon.innerHTML = `<ion-icon name="${c.name}" style="font-size:18px;color:${c.clr};"></ion-icon>`;
    msgEl.textContent = msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    toast.style.pointerEvents = 'auto';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(-8px)';
        toast.style.pointerEvents = 'none';
    }, type === 'success' ? 1800 : 3000);
}

// ── Avatar preview ────────────────────────────────────────────────────────────
const fotoInput = document.getElementById('fotoInput');
if (fotoInput) {
    fotoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const preview     = document.getElementById('avatarPreview');
        const placeholder = document.getElementById('avatarPlaceholder');
        const icon        = document.getElementById('avatarIcon');
        const reader      = new FileReader();

        reader.onload = e => {
            // Tampilkan img preview dengan foto baru
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                preview.style.display = '';
            }
            // Sembunyikan placeholder div (hanya ada saat belum ada foto)
            if (placeholder) placeholder.style.display = 'none';
            // Sembunyikan ion-icon (ada di kedua kondisi, tapi display:none di kondisi sudah ada foto)
            if (icon) icon.style.display = 'none';
        };

        reader.readAsDataURL(file);
    });
}

// ── Bottom Sheet ──────────────────────────────────────────────────────────────
let activeSheet  = null;
let provinsiData = [];
let kotaData     = [];
const API_BASE   = "{{ url('/profil') }}";

async function openSheet(type) {
    activeSheet = type;
    const backdrop = document.getElementById('sheetBackdrop');
    const sheet    = document.getElementById(type + 'Sheet');
    backdrop.classList.remove('hidden');
    requestAnimationFrame(() => { backdrop.style.opacity = '1'; sheet.classList.add('open'); });
    if (type === 'provinsi' && provinsiData.length === 0) await loadProvinsi();
    if (type === 'kota') {
        const idProv = document.getElementById('idProvinsi').value;
        if (!idProv) { closeSheet(); showToast('Pilih provinsi terlebih dahulu!'); return; }
        await loadKota(idProv);
    }
}
function closeSheet() {
    if (!activeSheet) return;
    const backdrop = document.getElementById('sheetBackdrop');
    const sheet    = document.getElementById(activeSheet + 'Sheet');
    backdrop.style.opacity = '0';
    sheet.classList.remove('open');
    setTimeout(() => { backdrop.classList.add('hidden'); activeSheet = null; }, 350);
}
async function loadProvinsi() {
    try {
        const res  = await fetch(`${API_BASE}/provinsi`, { headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN } });
        const data = await res.json();
        if (data.success) { provinsiData = data.data; renderList('provinsi', provinsiData); }
    } catch { showToast('Gagal memuat data provinsi.'); }
}
async function loadKota(idProv) {
    kotaData = []; renderListLoading('kota');
    try {
        const res  = await fetch(`${API_BASE}/kota/${idProv}`, { headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN } });
        const data = await res.json();
        if (data.success) { kotaData = data.data; renderList('kota', kotaData); }
    } catch { showToast('Gagal memuat data kota.'); }
}
function renderListLoading(type) {
    document.getElementById(type + 'List').innerHTML = `<div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin" style="color:#8B46D3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></div>`;
}
function renderList(type, data) {
    const el = document.getElementById(type + 'List');
    if (!data || !data.length) { el.innerHTML = '<p class="text-center text-[#9CA3AF] text-sm py-8 font-semibold">Data tidak ditemukan</p>'; return; }
    el.innerHTML = data.map(item =>
        `<button type="button" onclick="selectItem('${type}',${item.id},'${item.nama.replace(/'/g,"\\'")}')"
                 class="w-full text-left px-5 py-[13px] border-b border-[#F5F4FB] text-[14px] font-semibold text-[#1E1B2E] hover:bg-[#F5F4FB] transition-colors">
             ${item.nama}
         </button>`
    ).join('');
}
function selectItem(type, id, nama) {
    if (type === 'provinsi') {
        document.getElementById('idProvinsi').value = id;
        document.getElementById('provinsiLabel').textContent = nama;
        document.getElementById('provinsiLabel').className = 'text-[#1E1B2E] font-[600]';
        document.getElementById('idKota').value = '';
        document.getElementById('kotaLabel').textContent = 'Select City';
        document.getElementById('kotaLabel').className = 'text-[#B0A8CC] font-[600]';
        kotaData = [];
        const kb = document.getElementById('kotaBtn');
        if (kb) { kb.disabled = false; kb.classList.remove('opacity-50'); }
    } else {
        document.getElementById('idKota').value = id;
        document.getElementById('kotaLabel').textContent = nama;
        document.getElementById('kotaLabel').className = 'text-[#1E1B2E] font-[600]';
    }
    closeSheet();
}
function filterList(type, q) {
    const src = type === 'provinsi' ? provinsiData : kotaData;
    renderList(type, src.filter(i => i.nama.toLowerCase().includes(q.toLowerCase())));
}

// ── Form Submit ───────────────────────────────────────────────────────────────
const form = document.getElementById('profileForm');
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name   = document.getElementById('name')?.value.trim();
        const noHp   = document.getElementById('noHp')?.value.trim();
        const tgl    = document.getElementById('tanggalLahir')?.value;
        const gender = document.getElementById('genderSelect')?.value;
        const prov   = document.getElementById('idProvinsi')?.value;
        const kota   = document.getElementById('idKota')?.value;
        const alamat = document.getElementById('alamat')?.value.trim();

        if (!name)   return showToast('Nama wajib diisi!');
        if (!noHp)   return showToast('Nomor HP wajib diisi!');
        if (!tgl)    return showToast('Tanggal lahir wajib diisi!');
        if (!gender) return showToast('Gender wajib dipilih!');
        if (!prov)   return showToast('Provinsi wajib dipilih!');
        if (!kota)   return showToast('Kota wajib dipilih!');
        if (!alamat) return showToast('Alamat wajib diisi!');

        const btn     = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const spinner = document.getElementById('btnSpinner');
        btn.disabled = true;
        btnText.textContent = 'Menyimpan...';
        btnIcon.style.display = 'none';
        spinner.classList.remove('hidden');

        try {
            const fd  = new FormData(form);
            // fotoInput berada di luar <form>, jadi harus ditambahkan manual
            const fotoInput = document.getElementById('fotoInput');
            if (fotoInput && fotoInput.files[0]) {
                fd.append('foto', fotoInput.files[0]);
            }
            const res = await fetch('{{ route("profil.update") }}', {
                method: 'POST',
                headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN },
                body: fd
            });
            const data = await res.json();
            if (data.success) {
                showSuccessModal();
            } else {
                const err = data.errors ? Object.values(data.errors)[0] : (data.message || 'Gagal menyimpan profil.');
                showToast(Array.isArray(err) ? err[0] : err);
            }
        } catch { showToast('Terjadi kesalahan. Coba lagi.'); }
        finally {
            btn.disabled = false;
            btnText.textContent = 'Save Changes';
            btnIcon.style.display = '';
            spinner.classList.add('hidden');
        }
    });
}

let _countdownIv = null;
function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => { modal.style.opacity = '1'; });
    let secs = 5;
    const el = document.getElementById('successCountdown');
    clearInterval(_countdownIv);
    _countdownIv = setInterval(() => {
        secs--;
        if (el) el.textContent = secs;
        if (secs <= 0) {
            clearInterval(_countdownIv);
            window.location.href = '{{ route("dashboard") }}';
        }
    }, 1000);
}
</script>

@include('partials.auth-guard')
</body>
</html>