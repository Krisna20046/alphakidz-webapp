@extends('layouts.app')

@section('title', 'Detail Nanny - {{ $nanny['name'] ?? 'Nanny' }}')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    @keyframes avatarPulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(139,70,211,0.3); }
        50%     { box-shadow: 0 0 0 8px rgba(139,70,211,0); }
    }
    .avatar-pulse { animation: avatarPulse 2.5s ease-in-out 0.5s infinite; }

    .section-card { background: #FFFFFF; border-radius: 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.09); }
    .detail-item { background: #F8F8FB; border: 1px solid #ECEAF4; border-radius: 10px; }
    .modal-overlay { background: rgba(30,11,60,0.5); backdrop-filter: blur(4px); }
    .modal-card { animation: slideUp 0.25s ease forwards; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ route('konsultan-nanny-list') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Detail Nanny</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Informasi lengkap profil<br>dan penambahan nanny</p>
        </div>
    </div>
</div>

@if(!isset($nanny))
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <x-empty-state
        icon="person-circle-outline"
        title="Data tidak ditemukan"
        description="Data yang Anda cari tidak tersedia"
    >
        <a href="{{ route('konsultan-nanny-list') }}"
           class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">
            Kembali ke Daftar
        </a>
    </x-empty-state>
</div>

@else
@php
    $isActive = (int)($nanny['is_active'] ?? 1) === 1;
    $isMale = ($nanny['gender'] ?? '') === 'L';
@endphp

<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">

    @if(session('success'))
    <div id="flash-success" class="bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-2xl flex items-center gap-2">
        <ion-icon name="checkmark-circle" style="font-size:16px;color:#16A34A;flex-shrink:0;"></ion-icon>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div id="flash-error" class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-4 py-3 rounded-2xl flex items-center gap-2">
        <ion-icon name="alert-circle" style="font-size:16px;color:#DC2626;flex-shrink:0;"></ion-icon>
        {{ session('error') }}
    </div>
    @endif

    {{-- Profile card --}}
    <div class="section-card anim delay-2 p-5">
        <div class="flex flex-col items-center">
            @if(!empty($nanny['foto']))
            <img src="{{ $nanny['foto'] }}" alt="{{ $nanny['name'] }}"
                 class="avatar-pulse w-[88px] h-[88px] rounded-full object-cover border-4 border-[#EDE9FE] shadow-[0_3px_10px_rgba(0,0,0,0.12)]"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="avatar-pulse w-[88px] h-[88px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] items-center justify-center hidden">
                <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
            </div>
            @else
            <div class="avatar-pulse w-[88px] h-[88px] rounded-full bg-[#F3F0FD] border-4 border-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
            </div>
            @endif

            <h2 class="text-[#1E1B2E] text-[22px] font-extrabold mt-3 mb-2">{{ $nanny['name'] }}</h2>
            <div class="flex items-center gap-1.5 bg-[#EDE9FE] px-3 py-1.5 rounded-full mb-2">
                <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">{{ $nanny['posisi'] ?? 'Nanny' }}</span>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $isActive ? 'bg-[#DCFCE7]' : 'bg-[#FEE2E2]' }}">
                <ion-icon name="ellipse" style="font-size:8px;color:{{ $isActive ? '#166534' : '#991B1B' }};"></ion-icon>
                <span class="text-[10px] font-extrabold tracking-wide uppercase {{ $isActive ? 'text-[#166534]' : 'text-[#991B1B]' }}">
                    {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
                </span>
            </div>
        </div>

        @if(!empty($nanny['bio']))
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
            <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0 mt-0.5">
                <ion-icon name="information-circle-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1">
                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Bio</p>
                <p class="text-[#1E1B2E] text-[12px] font-semibold leading-relaxed">{{ $nanny['bio'] }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Contact info --}}
    <div class="section-card anim delay-3 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="call" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Kontak</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <div class="space-y-2">
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                    <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $nanny['email'] ?? '-' }}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="call-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Nomor HP</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $nanny['no_hp'] ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal info --}}
    <div class="section-card anim delay-4 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="person-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Pribadi</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <div class="space-y-2">
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Tanggal Lahir</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $nanny['tanggal_lahir'] ?? '-' }}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="{{ $isMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:{{ $isMale ? '#4F46E5' : '#EC4899' }};"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                        @php $g = $nanny['gender'] ?? ''; echo $g === 'L' ? 'Laki-laki' : ($g === 'P' ? 'Perempuan' : '-'); @endphp
                    </p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="location-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Lokasi</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                        @if(!empty($nanny['kota']) && !empty($nanny['provinsi']))
                            {{ $nanny['kota'] }}, {{ $nanny['provinsi'] }}
                        @else -
                        @endif
                    </p>
                </div>
            </div>
            @if(!empty($nanny['alamat']))
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="home-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Alamat</p>
                    <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['alamat'] }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Professional info --}}
    @if(!empty($nanny['skill']) || !empty($nanny['pengalaman']) || !empty($nanny['sertifikasi']))
    <div class="section-card anim delay-5 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="briefcase" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Profesional</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <div class="space-y-2">
            @if(!empty($nanny['skill']))
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="star-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Skill</p>
                    <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['skill'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($nanny['pengalaman']))
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="time-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Pengalaman</p>
                    <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['pengalaman'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($nanny['sertifikasi']))
            <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#DCFCE7] flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="ribbon-outline" style="font-size:16px;color:#166534;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Sertifikasi</p>
                    <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $nanny['sertifikasi'] }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Supervision --}}
    <div class="section-card anim delay-5 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="people" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Pengawasan</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <div class="detail-item px-3 py-2.5 flex items-center gap-3">
            @if(!empty($nanny['konsultan']))
            <div class="w-8 h-8 rounded-[8px] bg-[#DCFCE7] flex items-center justify-center shrink-0">
                <ion-icon name="checkmark-circle-outline" style="font-size:16px;color:#166534;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Konsultan</p>
                <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $nanny['konsultan']['name'] ?? '-' }}</p>
            </div>
            @else
            <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="alert-circle-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1">
                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Konsultan</p>
                <p class="text-[#1E1B2E] text-[13px] font-extrabold">Belum ada konsultan</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Add button --}}
    <div class="anim delay-6 space-y-3 pt-1">
        <button onclick="openConfirmModal()"
                class="w-full flex items-center justify-center gap-2 h-[52px] rounded-2xl font-extrabold text-[14px] bg-[#8B46D3] text-white shadow-[0_8px_18px_rgba(139,70,211,0.32)] active:scale-[0.97] transition-transform">
            <ion-icon name="person-add-outline" style="font-size:18px;"></ion-icon>
            Tambahkan Nanny
        </button>
    </div>
</div>

@push('modals')
<div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-end justify-center sm:items-center" style="padding-bottom: 80px;">
    <div class="modal-overlay absolute inset-0" onclick="closeConfirmModal()"></div>
    <div class="modal-card relative bg-white rounded-[24px] mx-5 p-6 w-full max-w-sm shadow-[0_20px_60px_rgba(0,0,0,0.2)] z-10">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-[68px] h-[68px] rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
                <ion-icon name="person-add" style="font-size:36px;color:#8B46D3;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-extrabold text-[18px] mb-2">Tambahkan Nanny?</h3>
            <p class="text-[#8B86A5] text-[13px] leading-relaxed">
                Anda akan menambahkan <span class="font-extrabold text-[#1E1B2E]">{{ $nanny['name'] }}</span> ke daftar nanny yang Anda awasi.
            </p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeConfirmModal()"
                    class="flex-1 h-[48px] rounded-2xl border border-[#ECEAF4] bg-[#F8F8FB] text-[#8B86A5] font-extrabold text-[13px] active:scale-[0.97] transition-transform">
                Batal
            </button>
            <form action="{{ route('konsultan-nanny-add') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="id_nanny" value="{{ $nanny['id'] }}">
                <button type="submit"
                        class="w-full h-[48px] rounded-2xl bg-[#8B46D3] text-white font-extrabold text-[13px] shadow-[0_4px_12px_rgba(139,70,211,0.35)] active:scale-[0.97] transition-transform">
                    Ya, Tambahkan
                </button>
            </form>
        </div>
    </div>
</div>
@endpush
@endif
@endsection

@push('scripts')
<script>
    function openConfirmModal() {
        document.getElementById('confirmModal').classList.remove('hidden');
    }
    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    setTimeout(function () {
        ['flash-success', 'flash-error'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }, 4000);
</script>
@endpush
