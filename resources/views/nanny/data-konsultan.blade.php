@extends('layouts.app')

@section('title', 'Consultant Data')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

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
    .btn-chat {
        background: #8B46D3;
        color: #FFFFFF;
        border-radius: 14px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 52px;
        font-size: 15px;
        width: 100%;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(139,70,211,0.35);
        transition: opacity .15s, transform .12s;
    }
    .btn-chat:hover  { opacity: .9; }
    .btn-chat:active { transform: scale(0.98); }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Consultant Data</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Complete Profile Information of Personal Consultant</p>
        </div>
    </div>
</div>

@if($data)
@php
    $genderText   = ($data['gender'] ?? '') === 'L' ? 'Laki-laki' : (($data['gender'] ?? '') === 'P' ? 'Perempuan' : '-');
    $isMale       = ($data['gender'] ?? '') === 'L';
    $locationText = (!empty($data['kota']) && !empty($data['provinsi']))
                    ? $data['kota'].', '.$data['provinsi']
                    : ($data['kota'] ?? $data['provinsi'] ?? '-');
@endphp

<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
            bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
            rounded-t-[50px] -mt-[50px] relative z-20
            hide-scrollbar space-y-4">

    {{-- PROFILE CARD --}}
    <div class="section-card anim delay-2 p-5">
        <div class="flex flex-col items-center">
            @if(!empty($data['foto']))
            <img src="{{ $data['foto'] }}" alt="{{ $data['name'] }}"
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

            <h2 class="text-[#1E1B2E] text-[25px] font-extrabold mt-3">{{ $data['name'] }}</h2>

            @if(!empty($data['spesialis']))
            <div class="mt-2 bg-[#EFE9FB] px-3 py-1 rounded-full flex items-center gap-1.5">
                <ion-icon name="school-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">{{ $data['spesialis'] }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- BIO --}}
    @if(!empty($data['bio']))
    <div class="section-card anim delay-3 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="information-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Bio</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <p class="text-[#4B5563] text-[13px] font-semibold leading-relaxed">{{ $data['bio'] }}</p>
    </div>
    @endif

    {{-- CONTACT INFORMATION --}}
    <div class="section-card anim delay-3 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="call" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Contact Information</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>

        <div class="space-y-2">
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                    <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $data['email'] ?? '-' }}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="call-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Nomor HP</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $data['no_hp'] ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- PERSONAL INFORMATION --}}
    <div class="section-card anim delay-4 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="person-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Personal Information</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>

        <div class="space-y-2">
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="calendar-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Tanggal Lahir</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $data['tanggal_lahir'] ?? '-' }}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="{{ $isMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:#EC4899;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $genderText }}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="location-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Lokasi</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $locationText }}</p>
                </div>
            </div>
            @if(!empty($data['alamat']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="home-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Alamat</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold leading-snug">{{ $data['alamat'] }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- PROFESSIONAL INFORMATION --}}
    @if(!empty($data['skill']) || !empty($data['pengalaman']) || !empty($data['sertifikasi']))
    <div class="section-card anim delay-5 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="briefcase" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Informasi Profesional</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>

        <div class="space-y-2">
            @if(!empty($data['skill']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                    <ion-icon name="star-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Skill</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold leading-snug">{{ $data['skill'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($data['pengalaman']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="time-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Pengalaman</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold leading-snug">{{ $data['pengalaman'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($data['sertifikasi']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="ribbon-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Sertifikasi</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold leading-snug">{{ $data['sertifikasi'] }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- CONTACT BUTTON --}}
    <div class="anim delay-5 pt-1 pb-4">
        <a href="{{ route('chat.room', ['id' => $data['id_user'] ?? 0]) }}" class="btn-chat">
            <ion-icon name="chatbubble-ellipses-outline" style="font-size:18px;"></ion-icon>
            Hubungi Konsultan
        </a>
    </div>

</div>

@else
{{-- EMPTY STATE --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <x-empty-state
        icon="person-circle-outline"
        title="Data konsultan tidak ditemukan"
        description="Belum ada konsultan yang ditugaskan untuk Anda"
    />
</div>
@endif
@endsection
