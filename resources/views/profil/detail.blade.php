<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $isEditing ? 'Edit Profil' : 'Detail Profil' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

        @media (min-width: 640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,0.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; position:relative; }
        }

        .input-field { transition: border-color 0.2s, box-shadow 0.2s; }
        .input-field:focus { outline:none; border-color:#7B1E5A; box-shadow:0 0 0 3px rgba(123,30,90,0.1); }

        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }

        /* Bottom sheet modal */
        .sheet { transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); transform: translateY(100%); }
        .sheet.open { transform: translateY(0); }
        .sheet-backdrop { transition: opacity 0.3s ease; }

        /* Toast */
        #toast { transition: all 0.3s ease; transform: translateY(-100%); opacity: 0; }
        #toast.show { transform: translateY(0); opacity: 1; }

        @keyframes slideUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .anim-up { animation: slideUp 0.4s ease forwards; }
        .delay-1 { animation-delay:0.05s; opacity:0; }
        .delay-2 { animation-delay:0.12s; opacity:0; }
        .delay-3 { animation-delay:0.19s; opacity:0; }
        .delay-4 { animation-delay:0.26s; opacity:0; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- TOP BAR -->
    <div class="flex items-center gap-3 px-4 pt-4 pb-3 bg-plum-pale shrink-0 border-b border-plum-soft/40">
        <a href="{{ route('profil.index') }}"
           class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center">
            <ion-icon name="arrow-back" style="font-size:20px;color:#7B1E5A;"></ion-icon>
        </a>
        <h1 class="text-plum-dark font-extrabold text-base flex-1">
            {{ $isEditing ? ($user['is_filled'] == 0 ? 'Lengkapi Profil' : 'Edit Profil') : 'Detail Profil' }}
        </h1>
        @if(!$isEditing)
        <a href="{{ route('profil.detail', ['edit' => 1]) }}"
           class="flex items-center gap-1.5 bg-plum text-white text-xs font-bold px-3 py-2 rounded-xl">
            <ion-icon name="create-outline" style="font-size:14px;"></ion-icon>
            Edit
        </a>
        @endif
    </div>

    <!-- TOAST -->
    <div id="toast" class="absolute top-14 left-0 right-0 z-50 px-4">
        <div id="toastInner" class="bg-red-500 text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
            <ion-icon name="alert-circle-outline" style="font-size:16px;flex-shrink:0;"></ion-icon>
            <span id="toastMsg"></span>
        </div>
    </div>

    <!-- SCROLLABLE -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-4 pb-6">

    @if($isEditing)
    {{-- ═══════════════ EDIT MODE ═══════════════ --}}

        @if($user['is_filled'] == 0)
        <!-- Incomplete profile banner -->
        <div class="anim-up delay-1 mb-4 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                <ion-icon name="information-circle" style="font-size:20px;color:#d97706;"></ion-icon>
            </div>
            <div>
                <p class="text-amber-800 font-bold text-sm">Profil Belum Lengkap</p>
                <p class="text-amber-700 text-xs mt-0.5 leading-relaxed">Lengkapi data profil untuk melanjutkan menggunakan aplikasi.</p>
            </div>
        </div>
        @endif

        <form id="profileForm" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- FOTO PROFIL -->
            <div class="anim-up delay-1 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 flex flex-col items-center">
                <p class="text-plum-dark font-bold text-sm mb-4 self-start">Foto Profil</p>
                <div class="relative">
                    <div id="avatarPreviewWrap" class="w-24 h-24 rounded-full border-4 border-plum-soft overflow-hidden bg-plum-soft flex items-center justify-center">
                        @if($user['foto_url'] ?? null)
                            <img id="avatarPreview" src="{{ $user['foto_url'] }}" class="w-full h-full object-cover" alt="foto"/>
                        @else
                            <ion-icon id="avatarIcon" name="person" style="font-size:44px;color:#7B1E5A;"></ion-icon>
                            <img id="avatarPreview" src="" class="w-full h-full object-cover hidden" alt="foto"/>
                        @endif
                    </div>
                    <label for="fotoInput"
                           class="absolute -bottom-1 -right-1 w-9 h-9 rounded-full bg-plum border-2 border-white flex items-center justify-center cursor-pointer shadow-md">
                        <ion-icon name="camera" style="font-size:16px;color:white;"></ion-icon>
                    </label>
                    <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden">
                </div>
                <p class="text-plum-muted text-xs mt-3">Ketuk kamera untuk ganti foto</p>
            </div>

            <!-- DATA UTAMA -->
            <div class="anim-up delay-2 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 space-y-4">
                <p class="text-plum-dark font-bold text-sm">Data Utama</p>

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" id="name"
                           value="{{ $user['name'] ?? '' }}"
                           placeholder="Nama lengkap"
                           class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                </div>

                <!-- No HP -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Nomor HP <span class="text-red-400">*</span></label>
                    <input type="tel" name="no_hp" id="noHp"
                           value="{{ $user['no_hp'] ?? '' }}"
                           placeholder="081234567890"
                           class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Tanggal Lahir <span class="text-red-400">*</span></label>
                    <input type="date" name="tanggal_lahir" id="tanggalLahir"
                           value="{{ $user['tanggal_lahir'] ?? '' }}"
                           class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark"/>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-2">Gender <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="setGender('L')"
                                id="genderL"
                                class="gender-btn flex items-center justify-center gap-2 py-3 rounded-2xl border-2 text-sm font-bold transition-all
                                       {{ ($user['gender'] ?? '') === 'L' ? 'border-plum bg-plum-soft text-plum' : 'border-plum-soft bg-white text-plum-muted' }}">
                            <ion-icon name="male-outline" style="font-size:18px;"></ion-icon> Laki-laki
                        </button>
                        <button type="button" onclick="setGender('P')"
                                id="genderP"
                                class="gender-btn flex items-center justify-center gap-2 py-3 rounded-2xl border-2 text-sm font-bold transition-all
                                       {{ ($user['gender'] ?? '') === 'P' ? 'border-plum bg-plum-soft text-plum' : 'border-plum-soft bg-white text-plum-muted' }}">
                            <ion-icon name="female-outline" style="font-size:18px;"></ion-icon> Perempuan
                        </button>
                    </div>
                    <input type="hidden" name="gender" id="genderInput" value="{{ $user['gender'] ?? '' }}">
                </div>
            </div>

            <!-- LOKASI -->
            <div class="anim-up delay-3 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 space-y-4">
                <p class="text-plum-dark font-bold text-sm">Lokasi</p>

                <!-- Provinsi -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Provinsi <span class="text-red-400">*</span></label>
                    <button type="button" onclick="openSheet('provinsi')"
                            class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-left flex items-center justify-between">
                        <span id="provinsiLabel" class="{{ ($user['provinsi'] ?? '') ? 'text-plum-dark' : 'text-plum-muted/50' }}">
                            {{ $user['provinsi'] ?? 'Pilih Provinsi' }}
                        </span>
                        <ion-icon name="chevron-down" style="font-size:18px;color:#A2397B;"></ion-icon>
                    </button>
                    <input type="hidden" name="id_provinsi" id="idProvinsi" value="{{ $user['id_provinsi'] ?? '' }}">
                </div>

                <!-- Kota -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Kota <span class="text-red-400">*</span></label>
                    <button type="button" id="kotaBtn" onclick="openSheet('kota')"
                            class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-left flex items-center justify-between
                                   {{ !($user['id_provinsi'] ?? '') ? 'opacity-50' : '' }}"
                            {{ !($user['id_provinsi'] ?? '') ? 'disabled' : '' }}>
                        <span id="kotaLabel" class="{{ ($user['kota'] ?? '') ? 'text-plum-dark' : 'text-plum-muted/50' }}">
                            {{ $user['kota'] ?? 'Pilih Kota' }}
                        </span>
                        <ion-icon name="chevron-down" style="font-size:18px;color:#A2397B;"></ion-icon>
                    </button>
                    <input type="hidden" name="id_kota" id="idKota" value="{{ $user['id_kota'] ?? '' }}">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Alamat Lengkap <span class="text-red-400">*</span></label>
                    <textarea name="alamat" id="alamat" rows="3"
                              placeholder="Masukkan alamat lengkap"
                              class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50 resize-none">{{ $user['alamat'] ?? '' }}</textarea>
                </div>
            </div>

            <!-- INFORMASI TAMBAHAN -->
            <div class="anim-up delay-4 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 space-y-4">
                <div>
                    <p class="text-plum-dark font-bold text-sm">Informasi Tambahan</p>
                    <p class="text-plum-muted text-xs mt-0.5">Opsional — tingkatkan visibilitas profil</p>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Bio</label>
                    <textarea name="bio" rows="3"
                              placeholder="Ceritakan tentang diri Anda"
                              class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50 resize-none">{{ $user['bio'] ?? '' }}</textarea>
                </div>

                {{-- Field skill/pengalaman/sertifikasi hanya untuk non-Majikan (id_role != 2) --}}
                @if(($user['id_role'] ?? 0) != 2)
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Skill</label>
                    <textarea name="skill" rows="2"
                              placeholder="Contoh: Masakan Nusantara, Asuh Anak"
                              class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50 resize-none">{{ $user['skill'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Pengalaman (tahun)</label>
                    <input type="number" name="pengalaman"
                           value="{{ $user['pengalaman'] ?? '' }}"
                           placeholder="Contoh: 3" min="0"
                           class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Sertifikasi</label>
                    <textarea name="sertifikasi" rows="2"
                              placeholder="Contoh: CPR, First Aid, PAUD"
                              class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50 resize-none">{{ $user['sertifikasi'] ?? '' }}</textarea>
                </div>
                @endif
            </div>

            <!-- SAVE BUTTON -->
            <div class="anim-up delay-4">
                <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-plum to-plum-light text-white font-bold py-4 rounded-2xl shadow-lg shadow-plum/30 flex items-center justify-center gap-2 text-sm active:scale-97 transition-all">
                    <ion-icon name="checkmark-circle-outline" id="btnIcon" style="font-size:18px;"></ion-icon>
                    <span id="btnText">Simpan Profil</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>

                @if($user['is_filled'] == 1)
                <a href="{{ route('profil.detail') }}"
                   class="block text-center text-plum-muted font-semibold text-sm py-3 mt-2">
                    Batal
                </a>
                @endif
            </div>

            <div class="h-4"></div>
        </form>

    @else
    {{-- ═══════════════ VIEW MODE ═══════════════ --}}

        <!-- PROFILE HEADER CARD -->
        <div class="anim-up delay-1 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 flex flex-col items-center">
            @if($user['foto_url'] ?? null)
                <img src="{{ $user['foto_url'] }}" class="w-24 h-24 rounded-full border-4 border-plum-soft object-cover mb-4" alt="foto"/>
            @else
                <div class="w-24 h-24 rounded-full border-4 border-plum-soft bg-plum-soft flex items-center justify-center mb-4">
                    <ion-icon name="person" style="font-size:44px;color:#7B1E5A;"></ion-icon>
                </div>
            @endif
            <h2 class="text-plum-dark text-xl font-extrabold">{{ $user['name'] ?? '-' }}</h2>
            <div class="mt-2 bg-plum-soft px-4 py-1.5 rounded-full">
                <span class="text-plum text-xs font-bold">{{ $user['role'] ?? '-' }}</span>
            </div>
        </div>

        <!-- INFO PRIBADI -->
        <div class="anim-up delay-2 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 space-y-4">
            <p class="text-plum-dark font-bold text-sm">Informasi Pribadi</p>

            @php
                $infoRows = [
                    ['icon' => 'mail-outline',     'label' => 'Email',         'value' => $user['email'] ?? '-'],
                    ['icon' => 'call-outline',      'label' => 'No. HP',        'value' => $user['no_hp'] ?? '-'],
                    ['icon' => 'calendar-outline',  'label' => 'Tanggal Lahir', 'value' => $user['tanggal_lahir'] ?? '-'],
                    ['icon' => 'person-outline',    'label' => 'Gender',        'value' => ($user['gender'] ?? '') === 'L' ? 'Laki-laki' : (($user['gender'] ?? '') === 'P' ? 'Perempuan' : '-')],
                    ['icon' => 'location-outline',  'label' => 'Lokasi',        'value' => ($user['kota'] ?? '') && ($user['provinsi'] ?? '') ? $user['kota'].', '.$user['provinsi'] : '-'],
                    ['icon' => 'home-outline',      'label' => 'Alamat',        'value' => $user['alamat'] ?? '-'],
                ];
            @endphp

            @foreach($infoRows as $row)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="{{ $row['icon'] }}" style="font-size:17px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">{{ $row['label'] }}</p>
                    <p class="text-plum-dark text-sm font-medium mt-0.5 break-words">{{ $row['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Bio --}}
        @if($user['bio'] ?? null)
        <div class="anim-up delay-3 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10">
            <p class="text-plum-dark font-bold text-sm mb-2">Bio</p>
            <p class="text-plum-dark text-sm leading-relaxed">{{ $user['bio'] }}</p>
        </div>
        @endif

        {{-- Profesional (non-Majikan) --}}
        @if(($user['id_role'] ?? 0) != 2 && (($user['skill'] ?? null) || ($user['pengalaman'] ?? null) || ($user['sertifikasi'] ?? null)))
        <div class="anim-up delay-3 bg-white rounded-3xl p-5 mb-4 shadow-sm shadow-plum/10 space-y-4">
            <p class="text-plum font-bold text-sm">Informasi Profesional</p>

            @if($user['skill'] ?? null)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="star-outline" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <div><p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">Skill</p>
                <p class="text-plum-dark text-sm font-medium mt-0.5">{{ $user['skill'] }}</p></div>
            </div>
            @endif

            @if($user['pengalaman'] ?? null)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="briefcase-outline" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <div><p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">Pengalaman</p>
                <p class="text-plum-dark text-sm font-medium mt-0.5">{{ $user['pengalaman'] }} tahun</p></div>
            </div>
            @endif

            @if($user['sertifikasi'] ?? null)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="ribbon-outline" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <div><p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">Sertifikasi</p>
                <p class="text-plum-dark text-sm font-medium mt-0.5">{{ $user['sertifikasi'] }}</p></div>
            </div>
            @endif
        </div>
        @endif

        <!-- EDIT BUTTON -->
        <div class="anim-up delay-4">
            <a href="{{ route('profil.detail', ['edit' => 1]) }}"
               class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-plum to-plum-light text-white font-bold py-4 rounded-2xl shadow-lg shadow-plum/30 text-sm">
                <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                Edit Profil
            </a>
        </div>

        <div class="h-4"></div>

    @endif
    </div>{{-- end scrollable --}}

</div>
</div>

{{-- ═══════════ BOTTOM SHEETS ═══════════ --}}

<!-- Backdrop -->
<div id="sheetBackdrop"
     class="sheet-backdrop fixed inset-0 bg-black/50 z-40 hidden opacity-0"
     onclick="closeSheet()">
</div>

<!-- Provinsi Sheet -->
<div id="provinsiSheet" class="sheet fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-3xl max-h-[75vh] flex flex-col sm:max-w-[390px] sm:mx-auto">
    <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-plum-soft/50 shrink-0">
        <h3 class="text-plum-dark font-extrabold text-base">Pilih Provinsi</h3>
        <button onclick="closeSheet()" class="w-8 h-8 rounded-full bg-plum-soft flex items-center justify-center">
            <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
        </button>
    </div>
    <div class="px-4 py-3 shrink-0">
        <div class="flex items-center gap-2 bg-plum-soft rounded-2xl px-4 py-2.5">
            <ion-icon name="search-outline" style="font-size:16px;color:#A2397B;flex-shrink:0;"></ion-icon>
            <input type="text" id="provinsiSearch" placeholder="Cari provinsi..."
                   oninput="filterList('provinsi', this.value)"
                   class="flex-1 bg-transparent text-sm text-plum-dark placeholder-plum-muted/60 outline-none font-medium"/>
        </div>
    </div>
    <div id="provinsiList" class="overflow-y-auto flex-1 pb-4">
        <div class="flex justify-center py-6">
            <svg class="w-6 h-6 animate-spin text-plum" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Kota Sheet -->
<div id="kotaSheet" class="sheet fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-3xl max-h-[75vh] flex flex-col sm:max-w-[390px] sm:mx-auto">
    <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-plum-soft/50 shrink-0">
        <h3 class="text-plum-dark font-extrabold text-base">Pilih Kota</h3>
        <button onclick="closeSheet()" class="w-8 h-8 rounded-full bg-plum-soft flex items-center justify-center">
            <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
        </button>
    </div>
    <div class="px-4 py-3 shrink-0">
        <div class="flex items-center gap-2 bg-plum-soft rounded-2xl px-4 py-2.5">
            <ion-icon name="search-outline" style="font-size:16px;color:#A2397B;flex-shrink:0;"></ion-icon>
            <input type="text" id="kotaSearch" placeholder="Cari kota..."
                   oninput="filterList('kota', this.value)"
                   class="flex-1 bg-transparent text-sm text-plum-dark placeholder-plum-muted/60 outline-none font-medium"/>
        </div>
    </div>
    <div id="kotaList" class="overflow-y-auto flex-1 pb-4">
        <div class="flex justify-center py-6">
            <svg class="w-6 h-6 animate-spin text-plum" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>
</div>

<script>
// Clock
function updateClock() {
    const el = document.getElementById('statusTime');
    if (el) { const n = new Date(); el.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`; }
}
updateClock(); setInterval(updateClock, 30000);

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'error') {
    const t = document.getElementById('toast');
    const inner = document.getElementById('toastInner');
    document.getElementById('toastMsg').textContent = msg;
    inner.className = `${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2`;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

// ── Gender toggle ─────────────────────────────────────────────────────────────
function setGender(val) {
    document.getElementById('genderInput').value = val;
    const activeClass = 'border-plum bg-plum-soft text-plum';
    const inactiveClass = 'border-plum-soft bg-white text-plum-muted';
    ['L','P'].forEach(g => {
        const btn = document.getElementById('gender' + g);
        if (!btn) return;
        btn.className = btn.className.replace(activeClass, '').replace(inactiveClass, '').trim();
        btn.className += ' ' + (g === val ? activeClass : inactiveClass);
    });
}

// ── Foto preview ─────────────────────────────────────────────────────────────
const fotoInput = document.getElementById('fotoInput');
if (fotoInput) {
    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const preview = document.getElementById('avatarPreview');
        const icon    = document.getElementById('avatarIcon');
        const reader  = new FileReader();
        reader.onload = e => {
            if (preview) { preview.src = e.target.result; preview.classList.remove('hidden'); }
            if (icon)    { icon.style.display = 'none'; }
        };
        reader.readAsDataURL(file);
    });
}

// ── Bottom Sheet ──────────────────────────────────────────────────────────────
let activeSheet    = null;
let provinsiData   = [];
let kotaData       = [];
const API_BASE     = "{{ url('/profil') }}";
const CSRF         = "{{ csrf_token() }}";

async function openSheet(type) {
    activeSheet = type;
    const backdrop = document.getElementById('sheetBackdrop');
    const sheet    = document.getElementById(type + 'Sheet');

    backdrop.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.style.opacity = '1';
        sheet.classList.add('open');
    });

    if (type === 'provinsi' && provinsiData.length === 0) {
        await loadProvinsi();
    }
    if (type === 'kota') {
        const idProvinsi = document.getElementById('idProvinsi').value;
        if (!idProvinsi) { closeSheet(); showToast('Pilih provinsi terlebih dahulu!'); return; }
        await loadKota(idProvinsi);
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
        const res  = await fetch(`${API_BASE}/provinsi`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        const data = await res.json();
        if (data.success) {
            provinsiData = data.data;
            renderList('provinsi', provinsiData);
        }
    } catch (e) { showToast('Gagal memuat data provinsi.'); }
}

async function loadKota(idProvinsi) {
    kotaData = [];
    renderListLoading('kota');
    try {
        const res  = await fetch(`${API_BASE}/kota/${idProvinsi}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        const data = await res.json();
        if (data.success) {
            kotaData = data.data;
            renderList('kota', kotaData);
        }
    } catch (e) { showToast('Gagal memuat data kota.'); }
}

function renderListLoading(type) {
    document.getElementById(type + 'List').innerHTML = `
        <div class="flex justify-center py-6">
            <svg class="w-6 h-6 animate-spin text-plum" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>`;
}

function renderList(type, data) {
    const container = document.getElementById(type + 'List');
    if (!data || data.length === 0) {
        container.innerHTML = '<p class="text-center text-plum-muted text-sm py-8">Data tidak ditemukan</p>';
        return;
    }
    container.innerHTML = data.map(item => `
        <button type="button"
                onclick="selectItem('${type}', ${item.id}, '${item.nama.replace(/'/g, "\\'")}')"
                class="w-full text-left px-5 py-3.5 border-b border-plum-soft/40 text-sm font-medium text-plum-dark hover:bg-plum-soft/50 transition-colors">
            ${item.nama}
        </button>`).join('');
}

function selectItem(type, id, nama) {
    if (type === 'provinsi') {
        document.getElementById('idProvinsi').value  = id;
        document.getElementById('provinsiLabel').textContent = nama;
        document.getElementById('provinsiLabel').className   = 'text-plum-dark';
        // Reset kota
        document.getElementById('idKota').value   = '';
        document.getElementById('kotaLabel').textContent  = 'Pilih Kota';
        document.getElementById('kotaLabel').className    = 'text-plum-muted/50';
        kotaData = [];
        const kotaBtn = document.getElementById('kotaBtn');
        if (kotaBtn) { kotaBtn.disabled = false; kotaBtn.classList.remove('opacity-50'); }
    } else {
        document.getElementById('idKota').value  = id;
        document.getElementById('kotaLabel').textContent = nama;
        document.getElementById('kotaLabel').className   = 'text-plum-dark';
    }
    closeSheet();
}

function filterList(type, query) {
    const source = type === 'provinsi' ? provinsiData : kotaData;
    const filtered = source.filter(item => item.nama.toLowerCase().includes(query.toLowerCase()));
    renderList(type, filtered);
}

// ── Form Submit ───────────────────────────────────────────────────────────────
const form = document.getElementById('profileForm');
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validasi
        const name    = document.getElementById('name')?.value.trim();
        const noHp    = document.getElementById('noHp')?.value.trim();
        const tgl     = document.getElementById('tanggalLahir')?.value;
        const gender  = document.getElementById('genderInput')?.value;
        const prov    = document.getElementById('idProvinsi')?.value;
        const kota    = document.getElementById('idKota')?.value;
        const alamat  = document.getElementById('alamat')?.value.trim();

        if (!name)   return showToast('Nama wajib diisi!');
        if (!noHp)   return showToast('Nomor HP wajib diisi!');
        if (!tgl)    return showToast('Tanggal lahir wajib diisi!');
        if (!gender) return showToast('Gender wajib dipilih!');
        if (!prov)   return showToast('Provinsi wajib dipilih!');
        if (!kota)   return showToast('Kota wajib dipilih!');
        if (!alamat) return showToast('Alamat wajib diisi!');

        // Loading state
        document.getElementById('submitBtn').disabled  = true;
        document.getElementById('btnText').textContent = 'Menyimpan...';
        document.getElementById('btnIcon').style.display   = 'none';
        document.getElementById('btnSpinner').classList.remove('hidden');

        try {
            const formData = new FormData(form);
            const res  = await fetch('{{ route("profil.update") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: formData,
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message || 'Profil berhasil disimpan!', 'success');
                setTimeout(() => { window.location.href = '{{ route("profil.detail") }}'; }, 1500);
            } else {
                const errMsg = data.errors ? Object.values(data.errors)[0] : (data.message || 'Gagal menyimpan profil.');
                showToast(Array.isArray(errMsg) ? errMsg[0] : errMsg);
            }
        } catch (err) {
            showToast('Terjadi kesalahan. Coba lagi.');
        } finally {
            document.getElementById('submitBtn').disabled  = false;
            document.getElementById('btnText').textContent = 'Simpan Profil';
            document.getElementById('btnIcon').style.display   = '';
            document.getElementById('btnSpinner').classList.add('hidden');
        }
    });
}
</script>
</body>
</html>