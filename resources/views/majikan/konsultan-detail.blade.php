@extends('layouts.app')

@section('title', 'Consultant Details — ' . ($konsultan['name'] ?? 'Consultant'))

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
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
    .btn-contact {
        background: #FFFFFF;
        border: 1px solid #E7E3F5;
        color: #8B46D3;
        border-radius: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 48px;
    }
    .btn-contact.disabled {
        color: #B8B3CC;
        background: #F7F7FA;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ route('majikan-konsultan-list') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Consultant Details</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Complete Profile Information and<br>Expertise</p>
        </div>
    </div>
</div>

@if(!isset($konsultan))
{{-- NOT FOUND --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <x-empty-state
        icon="person-circle-outline"
        title="Data not found"
        description="The data you are looking for is unavailable"
    >
        <a href="{{ route('majikan-konsultan-list') }}"
           class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">
            Back to List
        </a>
    </x-empty-state>
</div>

@else
@php
    $rating         = $konsultan['rating'] ?? '4.9';
    $experienceYears = $konsultan['pengalaman_tahun'] ?? ($konsultan['lama_pengalaman'] ?? '-');
    $genderText     = ($konsultan['gender'] ?? '') === 'L' ? 'Male' : (($konsultan['gender'] ?? '') === 'P' ? 'Female' : '-');
    $locationText   = (!empty($konsultan['kota']) && !empty($konsultan['provinsi']))
                        ? $konsultan['kota'].', '.$konsultan['provinsi']
                        : ($konsultan['kota'] ?? $konsultan['provinsi'] ?? '-');
    $isMale         = ($konsultan['gender'] ?? '') === 'L';
@endphp

<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">

    {{-- ── PROFILE CARD ── --}}
    <div class="section-card anim delay-2 p-5">
        <div class="flex flex-col items-center">
            @if(!empty($konsultan['foto_url']))
            <img src="{{ $konsultan['foto_url'] }}" alt="{{ $konsultan['name'] }}"
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

            <h2 class="text-[#1E1B2E] text-[25px] font-extrabold mt-3">{{ $konsultan['name'] }}</h2>

            <div class="mt-2 bg-[#EFE9FB] px-3 py-1 rounded-full flex items-center gap-1.5">
                <ion-icon name="{{ !empty($konsultan['spesialis']) ? 'school-outline' : 'briefcase-outline' }}" style="font-size:12px;color:#8B46D3;"></ion-icon>
                <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">
                    {{ $konsultan['spesialis'] ?? ($konsultan['role'] ?? 'Certified Consultant') }}
                </span>
            </div>
        </div>

        {{-- <div class="h-px bg-[#E5E1F0] my-4"></div> --}}

        <div class="grid grid-cols-3 gap-2 text-center">
            {{-- <div>
                <p class="text-[#7C7893] text-[10px] uppercase tracking-[1.2px] font-extrabold">Rating</p>
                <p class="text-[#1E1B2E] text-[13px] font-extrabold mt-1">⭐ {{ $rating }}</p>
            </div>
            <div>
                <p class="text-[#7C7893] text-[10px] uppercase tracking-[1.2px] font-extrabold">Experience</p>
                <p class="text-[#1E1B2E] text-[13px] font-extrabold mt-1">
                    {{ $experienceYears !== '-' ? $experienceYears.' Yrs' : '-' }}
                </p>
            </div>
            <div>
                <p class="text-[#7C7893] text-[10px] uppercase tracking-[1.2px] font-extrabold">Clients</p>
                <p class="text-[#1E1B2E] text-[13px] font-extrabold mt-1">{{ $konsultan['total_klien'] ?? '-' }}</p>
            </div> --}}
        </div>
    </div>

    {{-- ── BIO CARD (kondisional) ── --}}
    @if(!empty($konsultan['bio']))
    <div class="section-card anim delay-3 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="information-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Bio</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <p class="text-[#4B5563] text-[13px] font-semibold leading-relaxed">{{ $konsultan['bio'] }}</p>
    </div>
    @endif

    {{-- ── CONTACT INFORMATION ── --}}
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
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['email'] ?? '-' }}</p>
                </div>
            </div>
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="call-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Phone Number</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['no_hp'] ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PERSONAL INFORMATION ── --}}
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
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Date Of Birth</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['tanggal_lahir'] ?? '-' }}</p>
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
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Location</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $locationText }}</p>
                </div>
            </div>
            @if(!empty($konsultan['alamat']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="home-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Address</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['alamat'] }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── PROFESSIONAL INFORMATION (kondisional) ── --}}
    @if(!empty($konsultan['skill']) || !empty($konsultan['pengalaman']) || !empty($konsultan['sertifikasi']))
    <div class="section-card anim delay-5 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="briefcase" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Professional Information</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>

        <div class="space-y-2">
            @if(!empty($konsultan['skill']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                    <ion-icon name="star-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Skill</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['skill'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($konsultan['pengalaman']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="time-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Experience</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['pengalaman'] }}</p>
                </div>
            </div>
            @endif
            @if(!empty($konsultan['sertifikasi']))
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                    <ion-icon name="ribbon-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Certification</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $konsultan['sertifikasi'] }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── CONTACT BUTTON ── --}}
    <div class="anim delay-5 pt-1">
        <a href="{{ route('chat.room', [$konsultan['id_user'] ?? $konsultan['id'], 'nama' =>($konsultan['name'])]) }}"
           class="btn-contact shadow-[0_2px_10px_rgba(0,0,0,0.06)] w-full">
            <ion-icon name="chatbubble-ellipses-outline" style="font-size:16px;"></ion-icon>
            <span>Contact Consultant</span>
        </a>
    </div>

</div>
@endif
@endsection
