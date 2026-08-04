@extends('layouts.app')

@section('title', 'Assignment Details - ' . ($assignment['majikan_name'] ?? 'Employer'))

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    @keyframes avatarPulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(139,70,211,0.3); }
        50% { box-shadow: 0 0 0 8px rgba(139,70,211,0); }
    }
    .avatar-pulse { animation: avatarPulse 2.5s ease-in-out 0.5s infinite; }

    .section-card { background: #FFFFFF; border-radius: 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.09); }
    .detail-item { background: #F8F8FB; border: 1px solid #ECEAF4; border-radius: 10px; }
    .connector-line { width: 2px; height: 18px; background: linear-gradient(to bottom, #DDD6EF, #8B46D3); border-radius: 2px; }
    .connector-dot { width: 10px; height: 10px; border-radius: 999px; background: #8B46D3; border: 2px solid #EDE9FE; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ route('konsultan-majikan-nanny') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Assignment Details</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Complete employer<br>and assigned nanny information</p>
        </div>
    </div>
</div>

@if(!isset($assignment))
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <x-empty-state
        icon="document-outline"
        title="Data not found"
        description="The assignment data you are looking for is unavailable"
    >
        <a href="{{ route('konsultan-majikan-nanny') }}"
           class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-[0_8px_20px_rgba(139,70,211,0.35)]">
            Back to List
        </a>
    </x-empty-state>
</div>

@else
@php
    $a = $assignment;
    $status = strtolower($a['status'] ?? '');
    $isActive = $status === 'active' || $status === 'aktif';
    $majMale = ($a['majikan_gender'] ?? '') === 'L';
    $nannyMale = ($a['nanny_gender'] ?? '') === 'L';
    $formatDate = function ($date) {
        if (empty($date)) return '-';
        try {
            return (new \DateTime($date))->format('d M Y');
        } catch (\Throwable $e) {
            return $date;
        }
    };
    $calcAge = function ($date) {
        if (empty($date)) return '-';
        try {
            return (new \DateTime())->diff(new \DateTime($date))->y . ' yrs';
        } catch (\Throwable $e) {
            return '-';
        }
    };
@endphp

<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar space-y-4">

    {{-- Employer profile card --}}
    <div class="section-card anim delay-2 p-5">
        <div class="flex flex-col items-center">
            @if(!empty($a['majikan_foto']))
            <img src="{{ $a['majikan_foto'] }}" alt="{{ $a['majikan_name'] }}"
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

            <h2 class="text-[#1E1B2E] text-[22px] font-extrabold mt-3 mb-2">{{ $a['majikan_name'] ?? '-' }}</h2>

            <div class="flex items-center gap-1.5 bg-[#EDE9FE] px-3 py-1.5 rounded-full mb-2">
                <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                <span class="text-[#8B46D3] text-[10px] font-extrabold tracking-wide uppercase">Employer</span>
            </div>

            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $isActive ? 'bg-[#DCFCE7]' : 'bg-[#FEE2E2]' }}">
                <ion-icon name="ellipse" style="font-size:8px;color:{{ $isActive ? '#166534' : '#991B1B' }};"></ion-icon>
                <span class="text-[10px] font-extrabold tracking-wide uppercase {{ $isActive ? 'text-[#166534]' : 'text-[#991B1B]' }}">
                    {{ $isActive ? 'ACTIVE' : 'INACTIVE' }}
                </span>
            </div>
        </div>

        @if(!empty($a['catatan']))
        <div class="h-px bg-[#E5E1F0] my-4"></div>
        <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
            <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0 mt-0.5">
                <ion-icon name="document-text-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1">
                <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Notes</p>
                <p class="text-[#1E1B2E] text-[12px] font-semibold leading-relaxed">{{ $a['catatan'] }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Assignment info --}}
    <div class="section-card anim delay-3 p-5">
        <div class="flex items-center gap-2">
            <ion-icon name="clipboard" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Assignment Information</h3>
        </div>
        <div class="h-px bg-[#E5E1F0] my-4"></div>

        <div class="space-y-2">
            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                    <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Assignment Period</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                        {{ $formatDate($a['tanggal_mulai'] ?? null) }} - {{ $formatDate($a['tanggal_selesai'] ?? null) }}
                    </p>
                </div>
            </div>

            <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="time-outline" style="font-size:16px;color:#4F46E5;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Assignment Status</p>
                    <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $isActive ? 'Active' : 'Inactive' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="anim delay-4 space-y-2">
        <p class="text-[#8B86A5] text-[10px] font-extrabold uppercase tracking-[1.8px] text-center">Assignment Relationship</p>

        {{-- Employer details --}}
        <div class="section-card p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="person-circle" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Employer Information</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="space-y-2">
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                        <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $a['majikan_email'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $majMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:{{ $majMale ? '#4F46E5' : '#EC4899' }};"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $majMale ? 'Male' : 'Female' }}</p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Date Of Birth</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            {{ $formatDate($a['majikan_tanggal_lahir'] ?? null) }}
                            @if(!empty($a['majikan_tanggal_lahir']))
                            <span class="text-[#8B86A5] font-semibold">({{ $calcAge($a['majikan_tanggal_lahir']) }})</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                        <ion-icon name="location-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Location</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            @if(!empty($a['majikan_kota']) && !empty($a['majikan_provinsi']))
                                {{ $a['majikan_kota'] }}, {{ $a['majikan_provinsi'] }}
                            @else - @endif
                        </p>
                    </div>
                </div>

                @if(!empty($a['majikan_alamat']))
                <div class="bg-[#F8F8FB] border border-[#ECEAF4] rounded-[10px] px-3 py-2.5 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0 mt-0.5">
                        <ion-icon name="home-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px] mb-1">Address</p>
                        <p class="text-[#1E1B2E] text-[12px] font-semibold leading-snug">{{ $a['majikan_alamat'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Connector --}}
        <div class="flex flex-col items-center py-1">
            <div class="connector-line"></div>
            <div class="connector-dot"></div>
            <div class="connector-line"></div>
        </div>

        {{-- Nanny details --}}
        <div class="section-card p-5">
            <div class="flex items-center gap-2">
                <ion-icon name="heart" style="font-size:16px;color:#8B46D3;"></ion-icon>
                <h3 class="text-[#1E1B2E] text-[20px] font-extrabold leading-none">Nanny Information</h3>
            </div>
            <div class="h-px bg-[#E5E1F0] my-4"></div>

            <div class="flex items-center gap-3 mb-3">
                @if(!empty($a['nanny_foto']))
                <img src="{{ $a['nanny_foto'] }}" alt="{{ $a['nanny_name'] }}"
                     class="w-[56px] h-[56px] rounded-[10px] object-cover bg-[#F3F0FD] shrink-0"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-[56px] h-[56px] rounded-[10px] items-center justify-center hidden bg-[#F3F0FD] shrink-0">
                    <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                </div>
                @else
                <div class="w-[56px] h-[56px] rounded-[10px] flex items-center justify-center bg-[#F3F0FD] shrink-0">
                    <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $a['nanny_name'] ?? '-' }}</p>
                    <div class="inline-flex items-center gap-1 bg-[#EDE9FE] px-2.5 py-1 rounded-full mt-1">
                        <ion-icon name="briefcase-outline" style="font-size:11px;color:#8B46D3;"></ion-icon>
                        <span class="text-[#8B46D3] text-[10px] font-extrabold uppercase">Nanny</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EFE9FB] flex items-center justify-center shrink-0">
                        <ion-icon name="at-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Email</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold truncate">{{ $a['nanny_email'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $nannyMale ? 'male-outline' : 'female-outline' }}" style="font-size:16px;color:{{ $nannyMale ? '#4F46E5' : '#EC4899' }};"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Gender</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">{{ $nannyMale ? 'Male' : 'Female' }}</p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FDE8EF] flex items-center justify-center shrink-0">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#EC4899;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Date Of Birth</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            {{ $formatDate($a['nanny_tanggal_lahir'] ?? null) }}
                            @if(!empty($a['nanny_tanggal_lahir']))
                            <span class="text-[#8B86A5] font-semibold">({{ $calcAge($a['nanny_tanggal_lahir']) }})</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="detail-item px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-[#FEF3E2] flex items-center justify-center shrink-0">
                        <ion-icon name="location-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#8B86A5] text-[9px] font-extrabold uppercase tracking-[1.8px]">Location</p>
                        <p class="text-[#1E1B2E] text-[13px] font-extrabold">
                            @if(!empty($a['nanny_kota']) && !empty($a['nanny_provinsi']))
                                {{ $a['nanny_kota'] }}, {{ $a['nanny_provinsi'] }}
                            @else - @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action button --}}
    <div class="anim delay-6 space-y-3 pt-1">
        <a href="{{ route('chat.room', $a['id_majikan'] ?? 0) }}?nama={{ urlencode($a['majikan_name'] ?? '') }}"
           class="bg-white border border-[#E7E3F5] text-[#8B46D3] rounded-2xl font-extrabold flex items-center justify-center gap-2 h-[52px] shadow-[0_2px_10px_rgba(0,0,0,0.06)] active:scale-[0.97] transition-transform">
            <ion-icon name="chatbubble-ellipses-outline" style="font-size:18px;"></ion-icon>
            <span>Chat with Employer</span>
        </a>
    </div>
</div>
@endif
@endsection
