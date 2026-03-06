<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $isEdit ? 'Ubah Data Anak' : 'Tambah Data Anak' }}</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambahkan SweetAlert2 CSS dan JS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { plum:{DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B'} },
                fontFamily: { sans:['Plus Jakarta Sans','sans-serif'] }
            }}
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFF9FB; }
        @media (min-width:640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3,#ede0f0,#e8d5ee); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,0.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; position:relative; }
        }
        .input-field { transition:border-color .2s,box-shadow .2s; }
        .input-field:focus { outline:none; border-color:#7B1E5A; box-shadow:0 0 0 3px rgba(123,30,90,0.1); }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        @keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim-up{animation:slideUp .4s ease forwards;}
        .d1{animation-delay:.05s;opacity:0} .d2{animation-delay:.12s;opacity:0} .d3{animation-delay:.19s;opacity:0} .d4{animation-delay:.26s;opacity:0}
        
        /* Custom SweetAlert2 styling */
        .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-radius: 24px !important;
            padding: 20px !important;
        }
        .swal2-title {
            color: #4A0E35 !important;
            font-weight: 800 !important;
            font-size: 1.25rem !important;
        }
        .swal2-html-container {
            color: #A2397B !important;
            font-weight: 500 !important;
        }
        .swal2-confirm {
            background: linear-gradient(to right, #7B1E5A, #9B2E72) !important;
            border-radius: 16px !important;
            font-weight: 700 !important;
            padding: 12px 24px !important;
        }
        .swal2-cancel {
            border-radius: 16px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
        }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- TOP BAR -->
    <div class="flex items-center gap-3 px-4 pt-4 pb-3 bg-plum-pale shrink-0 border-b border-plum-soft/40">
        <a href="{{ $isEdit ? route('profil.anak.detail', $anak['id']) : route('profil.data-anak') }}"
           class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center">
            <ion-icon name="arrow-back" style="font-size:20px;color:#7B1E5A;"></ion-icon>
        </a>
        <h1 class="text-plum-dark font-extrabold text-base flex-1">
            {{ $isEdit ? 'Ubah Data Anak' : 'Tambah Data Anak' }}
        </h1>
    </div>


    <!-- SCROLL BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-4 space-y-4">

        <form id="anakForm" enctype="multipart/form-data" novalidate>
            @csrf
            @if($isEdit)
            <input type="hidden" name="id" value="{{ $anak['id'] }}">
            @endif

            <!-- FOTO -->
            <div class="anim-up d1 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 flex flex-col items-center">
                <p class="text-plum-dark font-bold text-sm mb-4 self-start">Foto Anak</p>
                <div class="relative">
                    <div class="w-24 h-24 rounded-2xl border-4 border-plum-soft overflow-hidden bg-plum-soft flex items-center justify-center" id="avatarWrap">
                        @if($isEdit && ($anak['foto'] ?? null))
                            <img id="avatarPreview" src="{{ $anak['foto'] }}" class="w-full h-full object-cover" alt="foto"/>
                        @else
                            <ion-icon id="avatarIcon" name="happy-outline" style="font-size:40px;color:#7B1E5A;"></ion-icon>
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
            <div class="anim-up d2 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 space-y-4">
                <p class="text-plum-dark font-bold text-sm">Data Utama</p>

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Nama Anak <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <ion-icon name="happy-outline" style="font-size:16px;color:#A2397B;"></ion-icon>
                        </div>
                        <input type="text" name="nama" id="nama"
                               value="{{ $anak['nama'] ?? '' }}"
                               placeholder="Nama anak"
                               class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                    </div>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-2">Gender <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="setGender('L')" id="genderL"
                                class="gender-btn flex items-center justify-center gap-2 py-3 rounded-2xl border-2 text-sm font-bold transition-all
                                       {{ ($anak['gender'] ?? '') === 'L' ? 'border-plum bg-plum-soft text-plum' : 'border-plum-soft bg-white text-plum-muted' }}">
                            <ion-icon name="male-outline" style="font-size:18px;"></ion-icon> Laki-laki
                        </button>
                        <button type="button" onclick="setGender('P')" id="genderP"
                                class="gender-btn flex items-center justify-center gap-2 py-3 rounded-2xl border-2 text-sm font-bold transition-all
                                       {{ ($anak['gender'] ?? '') === 'P' ? 'border-plum bg-plum-soft text-plum' : 'border-plum-soft bg-white text-plum-muted' }}">
                            <ion-icon name="female-outline" style="font-size:18px;"></ion-icon> Perempuan
                        </button>
                    </div>
                    <input type="hidden" name="gender" id="genderInput" value="{{ $anak['gender'] ?? '' }}">
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Tanggal Lahir <span class="text-red-400">*</span></label>
                    <input type="date" name="tanggal_lahir" id="tanggalLahir"
                           value="{{ $anak['tanggal_lahir'] ?? '' }}"
                           max="{{ date('Y-m-d') }}"
                           class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark"/>
                </div>
            </div>

            <!-- INFORMASI TAMBAHAN -->
            <div class="anim-up d3 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 space-y-4">
                <div>
                    <p class="text-plum-dark font-bold text-sm">Informasi Tambahan</p>
                    <p class="text-plum-muted text-xs mt-0.5">Opsional — lengkapi data anak</p>
                </div>

                <!-- Catatan Khusus -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Catatan Khusus</label>
                    <textarea name="catatan_khusus" rows="3"
                              placeholder="Catatan khusus yang perlu diketahui"
                              class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl px-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50 resize-none">{{ $anak['catatan_khusus'] ?? '' }}</textarea>
                </div>

                <!-- Alergi -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Alergi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <ion-icon name="warning-outline" style="font-size:15px;color:#A2397B;"></ion-icon>
                        </div>
                        <input type="text" name="alergi"
                               value="{{ $anak['alergi'] ?? '' }}"
                               placeholder="Contoh: Susu sapi, kacang"
                               class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                    </div>
                </div>

                <!-- Hobi -->
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">Hobi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <ion-icon name="heart-outline" style="font-size:15px;color:#A2397B;"></ion-icon>
                        </div>
                        <input type="text" name="hobi"
                               value="{{ $anak['hobi'] ?? '' }}"
                               placeholder="Contoh: Menggambar, bermain bola"
                               class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                    </div>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="anim-up d4 space-y-2">
                <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-plum to-plum-light text-white font-bold py-4 rounded-2xl shadow-lg shadow-plum/30 flex items-center justify-center gap-2 text-sm active:scale-97 transition-all">
                    <ion-icon name="save-outline" id="btnIcon" style="font-size:18px;"></ion-icon>
                    <span id="btnText">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data Anak' }}</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>
                <a href="{{ $isEdit ? route('profil.anak.detail', $anak['id']) : route('profil.data-anak') }}"
                   class="block text-center text-plum-muted font-semibold text-sm py-3">Batal</a>
            </div>

            <div class="h-4"></div>
        </form>
    </div>

</div>
</div>

<script>
function updateClock(){const el=document.getElementById('statusTime');if(el){const n=new Date();el.textContent=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;}}
updateClock();setInterval(updateClock,30000);

// Gender
function setGender(val) {
    document.getElementById('genderInput').value = val;
    ['L','P'].forEach(g => {
        const btn = document.getElementById('gender'+g);
        btn.className = btn.className
            .replace('border-plum bg-plum-soft text-plum','')
            .replace('border-plum-soft bg-white text-plum-muted','').trim();
        btn.className += ' ' + (g===val ? 'border-plum bg-plum-soft text-plum' : 'border-plum-soft bg-white text-plum-muted');
    });
}

// Foto preview
document.getElementById('fotoInput').addEventListener('change', function() {
    const file = this.files[0]; if(!file) return;
    const preview = document.getElementById('avatarPreview');
    const icon    = document.getElementById('avatarIcon');
    const reader  = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result; preview.classList.remove('hidden');
        if(icon) icon.style.display='none';
    };
    reader.readAsDataURL(file);
});

// SweetAlert2 functions
function showAlert(msg, type = 'error') {
    Swal.fire({
        text: msg,
        icon: type,
        confirmButtonText: 'OK',
        confirmButtonColor: '#7B1E5A',
        timer: type === 'success' ? 2000 : undefined,
        timerProgressBar: type === 'success',
        showClass: {
            popup: 'animate__animated animate__fadeInUp animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutDown animate__faster'
        }
    });
}

function showSuccessAlert(msg, redirectUrl) {
    Swal.fire({
        text: msg,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#7B1E5A',
        timer: 2000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInUp animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutDown animate__faster'
        }
    }).then(() => {
        window.location.href = redirectUrl;
    });
}

function setLoading(v) {
    document.getElementById('submitBtn').disabled = v;
    document.getElementById('btnIcon').style.display = v?'none':'';
    document.getElementById('btnSpinner').classList.toggle('hidden',!v);
    document.getElementById('btnText').textContent = v ? 'Menyimpan...' : '{{ $isEdit ? "Simpan Perubahan" : "Simpan Data Anak" }}';
}

const isEdit  = {{ $isEdit ? 'true' : 'false' }};
const anakId  = {{ $isEdit ? $anak['id'] : 'null' }};
const CSRF    = "{{ csrf_token() }}";

document.getElementById('anakForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const nama      = document.getElementById('nama').value.trim();
    const gender    = document.getElementById('genderInput').value;
    const tgl       = document.getElementById('tanggalLahir').value;

    if (!nama)   return showAlert('Nama anak wajib diisi!');
    if (!gender) return showAlert('Gender wajib dipilih!');
    if (!tgl)    return showAlert('Tanggal lahir wajib diisi!');

    // Validasi tanggal tidak lebih dari hari ini
    if (new Date(tgl) > new Date()) return showAlert('Tanggal lahir tidak boleh melebihi hari ini!');

    setLoading(true);
    try {
        const fd  = new FormData(document.getElementById('anakForm'));
        const url = isEdit
            ? '{{ route("profil.anak.update") }}'
            : '{{ route("profil.anak.store") }}';

        const res  = await fetch(url, { method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}, body:fd });
        const data = await res.json();

        if (data.success) {
            showSuccessAlert(
                data.message || 'Data berhasil disimpan!',
                data.redirect || '{{ route("profil.data-anak") }}'
            );
        } else {
            const err = data.errors ? Object.values(data.errors)[0] : data.message;
            showAlert(Array.isArray(err) ? err[0] : (err||'Gagal menyimpan.'));
        }
    } catch(err) { 
        showAlert('Terjadi kesalahan. Coba lagi.'); 
    }
    finally { setLoading(false); }
});
</script>
</body>
</html>