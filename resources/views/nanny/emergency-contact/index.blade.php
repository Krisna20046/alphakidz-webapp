@extends('layouts.app')

@section('title', 'Emergency Contacts')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    .anak-card { transition: opacity .15s ease, transform .15s ease; }
    .anak-card:hover { opacity: .85; }
    .anak-card:active { transform: scale(0.98); opacity: .7; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Emergency Contacts</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Manage your child's emergency contacts</p>
        </div>
    </div>
</div>

@if(count($anakList) > 0)
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    <div class="flex items-center justify-between anim delay-2 mb-3">
        <span style="font-size:18px;font-weight:700;color:#1E1B2E;">Choose Child</span>
        <span style="background:#EDE9FE;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700;color:#8B46D3;">
            {{ count($anakList) }}
        </span>
    </div>

    <div class="flex flex-col gap-3">
        @foreach($anakList as $i => $anak)
        <a href="{{ route('nanny-emergency-contacts-show', $anak['id']) }}"
           class="anak-card flex items-center bg-white"
           style="border-radius:16px;border:2px solid #EAE6F5;padding:16px;
                  animation:slideUp 0.3s ease {{ $i*0.06 }}s both;opacity:0;">
            <div style="margin-right:12px;flex-shrink:0;">
                @if(!empty($anak['foto']))
                <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}" class="object-cover"
                     style="width:56px;height:56px;border-radius:28px;border:3px solid #EDE9FE;"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="items-center justify-center hidden"
                     style="width:56px;height:56px;border-radius:28px;background:#F3F0FD;border:3px solid #EDE9FE;">
                    <ion-icon name="body" style="font-size:24px;color:#8B46D3;"></ion-icon>
                </div>
                @else
                <div class="flex items-center justify-center"
                     style="width:56px;height:56px;border-radius:28px;background:#F3F0FD;border:3px solid #EDE9FE;">
                    <ion-icon name="body" style="font-size:24px;color:#8B46D3;"></ion-icon>
                </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="line-clamp-1" style="font-size:16px;font-weight:700;color:#1E1B2E;margin-bottom:6px;">
                    {{ $anak['nama'] }}
                </p>
                <div class="flex items-center" style="gap:12px;">
                    @php $m = ($anak['gender']??'') === 'L'; @endphp
                    <span style="font-size:13px;color:#8B86A5;font-weight:500;">{{ $m ? 'Male' : 'Female' }}</span>
                    <div class="flex items-center" style="gap:4px;">
                        <ion-icon name="call-outline" style="font-size:13px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span style="font-size:13px;color:#8B86A5;font-weight:500;">Contacts</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#EDE9FE;margin-left:8px;">
                <ion-icon name="chevron-forward" style="font-size:20px;color:#C4B5FD;"></ion-icon>
            </div>
        </a>
        @endforeach
    </div>
</div>
@else
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[32px] -mt-[50px] relative z-20 hide-scrollbar">
    <div class="flex flex-col items-center justify-center" style="padding:80px 40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#EDE9FE;margin-bottom:24px;">
            <ion-icon name="call-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#1E1B2E;margin-bottom:8px;">No assigned child yet</p>
        <p class="text-center" style="font-size:14px;color:#9CA3AF;line-height:20px;">No children are assigned to you right now.</p>
    </div>
</div>
@endif
@endsection