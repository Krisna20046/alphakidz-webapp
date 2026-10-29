@php
    // Today state: rows for this nanny's own attendance (self). Checked-in = open checkin.
    $todayRec = count($today) > 0 ? $today[0] : null;
    $checkedIn  = $todayRec && empty($todayRec['checkout_time']);
    $checkedOut = $todayRec && !empty($todayRec['checkout_time']);
@endphp

@if(!$ctx)
{{-- No active assignment: empty state (no child picker needed) --}}
<div id="atNoAssignment" class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-6">
    <div class="flex flex-col items-center text-center">
        <div class="w-16 h-16 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
            <ion-icon name="time-outline" style="font-size:32px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[15px] font-extrabold text-[#1E1B2E] mb-1">No active assignment</p>
        <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed">
            You have no active assignment right now. Attendance will appear here once you are hired.
        </p>
    </div>
</div>
@else
{{-- Today status card (AJAX-refreshed after check-in/out) --}}
<div id="atTodayStatus" class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
    <div class="flex items-center gap-2 mb-3">
        <div class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
            <ion-icon name="time-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
        </div>
        <div class="flex-1">
            <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Today's Attendance</span>
            <span class="text-[11px] font-semibold text-[#8B86A5]">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
        @if($checkedIn || $checkedOut)
        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $checkedIn ? 'bg-[#FEF3C7] text-[#D97706]' : 'bg-[#F0FDF4] text-[#16A34A]' }}">
            <ion-icon name="{{ $checkedIn ? 'time-outline' : 'checkmark-circle' }}" style="font-size:11px;"></ion-icon>
            {{ $checkedIn ? 'Checked In' : 'Checked Out' }}
        </span>
        @else
        <span class="px-2.5 py-1 rounded-full bg-[#F3F4F6] text-[10px] font-bold text-[#6B7280]">
            Not Checked In
        </span>
        @endif
    </div>

    @if($checkedOut)
    {{-- Already attended today (checked in AND checked out): show note, hide all buttons --}}
    <div class="rounded-2xl bg-[#F0FDF4] border border-[#BBF7D0] p-4 text-center">
        <div class="w-12 h-12 rounded-full bg-[#16A34A] text-white flex items-center justify-center mx-auto mb-2">
            <ion-icon name="checkmark" style="font-size:24px;"></ion-icon>
        </div>
        <p class="text-[14px] font-extrabold text-[#166534]">Attendance complete</p>
        <p class="text-[12px] text-[#15803D] mt-1">You've checked in &amp; out today.</p>
        <div class="flex items-center justify-center gap-4 mt-3 text-sm">
            <span class="text-[#8B86A5] text-[12px] font-bold"><ion-icon name="log-in-outline" style="font-size:14px;color:#D97706;"></ion-icon>&nbsp;{{ !empty($todayRec['checkin_time']) ? \Carbon\Carbon::parse($todayRec['checkin_time'])->format('H:i') : '—' }}</span>
            <span class="text-[#8B86A5] text-[12px] font-bold"><ion-icon name="log-out-outline" style="font-size:14px;color:#16A34A;"></ion-icon>&nbsp;{{ !empty($todayRec['checkout_time']) ? \Carbon\Carbon::parse($todayRec['checkout_time'])->format('H:i') : '—' }}</span>
        </div>
    </div>
    @elseif($checkedIn)
    {{-- Checked-in: show time + Check-out form --}}
    <div class="mb-3 rounded-xl bg-[#FEF9E7] border border-[#F5E1A4] p-3 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-[#FDE68A] flex items-center justify-center shrink-0">
            <ion-icon name="checkmark-circle" style="font-size:20px;color:#D97706;"></ion-icon>
        </div>
        <div class="flex-1">
            <span class="text-[13px] font-extrabold text-[#92400E] block">Checked in</span>
            <span class="text-[11px] font-semibold text-[#B45309]">
                {{ \Carbon\Carbon::parse($todayRec['checkin_time'])->translatedFormat('H:i') }}
                @if(!empty($todayRec['lat']) && !empty($todayRec['lng']))
                &nbsp;· <ion-icon name="location" style="font-size:11px;"></ion-icon> GPS recorded
                @endif
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('nanny-attendance-checkout') }}" id="checkoutForm" enctype="multipart/form-data" onsubmit="return atSubmitCheckout(event)">
        @csrf
        <input type="hidden" name="checkout_time" id="checkoutTime" value="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}">
        <label class="text-[11px] font-bold text-[#8B86A5] block mb-1.5">Notes (optional)</label>
        <textarea name="notes" rows="2" placeholder="Check-out note…"
            class="w-full rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-sm text-[#1E1B2E] outline-none focus:border-[#8B46D3]"></textarea>
        <label class="text-[11px] font-bold text-[#8B86A5] block mb-1.5 mt-2">Photo <span class="text-[#DC2626] font-extrabold">*</span></label>
        <input type="file" id="coPhotoInput" name="checkout_photo" accept="image/*" capture="user" class="hidden" onchange="atShowPhoto('coPhotoInput','co','coPhotoPreview','coPhotoName')">
        <button type="button" onclick="document.getElementById('coPhotoInput').click()"
            class="w-full py-2.5 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5">
            <ion-icon name="camera-outline" style="font-size:15px;"></ion-icon>
            <span id="coPhotoName">Open Camera</span>
        </button>
        <img id="coPhotoPreview" class="hidden mt-1.5 w-full max-h-40 object-cover rounded-xl border border-[#DDD6EF]">
        <button type="submit"
            class="mt-3 w-full py-3 rounded-2xl bg-[#D97706] text-white text-[13px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.99] transition-transform">
            <ion-icon name="log-out-outline" style="font-size:16px;"></ion-icon>
            Check Out Now
        </button>
    </form>
    @elseif (!$checkedIn)
    {{-- Not checked in: GPS detect + photo + Check-in form --}}
    <form method="POST" action="{{ route('nanny-attendance-checkin') }}" id="checkinForm" enctype="multipart/form-data" onsubmit="return atSubmitCheckin(event)">
        @csrf
        <input type="hidden" name="checkin_time" id="checkinTime" value="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}">
        <input type="hidden" name="lat" id="checkinLat">
        <input type="hidden" name="lng" id="checkinLng">

        <label class="text-[11px] font-bold text-[#8B86A5] block mb-1.5">Location (GPS)</label>
        <div class="flex items-center gap-3">
            <button type="button" onclick="atGetLocation()" id="atLocationBtn"
                class="flex-1 py-2.5 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5">
                <ion-icon name="location-outline" style="font-size:15px;"></ion-icon>
                <span id="atLocationBtnText">Detect Location</span>
            </button>
        </div>
        <p id="atLocationInfo" class="hidden text-[11px] font-bold text-[#16A34A] mt-1.5">
            <ion-icon name="checkmark-circle" style="font-size:12px;"></ion-icon> Location detected & recorded
        </p>
        <p id="atLocationErr" class="hidden text-[11px] font-bold text-[#DC2626] mt-1.5">
            Location detection failed. Tap Detect to retry, or check in without GPS.
        </p>
        <p class="text-[10px] font-semibold text-[#8B86A5] mt-1">
            GPS is used as proof of location for this check-in.
        </p>

        <label class="text-[11px] font-bold text-[#8B86A5] block mb-1.5 mt-3">Notes (optional)</label>
        <textarea name="notes" rows="2" placeholder="Check-in note…"
            class="w-full rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-[13px] text-[#1E1B2E] outline-none focus:border-[#8B46D3]"></textarea>

        <label class="text-[11px] font-bold text-[#8B86A5] block mb-1.5 mt-2">Photo <span class="text-[#DC2626] font-extrabold">*</span></label>
        <input type="file" id="ciPhotoInput" name="location_photo" accept="image/*" capture="user" class="hidden" onchange="atShowPhoto('ciPhotoInput','ci','ciPhotoPreview','ciPhotoName')">
        <button type="button" onclick="document.getElementById('ciPhotoInput').click()"
            class="w-full py-2.5 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5">
            <ion-icon name="camera-outline" style="font-size:15px;"></ion-icon>
            <span id="ciPhotoName">Open Camera</span>
        </button>
        <img id="ciPhotoPreview" class="hidden mt-1.5 w-full max-h-40 object-cover rounded-xl border border-[#DDD6EF]">

        <button type="submit"
            class="mt-3 w-full py-3 rounded-2xl bg-[#8B46D3] text-white text-[13px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.99] transition-transform">
            <ion-icon name="log-in-outline" style="font-size:16px;"></ion-icon>
            Check In Now
        </button>
    </form>
    @endif
</div>
@endif