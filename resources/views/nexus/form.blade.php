@php
    $roleId = session('user')['id_role'] ?? null;
@endphp

@extends('layouts.app')

@php $activeNav = 'home' @endphp

@section('title', 'Ajukan Pertanyaan')

@push('styles')
<style>
    .search-input:focus { outline: none; }

    .cat-pill {
        border-radius: 12px;
        padding: 8px 14px;
        color: #7C7893;
        font-size: 13px;
        font-weight: 800;
        line-height: 1;
        transition: all .15s ease;
        background: white;
        border: 1.5px solid #DDD6EF;
        cursor: pointer;
    }
    .cat-pill.active {
        background: linear-gradient(to right, #7C3AED, #8B46D3);
        color: white;
        box-shadow: 0 6px 14px rgba(139,70,211,0.28);
        border-color: transparent;
    }

    .form-input {
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1.5px solid #DDD6EF;
        background: white;
        font-size: 14px;
        font-family: 'Nunito', sans-serif;
        font-weight: 700;
        color: #1E1B2E;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        box-sizing: border-box;
    }
    .form-input:focus {
        border-color: #8B46D3;
        box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
    }
    .form-input::placeholder { color: #A8A2C2; font-weight: 600; }
    textarea.form-input { min-height: 120px; resize: vertical; }

    .btn-submit {
        width: 100%;
        background: linear-gradient(to right, #7C3AED, #8B46D3);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-size: 16px;
        font-weight: 900;
        cursor: pointer;
        transition: transform .12s, opacity .15s;
        font-family: 'Nunito', sans-serif;
        box-shadow: 0 8px 24px rgba(139,70,211,0.38);
    }
    .btn-submit:active { transform: scale(.97); }
    .btn-submit:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .toast {
        position: fixed; bottom: 100px; left: 50%; transform: translateX(-50%);
        background: #1E1B2E; color: white; padding: 12px 24px;
        border-radius: 12px; font-weight: 700; font-size: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,.2); z-index: 999;
        opacity: 0; transition: opacity .3s;
    }
    .toast.show { opacity: 1; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nexus.nexus-index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Ajukan Pertanyaan</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5">Tim Nexus akan menjawab pertanyaan kamu</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <div class="anim delay-2 bg-white rounded-[20px] p-4 shadow-[0_2px_12px_rgba(0,0,0,0.08)] space-y-5">
        {{-- Judul --}}
        <div>
            <p class="text-[#5A556E] text-[13px] font-extrabold uppercase tracking-[1.8px] mb-2">Judul Pertanyaan</p>
            <input type="text" class="form-input" id="judul"
                placeholder="Contoh: Anak susah makan, bagaimana mengatasinya?"
                maxlength="255">
        </div>

        {{-- Kategori --}}
        <div>
            <p class="text-[#5A556E] text-[13px] font-extrabold uppercase tracking-[1.8px] mb-3">
                Kategori <span class="text-[#A8A2C2] font-normal normal-case tracking-normal">(opsional)</span>
            </p>
            <div class="flex flex-wrap gap-2" id="kategoriPills">
                <div class="cat-pill" data-value="">Semua</div>
                <div class="cat-pill" data-value="tumbuh_kembang">🌱 Tumbuh Kembang</div>
                <div class="cat-pill" data-value="gizi">🍎 Gizi & Makanan</div>
                <div class="cat-pill" data-value="kesehatan">🏥 Kesehatan</div>
                <div class="cat-pill" data-value="pendidikan">📚 Pendidikan</div>
                <div class="cat-pill" data-value="perilaku">🧠 Perilaku</div>
                <div class="cat-pill" data-value="lainnya">📌 Lainnya</div>
            </div>
        </div>
    </div>

    <div class="anim delay-3 mt-5">
        <button class="btn-submit" id="btnSubmit" onclick="submitQuestion()">
            <ion-icon name="paper-plane" style="font-size:18px;margin-right:6px;vertical-align:-2px;"></ion-icon>
            Kirim Pertanyaan
        </button>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
const CSRF = "{{ csrf_token() }}";
const API_BASE = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "")), "/") }}';
let selectedKategori = '';

document.getElementById('kategoriPills').addEventListener('click', function(e) {
    const pill = e.target.closest('.cat-pill');
    if (!pill) return;
    this.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    pill.classList.add('active');
    selectedKategori = pill.dataset.value;
});

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

async function submitQuestion() {
    const judul = document.getElementById('judul').value.trim();
    if (!judul) { showToast('Judul pertanyaan wajib diisi'); return; }

    const btn = document.getElementById('btnSubmit');
    btn.disabled = true; btn.innerHTML = 'Mengirim...';

    try {
        const res = await fetch(`${API_BASE}/nexus`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer {{ session("token") }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ judul, kategori: selectedKategori || null })
        });
        const json = await res.json();
        if (!res.ok) { showToast(json.message || 'Gagal mengirim'); btn.disabled = false; btn.innerHTML = 'Kirim Pertanyaan'; return; }
        window.location.href = '{{ route("nexus.nexus-index") }}';
    } catch (e) {
        showToast('Gagal mengirim pertanyaan');
        btn.disabled = false; btn.innerHTML = 'Kirim Pertanyaan';
    }
}
</script>
@endpush