<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $isEdit ? 'Ubah Data Anak' : 'Tambah Data Anak' }}</title>
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
            @csrf
            @if($isEdit)
            <input type="hidden" name="id" value="{{ $anak['id'] }}">
            @endif

            <div class="anim delay-2 bg-white rounded-[24px] p-[18px] shadow-[0_2px_12px_rgba(0,0,0,0.07)]">
                <div class="flex flex-col items-center">
                    <div class="relative mb-2">
                        <div class="w-[96px] h-[96px] rounded-full border-4 border-[#EDE9FE] overflow-hidden bg-[#F3F0FD] flex items-center justify-center" id="avatarWrap">
                            @if($isEdit && ($anak['foto'] ?? null))
                                <img id="avatarPreview" src="{{ $anak['foto'] }}" class="w-full h-full object-cover" alt="foto"/>
                            @else
                                <ion-icon id="avatarIcon" name="happy-outline" style="font-size:42px;color:#8B46D3;"></ion-icon>
                                <img id="avatarPreview" src="" class="w-full h-full object-cover hidden" alt="foto"/>
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
                        <input type="date" name="tanggal_lahir" id="tanggalLahir" value="{{ $anak['tanggal_lahir'] ?? '' }}" max="{{ date('Y-m-d') }}"
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

const isEdit = {{ $isEdit ? 'true' : 'false' }};
const CSRF = "{{ csrf_token() }}";

document.getElementById('anakForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const nama = document.getElementById('nama').value.trim();
    const gender = document.getElementById('genderInput').value;
    const tgl = document.getElementById('tanggalLahir').value;

    if (!nama) return showAlert('Nama anak wajib diisi!');
    if (!gender) return showAlert('Gender wajib dipilih!');
    if (!tgl) return showAlert('Tanggal lahir wajib diisi!');
    if (new Date(tgl) > new Date()) return showAlert('Tanggal lahir tidak boleh melebihi hari ini!');

    setLoading(true);
    try {
        const fd = new FormData(document.getElementById('anakForm'));
        // Jangan kirim field foto jika tidak ada file baru
        if (!document.getElementById('fotoInput').files.length) {
            fd.delete('foto');
        }
        const url = isEdit ? '{{ route("profil.anak.update") }}' : '{{ route("profil.anak.store") }}';

        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            showSuccessAlert(data.message || 'Data berhasil disimpan!', data.redirect || '{{ route("profil.data-anak") }}');
        } else {
            const err = data.errors ? Object.values(data.errors)[0] : data.message;
            showAlert(Array.isArray(err) ? err[0] : (err || 'Gagal menyimpan.'));
        }
    } catch (err) {
        showAlert('Terjadi kesalahan. Coba lagi.');
    } finally {
        setLoading(false);
    }
});
</script>
</body>
</html>