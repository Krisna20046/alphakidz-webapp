@extends('layouts.app')

@section('title', 'User Details')

@php
    $u        = $user;
    $isActive = (int)($u['is_active'] ?? 0) === 1;
    $role     = strtolower($u['role'] ?? 'nanny');
    $roleMap  = ['admin'=>'Admin','konsultan'=>'Consultant','majikan'=>'Employer','nanny'=>'Nanny'];
    $roleLabel = $roleMap[$role] ?? ucfirst($role);
    $initial  = strtoupper(substr($u['name'] ?? '?', 0, 1));
    $genderMap = ['L'=>'Male','P'=>'Female'];
    $gender    = $genderMap[$u['gender'] ?? ''] ?? '-';
    $address   = collect([$u['alamat'] ?? null, $u['kota'] ?? null, $u['provinsi'] ?? null])->filter()->implode(', ') ?: '-';
    $birthDate = !empty($u['tanggal_lahir'])
        ? \Carbon\Carbon::parse($u['tanggal_lahir'])->translatedFormat('j F Y')
        : '-';
    $age = !empty($u['tanggal_lahir'])
        ? \Carbon\Carbon::parse($u['tanggal_lahir'])->age . ' years'
        : '';
    $joinDate = !empty($u['created_at'])
        ? \Carbon\Carbon::parse($u['created_at'])->translatedFormat('j F Y')
        : '-';
    $updateDate = !empty($u['updated_at'])
        ? \Carbon\Carbon::parse($u['updated_at'])->translatedFormat('j F Y')
        : '-';
@endphp

@push('styles')
<style>
    .badge-admin     { background:#F3E5F5; color:#6A1B9A; }
    .badge-konsultan { background:#E3F2FD; color:#0D47A1; }
    .badge-majikan   { background:#E8F5E9; color:#1B5E20; }
    .badge-nanny     { background:#FFF3E0; color:#E65100; }
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.94); }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('admin-kelola-akun') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">User Details</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Full account information</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Profile Card --}}
    <div class="anim delay-2 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <div class="flex items-center gap-4">
            @if(!empty($u['foto']))
            <img src="{{ $u['foto'] }}" alt="{{ $u['name'] }}"
                 class="w-16 h-16 rounded-2xl object-cover shrink-0">
            @else
            <div class="w-16 h-16 rounded-2xl bg-[#8B46D3] flex items-center justify-center shrink-0 text-white text-2xl font-bold">
                {{ $initial }}
            </div>
            @endif

            <div class="flex-1 min-w-0">
                <h2 class="text-[#1E1B2E] font-extrabold text-lg leading-tight">{{ $u['name'] }}</h2>
                <p class="text-[#8B86A5] text-sm truncate mt-0.5">{{ $u['email'] }}</p>
                <div class="flex gap-2 mt-2 flex-wrap">
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full badge-{{ $role }}">{{ $roleLabel }}</span>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full
                        {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $isActive ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Contact Info --}}
    <div class="anim delay-2 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <h3 class="text-[#1E1B2E] font-extrabold text-sm mb-4 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="call-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            </div>
            Contact Information
        </h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <ion-icon name="call-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Phone No.</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $u['no_hp'] ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <ion-icon name="location-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Address</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $address }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal Info --}}
    <div class="anim delay-3 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <h3 class="text-[#1E1B2E] font-extrabold text-sm mb-4 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="person-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            </div>
            Personal Information
        </h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <ion-icon name="person-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Gender</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $gender }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <ion-icon name="calendar-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Date of Birth</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">
                        {{ $birthDate }}{{ $age ? " ($age)" : '' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="anim delay-3 bg-white rounded-3xl p-5 border border-[#DDD6EF]">
        <h3 class="text-[#1E1B2E] font-extrabold text-sm mb-4 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="shield-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            </div>
            Account Information
        </h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <ion-icon name="calendar-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Joined</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $joinDate }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <ion-icon name="time-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                <div>
                    <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Last Updated</p>
                    <p class="text-[#1E1B2E] text-sm font-bold mt-0.5">{{ $updateDate }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="anim delay-4 space-y-3">
        <a href="{{ route('admin-kelola-akun.edit', $u['id']) }}"
           class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm shadow-lg shadow-[#8B46D3]/30">
            <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
            Edit Account Data
        </a>

        <form action="{{ route('admin-kelola-akun.status', $u['id']) }}" method="POST">
            @csrf
            <input type="hidden" name="is_active" value="{{ $isActive ? 0 : 1 }}">
            <button type="submit"
                onclick="return confirm('{{ $isActive ? 'Deactivate' : 'Activate' }} this account?')"
                class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-sm
                    {{ $isActive ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                <ion-icon name="{{ $isActive ? 'pause-circle-outline' : 'play-circle-outline' }}" style="font-size:18px;"></ion-icon>
                {{ $isActive ? 'Deactivate Account' : 'Activate Account' }}
            </button>
        </form>

        <form action="{{ route('admin-kelola-akun.destroy', $u['id']) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                onclick="return confirm('Permanently delete this account? This action cannot be undone.')"
                class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-sm bg-red-50 text-red-500 border border-red-200">
                <ion-icon name="trash-outline" style="font-size:18px;"></ion-icon>
                Delete Account
            </button>
        </form>
    </div>

</div>
@endsection
