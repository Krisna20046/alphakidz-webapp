<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $isEdit ? 'Edit Child Data' : 'Add Child Data' }}</title>
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .input-field {
            background: white;
            border: 1.5px solid #DDD6EF;
            border-radius: 14px;
            color: #1E1B2E;
            font-weight: 700;
            font-size: 13px;
            transition: border-color .2s, box-shadow .2s;
        }
        .input-field::placeholder { color: #A8A2C2; font-weight: 600; }
        .input-field:focus {
            outline: none;
            border-color: #8B46D3;
            box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
        }

        .gender-btn {
            border: 1.5px solid #DDD6EF;
            border-radius: 14px;
            background: #fff;
            color: #8B86A5;
            transition: all .2s;
        }
        .gender-btn.active {
            border-color: #8B46D3;
            background: #F1EAFE;
            color: #8B46D3;
        }

        .swal2-popup {
            font-family: 'Nunito', sans-serif;
            border-radius: 22px !important;
            padding: 18px !important;
        }
        .swal2-title { color: #1E1B2E !important; font-weight: 800 !important; font-size: 1.15rem !important; }
        .swal2-html-container { color: #6B6589 !important; font-weight: 600 !important; }
        .swal2-confirm {
            background: linear-gradient(to right, #7C3AED, #8B46D3) !important;
            border-radius: 14px !important;
            font-weight: 800 !important;
            padding: 10px 22px !important;
        }

        /* ── Medical Information (form) ───────────────────────── */
        .med-section-label {
            display:flex; align-items:center; gap:8px; margin-bottom:10px;
        }
        .med-section-label .med-section-icon {
            width:30px; height:30px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .med-section-label .txt { font-weight:800; font-size:13.5px; color:#2C293A; }
        .med-add-btn {
            margin-left:auto; display:flex; align-items:center; gap:4px;
            background:#F1EAFE; color:#8B46D3; font-weight:800; font-size:12px;
            padding:6px 12px; border-radius:20px; border:none; cursor:pointer;
            transition:all .15s;
        }
        .med-add-btn:active { transform:scale(0.95); }

        .med-entry-card {
            background:#FBFAFF; border:1.5px solid #F0ECF9; border-radius:16px;
            padding:14px; margin-bottom:10px; position:relative;
        }
        .med-entry-head {
            display:flex; align-items:center; margin-bottom:10px;
        }
        .med-entry-title {
            font-size:11.5px; font-weight:800; color:#8B46D3; text-transform:uppercase; letter-spacing:0.5px;
            background:#EDE9FE; padding:3px 10px; border-radius:20px;
        }
        .med-remove-btn {
            margin-left:auto; width:28px; height:28px; border-radius:50%; background:#FEE2E2; border:none;
            color:#EF4444; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
        }
        .med-field { margin-bottom:8px; }
        .med-field:last-of-type { margin-bottom:0; }
        .med-field label {
            display:block; font-size:10.5px; font-weight:800; color:#A79BC7; text-transform:uppercase;
            letter-spacing:0.4px; margin-bottom:3px;
        }
        .med-field input, .med-field select, .med-field textarea {
            width:100%; background:white; border:1.5px solid #E7E1F5; border-radius:10px;
            padding:9px 12px; font-size:12.5px; font-weight:700; color:#1E1B2E;
            font-family:'Nunito',sans-serif; outline:none; transition:border-color .15s;
        }
        .med-field input::placeholder, .med-field textarea::placeholder { color:#B7B0D1; font-weight:600; }
        .med-field input:focus, .med-field select:focus, .med-field textarea:focus { border-color:#8B46D3; }
        .med-field-row { display:grid; grid-template-columns:1fr 1fr; gap:8px; }

        .med-empty-state {
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:22px 10px; text-align:center; border:1.5px dashed #E1D9F5; border-radius:14px;
        }
        .med-empty-state ion-icon { font-size:26px; color:#D9D0F0; margin-bottom:6px; }
        .med-empty-state p { font-size:12px; font-weight:700; color:#A79BC7; }
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

    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ $isEdit ? route('profil.anak.detail', $anak['id']) : route('profil.data-anak') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">{{ $isEdit ? 'Edit Child Data' : 'Add Child Data' }}</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">{{ $isEdit ? 'Update your child profile' : 'Complete your child profile' }}</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
        <form id="anakForm" enctype="multipart/form-data" novalidate class="space-y-5">
            @if($isEdit)
            <input type="hidden" name="id" value="{{ $anak['id'] }}">
            @endif

            <div class="anim delay-2 bg-white rounded-[24px] p-[18px] shadow-[0_2px_12px_rgba(0,0,0,0.07)]">
                <div class="flex flex-col items-center">
                    <div class="relative mb-2">
                        <div class="w-[96px] h-[96px] rounded-full border-4 border-[#EDE9FE] overflow-hidden bg-[#F3F0FD] flex items-center justify-center" id="avatarWrap">
                            @if($isEdit && ($anak['foto'] ?? null))
                                <img id="avatarPreview" src="{{ $anak['foto'] }}" class="w-full h-full object-cover" alt="photo"/>
                            @else
                                <ion-icon id="avatarIcon" name="happy-outline" style="font-size:42px;color:#8B46D3;"></ion-icon>
                                <img id="avatarPreview" src="" class="w-full h-full object-cover hidden" alt="photo"/>
                            @endif
                        </div>
                        <label for="fotoInput"
                               class="absolute bottom-0 right-0 w-9 h-9 rounded-full bg-[#8B46D3] border-[2px] border-white flex items-center justify-center cursor-pointer shadow-[0_6px_14px_rgba(139,70,211,0.35)]">
                            <ion-icon name="camera" style="font-size:16px;color:white;"></ion-icon>
                        </label>
                        <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden">
                    </div>
                    <p class="text-[#8B46D3] text-sm font-bold">Add Profile Photo</p>
                </div>
            </div>

            <div class="anim delay-3 bg-white rounded-[24px] p-[18px] shadow-[0_2px_12px_rgba(0,0,0,0.07)] space-y-4">
                <div>
                    <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">Personal Information</p>
                </div>

                <div>
                    <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ $anak['nama'] ?? '' }}" placeholder="Enter Full Name"
                           class="input-field w-full px-4 py-3"/>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Date Of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggalLahir" value="{{ $anak['tanggal_lahir'] ? \Illuminate\Support\Str::substr($anak['tanggal_lahir'], 0, 10) : '' }}" max="{{ date('Y-m-d') }}"
                               class="input-field w-full px-3 py-3"/>
                    </div>
                    <div>
                        <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Gender <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setGender('L')" id="genderL"
                                    class="gender-btn {{ ($anak['gender'] ?? '') === 'L' ? 'active' : '' }} flex items-center justify-center gap-1.5 py-3 text-[12px] font-extrabold">
                                <ion-icon name="male-outline" style="font-size:14px;"></ion-icon>L
                            </button>
                            <button type="button" onclick="setGender('P')" id="genderP"
                                    class="gender-btn {{ ($anak['gender'] ?? '') === 'P' ? 'active' : '' }} flex items-center justify-center gap-1.5 py-3 text-[12px] font-extrabold">
                                <ion-icon name="female-outline" style="font-size:14px;"></ion-icon>P
                            </button>
                        </div>
                        <input type="hidden" name="gender" id="genderInput" value="{{ $anak['gender'] ?? '' }}">
                    </div>
                </div>

                <div>
                    <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Place of Birth</label>
                    <input type="text" name="tempat_lahir" value="{{ $anak['tempat_lahir'] ?? '' }}" placeholder="e.g. Jakarta"
                           class="input-field w-full px-4 py-3"/>
                </div>
            </div>

            <div class="anim delay-4 bg-white rounded-[24px] p-[18px] shadow-[0_2px_12px_rgba(0,0,0,0.07)] space-y-4">
                <div>
                    <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">More Information</p>
                    <p class="text-[#8B46D3] text-[12px] font-bold">Optional - Improve Your Profile</p>
                </div>

                <div>
                    <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Special Note</label>
                    <textarea name="catatan_khusus" rows="3" placeholder="Special notes to be aware of"
                              class="input-field w-full px-4 py-3 resize-none">{{ $anak['catatan_khusus'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Allergies</label>
                    <input type="text" name="alergi" value="{{ $anak['alergi'] ?? '' }}" placeholder="Ex : Chocolate"
                           class="input-field w-full px-4 py-3"/>
                </div>

                <div>
                    <label class="block text-[#2C293A] text-[13px] font-extrabold mb-2">Hobby</label>
                    <input type="text" name="hobi" value="{{ $anak['hobi'] ?? '' }}" placeholder="Ex : Singing"
                           class="input-field w-full px-4 py-3"/>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 MEDICAL INFORMATION (RS, Dokter, Vaksin)
                 ══════════════════════════════════════════════════════════════ --}}
            <div class="anim delay-4 bg-white rounded-[24px] p-[18px] shadow-[0_2px_12px_rgba(0,0,0,0.07)] space-y-5">
                <div>
                    <p class="text-[#5A556E] text-[16px] font-extrabold tracking-wide uppercase">Medical Information</p>
                    <p class="text-[#8B46D3] text-[12px] font-bold">Optional - Hospital, Doctor, Vaccine</p>
                </div>

                {{-- Rumah Sakit --}}
                <div>
                    <div class="med-section-label">
                        <div class="med-section-icon" style="background:#E0F2FE;">
                            <ion-icon name="business-outline" style="font-size:15px;color:#0284C7;"></ion-icon>
                        </div>
                        <span class="txt">Regular Hospital</span>
                        <button type="button" onclick="addMedItem('rs')" class="med-add-btn">
                            <ion-icon name="add-circle" style="font-size:15px;"></ion-icon> Add
                        </button>
                    </div>
                    <div id="rsContainer">
                        @if($isEdit && !empty($rumahSakit))
                            @foreach($rumahSakit as $i => $rs)
                            <div class="med-entry med-entry-card" data-type="rs" data-index="{{ $i }}">
                                <div class="med-entry-head">
                                    <span class="med-entry-title">Hospital {{ $i + 1 }}</span>
                                    <input type="hidden" name="rs[{{ $i }}][id]" value="{{ $rs['id'] ?? '' }}">
                                    <button type="button" onclick="this.closest('.med-entry').remove()" class="med-remove-btn">
                                        <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon>
                                    </button>
                                </div>
                                <div class="med-field">
                                    <label>Hospital Name</label>
                                    <input type="text" name="rs[{{ $i }}][nama_rs]" value="{{ $rs['nama_rs'] }}" placeholder="e.g. RKZ">
                                </div>
                                <div class="med-field">
                                    <label>Category</label>
                                    <select name="rs[{{ $i }}][kategori]">
                                        <option value="rs" {{ ($rs['kategori']??'')=='rs'?'selected':'' }}>Hospital</option>
                                        <option value="klinik" {{ ($rs['kategori']??'')=='klinik'?'selected':'' }}>Clinic</option>
                                        <option value="puskesmas" {{ ($rs['kategori']??'')=='puskesmas'?'selected':'' }}>Health Center</option>
                                    </select>
                                </div>
                                <div class="med-field-row">
                                    <div class="med-field">
                                        <label>Address</label>
                                        <input type="text" name="rs[{{ $i }}][alamat]" value="{{ $rs['alamat'] ?? '' }}" placeholder="Address">
                                    </div>
                                    <div class="med-field">
                                        <label>Phone Number</label>
                                        <input type="text" name="rs[{{ $i }}][no_telp]" value="{{ $rs['no_telp'] ?? '' }}" placeholder="0821...">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="rsEmpty" class="med-empty-state {{ ($isEdit && !empty($rumahSakit)) ? 'hidden' : '' }}">
                        <ion-icon name="business-outline"></ion-icon>
                        <p>No hospital added yet.</p>
                    </div>
                </div>

                {{-- Dokter --}}
                <div>
                    <div class="med-section-label">
                        <div class="med-section-icon" style="background:#EDE9FE;">
                            <ion-icon name="medkit-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                        </div>
                        <span class="txt">Trusted Doctor</span>
                        <button type="button" onclick="addMedItem('dokter')" class="med-add-btn">
                            <ion-icon name="add-circle" style="font-size:15px;"></ion-icon> Add
                        </button>
                    </div>
                    <div id="dokterContainer">
                        @if($isEdit && !empty($dokter))
                            @foreach($dokter as $i => $d)
                            <div class="med-entry med-entry-card" data-type="dokter">
                                <div class="med-entry-head">
                                    <span class="med-entry-title">Doctor {{ $i + 1 }}</span>
                                    <input type="hidden" name="dokter[{{ $i }}][id]" value="{{ $d['id'] ?? '' }}">
                                    <button type="button" onclick="this.closest('.med-entry').remove()" class="med-remove-btn">
                                        <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon>
                                    </button>
                                </div>
                                <div class="med-field">
                                    <label>Doctor Name</label>
                                    <input type="text" name="dokter[{{ $i }}][nama_dokter]" value="{{ $d['nama_dokter'] }}" placeholder="Full name">
                                </div>
                                <div class="med-field">
                                    <label>Specialization</label>
                                    <input type="text" name="dokter[{{ $i }}][spesialisasi]" value="{{ $d['spesialisasi'] ?? '' }}" placeholder="e.g. Sp.A (Pediatrics)">
                                </div>
                                <div class="med-field-row">
                                    <div class="med-field">
                                        <label>Phone Number</label>
                                        <input type="text" name="dokter[{{ $i }}][no_telp]" value="{{ $d['no_telp'] ?? '' }}" placeholder="0821...">
                                    </div>
                                    <div class="med-field">
                                        <label>Practice Address</label>
                                        <input type="text" name="dokter[{{ $i }}][alamat_praktek]" value="{{ $d['alamat_praktek'] ?? '' }}" placeholder="Practice address">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="dokterEmpty" class="med-empty-state {{ ($isEdit && !empty($dokter)) ? 'hidden' : '' }}">
                        <ion-icon name="medkit-outline"></ion-icon>
                        <p>No doctor added yet.</p>
                    </div>
                </div>

                {{-- Vaksin --}}
                <div>
                    <div class="med-section-label">
                        <div class="med-section-icon" style="background:#FDE8EF;">
                            <ion-icon name="shield-checkmark-outline" style="font-size:15px;color:#EC4899;"></ion-icon>
                        </div>
                        <span class="txt">Vaccine List</span>
                        <button type="button" onclick="addMedItem('vaksin')" class="med-add-btn">
                            <ion-icon name="add-circle" style="font-size:15px;"></ion-icon> Add
                        </button>
                    </div>
                    <div id="vaksinContainer">
                        @if($isEdit && !empty($vaksin))
                            @foreach($vaksin as $i => $v)
                            <div class="med-entry med-entry-card" data-type="vaksin">
                                <div class="med-entry-head">
                                    <span class="med-entry-title">Vaccine {{ $i + 1 }}</span>
                                    <input type="hidden" name="vaksin[{{ $i }}][id]" value="{{ $v['id'] ?? '' }}">
                                    <button type="button" onclick="this.closest('.med-entry').remove()" class="med-remove-btn">
                                        <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon>
                                    </button>
                                </div>
                                <div class="med-field-row">
                                    <div class="med-field">
                                        <label>Vaccine Name</label>
                                        <input type="text" name="vaksin[{{ $i }}][nama_vaksin]" value="{{ $v['nama_vaksin'] }}" placeholder="BCG, Polio, etc.">
                                    </div>
                                    <div class="med-field">
                                        <label>Vaccine Date</label>
                                        <input type="date" name="vaksin[{{ $i }}][tanggal_vaksin]" value="{{ $v['tanggal_vaksin'] ? \Illuminate\Support\Str::substr($v['tanggal_vaksin'], 0, 10) : '' }}">
                                    </div>
                                </div>
                                <div class="med-field-row">
                                    <div class="med-field">
                                        <label>Vaccine Location</label>
                                        <input type="text" name="vaksin[{{ $i }}][tempat_vaksin]" value="{{ $v['tempat_vaksin'] ?? '' }}" placeholder="e.g. Health Center A">
                                    </div>
                                    <div class="med-field">
                                        <label>Administering Doctor</label>
                                        <input type="text" name="vaksin[{{ $i }}][dokter_pemberi]" value="{{ $v['dokter_pemberi'] ?? '' }}" placeholder="Doctor name">
                                    </div>
                                </div>
                                <div class="med-field">
                                    <label>Notes</label>
                                    <textarea name="vaksin[{{ $i }}][catatan]" placeholder="Side effects, reactions, etc." rows="2">{{ $v['catatan'] ?? '' }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="vaksinEmpty" class="med-empty-state {{ ($isEdit && !empty($vaksin)) ? 'hidden' : '' }}">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        <p>No vaccine added yet.</p>
                    </div>
                </div>
            </div>

            <div class="anim delay-4 space-y-3 pt-1">
                <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-[#7C3AED] to-[#8B46D3] text-white font-extrabold py-4 rounded-[12px] shadow-[0_8px_24px_rgba(139,70,211,0.38)] flex items-center justify-center gap-2 text-[15px]">
                    <ion-icon name="save-outline" id="btnIcon" style="font-size:18px;"></ion-icon>
                    <span id="btnText" class="leading-none">Save</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>

                <a href="{{ $isEdit ? route('profil.anak.detail', $anak['id']) : route('profil.data-anak') }}"
                   class="w-full bg-white text-[#D22F2F] font-extrabold py-4 rounded-[12px] flex items-center justify-center gap-2 text-[15px] shadow-[0_2px_10px_rgba(0,0,0,0.05)]">
                    <ion-icon name="close-circle" style="font-size:18px;"></ion-icon>
                    <span class="leading-none">Cancel</span>
                </a>
            </div>
        </form>
    </div>

</div>
</div>

<script>
(function () {
    const el = document.getElementById('statusTime');
    function tick() {
        const now = new Date();
        if (el) {
            el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
    }
    tick();
    setInterval(tick, 30000);
})();

function setGender(val) {
    document.getElementById('genderInput').value = val;
    ['L', 'P'].forEach(g => {
        const btn = document.getElementById('gender' + g);
        btn.classList.toggle('active', g === val);
    });
}

document.getElementById('fotoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const preview = document.getElementById('avatarPreview');
    const icon = document.getElementById('avatarIcon');
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (icon) icon.style.display = 'none';
    };
    reader.readAsDataURL(file);
});

function showAlert(msg, type = 'error') {
    Swal.fire({
        text: msg,
        icon: type,
        confirmButtonText: 'OK',
        confirmButtonColor: '#8B46D3',
        timer: type === 'success' ? 2000 : undefined,
        timerProgressBar: type === 'success'
    });
}

function showSuccessAlert(msg, redirectUrl) {
    Swal.fire({
        text: msg,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#8B46D3',
        timer: 2000,
        timerProgressBar: true
    }).then(() => {
        window.location.href = redirectUrl;
    });
}

function setLoading(v) {
    document.getElementById('submitBtn').disabled = v;
    document.getElementById('btnIcon').style.display = v ? 'none' : '';
    document.getElementById('btnSpinner').classList.toggle('hidden', !v);
    document.getElementById('btnText').textContent = v ? 'Saving...' : 'Save';
}

function setLoadingText(text) {
    const btnText = document.getElementById('btnText');
    if (btnText) btnText.textContent = text;
}

// ── Medical Entry Dynamic Add ──────────────────────────────
let medCounters = { rs: 0, dokter: 0, vaksin: 0 };
const medEntryTitles = { rs: 'Hospital', dokter: 'Doctor', vaksin: 'Vaccine' };

function refreshEntryNumbers(type) {
    const container = document.getElementById(type + 'Container');
    container.querySelectorAll('.med-entry').forEach((el, i) => {
        const titleEl = el.querySelector('.med-entry-title');
        if (titleEl) titleEl.textContent = medEntryTitles[type] + ' ' + (i + 1);
    });
}

function addMedItem(type) {
    const container = document.getElementById(type + 'Container');
    const emptyMsg = document.getElementById(type + 'Empty');
    if (emptyMsg) emptyMsg.classList.add('hidden');

    const idx = medCounters[type]++;
    const num = container.querySelectorAll('.med-entry').length + 1;

    let inner = '';

    if (type === 'rs') {
        inner = `
            <div class="med-entry-head">
                <span class="med-entry-title">Hospital ${num}</span>
                <button type="button" onclick="removeMedItem(this,'rs')" class="med-remove-btn"><ion-icon name="trash-outline" style="font-size:14px;"></ion-icon></button>
            </div>
            <div class="med-field">
                <label>Hospital Name</label>
                <input type="text" name="rs[new_${idx}][nama_rs]" placeholder="e.g. RKZ">
            </div>
            <div class="med-field">
                <label>Category</label>
                <select name="rs[new_${idx}][kategori]">
                    <option value="rs">Hospital</option>
                    <option value="klinik">Clinic</option>
                    <option value="puskesmas">Health Center</option>
                </select>
            </div>
            <div class="med-field-row">
                <div class="med-field">
                    <label>Address</label>
                    <input type="text" name="rs[new_${idx}][alamat]" placeholder="Address">
                </div>
                <div class="med-field">
                    <label>Phone Number</label>
                    <input type="text" name="rs[new_${idx}][no_telp]" placeholder="0821...">
                </div>
            </div>`;
    } else if (type === 'dokter') {
        inner = `
            <div class="med-entry-head">
                <span class="med-entry-title">Doctor ${num}</span>
                <button type="button" onclick="removeMedItem(this,'dokter')" class="med-remove-btn"><ion-icon name="trash-outline" style="font-size:14px;"></ion-icon></button>
            </div>
            <div class="med-field">
                <label>Doctor Name</label>
                <input type="text" name="dokter[new_${idx}][nama_dokter]" placeholder="Full name">
            </div>
            <div class="med-field">
                <label>Specialization</label>
                <input type="text" name="dokter[new_${idx}][spesialisasi]" placeholder="e.g. Sp.A (Pediatrics)">
            </div>
            <div class="med-field-row">
                <div class="med-field">
                    <label>Phone Number</label>
                    <input type="text" name="dokter[new_${idx}][no_telp]" placeholder="0821...">
                </div>
                <div class="med-field">
                    <label>Practice Address</label>
                    <input type="text" name="dokter[new_${idx}][alamat_praktek]" placeholder="Practice address">
                </div>
            </div>`;
    } else if (type === 'vaksin') {
        inner = `
            <div class="med-entry-head">
                <span class="med-entry-title">Vaccine ${num}</span>
                <button type="button" onclick="removeMedItem(this,'vaksin')" class="med-remove-btn"><ion-icon name="trash-outline" style="font-size:14px;"></ion-icon></button>
            </div>
            <div class="med-field-row">
                <div class="med-field">
                    <label>Vaccine Name</label>
                    <input type="text" name="vaksin[new_${idx}][nama_vaksin]" placeholder="BCG, Polio, etc.">
                </div>
                <div class="med-field">
                    <label>Vaccine Date</label>
                    <input type="date" name="vaksin[new_${idx}][tanggal_vaksin]">
                </div>
            </div>
            <div class="med-field-row">
                <div class="med-field">
                    <label>Vaccine Location</label>
                    <input type="text" name="vaksin[new_${idx}][tempat_vaksin]" placeholder="e.g. Health Center A">
                </div>
                <div class="med-field">
                    <label>Administering Doctor</label>
                    <input type="text" name="vaksin[new_${idx}][dokter_pemberi]" placeholder="Doctor name">
                </div>
            </div>
            <div class="med-field">
                <label>Notes</label>
                <textarea name="vaksin[new_${idx}][catatan]" placeholder="Side effects, reactions, etc." rows="2"></textarea>
            </div>`;
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'med-entry med-entry-card';
    wrapper.setAttribute('data-type', type);
    wrapper.innerHTML = inner;
    container.appendChild(wrapper);
}

function removeMedItem(btn, type) {
    btn.closest('.med-entry').remove();
    refreshEntryNumbers(type);
    const container = document.getElementById(type + 'Container');
    const emptyMsg = document.getElementById(type + 'Empty');
    if (emptyMsg && container.querySelectorAll('.med-entry').length === 0) {
        emptyMsg.classList.remove('hidden');
    }
}

const isEdit = {{ $isEdit ? 'true' : 'false' }};
const API_BASE_URL = '{{ rtrim(config('services.api.base_url', env('API_BASE_URL', 'http://127.0.0.1:8001/api')), '/') }}';
const AUTH_TOKEN = '{{ session('token') }}';

// ── API Headers ──────────────────────────────────────────────
function apiHeaders(hasFile = false) {
    const headers = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + AUTH_TOKEN,
    };
    if (!hasFile) {
        headers['Content-Type'] = 'application/json';
    }
    return headers;
}

async function apiPost(url, body, hasFile = false) {
    const opts = {
        method: 'POST',
        headers: apiHeaders(hasFile),
    };
    if (hasFile) {
        delete opts.headers['Content-Type']; // biar browser set boundary
        opts.body = body;
    } else {
        opts.body = JSON.stringify(body);
    }
    const res = await fetch(url, opts);
    const data = await res.json();
    if (!res.ok || data.status !== 'success') {
        throw { response: data, status: res.status };
    }
    return data;
}

async function apiPut(url, body) {
    const res = await fetch(url, {
        method: 'PUT',
        headers: apiHeaders(false),
        body: JSON.stringify(body),
    });
    const data = await res.json();
    if (!res.ok || data.status !== 'success') {
        throw { response: data, status: res.status };
    }
    return data;
}

async function apiDelete(url) {
    const res = await fetch(url, {
        method: 'DELETE',
        headers: apiHeaders(false),
    });
    const data = await res.json();
    if (!res.ok || data.status !== 'success') {
        throw { response: data, status: res.status };
    }
    return data;
}

// Track existing medical entry IDs for delete detection on save
const existingMedIds = { rs: [], dokter: [], vaksin: [] };
if (isEdit) {
    ['rs', 'dokter', 'vaksin'].forEach(function(type) {
        document.querySelectorAll('#' + type + 'Container .med-entry input[name$="[id]"]').forEach(function(el) {
            if (el.value) existingMedIds[type].push(el.value);
        });
    });
}

document.getElementById('anakForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    // ── Validasi ──
    const nama = document.getElementById('nama').value.trim();
    const gender = document.getElementById('genderInput').value;
    const tgl = document.getElementById('tanggalLahir').value;

    if (!nama) return showAlert('Child name is required!');
    if (!gender) return showAlert('Gender is required!');
    if (!tgl) return showAlert('Date of birth is required!');
    if (new Date(tgl) > new Date()) return showAlert('Date of birth cannot be in the future!');

    // ── Kirim ──
    setLoading(true);
    try {
        const fd = new FormData(document.getElementById('anakForm'));

        // Hanya kirim field utama anak (buang medical fields)
        const mainFields = ['id', 'nama', 'gender', 'tanggal_lahir', 'tempat_lahir', 'catatan_khusus', 'alergi', 'hobi', 'foto'];
        const mainFd = new FormData();
        for (const field of mainFields) {
            const val = fd.get(field);
            if (val) mainFd.append(field, val);
        }
        // Foto: kirim hanya jika ada file baru
        const fotoInput = document.getElementById('fotoInput');
        if (!fotoInput.files.length) {
            mainFd.delete('foto');
        }

        const isCreate = !isEdit;
        const url = isCreate
            ? `${API_BASE_URL}/user-anak`
            : `${API_BASE_URL}/user-anak-update`;

        console.log('🚀 API Call:', url);
        console.log('📦 Data:', Object.fromEntries(mainFd.entries()));

        const data = await apiPost(url, mainFd, true);
        console.log('✅ API Response:', data);

        const childId = isEdit
            ? {{ $anak['id'] ?? 'null' }}
            : data.user_anak?.id;

        console.log('👶 Child ID:', childId);

        if (childId) {
            setLoadingText('Saving Medical Data...');
            const medResult = await saveMedicalData(childId);
            console.log('🏥 Medical result:', medResult);
        }

        showSuccessAlert(
            data.message || 'Data saved successfully!',
            '{{ route("profil.data-anak") }}'
        );

    } catch (err) {
        console.error('❌ Error:', err);
        const msg = err.response?.message || err.message || 'An error occurred. Try again.';
        const detail = err.response?.errors
            ? Object.values(err.response.errors).flat().join(', ')
            : null;
        showAlert(detail || msg);
    } finally {
        setLoading(false);
    }
});

async function saveMedicalData(childId) {
    console.log('\n=== 🏥 Medical Data Save ===');
    console.log('Child ID:', childId);

    const medTypes = {
        rs:     { path: 'rumah-sakit', required: 'nama_rs' },
        dokter: { path: 'dokter',      required: 'nama_dokter' },
        vaksin: { path: 'vaksin',      required: 'nama_vaksin' },
    };

    const results = { created: [], updated: [], deleted: [], errors: [] };

    for (const [type, cfg] of Object.entries(medTypes)) {
        const container = document.getElementById(type + 'Container');
        if (!container) continue;

        const baseUrl = `${API_BASE_URL}/anak/medical/${cfg.path}`;
        const currentIds = [];
        const entries = container.querySelectorAll('.med-entry');
        console.log(`\n--- ${type} (${entries.length} entries) ---`);

        for (const el of entries) {
            const payload = {};
            let hasValue = false;
            let entryId = null;

            el.querySelectorAll('input, select, textarea').forEach(inp => {
                const parts = inp.name.match(/\[(.+?)\]/g);
                const key = parts ? parts[parts.length - 1].replace(/[\[\]]/g, '') : null;
                if (!key) return;
                payload[key] = inp.value;
                if (key === 'id' && inp.value) entryId = inp.value;
                if (inp.value?.trim()) hasValue = true;
            });

            if (!hasValue) continue;

            try {
                if (entryId) {
                    // UPDATE — PUT /api/anak/medical/{type} with { id, ... } in body
                    currentIds.push(entryId);
                    await apiPut(baseUrl, { ...payload, id_anak: childId });
                    results.updated.push(`${type}:${entryId}`);
                    console.log(`✓ Updated ${type} ${entryId}`);
                } else {
                    // CREATE — POST /api/anak/medical/{type}
                    delete payload.id;
                    if (payload[cfg.required]) {
                        const res = await apiPost(baseUrl, { ...payload, id_anak: childId });
                        if (res.data?.id) currentIds.push(String(res.data.id));
                        results.created.push(type);
                        console.log(`✓ Created ${type}`);
                    }
                }
            } catch (err) {
                console.error(`✗ Error ${type}:`, err.response?.message || err.message);
                results.errors.push({ type, entryId, error: err.response?.message });
            }
        }

        // DELETE removed entries
        const existing = existingMedIds[type] || [];
        const toDelete = existing.filter(id => !currentIds.includes(id));
        for (const id of toDelete) {
            try {
                await apiDelete(`${baseUrl}/${id}?id_anak=${childId}`);
                results.deleted.push(`${type}:${id}`);
                console.log(`✓ Deleted ${type} ${id}`);
            } catch (err) {
                console.error(`✗ Delete error ${type}:${id}`, err.response?.message || err.message);
                results.errors.push({ type, entryId: id, error: err.response?.message });
            }
        }
    }

    console.log('=== ✅ Medical Save Complete ===', results);
    return results;
}
</script>
</body>
</html>
