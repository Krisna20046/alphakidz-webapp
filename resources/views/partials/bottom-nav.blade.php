{{--
    resources/views/partials/bottom-nav.blade.php
    Usage: @include('partials.bottom-nav', ['active' => 'home'])
    Active values: 'home' | 'artikel' | 'profil'
--}}

@php $active = $active ?? 'home'; @endphp

<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-[#F0EAFF] flex items-center h-16 z-50 shadow-[0_-4px_20px_rgba(124,58,237,0.08)] pb-[env(safe-area-inset-bottom,0px)] sm:w-[390px] sm:left-1/2 sm:-translate-x-1/2 sm:rounded-b-[44px]">

    {{-- Home --}}
    <a href="{{ route('dashboard') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 no-underline relative h-full cursor-pointer transition-opacity duration-150 ease-in-out active:opacity-70">
        
        @if($active === 'home')
            <div class="w-[60px] h-[60px] rounded-full bg-[#8B46D3] flex items-center justify-center absolute -top-6 animate-[navPop_0.25s_cubic-bezier(0.34,1.56,0.64,1)]" style="border: 5px solid white">
                <ion-icon name="home" style="font-size:22px;color:white;"></ion-icon>
            </div>
            <div class="absolute bottom-0 w-6 h-[3px] bg-[#8B46D3] rounded-t-[3px]"></div>
            <span class="font-['Nunito'] text-[10px] font-extrabold text-[#8B46D3] mt-[30px]">
                Home
            </span>
        @else
            <div class="w-6 h-6 flex items-center justify-center">
                <ion-icon name="home-outline" style="font-size:22px;color:#9CA3AF;"></ion-icon>
            </div>
            <span class="font-['Nunito'] text-[10px] font-semibold text-[#9CA3AF]">
                Home
            </span>
        @endif
    </a>

    {{-- Artikel --}}
    <a href="{{ route('artikel.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 no-underline relative h-full cursor-pointer transition-opacity duration-150 ease-in-out active:opacity-70">
        
        @if($active === 'artikel')
            <div class="w-[60px] h-[60px] rounded-full bg-[#8B46D3] flex items-center justify-center absolute -top-6 animate-[navPop_0.25s_cubic-bezier(0.34,1.56,0.64,1)]" style="border: 5px solid white">
                <ion-icon name="book" style="font-size:22px;color:white;"></ion-icon>
            </div>
            <div class="absolute bottom-0 w-6 h-[3px] bg-[#8B46D3] rounded-t-[3px]"></div>
            <span class="font-['Nunito'] text-[10px] font-extrabold text-[#8B46D3] mt-[30px]">
                Article
            </span>
        @else
            <div class="w-6 h-6 flex items-center justify-center">
                <ion-icon name="book-outline" style="font-size:22px;color:#9CA3AF;"></ion-icon>
            </div>
            <span class="font-['Nunito'] text-[10px] font-semibold text-[#9CA3AF]">
                Article
            </span>
        @endif
    </a>

    {{-- Profil --}}
    <a href="{{ route('profil.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 no-underline relative h-full cursor-pointer transition-opacity duration-150 ease-in-out active:opacity-70">
        
        @if($active === 'profil')
            <div class="w-[60px] h-[60px] rounded-full bg-[#8B46D3] flex items-center justify-center absolute -top-6 animate-[navPop_0.25s_cubic-bezier(0.34,1.56,0.64,1)]" style="border: 5px solid white">
                <ion-icon name="person" style="font-size:22px;color:white;"></ion-icon>
            </div>
            <div class="absolute bottom-0 w-6 h-[3px] bg-[#8B46D3] rounded-t-[3px]"></div>
            <span class="font-['Nunito'] text-[10px] font-extrabold text-[#8B46D3] mt-[30px]">
                Profile
            </span>
        @else
            <div class="w-6 h-6 flex items-center justify-center">
                <ion-icon name="person-outline" style="font-size:22px;color:#9CA3AF;"></ion-icon>
            </div>
            <span class="font-['Nunito'] text-[10px] font-semibold text-[#9CA3AF]">
                Profile
            </span>
        @endif
    </a>

</nav>

{{-- Tambahkan keyframes untuk animasi navPop --}}
<style>
    @keyframes navPop {
        0% { transform: scale(0.8); }
        60% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>