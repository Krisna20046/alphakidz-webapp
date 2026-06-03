@extends('layouts.app')

@section('title', 'Nanny Anda')

@push('styles')
<style>
    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .nanny-card { transition: transform .15s ease; }
    .nanny-card:active { transform: scale(0.98); }

    .badge-aktif { background: #DCFCE7; color: #166534; }
    .badge-nonaktif { background: #FEE2E2; color: #991B1B; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Nanny Anda</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Daftar nanny di bawah pengawasan Anda</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    @if(session('success'))
    <div id="flash-success" class="anim delay-2 bg-[#ECFDF3] border border-[#BBF7D0] text-[#166534] text-[12px] font-bold px-4 py-3 rounded-[18px] flex items-center gap-2 shadow-[0_2px_8px_rgba(0,0,0,0.04)]">
        <ion-icon name="checkmark-circle" style="font-size:16px;color:#16A34A;flex-shrink:0;"></ion-icon>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div id="flash-error" class="anim delay-2 bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] text-[12px] font-bold px-4 py-3 rounded-[18px] flex items-center gap-2 shadow-[0_2px_8px_rgba(0,0,0,0.04)]">
        <ion-icon name="alert-circle" style="font-size:16px;color:#DC2626;flex-shrink:0;"></ion-icon>
        {{ session('error') }}
    </div>
    @endif

    <div class="anim delay-2 flex items-center justify-between px-1">
        <div>
            <h2 class="text-[#1E1B2E] font-extrabold text-[16px]">Daftar Nanny</h2>
            <p class="text-[#8B86A5] text-[11px] font-semibold mt-0.5">Pantau nanny yang sedang terhubung dengan Anda</p>
        </div>
        <div class="min-w-[38px] h-[32px] px-3 rounded-full bg-white border border-[#E6E1F2] flex items-center justify-center shadow-[0_2px_8px_rgba(0,0,0,0.05)]">
            <span class="text-[#8B46D3] text-[12px] font-extrabold">{{ count($nannies ?? []) }}</span>
        </div>
    </div>

    <div class="anim delay-3">
        @if(isset($nannies) && count($nannies) > 0)
        <div class="flex flex-col gap-3 pb-6">
            @foreach($nannies as $i => $nanny)
            @php
                $isMale = ($nanny['gender'] ?? '') === 'L';
                $isActive = (int)($nanny['is_active'] ?? 1) === 1;
            @endphp
            <a href="{{ route('konsultan-nanny-anda-detail', $nanny['id']) }}"
               class="nanny-card block bg-white rounded-[14px] px-3 py-3 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5]"
               style="animation: slideUp 0.35s ease {{ $i * 0.05 }}s both; opacity:0;">

                <div class="flex items-center gap-3">
                    @if(!empty($nanny['foto']))
                    <img src="{{ $nanny['foto'] }}"
                         alt="{{ $nanny['name'] }}"
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
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $nanny['name'] }}</p>
                            <span class="text-[9px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0 whitespace-nowrap {{ $isActive ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1 mb-0.5">
                            <ion-icon name="at-outline" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $nanny['email'] ?? '-' }}</span>
                        </div>

                        <div class="flex items-center gap-1 mb-0.5">
                            <ion-icon name="{{ $isMale ? 'male-outline' : 'female-outline' }}" style="font-size:11px;color:#F59E0B;flex-shrink:0;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold truncate">{{ $isMale ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>

                        @if(!empty($nanny['catatan']))
                        <div class="flex items-start gap-1">
                            <ion-icon name="document-text-outline" style="font-size:11px;color:#10B981;flex-shrink:0;margin-top:1px;"></ion-icon>
                            <span class="text-[#8B86A5] text-[11px] font-semibold leading-relaxed line-clamp-2">{{ $nanny['catatan'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center pt-16 pb-10 px-8">
            <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                <ion-icon name="people-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Belum ada nanny</h3>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                Anda belum memiliki nanny yang terdaftar di bawah pengawasan Anda
            </p>
            <a href="{{ route('konsultan-nanny-list') }}"
               class="mt-6 bg-[#8B46D3] text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-[0_8px_18px_rgba(139,70,211,0.35)] flex items-center gap-2">
                <ion-icon name="search-outline" style="font-size:16px;"></ion-icon>
                Cari Nanny
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    setTimeout(() => {
        ['flash-success', 'flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }, 4000);
</script>
@endpush
