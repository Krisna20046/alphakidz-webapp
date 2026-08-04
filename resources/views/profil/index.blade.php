@extends('layouts.app')

@php $activeNav = 'profil' @endphp

@section('title', 'Profil')

@push('styles')
<style>
    @keyframes avatarIn {
        from { opacity: 0; transform: translateY(12px) scale(0.88); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .avatar-in { animation: avatarIn 0.45s cubic-bezier(0.34,1.56,0.64,1) 0.12s forwards; opacity: 0; }

    .menu-card { transition: transform 0.15s ease; }
    .menu-card:active { transform: scale(0.97); }

    #logoutModal { transition: opacity 0.2s ease; }
    #logoutModalBox { transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1); }
</style>
@endpush

@section('content')
<!-- PURPLE HEADER -->
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <span class="text-white text-[17px] font-extrabold tracking-wide">My Profile</span>
    </div>
</div>

<!-- WHITE BODY -->
<div class="flex-1 overflow-y-auto px-[30px] pt-[30px] pb-20 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 flex flex-col gap-5 hide-scrollbar">

    <!-- PROFILE INFO SECTION -->
    <div class="flex flex-col items-center pt-[28px] pb-[24px] border-b border-[#F3F0FB]">

        <div class="avatar-in mb-[14px]">
            @if(session('user')['foto_url'] ?? null)
                <div class="w-[88px] h-[88px] rounded-full p-[3px]" style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                    <img src="{{ session('user')['foto_url'] }}" alt="foto" class="w-full h-full rounded-full object-cover border-2 border-white"/>
                </div>
            @else
                <div class="w-[88px] h-[88px] rounded-full p-[3px]" style="background: linear-gradient(135deg, #C4B5FD 0%, #8B46D3 100%);">
                    <div class="w-full h-full rounded-full bg-[#F0EDFB] border-2 border-white flex items-center justify-center">
                        <ion-icon name="person" style="font-size:42px;color:#8B46D3;"></ion-icon>
                    </div>
                </div>
            @endif
        </div>

        <h1 class="anim delay-2 text-[#1E1B2E] text-[22px] font-extrabold leading-tight mb-[6px]">
            {{ session('user')['name'] ?? 'User' }}
        </h1>

        <div class="anim delay-2 flex items-center gap-[6px] mb-[4px]">
            <ion-icon name="at-circle-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
            <span class="text-[#6B6589] text-[13px] font-semibold">{{ session('user')['email'] ?? '' }}</span>
        </div>

        <div class="anim delay-2 flex items-center gap-[6px] mb-[14px]">
            <ion-icon name="call" style="font-size:14px;color:#F59E0B;"></ion-icon>
            <span class="text-[#6B6589] text-[13px] font-semibold">{{ session('user')['no_hp'] ?? '' }}</span>
        </div>

        <div class="anim delay-3">
            <span class="inline-block px-5 py-[6px] rounded-full bg-[#EDE9FE] text-[#8B46D3] text-[12px] font-bold">
                {{ session('user')['role'] ?? '' }}
            </span>
        </div>
    </div>

    <!-- GENERAL SETTINGS -->
    <div class="pt-[22px] flex flex-col gap-[10px]">

        <div class="anim delay-3 mb-[4px]">
            <span class="text-[#9CA3AF] text-[11px] font-extrabold uppercase tracking-[0.12em]">General Settings</span>
        </div>

        @php
            $settingMenus = [
                ['route' => 'profil.detail',  'icon' => 'person-outline',  'label' => 'Edit Profile',     'sub' => 'Personal information and profile photo',            'iconBg' => '#EDE9FE', 'iconClr' => '#8B46D3'],
                ['route' => 'profil.edit-akun','icon' => 'people-outline',  'label' => 'Edit Account',     'sub' => 'Email, password, and security',                      'iconBg' => '#FCE7F3', 'iconClr' => '#EC4899'],
                ['route' => 'profil.data-anak', 'icon' => 'happy-outline',  'label' => 'Child Data',       'sub' => "Child's health history and preferences",             'iconBg' => '#E0E7FF', 'iconClr' => '#6366F1', 'role' => 'Majikan'],
                ['route' => 'reminder.index',   'icon' => 'alarm-outline',  'label' => 'Reminders',        'sub' => 'Reminders for your needs',                           'iconBg' => '#FEF3C7', 'iconClr' => '#F59E0B'],
                ['route' => 'stock.index',      'icon' => 'pricetag-outline','label' => 'Expense Tracking', 'sub' => "Transparent recording of children's expenses",       'iconBg' => '#DBEAFE', 'iconClr' => '#3B82F6'],
            ];
            $delayMs = 280;
        @endphp

        @foreach($settingMenus as $item)
            @if(isset($item['role']) && (session('user')['role'] ?? '') !== $item['role'])
                @continue
            @endif
            @php
                $delayMs += 70;
                try { $url = route($item['route']); } catch (\Exception $e) { $url = '#'; }
            @endphp
            <a href="{{ $url }}" class="menu-card block" style="animation: slideUp 0.4s ease {{ $delayMs }}ms both;">
                <div class="bg-[#FAFAFA] rounded-[16px] flex items-center gap-[14px] px-[14px] py-[13px] shadow-[0_2px_12px_rgba(0,0,0,0.07)]">
                    <div class="w-[48px] h-[48px] rounded-[14px] flex items-center justify-center shrink-0" style="background:{{ $item['iconBg'] }};">
                        <ion-icon name="{{ $item['icon'] }}" style="font-size:22px;color:{{ $item['iconClr'] }};"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] text-[14px] font-bold leading-tight">{{ $item['label'] }}</p>
                        <p class="text-[#9CA3AF] text-[11px] font-medium mt-[3px] leading-snug">{{ $item['sub'] }}</p>
                    </div>
                    <ion-icon name="chevron-forward" style="font-size:16px;color:#C4B5FD;" class="shrink-0"></ion-icon>
                </div>
            </a>
        @endforeach

        <!-- LOGOUT BUTTON -->
        <div style="animation: slideUp 0.4s ease {{ $delayMs + 70 }}ms both; opacity:0;">
            <button onclick="showLogoutModal()" class="w-full mt-[4px] flex items-center justify-center gap-2 bg-white border border-[#FECACA] rounded-[16px] py-[15px] shadow-[0_1px_5px_rgba(0,0,0,0.06)] transition-transform duration-150 active:scale-[0.97]">
                <ion-icon name="log-out-outline" style="font-size:20px;color:#EF4444;"></ion-icon>
                <span class="text-[#EF4444] text-[14px] font-extrabold tracking-wide">Logout</span>
            </button>
        </div>

    </div>

    <div class="h-4"></div>
</div>

<!-- LOGOUT CONFIRM MODAL -->
<div id="logoutModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 hidden opacity-0 px-5 pb-8 sm:pb-0 z-[9999]">
    <div id="logoutModalBox" class="w-full max-w-sm bg-white rounded-[28px] p-6 shadow-2xl scale-90">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <ion-icon name="log-out-outline" style="font-size:32px;color:#ef4444;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] text-lg font-extrabold mb-1">Log out of your account?</h3>
            <p class="text-[#9CA3AF] text-sm leading-relaxed">You need to log in again to access the app.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="hideLogoutModal()" class="flex-1 py-[14px] rounded-2xl border-2 border-[#EDE9FE] text-[#8B46D3] font-bold text-sm active:bg-[#EDE9FE] transition-all">Cancel</button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1" id="logoutForm">
                @csrf
                <button type="button" onclick="doLogout()" class="w-full py-[14px] rounded-2xl bg-red-500 text-white font-bold text-sm active:bg-red-600 transition-all">Yes, Log Out</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal    = document.getElementById('logoutModal');
const modalBox = document.getElementById('logoutModalBox');

function showLogoutModal() {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modalBox.style.transform = 'scale(1)';
    });
}
function hideLogoutModal() {
    modal.style.opacity = '0';
    modalBox.style.transform = 'scale(0.9)';
    setTimeout(() => modal.classList.add('hidden'), 200);
}
function doLogout() {
    if (typeof removeFcmTokenOnLogout === 'function') removeFcmTokenOnLogout();
    // Hapus semua cache aplikasi saat logout
    if (window.apiCache) window.apiCache.clear();
    document.getElementById('logoutForm').submit();
}
modal.addEventListener('click', (e) => { if (e.target === modal) hideLogoutModal(); });
</script>
@endpush
