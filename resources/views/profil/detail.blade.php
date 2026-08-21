@extends('layouts.app')

@php $activeNav = 'profil' @endphp

@section('title', $isEditing ? 'Edit Profile' : 'Profile Details')

@push('styles')
<style>
    @keyframes avatarIn {
        from { opacity: 0; transform: scale(0.82); }
        to   { opacity: 1; transform: scale(1); }
    }
    .avatar-in { animation: avatarIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.1s forwards; opacity: 0; }

    .inp {
        width: 100%;
        background: #F5F4FB;
        border: 1.5px solid #E8E4F5;
        border-radius: 12px;
        padding: 13px 16px;
        font-size: 14px;
        font-weight: 600;
        color: #1E1B2E;
        font-family: 'Nunito', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .inp:focus { outline: none; border-color: #8B46D3; box-shadow: 0 0 0 3px rgba(139,70,211,0.12); }
    .inp::placeholder { color: #B0A8CC; font-weight: 500; }

    select.inp { appearance: none; -webkit-appearance: none; cursor: pointer;
                 background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238B46D3' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                 background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; }

    label.field-label { display: block; font-size: 13px; font-weight: 700; color: #1E1B2E; margin-bottom: 7px; }
    label.field-label .req { color: #EF4444; margin-left: 2px; }

    .sheet { transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); transform: translateY(100%); }
    .sheet.open { transform: translateY(0); }
    .sheet-backdrop { transition: opacity 0.3s ease; }

    .info-row { display: flex; align-items: flex-start; gap: 14px; padding: 13px 16px;
                background: #F8F7FF; border-radius: 14px; }
    .info-icon { width: 40px; height: 40px; border-radius: 12px; display: flex;
                 align-items: center; justify-content: center; flex-shrink: 0; }

    /* Success Modal */
    #successModal { transition: opacity 0.25s ease; }
    @keyframes modalBoxIn {
        from { opacity: 0; transform: translateY(40px) scale(0.94); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-box-in { animation: modalBoxIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards; }
    @keyframes badgePop {
        0%   { opacity: 0; transform: scale(0.3) rotate(-15deg); }
        65%  { transform: scale(1.15) rotate(4deg); }
        100% { opacity: 1; transform: scale(1) rotate(0deg); }
    }
    .badge-pop { animation: badgePop 0.65s cubic-bezier(0.34,1.56,0.64,1) 0.25s both; }
    @keyframes floatDot {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-7px); }
    }
    .dot-r { animation: floatDot 2.2s ease-in-out infinite; }
    .dot-o { animation: floatDot 2.7s ease-in-out 0.4s infinite; }
    .dot-b { animation: floatDot 2.5s ease-in-out 0.7s infinite; }
    @keyframes spinSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .spin-slow { animation: spinSlow 9s linear infinite; }
    @keyframes pulseDot { 0%, 100% { opacity: 1; } 50% { opacity: 0.25; } }
    .pulse-dot { animation: pulseDot 1s ease-in-out infinite; }
    .txt-in-1 { animation: slideUp 0.45s ease 0.5s both; }
    .txt-in-2 { animation: slideUp 0.45s ease 0.65s both; }
    .btn-in   { animation: slideUp 0.45s ease 0.8s both; }
    .rdr-in   { animation: slideUp 0.45s ease 0.95s both; }
</style>
@endpush

@section('content')
<!-- PURPLE HEADER -->
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('profil.index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <span class="text-white text-[17px] font-extrabold tracking-wide">My Profile</span>
    </div>
</div>

<!-- WHITE BODY -->
<div class="flex-1 overflow-y-auto px-[30px] pt-[30px] pb-20 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 flex flex-col gap-5 hide-scrollbar">

@if($isEditing)
{{-- ═══════════════ EDIT MODE ═══════════════ --}}

    <div class="flex flex-col items-center pt-[28px] pb-[20px]">
        <div class="avatar-in relative mb-2">
            <div class="w-[88px] h-[88px] rounded-full p-[3px]" style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                @if($user['foto_url'] ?? null)
                    <ion-icon id="avatarIcon" name="person" style="display:none; font-size:42px; color:#8B46D3;"></ion-icon>
                    <img id="avatarPreview" src="{{ $user['foto_url'] }}" alt="photo" class="w-full h-full rounded-full object-cover border-2 border-white"/>
                @else
                    <div id="avatarPlaceholder" class="w-full h-full rounded-full bg-[#F0EDFB] border-2 border-white flex items-center justify-center">
                        <ion-icon id="avatarIcon" name="person" style="font-size:42px; color:#8B46D3;"></ion-icon>
                    </div>
                    <img id="avatarPreview" src="" alt="photo" class="hidden" style="position:absolute; inset:3px; width:calc(100% - 6px); height:calc(100% - 6px); border-radius:9999px; object-fit:cover; border:2px solid white;"/>
                @endif
            </div>
            <label for="fotoInput"
                   class="absolute -bottom-1 -right-1 w-9 h-9 rounded-full bg-[#8B46D3] border-[2.5px] border-white flex items-center justify-center cursor-pointer shadow-md">
                <ion-icon name="camera" style="font-size:16px;color:white;"></ion-icon>
            </label>
            <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden">
        </div>
        <p class="text-[#8B46D3] text-[13px] font-bold mt-1">Change Profile Picture</p>
    </div>

    <form id="profileForm" enctype="multipart/form-data" novalidate class="flex flex-col gap-0">
        @csrf

        <div class="h-px bg-[#F0EDFB] mb-5"></div>

        <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF] mb-4">Personal Information</p>

        <div class="anim delay-2 mb-4">
            <label class="field-label">Full Name <span class="req">*</span></label>
            <input type="text" name="name" id="name" value="{{ $user['name'] ?? '' }}" placeholder="Full name" class="inp"/>
        </div>

        <div class="anim delay-2 mb-4">
            <label class="field-label">Phone Number <span class="req">*</span></label>
            <input type="tel" name="no_hp" id="noHp" value="{{ $user['no_hp'] ?? '' }}" placeholder="+62 8xxxxxxxxx" class="inp"/>
        </div>

        <div class="anim delay-2 flex gap-3 mb-4">
            <div class="flex-1">
                <label class="field-label">Date Of Birth <span class="req">*</span></label>
                <div class="relative">
                    <input type="date" name="tanggal_lahir" id="tanggalLahir" value="{{ $user['tanggal_lahir'] ?? '' }}" class="inp"/>
                    {{-- <ion-icon name="calendar-outline" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:17px;color:#8B46D3;pointer-events:none;"></ion-icon> --}}
                </div>
            </div>
            <div class="flex-1">
                <label class="field-label">Gender <span class="req">*</span></label>
                <select name="gender" id="genderSelect" class="inp">
                    <option value="">Select</option>
                    <option value="L" {{ ($user['gender'] ?? '') === 'L' ? 'selected' : '' }}>Male</option>
                    <option value="P" {{ ($user['gender'] ?? '') === 'P' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>

        <div class="anim delay-3 mb-4">
            <label class="field-label">Province <span class="req">*</span></label>
            <button type="button" onclick="openSheet('provinsi')" class="inp flex items-center justify-between text-left">
                <span id="provinsiLabel" class="{{ ($user['provinsi'] ?? '') ? 'text-[#1E1B2E]' : 'text-[#B0A8CC]' }} font-[600]">{{ $user['provinsi'] ?? 'Select Province' }}</span>
                <ion-icon name="chevron-down-outline" style="font-size:17px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            </button>
            <input type="hidden" name="id_provinsi" id="idProvinsi" value="{{ $user['id_provinsi'] ?? '' }}">
        </div>

        <div class="anim delay-3 mb-4">
            <label class="field-label">City / District <span class="req">*</span></label>
            <button type="button" id="kotaBtn" onclick="openSheet('kota')"
                    class="inp flex items-center justify-between text-left {{ !($user['id_provinsi'] ?? '') ? 'opacity-50' : '' }}">
                <span id="kotaLabel" class="{{ ($user['kota'] ?? '') ? 'text-[#1E1B2E]' : 'text-[#B0A8CC]' }} font-[600]">{{ $user['kota'] ?? 'Select City' }}</span>
                <ion-icon name="chevron-down-outline" style="font-size:17px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            </button>
            <input type="hidden" name="id_kota" id="idKota" value="{{ $user['id_kota'] ?? '' }}">
        </div>

        <div class="anim delay-3 mb-5">
            <label class="field-label">Address <span class="req">*</span></label>
            <textarea name="alamat" id="alamat" rows="3" placeholder="Enter full address" class="inp resize-none">{{ $user['alamat'] ?? '' }}</textarea>
        </div>

        <div class="h-px bg-[#F0EDFB] mb-5"></div>

        <div class="anim delay-4 mb-4">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF]">More Information</p>
            <p class="text-[#8B46D3] text-[12px] font-semibold mt-[3px]">Optional - Improve Your Profile</p>
        </div>

        <div class="anim delay-4 mb-4">
            <label class="field-label">Bio</label>
            <textarea name="bio" rows="3" placeholder="Tell me about yourself" class="inp resize-none">{{ $user['bio'] ?? '' }}</textarea>
        </div>

        @if(($user['id_role'] ?? 0) != 2)
        <div class="anim delay-4 mb-4">
            <label class="field-label">Skill</label>
            <textarea name="skill" rows="2" placeholder="e.g. Indonesian dishes, childcare" class="inp resize-none">{{ $user['skill'] ?? '' }}</textarea>
        </div>
        <div class="anim delay-4 mb-4">
            <label class="field-label">Experience (years)</label>
            <input type="number" name="pengalaman" value="{{ $user['pengalaman'] ?? '' }}" placeholder="e.g. 3" min="0" class="inp"/>
        </div>
        <div class="anim delay-5 mb-4">
            <label class="field-label">Certification</label>
            <textarea name="sertifikasi" rows="2" placeholder="e.g. CPR, First Aid, ECD" class="inp resize-none">{{ $user['sertifikasi'] ?? '' }}</textarea>
        </div>
        @endif

        <div class="anim delay-5 mt-2 flex flex-col gap-3">
            <button type="submit" id="submitBtn"
                    class="w-full flex items-center justify-center gap-2 bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-[14px] text-[14px] tracking-wide transition-transform duration-150 active:scale-[0.97] shadow-[0_4px_16px_rgba(139,70,211,0.35)]">
                <ion-icon name="save-outline" id="btnIcon" style="font-size:18px;"></ion-icon>
                <span id="btnText">Save Changes</span>
                <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </button>

            @if($user['is_filled'] == 1)
            <a href="{{ route('profil.detail') }}"
               class="w-full flex items-center justify-center gap-2 bg-white border-[1.5px] border-[#FECACA] text-[#EF4444] font-extrabold py-[15px] rounded-[14px] text-[14px] tracking-wide transition-transform duration-150 active:scale-[0.97]">
                <ion-icon name="close-circle" style="font-size:18px;color:#EF4444;"></ion-icon>
                Cancel
            </a>
            @endif
        </div>

    </form>
    <div class="h-6"></div>

@else
{{-- ═══════════════ VIEW MODE ═══════════════ --}}

    <div class="flex flex-col items-center pt-[28px] pb-[24px]">
        <div class="avatar-in mb-4">
            @if($user['foto_url'] ?? null)
                <div class="w-[88px] h-[88px] rounded-full p-[3px]" style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                    <img src="{{ $user['foto_url'] }}" alt="photo" class="w-full h-full rounded-full object-cover border-2 border-white"/>
                </div>
            @else
                <div class="w-[88px] h-[88px] rounded-full p-[3px]" style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                    <div class="w-full h-full rounded-full bg-[#F0EDFB] border-2 border-white flex items-center justify-center">
                        <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                    </div>
                </div>
            @endif
        </div>
        <h1 class="anim delay-2 text-[#1E1B2E] text-[22px] font-extrabold leading-tight mb-3">{{ $user['name'] ?? 'User' }}</h1>
        <div class="anim delay-2">
            <span class="inline-block px-5 py-[6px] rounded-full bg-[#EDE9FE] text-[#8B46D3] text-[12px] font-bold">{{ $user['role'] ?? '' }}</span>
        </div>
    </div>

    <div class="flex flex-col gap-[10px] pb-5">
        <div class="anim delay-2">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-[#9CA3AF] mb-[6px]">Personal Information</p>
        </div>

        @php
            $infoRows = [
                ['icon' => 'mail-outline',     'iconBg' => '#EDE9FE', 'iconClr' => '#8B46D3', 'label' => 'EMAIL',        'value' => $user['email'] ?? '-'],
                ['icon' => 'call-outline',      'iconBg' => '#FCE7F3', 'iconClr' => '#EC4899', 'label' => 'PHONE NUMBER', 'value' => $user['no_hp'] ?? '-'],
                ['icon' => 'calendar-outline',  'iconBg' => '#E0E7FF', 'iconClr' => '#6366F1', 'label' => 'DATE OF BIRTH','value' => $user['tanggal_lahir'] ?? '-'],
                ['icon' => 'transgender-outline','iconBg'=> '#FEF3C7','iconClr'=> '#F59E0B',   'label' => 'GENDER',
                 'value' => ($user['gender'] ?? '') === 'L' ? 'Male' : (($user['gender'] ?? '') === 'P' ? 'Female' : '-')],
                ['icon' => 'location-outline',  'iconBg' => '#EDE9FE', 'iconClr' => '#8B46D3', 'label' => 'LOCATION',    'value' => $user['kota'] ?? '-'],
                ['icon' => 'home-outline',      'iconBg' => '#FCE7F3', 'iconClr' => '#EC4899', 'label' => 'ADDRESS',     'value' => $user['alamat'] ?? '-'],
            ];
            $rowDelay = 200;
        @endphp

        @foreach($infoRows as $row)
        @php $rowDelay += 60; @endphp
        <div class="info-row shadow-[0_2px_12px_rgba(0,0,0,0.07)]" style="animation: slideUp 0.4s ease {{ $rowDelay }}ms both; opacity:0;">
            <div class="info-icon" style="background:{{ $row['iconBg'] }};">
                <ion-icon name="{{ $row['icon'] }}" style="font-size:18px;color:{{ $row['iconClr'] }};"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">{{ $row['label'] }}</p>
                <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px] break-words">{{ $row['value'] }}</p>
            </div>
        </div>
        @endforeach

        @if($user['bio'] ?? null)
        <div class="info-row" style="animation: slideUp 0.4s ease {{ $rowDelay + 60 }}ms both; opacity:0;">
            <div class="info-icon bg-[#F0EDFB]">
                <ion-icon name="document-text-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">BIO</p>
                <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px]">{{ $user['bio'] }}</p>
            </div>
        </div>
        @endif

        @if(($user['id_role'] ?? 0) != 2)
            @if($user['skill'] ?? null)
            <div class="info-row" style="animation: slideUp 0.4s ease {{ $rowDelay + 80 }}ms both; opacity:0;">
                <div class="info-icon bg-[#DBEAFE]">
                    <ion-icon name="star-outline" style="font-size:18px;color:#3B82F6;"></ion-icon>
                </div>
                <div><p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">SKILL</p>
                <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px]">{{ $user['skill'] }}</p></div>
            </div>
            @endif
            @if($user['pengalaman'] ?? null)
            <div class="info-row" style="animation: slideUp 0.4s ease {{ $rowDelay + 100 }}ms both; opacity:0;">
                <div class="info-icon bg-[#D1FAE5]">
                    <ion-icon name="briefcase-outline" style="font-size:18px;color:#10B981;"></ion-icon>
                </div>
                <div><p class="text-[9px] font-extrabold tracking-wider text-[#9CA3AF]">EXPERIENCE</p>
                <p class="text-[#1E1B2E] text-[14px] font-bold mt-[3px]">{{ $user['pengalaman'] }} years</p></div>
            </div>
            @endif
        @endif

        <div style="animation: slideUp 0.4s ease {{ $rowDelay + 120 }}ms both; opacity:0; margin-top: 8px;">
            <a href="{{ route('profil.detail', ['edit' => 1]) }}"
               class="w-full flex items-center justify-center gap-2 bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-[14px] text-[14px] tracking-wide shadow-[0_4px_16px_rgba(139,70,211,0.35)] transition-transform duration-150 active:scale-[0.97]">
                <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                Update Profile
            </a>
        </div>

    </div>

@endif
</div>
@endsection

@push('modals')
<!-- Backdrop for sheets -->
<div id="sheetBackdrop" class="sheet-backdrop fixed inset-0 bg-black/50 z-40 hidden opacity-0" onclick="closeSheet()"></div>

<!-- Provinsi Sheet -->
<div id="provinsiSheet" class="sheet fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-[28px] max-h-[75vh] flex flex-col sm:max-w-[390px] sm:mx-auto">
    <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#F0EDFB] shrink-0">
        <h3 class="text-[#1E1B2E] font-extrabold text-[16px]">Select Province</h3>
        <button onclick="closeSheet()" class="w-8 h-8 rounded-full bg-[#F0EDFB] flex items-center justify-center">
            <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
        </button>
    </div>
    <div class="px-4 py-3 shrink-0">
        <div class="flex items-center gap-2 bg-[#F5F4FB] rounded-[12px] px-4 py-2.5">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <input type="text" id="provinsiSearch" placeholder="Search province..." oninput="filterList('provinsi', this.value)" class="flex-1 bg-transparent text-sm text-[#1E1B2E] placeholder-[#B0A8CC] outline-none font-semibold"/>
        </div>
    </div>
    <div id="provinsiList" class="overflow-y-auto flex-1 pb-4">
        <div class="flex justify-center py-8">
            <svg class="w-6 h-6 animate-spin" style="color:#8B46D3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Kota Sheet -->
<div id="kotaSheet" class="sheet fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-[28px] max-h-[75vh] flex flex-col sm:max-w-[390px] sm:mx-auto">
    <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#F0EDFB] shrink-0">
        <h3 class="text-[#1E1B2E] font-extrabold text-[16px]">Select City</h3>
        <button onclick="closeSheet()" class="w-8 h-8 rounded-full bg-[#F0EDFB] flex items-center justify-center">
            <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
        </button>
    </div>
    <div class="px-4 py-3 shrink-0">
        <div class="flex items-center gap-2 bg-[#F5F4FB] rounded-[12px] px-4 py-2.5">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <input type="text" id="kotaSearch" placeholder="Search city..." oninput="filterList('kota', this.value)" class="flex-1 bg-transparent text-sm text-[#1E1B2E] placeholder-[#B0A8CC] outline-none font-semibold"/>
        </div>
    </div>
    <div id="kotaList" class="overflow-y-auto flex-1 pb-4">
        <div class="flex justify-center py-8">
            <svg class="w-6 h-6 animate-spin" style="color:#8B46D3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[60] flex items-center gap-3 bg-white rounded-2xl px-5 py-3.5 shadow-[0_8px_32px_rgba(0,0,0,0.15)] border border-[#F0EDFB] transition-all duration-300 opacity-0 -translate-y-2 pointer-events-none max-w-[340px] w-[90%]">
    <div id="toastIcon" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"></div>
    <p id="toastMsg" class="text-[#1E1B2E] text-[13px] font-bold flex-1"></p>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-[70] flex items-end justify-center hidden opacity-0" style="background: rgba(15,10,35,0.55); backdrop-filter: blur(4px);">
    <div class="modal-box-in w-full sm:max-w-[390px] bg-gradient-to-b from-white via-white to-[#D4BAEF]/40 rounded-t-[36px] pt-10 pb-12 px-8 flex flex-col items-center relative overflow-hidden min-h-[460px]">
        <div class="absolute top-6 right-8 pointer-events-none">
            <svg class="spin-slow w-[75px] h-[75px] text-[#D4BAEF]" viewBox="0 0 75 75" fill="none">
                <circle cx="37.5" cy="37.5" r="33" stroke="currentColor" stroke-width="2.5" stroke-dasharray="7 5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="dot-r absolute top-9 left-12 w-3.5 h-3.5 rounded-full bg-[#EF4444] pointer-events-none"></div>
        <div class="dot-o absolute top-[140px] left-8 w-3 h-3 rounded-full bg-[#F59E0B] pointer-events-none"></div>
        <div class="dot-b absolute top-[155px] right-10 w-3.5 h-3.5 rounded-full bg-[#3B82F6] pointer-events-none"></div>
        <div class="absolute bottom-16 left-6 pointer-events-none opacity-50">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <rect x="3" y="3" width="54" height="54" rx="14" stroke="#C4B5FD" stroke-width="2.5" fill="none"/>
            </svg>
        </div>
        <div class="badge-pop mb-7 z-10">
            <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                <path d="M60 7 L67 22 L83 18.5 L80.5 35 L95 43.5 L86 57 L95 70.5 L80.5 79 L83 95.5 L67 92 L60 107 L53 92 L37 95.5 L39.5 79 L25 70.5 L34 57 L25 43.5 L39.5 35 L37 18.5 L53 22 Z" fill="#22C55E"/>
                <path d="M41 60 L54 73 L79 47" stroke="white" stroke-width="5.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h2 class="txt-in-1 text-[#1E1B2E] text-[30px] font-extrabold mb-2 text-center z-10">Successful!</h2>
        <p class="txt-in-2 text-[#9CA3AF] text-[14px] font-semibold text-center leading-relaxed mb-9 max-w-[240px] z-10">Congratulations, your data has been successfully updated.</p>
        <div class="btn-in w-full z-10">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full bg-[#8B46D3] text-white font-extrabold py-[15px] rounded-full text-[14px] tracking-wide shadow-[0_4px_20px_rgba(139,70,211,0.32)] transition-transform duration-150 active:scale-[0.97]">
                Browse Home <span class="text-base leading-none">→</span>
            </a>
        </div>
        <div class="rdr-in flex items-center gap-2 mt-4 z-10">
            <span class="pulse-dot w-2 h-2 rounded-full bg-[#22C55E] inline-block"></span>
            <span class="text-[#9CA3AF] text-[11px] font-bold uppercase tracking-wider">AUTO REDIRECT IN <span id="successCountdown">5</span>s</span>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>

// Toast
function showToast(msg, type = 'error') {
    const toast  = document.getElementById('toast');
    const icon   = document.getElementById('toastIcon');
    const msgEl  = document.getElementById('toastMsg');
    const colors = { error: { bg:'#FEE2E2', clr:'#EF4444', name:'close-circle' },
                     success:{ bg:'#D1FAE5', clr:'#10B981', name:'checkmark-circle' } };
    const c = colors[type] || colors.error;
    icon.style.background = c.bg;
    icon.innerHTML = `<ion-icon name="${c.name}" style="font-size:18px;color:${c.clr};"></ion-icon>`;
    msgEl.textContent = msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    toast.style.pointerEvents = 'auto';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(-8px)';
        toast.style.pointerEvents = 'none';
    }, type === 'success' ? 1800 : 3000);
}

// Avatar preview
const fotoInput = document.getElementById('fotoInput');
if (fotoInput) {
    fotoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const preview     = document.getElementById('avatarPreview');
        const placeholder = document.getElementById('avatarPlaceholder');
        const icon        = document.getElementById('avatarIcon');
        const reader      = new FileReader();
        reader.onload = e => {
            if (preview) { preview.src = e.target.result; preview.classList.remove('hidden'); preview.style.display = ''; }
            if (placeholder) placeholder.style.display = 'none';
            if (icon) icon.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

// Bottom Sheet
let activeSheet  = null;
let provinsiData = [];
let kotaData     = [];
const API_BASE   = "{{ url('/profil') }}";

async function openSheet(type) {
    activeSheet = type;
    const backdrop = document.getElementById('sheetBackdrop');
    const sheet    = document.getElementById(type + 'Sheet');
    backdrop.classList.remove('hidden');
    requestAnimationFrame(() => { backdrop.style.opacity = '1'; sheet.classList.add('open'); });
    if (type === 'provinsi' && provinsiData.length === 0) await loadProvinsi();
    if (type === 'kota') {
        const idProv = document.getElementById('idProvinsi').value;
        if (!idProv) { closeSheet(); showToast('Select province first!'); return; }
        await loadKota(idProv);
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
        const res  = await fetch(`${API_BASE}/provinsi`, { headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN } });
        const data = await res.json();
        if (data.success) { provinsiData = data.data; renderList('provinsi', provinsiData); }
    } catch { showToast('Failed to load province data.'); }
}
async function loadKota(idProv) {
    kotaData = []; renderListLoading('kota');
    try {
        const res  = await fetch(`${API_BASE}/kota/${idProv}`, { headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN } });
        const data = await res.json();
        if (data.success) { kotaData = data.data; renderList('kota', kotaData); }
    } catch { showToast('Failed to load city data.'); }
}
function renderListLoading(type) {
    document.getElementById(type + 'List').innerHTML = `<div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin" style="color:#8B46D3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></div>`;
}
function renderList(type, data) {
    const el = document.getElementById(type + 'List');
        if (!data || !data.length) { el.innerHTML = '<p class="text-center text-[#9CA3AF] text-sm py-8 font-semibold">No data found</p>'; return; }
    el.innerHTML = data.map(item =>
        `<button type="button" onclick="selectItem('${type}',${item.id},'${item.nama.replace(/'/g,"\\'")}')" class="w-full text-left px-5 py-[13px] border-b border-[#F5F4FB] text-[14px] font-semibold text-[#1E1B2E] hover:bg-[#F5F4FB] transition-colors">${item.nama}</button>`
    ).join('');
}
function selectItem(type, id, nama) {
    if (type === 'provinsi') {
        document.getElementById('idProvinsi').value = id;
        document.getElementById('provinsiLabel').textContent = nama;
        document.getElementById('provinsiLabel').className = 'text-[#1E1B2E] font-[600]';
        document.getElementById('idKota').value = '';
        document.getElementById('kotaLabel').textContent = 'Select City';
        document.getElementById('kotaLabel').className = 'text-[#B0A8CC] font-[600]';
        kotaData = [];
        const kb = document.getElementById('kotaBtn');
        if (kb) { kb.disabled = false; kb.classList.remove('opacity-50'); }
    } else {
        document.getElementById('idKota').value = id;
        document.getElementById('kotaLabel').textContent = nama;
        document.getElementById('kotaLabel').className = 'text-[#1E1B2E] font-[600]';
    }
    closeSheet();
}
function filterList(type, q) {
    const src = type === 'provinsi' ? provinsiData : kotaData;
    renderList(type, src.filter(i => i.nama.toLowerCase().includes(q.toLowerCase())));
}

// Form submit
const form = document.getElementById('profileForm');
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name   = document.getElementById('name')?.value.trim();
        const noHp   = document.getElementById('noHp')?.value.trim();
        const tgl    = document.getElementById('tanggalLahir')?.value;
        const gender = document.getElementById('genderSelect')?.value;
        const prov   = document.getElementById('idProvinsi')?.value;
        const kota   = document.getElementById('idKota')?.value;
        const alamat = document.getElementById('alamat')?.value.trim();

        if (!name)   return showToast('Name is required!');
        if (!noHp)   return showToast('Phone number is required!');
        if (!tgl)    return showToast('Date of birth is required!');
        if (!gender) return showToast('Gender is required!');
        if (!prov)   return showToast('Province is required!');
        if (!kota)   return showToast('City is required!');
        if (!alamat) return showToast('Address is required!');

        const btn     = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const spinner = document.getElementById('btnSpinner');
        btn.disabled = true;
        btnText.textContent = 'Saving...';
        btnIcon.style.display = 'none';
        spinner.classList.remove('hidden');

        try {
            const fd  = new FormData(form);
            const fotoInput = document.getElementById('fotoInput');
            if (fotoInput && fotoInput.files[0]) fd.append('foto', fotoInput.files[0]);
            const res = await fetch('{{ route("profil.update") }}', {
                method: 'POST',
                headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN },
                body: fd
            });
            const data = await res.json();
            if (data.success) { showSuccessModal(); }
            else {
                const err = data.errors ? Object.values(data.errors)[0] : (data.message || 'Failed to save profile.');
                showToast(Array.isArray(err) ? err[0] : err);
            }
        } catch { showToast('An error occurred. Try again.'); }
        finally {
            btn.disabled = false;
            btnText.textContent = 'Save Changes';
            btnIcon.style.display = '';
            spinner.classList.add('hidden');
        }
    });
}

// Success modal countdown
let _countdownIv = null;
function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => { modal.style.opacity = '1'; });
    let secs = 5;
    const el = document.getElementById('successCountdown');
    clearInterval(_countdownIv);
    _countdownIv = setInterval(() => {
        secs--;
        if (el) el.textContent = secs;
        if (secs <= 0) { clearInterval(_countdownIv); window.location.href = '{{ route("dashboard") }}'; }
    }, 1000);
}
</script>
@endpush
