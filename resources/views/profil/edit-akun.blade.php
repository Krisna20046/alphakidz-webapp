<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Akun</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B' } },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }
        @media (min-width: 640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3,#ede0f0,#e8d5ee); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,0.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; position:relative; }
        }
        .input-field { transition: border-color .2s, box-shadow .2s; }
        .input-field:focus { outline:none; border-color:#7B1E5A; box-shadow:0 0 0 3px rgba(123,30,90,0.1); }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        
        /* Toast styles */
        #toast { 
            transition: all .3s ease; 
            transform: translateY(-100%); 
            opacity: 0;
            z-index: 40; /* Default lebih rendah */
            pointer-events: none; /* Biarkan klik tembus saat hidden */
        }
        #toast.show { 
            transform: translateY(0); 
            opacity: 1;
            z-index: 60 !important; /* Tinggi saat muncul */
            pointer-events: none; /* Tetap biarkan area toast tidak bisa diklik */
        }
        #toast.show #toastInner {
            pointer-events: none; /* Isi toast juga tidak bisa diklik */
        }
        
        /* Top bar z-index management */
        #topBar {
            transition: z-index 0.2s ease;
            z-index: 50; /* Default tinggi */
            position: relative;
        }
        
        /* Animasi */
        @keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim-up { animation:slideUp .4s ease forwards; }
        .d1{animation-delay:.05s;opacity:0} .d2{animation-delay:.12s;opacity:0} .d3{animation-delay:.19s;opacity:0}
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

    <!-- TOP BAR - Tambah ID -->
    <div id="topBar" class="flex items-center gap-3 px-4 pt-4 pb-3 bg-plum-pale shrink-0 border-b border-plum-soft/40">
        <a href="{{ route('profil.index') }}" class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center">
            <ion-icon name="arrow-back" style="font-size:20px;color:#7B1E5A;"></ion-icon>
        </a>
        <h1 class="text-plum-dark font-extrabold text-base flex-1">Edit Akun</h1>
    </div>

    <!-- TOAST -->
    <div id="toast" class="absolute top-14 left-0 right-0 px-4">
        <div id="toastInner" class="bg-red-500 text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2">
            <ion-icon name="alert-circle-outline" style="font-size:16px;flex-shrink:0;"></ion-icon>
            <span id="toastMsg"></span>
        </div>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-5 space-y-4">

        <!-- Info banner -->
        <div class="anim-up d1 bg-plum-soft rounded-2xl p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shrink-0">
                <ion-icon name="information-circle-outline" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </div>
            <p class="text-plum-dark text-xs font-medium leading-relaxed flex-1">
                Ubah email atau password akun Anda. Kosongkan kolom password jika tidak ingin mengubahnya.
            </p>
        </div>

        <form id="editAkunForm" novalidate class="space-y-4">
            @csrf

            <!-- EMAIL -->
            <div class="anim-up d1 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 space-y-3">
                <p class="text-plum-dark font-bold text-sm">Email</p>
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <ion-icon name="mail-outline" style="font-size:16px;color:#A2397B;"></ion-icon>
                    </div>
                    <input type="email" name="email" id="email"
                           value="{{ session('user')['email'] ?? '' }}"
                           placeholder="Email aktif Anda"
                           class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="anim-up d2 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 space-y-4">
                <div>
                    <p class="text-plum-dark font-bold text-sm">Ubah Password</p>
                    <p class="text-plum-muted text-xs mt-0.5">Opsional — kosongkan jika tidak ingin diubah</p>
                </div>

                @php
                    $pwFields = [
                        ['id'=>'passwordLama',   'name'=>'password_lama',             'label'=>'Password Lama',          'icon'=>'lock-closed-outline'],
                        ['id'=>'passwordBaru',   'name'=>'password_baru',             'label'=>'Password Baru',          'icon'=>'lock-open-outline'],
                        ['id'=>'passwordConfirm','name'=>'password_baru_confirmation','label'=>'Konfirmasi Password Baru','icon'=>'shield-checkmark-outline'],
                    ];
                @endphp

                @foreach($pwFields as $f)
                <div>
                    <label class="block text-xs font-semibold text-plum mb-1.5">{{ $f['label'] }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <ion-icon name="{{ $f['icon'] }}" style="font-size:16px;color:#A2397B;"></ion-icon>
                        </div>
                        <input type="password" name="{{ $f['name'] }}" id="{{ $f['id'] }}"
                               placeholder="••••••••"
                               class="input-field w-full bg-plum-pale border-2 border-plum-soft rounded-2xl pl-11 pr-12 py-3.5 text-sm font-medium text-plum-dark placeholder-plum-muted/50"/>
                        <button type="button" data-target="{{ $f['id'] }}"
                                class="eye-toggle absolute inset-y-0 right-4 flex items-center text-plum-muted hover:text-plum transition-colors">
                            <ion-icon class="eye-show" name="eye-outline" style="font-size:18px;"></ion-icon>
                            <ion-icon class="eye-hide hidden" name="eye-off-outline" style="font-size:18px;"></ion-icon>
                        </button>
                    </div>
                </div>
                @endforeach

                <!-- Confirm match hint -->
                <p id="confirmHint" class="text-xs font-semibold hidden"></p>
            </div>

            <!-- SUBMIT -->
            <div class="anim-up d3">
                <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-plum to-plum-light text-white font-bold py-4 rounded-2xl shadow-lg shadow-plum/30 flex items-center justify-center gap-2 text-sm active:scale-97 transition-all">
                    <ion-icon name="save-outline" id="btnIcon" style="font-size:18px;"></ion-icon>
                    <span id="btnText">Simpan Perubahan</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>
                <a href="{{ route('profil.index') }}"
                   class="block text-center text-plum-muted font-semibold text-sm py-3 mt-1">Batal</a>
            </div>
        </form>
        <div class="h-4"></div>
    </div>

</div>
</div>

<script>
function updateClock(){const el=document.getElementById('statusTime');if(el){const n=new Date();el.textContent=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;}}
updateClock();setInterval(updateClock,30000);

// Eye toggle
document.querySelectorAll('.eye-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        btn.querySelector('.eye-show').classList.toggle('hidden', isPass);
        btn.querySelector('.eye-hide').classList.toggle('hidden', !isPass);
    });
});

// Confirm match
const pwBaru    = document.getElementById('passwordBaru');
const pwConfirm = document.getElementById('passwordConfirm');
const hint      = document.getElementById('confirmHint');
function checkMatch() {
    if (!pwConfirm.value) { hint.classList.add('hidden'); return; }
    hint.classList.remove('hidden');
    if (pwBaru.value === pwConfirm.value) {
        hint.textContent = '✓ Password cocok'; hint.className = 'text-xs font-semibold text-green-600';
    } else {
        hint.textContent = '✗ Password tidak cocok'; hint.className = 'text-xs font-semibold text-red-500';
    }
}
pwBaru.addEventListener('input', checkMatch);
pwConfirm.addEventListener('input', checkMatch);

// Fungsi untuk mengatur z-index berdasarkan visibility toast
function updateZIndexBasedOnToast() {
    const topBar = document.getElementById('topBar');
    const toast = document.getElementById('toast');
    
    if (!topBar || !toast) return;
    
    // Cek apakah toast sedang muncul
    const isToastVisible = toast.classList.contains('show');
    
    if (isToastVisible) {
        // Toast muncul: prioritas toast lebih tinggi
        topBar.style.zIndex = '40';
        toast.style.zIndex = '60';
    } else {
        // Toast tidak muncul: top bar lebih tinggi
        topBar.style.zIndex = '50';
        toast.style.zIndex = '40';
    }
}

// Modifikasi fungsi showToast
function showToast(msg, type='error') {
    const t = document.getElementById('toast');
    const inner = document.getElementById('toastInner');
    
    // Set pesan dan warna
    document.getElementById('toastMsg').textContent = msg;
    inner.className = `${type==='success'?'bg-green-500':'bg-red-500'} text-white text-sm font-semibold px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2`;
    
    // Tampilkan toast
    t.classList.add('show');
    
    // Update z-index - toast muncul
    updateZIndexBasedOnToast();
    
    // Hapus toast setelah 3.5 detik
    setTimeout(() => {
        t.classList.remove('show');
        // Update z-index - toast hilang
        updateZIndexBasedOnToast();
    }, 3500);
}

// Panggil saat halaman dimuat untuk memastikan z-index awal benar
document.addEventListener('DOMContentLoaded', function() {
    updateZIndexBasedOnToast();
});

// Observer untuk mendeteksi perubahan class pada toast secara real-time
const toastElement = document.getElementById('toast');
if (toastElement) {
    const toastObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                updateZIndexBasedOnToast();
            }
        });
    });
    
    toastObserver.observe(toastElement, { attributes: true });
}

function setLoading(v) {
    document.getElementById('submitBtn').disabled = v;
    document.getElementById('btnText').textContent = v ? 'Menyimpan...' : 'Simpan Perubahan';
    document.getElementById('btnIcon').style.display = v ? 'none' : '';
    document.getElementById('btnSpinner').classList.toggle('hidden', !v);
}

document.getElementById('editAkunForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email   = document.getElementById('email').value.trim();
    const lama    = document.getElementById('passwordLama').value;
    const baru    = document.getElementById('passwordBaru').value;
    const confirm = document.getElementById('passwordConfirm').value;

    if (!email) return showToast('Email wajib diisi.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return showToast('Format email tidak valid.');
    if (baru || confirm || lama) {
        if (!lama)            return showToast('Password lama wajib diisi untuk mengubah password.');
        if (!baru)            return showToast('Password baru wajib diisi.');
        if (baru.length < 6) return showToast('Password baru minimal 6 karakter.');
        if (baru !== confirm) return showToast('Konfirmasi password tidak cocok.');
    }

    setLoading(true);
    try {
        const fd = new FormData(document.getElementById('editAkunForm'));
        const res = await fetch('{{ route("profil.update-akun") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: fd,
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message || 'Akun berhasil diperbarui!', 'success');
            setTimeout(() => window.location.href = '{{ route("profil.index") }}', 1500);
        } else {
            const err = data.errors ? Object.values(data.errors)[0] : data.message;
            showToast(Array.isArray(err) ? err[0] : (err || 'Gagal menyimpan.'));
        }
    } catch(e) { showToast('Terjadi kesalahan. Coba lagi.'); }
    finally { setLoading(false); }
});
</script>
</body>
</html>