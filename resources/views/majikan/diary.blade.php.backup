{{-- resources/views/majikan/diary.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Diary Nanny</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { font-family: 'Nunito', sans-serif; background: #E5E2F5; margin: 0; }

        /* ── Phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0 60px; background:#E5E2F5; }
            .phone-frame  { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(124,58,237,.28),0 0 0 8px #1a1030,0 0 0 10px #2d1a50; overflow:hidden; position:relative; }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height:100vh; }
            .phone-frame  { min-height:100vh; }
        }

        /* ── Header ── */
        .header-bg {
            background: #8B46D3;
            position: relative;
        }
        .header-bg::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 10% 80%, rgba(255,255,255,0.06) 0%, transparent 40%),
                repeating-linear-gradient(45deg, transparent, transparent 18px, rgba(255,255,255,0.025) 18px, rgba(255,255,255,0.025) 19px);
            pointer-events: none;
        }

        /* ── Nanny avatar row ── */
        .nanny-scroll { display:flex; gap:14px; overflow-x:auto;}
        .nanny-scroll::-webkit-scrollbar { display:none; }
        .nanny-scroll { -ms-overflow-style:none; scrollbar-width:none; }
        .nanny-item { display:flex; flex-direction:column; align-items:center; gap:5px; flex-shrink:0; cursor:pointer; }
        .nanny-ring {
            width: 66px; height: 66px; border-radius: 50%;
            border: 2.5px solid rgba(255, 255, 255, 0.25);
            padding: 2px;
            transition: border-color .2s;
        }
        .nanny-item.active .nanny-ring { border-color: rgba(124, 58, 237, .28); }
        .nanny-avatar {
            width: 100%; height: 100%; border-radius: 50%;
            object-fit: cover; background: rgba(255,255,255,0.15);
            display: block;
        }
        .nanny-avatar-placeholder {
            width: 100%; height: 100%; border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
        }
        .nanny-name {
            font-size: 12px; font-weight: 700;
            color: rgba(255,255,255,0.6);
            max-width: 72px; text-align: center;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: color .2s;
        }
        .nanny-item.active .nanny-name { color: #fff; }

        /* ── Content card ── */
        .content-card {
            background: linear-gradient(180deg, #F8F7FF 0%, #F0EDFB 100%);
            border-radius: 32px 32px 0 0;
            margin-top: -28px;
            position: relative; z-index: 20;
            flex: 1; display: flex; flex-direction: column;
        }

        /* ── Calendar ── */
        .cal-wrap {
            background: #fff;
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 2px 14px rgba(139,70,211,.08);
        }
        .cal-month-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
        .cal-month-btn {
            font-size: 18px; font-weight: 900; color: #1E1B2E;
            background: none; border: none; cursor: pointer; padding: 0;
            font-family: 'Nunito', sans-serif;
        }
        .cal-month-btn:hover { color: #8B46D3; }
        .full-month-btn {
            font-size: 13px; font-weight: 800; color: #8B46D3;
            background: none; border: none; cursor: pointer; padding: 0;
            font-family: 'Nunito', sans-serif;
        }
        .weekdays { display:grid; grid-template-columns:repeat(7,1fr); text-align:center; margin-bottom:4px; }
        .weekday  { font-size:11px; font-weight:800; color: #A8A2C2; padding:2px 0; }
        .days-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
        .day-cell {
            display:flex; align-items:center; justify-content:center;
            height:34px; border-radius:10px;
            font-size:14px; font-weight:700; color:#1E1B2E;
            cursor:pointer; transition:background .12s;
            font-family: 'Nunito', sans-serif;
        }
        .day-cell:hover    { background:#EDE9FE; }
        .day-cell.today    { background:transparent; color:#1E1B2E; border: 1.5px solid #8B46D3;}
        .day-cell.selected { background:#8B46D3; color:#fff; }
        .day-cell.other-month { color:#C4B5FD; font-weight:600; }
        .day-cell.has-dot::after {
            content:''; width:4px; height:4px; border-radius:50%;
            background:#8B46D3; position:absolute; bottom:2px; left:50%; transform:translateX(-50%);
        }
        .day-cell { position:relative; }
        .day-cell.today.has-dot::after,
        .day-cell.selected.has-dot::after { background:#fff; }

        /* ── Timeline ── */
        .timeline-section { display:flex; flex-direction:column; gap:12px; }
        .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .section-title { font-size:17px; font-weight:900; color:#1E1B2E; }
        .section-date  { font-size:12px; font-weight:700; color:#A8A2C2; }

        .timeline-row { display:flex; gap:10px; }
        .tl-left { display:flex; flex-direction:column; align-items:center; width:56px; flex-shrink:0; }
        .tl-time { text-align:center; }
        .tl-time .hh { font-size:13px; font-weight:900; color:#1E1B2E; line-height:1.1; }
        .tl-time .ampm { font-size:10px; font-weight:700; color:#A8A2C2; line-height:1; }
        .tl-dot { width:13px; height:13px; border-radius:50%; margin:5px 0 2px; flex-shrink:0; }
        .tl-line { width:2px; flex:1; min-height:16px; background:linear-gradient(to bottom,rgba(139,70,211,.2),rgba(139,70,211,.04)); margin:0 auto; }
        .tl-right { flex:1; }

        /* ── Activity card ── */
        .akt-card {
            border-radius: 14px;
            padding: 14px;
            border: 1.5px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .akt-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
        .akt-cat { display:flex; align-items:center; gap:7px; }
        .akt-icon { font-size:18px; }
        .akt-title { font-size:15px; font-weight:900; color:#1E1B2E; }
        .akt-desc  { font-size:13px; font-weight:600; color:#5A556E; line-height:1.5; margin-bottom:8px; }
        .akt-photo { width:100%; height:110px; object-fit:cover; border-radius:10px; margin-bottom:8px; background:#F0EDFB; display:block; }
        .akt-footer { display:flex; align-items:center; justify-content:flex-end; }
        .loc-btn {
            display:flex; align-items:center; gap:4px;
            font-size:12px; font-weight:700; color:#A8A2C2;
            background:none; border:none; cursor:pointer;
            font-family:'Nunito',sans-serif;
            transition:color .15s;
        }
        .loc-btn:hover { color:#8B46D3; }
        .tl-duration { font-size:11px; font-weight:700; color:#A8A2C2; margin-top:6px; }

        /* ── Animations ── */
        @keyframes slideUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim  { animation:slideUp .4s ease forwards; opacity:0; }
        .d1    { animation-delay:.05s; }
        .d2    { animation-delay:.12s; }
        .d3    { animation-delay:.19s; }
        .d4    { animation-delay:.26s; }

        @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

        .hide-scrollbar::-webkit-scrollbar{display:none;}
        .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}

        /* ── Modals ── */
        .modal-overlay {
            position:fixed; inset:0; background:rgba(0,0,0,.45);
            display:flex; align-items:center; justify-content:center;
            padding:24px; z-index:60;
            opacity:0; pointer-events:none;
            transition:opacity .22s ease;
        }
        .modal-overlay.open { opacity:1; pointer-events:auto; }
        .modal-box {
            background:#fff; border-radius:22px;
            width:100%; max-width:340px;
            transform:translateY(18px) scale(0.97);
            transition:transform .25s cubic-bezier(.22,1,.36,1);
            box-shadow:0 24px 60px rgba(139,70,211,.22);
        }
        .modal-overlay.open .modal-box { transform:translateY(0) scale(1); }

        /* Month/Year modal selects */
        .my-select {
            width:100%; padding:10px 14px; border-radius:10px;
            border:1.5px solid #EDE9FE; background:#F8F7FF;
            font-family:'Nunito',sans-serif; font-size:14px; font-weight:700; color:#1E1B2E;
            appearance:none; -webkit-appearance:none;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%238B46D3' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat:no-repeat; background-position:right 12px center;
            cursor:pointer;
        }
        .my-select:focus { outline:none; border-color:#8B46D3; box-shadow:0 0 0 3px rgba(139,70,211,.12); }

        /* Location modal map placeholder */
        .map-placeholder {
            width:100%; height:180px; border-radius:14px;
            background: linear-gradient(135deg,#e8f4e8 0%,#d4ead4 50%,#c8e0c8 100%);
            overflow:hidden; position:relative;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; color:#666; font-weight:600;
        }
        /* Fake map grid lines */
        .map-placeholder::before {
            content:'';
            position:absolute; inset:0;
            background-image:
                linear-gradient(rgba(0,0,0,.08) 1px,transparent 1px),
                linear-gradient(90deg,rgba(0,0,0,.08) 1px,transparent 1px),
                linear-gradient(rgba(0,0,0,.04) 1px,transparent 1px),
                linear-gradient(90deg,rgba(0,0,0,.04) 1px,transparent 1px);
            background-size:40px 40px,40px 40px,10px 10px,10px 10px;
        }
        .map-road-h { position:absolute; background:rgba(255,255,255,.7); border-radius:3px; }
        .map-road-v { position:absolute; background:rgba(255,255,255,.7); border-radius:3px; }

        /* ── Image Popup Modal ─────────────────────────────────────────────────── */
        .image-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            backdrop-filter: blur(8px);
        }

        .image-modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .image-modal-box {
            max-width: 90vw;
            max-height: 90vh;
            background: transparent;
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.34, 1.2, 0.64, 1);
            position: relative;
        }

        .image-modal-overlay.open .image-modal-box {
            transform: scale(1);
        }

        /* Frame pigura sesuai tema (purple theme) */
        .image-frame {
            position: relative;
            background: linear-gradient(135deg, #fff 0%, #f5f0ff 100%);
            padding: 20px;
            border-radius: 32px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        /* Inner frame (dekoratif) */
        .image-frame-inner {
            position: relative;
            border: 8px solid #8B46D3;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.3), 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        /* Dekorasi sudut pigura */
        .image-frame::before,
        .image-frame::after,
        .image-frame-inner::before,
        .image-frame-inner::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            pointer-events: none;
        }

        .image-frame::before {
            top: 12px;
            left: 12px;
            border-top: 4px solid #D4BAEF;
            border-left: 4px solid #D4BAEF;
            border-radius: 12px 0 0 0;
        }

        .image-frame::after {
            top: 12px;
            right: 12px;
            border-top: 4px solid #D4BAEF;
            border-right: 4px solid #D4BAEF;
            border-radius: 0 12px 0 0;
        }

        .image-frame-inner::before {
            bottom: 12px;
            left: 12px;
            border-bottom: 4px solid #D4BAEF;
            border-left: 4px solid #D4BAEF;
            border-radius: 0 0 0 12px;
        }

        .image-frame-inner::after {
            bottom: 12px;
            right: 12px;
            border-bottom: 4px solid #D4BAEF;
            border-right: 4px solid #D4BAEF;
            border-radius: 0 0 12px 0;
        }

        /* Gambar di dalam frame */
        .image-modal-img {
            display: block;
            max-width: 100%;
            max-height: calc(90vh - 80px);
            width: auto;
            height: auto;
            object-fit: contain;
            background: #1E1B2E;
            cursor: pointer;
        }

        /* Tombol close */
        .image-modal-close {
            position: absolute;
            top: -40px;
            right: -40px;
            width: 40px;
            height: 40px;
            background: #8B46D3;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 10;
        }

        .image-modal-close:hover {
            background: #6a2fb0;
            transform: scale(1.05);
        }
        /* Efek hover untuk gambar yang bisa diklik */
        .akt-photo {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .akt-photo:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(139, 70, 211, 0.2);
        }

        /* Responsive untuk mobile */
        @media (max-width: 639px) {
            .image-modal-close {
                top: -35px;
                right: -10px;
                width: 36px;
                height: 36px;
                font-size: 20px;
            }
            
            .image-frame {
                padding: 12px;
            }
            
            .image-frame-inner {
                border-width: 4px;
            }
        }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-[#F0EDFB] flex flex-col" style="max-height:100vh;">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between bg-[#8B46D3] px-6 pt-[14px] pb-1 text-white text-xs font-bold flex-shrink-0">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <!-- back button -->
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('dashboard') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Diary Nanny</span>
            </div>
        </div>
    </div>

    <!-- CONTENT CARD (white rounded top) -->
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

        <!-- nanny avatars -->
        <div style="margin-top:16px;">
            <!-- nanny avatars (moved) -->
            <div class="nanny-scroll" id="nannyScroll">
                @forelse($anakList ?? [] as $i => $anak)
                <div class="nanny-item {{ ($idAnak ?? null) == $anak['id'] ? 'active' : '' }}"
                    onclick="selectAnak({{ $anak['id'] }}, this)"
                    data-id="{{ $anak['id'] }}">
                    <div class="nanny-ring">
                        @if(!empty($anak['foto']))
                        <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}" class="nanny-avatar"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="nanny-avatar-placeholder" style="display:none;">
                            <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                        </div>
                        @else
                        <div class="nanny-avatar-placeholder">
                            <ion-icon name="person" style="font-size:26px;color:#8B46D3;"></ion-icon>
                        </div>
                        @endif
                    </div>
                    <span class="nanny-name" style="color:#1E1B2E;">
                        {{ $anak['nama'] }}
                    </span>
                </div>
                @empty
                <div style="padding:0 20px;color:#A8A2C2;font-size:13px;font-weight:700;">
                    Belum ada data anak
                </div>
                @endforelse
            </div>
        </div>

        <!-- CALENDAR -->
        <div class="cal-wrap anim d2">
            <div class="cal-month-row">
                <button class="cal-month-btn" id="btnMonthYear">{{ \Carbon\Carbon::parse($tanggal ?? now())->format('F Y') }}</button>
                <button class="full-month-btn" id="btnFullMonth">Full month</button>
            </div>
            <div class="weekdays">
                @foreach(['SUN','MON','TUE','WED','THU','FRI','SAT'] as $wd)
                <div class="weekday">{{ $wd }}</div>
                @endforeach
            </div>
            <div class="days-grid" id="calGrid"></div>
        </div>

        <!-- TIMELINE HEADER -->
        <div class="anim d3" style="padding:20px 0px 12px;">
            <div class="section-header">
                <span class="section-title">Today's Timeline</span>
                <span class="section-date" id="timelineDateLabel">
                    {{ \Carbon\Carbon::parse($tanggal ?? now())->locale('en')->isoFormat('dddd, MMMM D') }}
                </span>
            </div>
        </div>

        <!-- TIMELINE ITEMS -->
        <div class="timeline-section anim d4" id="timelineContainer">
            @php
                $katColors = [
                    'makan'   => ['bg'=>'#FFF4EC','border'=>'#FF9A6C','dot'=>'#FF9A6C','icon'=>'🍽️'],
                    'tidur'   => ['bg'=>'#EEF4FF','border'=>'#7BB4F0','dot'=>'#7BB4F0','icon'=>'🌙'],
                    'main'    => ['bg'=>'#FFFBEE','border'=>'#FFD93D','dot'=>'#FFD93D','icon'=>'⚽'],
                    'belajar' => ['bg'=>'#F0F2FF','border'=>'#9BB8FF','dot'=>'#9BB8FF','icon'=>'📖'],
                    'mandi'   => ['bg'=>'#FFF0F7','border'=>'#FFB4D6','dot'=>'#FFB4D6','icon'=>'🛁'],
                ];
                $katIcons = ['makan'=>'restaurant','tidur'=>'moon','main'=>'football','belajar'=>'book','mandi'=>'water'];
            @endphp

            @if(isset($aktivitas) && count($aktivitas) > 0)
                @foreach($aktivitas as $i => $item)
                @php
                    $kat    = $item['kategori'] ?? 'main';
                    $c      = $katColors[$kat] ?? ['bg'=>'#F0EDFB','border'=>'#C4B5FD','dot'=>'#8B46D3','icon'=>'📅'];
                    $isLast = $i === count($aktivitas) - 1;
                    $parts  = explode(':', $item['jam_mulai_fmt'] ?? '08:00');
                    $hh     = $parts[0] ?? '08'; $mm = $parts[1] ?? '00';
                    $ampm   = (int)$hh >= 12 ? 'PM' : 'AM';
                    $h12    = (int)$hh > 12 ? $hh - 12 : ((int)$hh === 0 ? 12 : (int)$hh);
                    $timeFmt = sprintf('%02d:%s', $h12, $mm);

                    $partsE  = explode(':', $item['jam_selesai_fmt'] ?? '09:30');
                    $hhE     = $partsE[0] ?? '09'; $mmE = $partsE[1] ?? '30';
                    $ampmE   = (int)$hhE >= 12 ? 'PM' : 'AM';
                    $h12E    = (int)$hhE > 12 ? $hhE - 12 : ((int)$hhE === 0 ? 12 : (int)$hhE);
                    $timeFmtE = sprintf('%02d:%s', $h12E, $mmE);
                @endphp
                <div class="timeline-row" style="animation:slideUp .3s ease {{ $i*.06 }}s both;opacity:0;">
                    <!-- left: time + dot + line -->
                    <div class="tl-left">
                        <div class="tl-time">
                            <div class="hh">{{ $timeFmt }}</div>
                            <div class="ampm">{{ $ampm }}</div>
                        </div>
                        <div class="tl-dot" style="background:{{ $c['dot'] }};"></div>
                        @if(!$isLast)<div class="tl-line"></div>@endif
                        <div class="tl-time" style="{{ $isLast ? '' : 'margin-top:auto;' }}">
                            <div class="hh">{{ $timeFmtE }}</div>
                            <div class="ampm">{{ $ampmE }}</div>
                        </div>
                    </div>
                    <!-- right: card -->
                    <div class="tl-right">
                        <div class="akt-card" style="background:{{ $c['bg'] }};border-color:{{ $c['border'] }}40;">
                            <!-- colored left strip -->
                            <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:{{ $c['border'] }};border-radius:4px 0 0 4px;"></div>
                            <div class="akt-card-header">
                                <div class="akt-cat">
                                    <span style="font-size:18px;">{{ $c['icon'] }}</span>
                                    <span class="akt-title">{{ ucfirst($kat) }}</span>
                                </div>
                                @if(!empty($item['mood']))
                                <span style="font-size:20px;">{{ ['senang'=>'😊','sedih'=>'😢','marah'=>'😠','biasa'=>'😐'][$item['mood']] ?? '😊' }}</span>
                                @endif
                            </div>
                            @if(!empty($item['deskripsi']))
                            <p class="akt-desc">{{ $item['deskripsi'] }}</p>
                            @endif
                            @if(!empty($item['foto_url']))
                            <img src="{{ $item['foto_url'] }}" class="akt-photo" alt=""
                                onclick="openImageModal('{{ $item['foto_url'] }}')"
                                style="cursor: pointer;">
                            @endif
                            <div class="akt-footer">
                                <button class="loc-btn"
                                    onclick="openLocationModal(
                                        '{{ addslashes($item['lokasi'] ?? '') }}', 
                                        '{{ addslashes($item['lat'] ?? '') }}', 
                                        '{{ addslashes($item['lng'] ?? '') }}'
                                    )">
                                    <ion-icon name="location-outline" style="font-size:14px;"></ion-icon>
                                    Location
                                </button>
                            </div>
                        </div>
                        <p class="tl-duration">Total Duration : {{ $item['durasi_fmt'] ?? '-' }}</p>
                    </div>
                </div>
                @endforeach
            @else
            <div class="flex flex-col items-center justify-center" style="padding:50px 20px 80px;">
                <div class="float-anim flex items-center justify-center"
                     style="width:100px;height:100px;border-radius:50%;background:#EDE9FE;margin-bottom:18px;">
                    <ion-icon name="calendar-clear-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon>
                </div>
                <p style="font-size:16px;font-weight:900;color:#1E1B2E;margin-bottom:6px;">Tidak ada aktivitas</p>
                <p style="font-size:13px;font-weight:700;color:#A8A2C2;">pada tanggal ini</p>
            </div>
            @endif
        </div>

    </div><!-- end content-card -->

    @include('partials.bottom-nav', ['active'=>'home'])
</div>
</div>

<!-- ── MODAL: Set Month & Year ─────────────────────────────────────────── -->
<div class="modal-overlay" id="modalMonthYear">
    <div class="modal-box" style="padding:24px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <span class="iconify" data-icon="ant-design:setting-filled" style="font-size:20px; color:#8B46D3;"></span>
            
            <span style="font-size:17px; font-weight:800; color:#1E1B2E;">Set Month And Year</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
            <div>
                <p style="font-size:13px;font-weight:800;color:#5A556E;margin-bottom:8px;">Select Month</p>
                <select id="myMonthSel" class="my-select">
                    <option value="0">January</option><option value="1">February</option>
                    <option value="2">March</option><option value="3">April</option>
                    <option value="4">May</option><option value="5">June</option>
                    <option value="6">July</option><option value="7">August</option>
                    <option value="8">September</option><option value="9">October</option>
                    <option value="10">November</option><option value="11">December</option>
                </select>
            </div>
            <div>
                <p style="font-size:13px;font-weight:800;color:#5A556E;margin-bottom:8px;">Select Year</p>
                <select id="myYearSel" class="my-select" id="myYearSel"></select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <button onclick="closeMonthYearModal()"
                style="flex:1; padding:10px 20px; border-radius:13px; border:2px solid #FFCACA; background:#FFF9F9;
                    font-family:'Nunito', sans-serif; font-size:14px; font-weight:800; color:#FF0000; cursor:pointer;
                    display:flex; align-items:center; justify-content:center; gap:8px;
                    transition: all 0.2s ease;">
                <span class="iconify" data-icon="carbon:close-filled" style="font-size:18px;"></span>
                Cancel
            </button>

            <button onclick="applyMonthYear()"
                style="flex:1; padding:10px 20px; border-radius:13px; border:none; background:#8B46D3;
                    font-family:'Nunito', sans-serif; font-size:14px; font-weight:800; color:#fff; cursor:pointer;
                    display:flex; align-items:center; justify-content:center; gap:8px;
                    box-shadow: 0 4px 12px rgba(139, 70, 211, 0.3);
                    transition: all 0.2s ease;">
                <span class="iconify" data-icon="lets-icons:check-fill" style="font-size:18px;"></span>
                Apply
            </button>
        </div>
    </div>
</div>

<!-- ── MODAL: Location ────────────────────────────────────────────────── -->
<div class="modal-overlay" id="modalLocation">
    <div class="modal-box" style="padding:0;overflow:hidden;">
        <!-- Map area -->
        <div class="map-placeholder" id="mapPlaceholder"></div>
        <!-- Location info -->
        <div style="padding:16px 18px 18px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1.5px solid #F0EDFB;">
                <div style="width:42px;height:42px;border-radius:12px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <ion-icon name="location" style="font-size:20px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p id="locModalName" style="font-size:15px;font-weight:900;color:#1E1B2E;margin-bottom:2px;">Nanny & Child: At Park</p>
                    <p id="locModalSub"  style="font-size:12px;font-weight:700;color:#A8A2C2;">200m away • Update 2m ago</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <button onclick="closeLocationModal()"
                    style="padding:13px;border-radius:12px;border:2px solid #EDE9FE;background:#fff;
                           font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#5A556E;cursor:pointer;">
                    Close
                </button>
                <button onclick="openInGMaps()"
                    style="padding:13px;border-radius:12px;border:none;background:#8B46D3;
                           font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#fff;cursor:pointer;
                           box-shadow:0 6px 18px rgba(139,70,211,.35);">
                    Open in GMaps
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL: Image Popup (Full Screen with Frame) ────────────────────────────────── -->
<div class="image-modal-overlay" id="imageModal">
    <div class="image-modal-box">
        <button class="image-modal-close" onclick="closeImageModal()">&times;</button>
        <div class="image-frame">
            <div class="image-frame-inner">
                <img id="modalImage" class="image-modal-img" alt="Full size image">
            </div>
        </div>
    </div>
</div>

<script>
// ── State ────────────────────────────────────────────────────────────────────
const MONTHS_EN = ['January','February','March','April','May','June',
                   'July','August','September','October','November','December'];

let currentAnakId = {{ $idAnak ?? 'null' }};
let currentKat    = "{{ $activeKat ?? '' }}";
let currentDate   = new Date("{{ $tanggal ?? date('Y-m-d') }}");
let calViewDate   = new Date(currentDate);
let isFullMonth   = false;
let locLat = null, locLng = null;

// ── Clock ────────────────────────────────────────────────────────────────────
function pad(n){ return String(n).padStart(2,'0'); }
function updateClock(){
    const n=new Date(), e=document.getElementById('statusTime');
    if(e) e.textContent=pad(n.getHours())+':'+pad(n.getMinutes());
}
updateClock(); setInterval(updateClock,30000);

// ── Reload ───────────────────────────────────────────────────────────────────
function fmtDate(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }

function reload(){
    if(!currentAnakId) return;
    const p=new URLSearchParams({tanggal:fmtDate(currentDate)});
    if(currentKat) p.set('kategori',currentKat);
    window.location.href='{{ url("/majikan/diary") }}/'+currentAnakId+'?'+p.toString();
}

// ── Anak selector ────────────────────────────────────────────────────────────
function selectAnak(id,el){
    document.querySelectorAll('.nanny-item').forEach(x=>x.classList.remove('active'));
    el.classList.add('active');
    currentAnakId=id;
    reload();
}

// ── Calendar ─────────────────────────────────────────────────────────────────
function daysInMonth(y,m){ return new Date(y,m+1,0).getDate(); }

function buildCalendar(){
    const y=calViewDate.getFullYear(), m=calViewDate.getMonth();
    document.getElementById('btnMonthYear').textContent=MONTHS_EN[m]+' '+y;

    const grid=document.getElementById('calGrid');
    grid.innerHTML='';
    const today=new Date();

    if(isFullMonth){
        const first=new Date(y,m,1).getDay();
        const days=daysInMonth(y,m);
        const prevDays=new Date(y,m,0).getDate();
        // prev month tail
        for(let i=first-1;i>=0;i--){
            const cell=document.createElement('div');
            cell.className='day-cell other-month';
            cell.textContent=prevDays-i;
            grid.appendChild(cell);
        }
        // current month
        for(let d=1;d<=days;d++){
            const cell=makeCell(d,y,m,today);
            grid.appendChild(cell);
        }
        // fill remaining
        const total=grid.children.length;
        const rem=total%7===0?0:7-(total%7);
        for(let d=1;d<=rem;d++){
            const cell=document.createElement('div');
            cell.className='day-cell other-month';
            cell.textContent=d;
            grid.appendChild(cell);
        }
    } else {
        // 7-day strip: Mon–Sun of the week containing currentDate
        const sel=new Date(currentDate);
        const dow=sel.getDay(); // 0=Sun
        // start from Sunday of this week
        const weekStart=new Date(sel);
        weekStart.setDate(sel.getDate()-dow);
        for(let i=0;i<7;i++){
            const dt=new Date(weekStart);
            dt.setDate(weekStart.getDate()+i);
            const cell=makeCell(dt.getDate(),dt.getFullYear(),dt.getMonth(),today);
            if(dt.getMonth()!==m) cell.classList.add('other-month');
            grid.appendChild(cell);
        }
    }
}

function makeCell(d,y,m,today){
    const cell=document.createElement('div');
    cell.className='day-cell';
    cell.textContent=d;
    const isToday=d===today.getDate()&&m===today.getMonth()&&y===today.getFullYear();
    const isSel  =d===currentDate.getDate()&&m===currentDate.getMonth()&&y===currentDate.getFullYear();
    if(isSel)        cell.classList.add('selected');
    else if(isToday) cell.classList.add('today');
    cell.addEventListener('click',()=>{
        currentDate=new Date(y,m,d);
        updateTimelineLabel();
        buildCalendar();
        reload();
    });
    return cell;
}

function updateTimelineLabel(){
    const el=document.getElementById('timelineDateLabel');
    if(!el) return;
    el.textContent=currentDate.toLocaleDateString('en-US',{weekday:'long',month:'long',day:'numeric'});
}

// Full month toggle
document.getElementById('btnFullMonth').addEventListener('click',()=>{
    isFullMonth=!isFullMonth;
    document.getElementById('btnFullMonth').textContent=isFullMonth?'Week view':'Full month';
    buildCalendar();
});

buildCalendar();

// ── Month/Year Modal ──────────────────────────────────────────────────────────
(function(){
    const yearSel=document.getElementById('myYearSel');
    const curY=new Date().getFullYear();
    for(let y=curY-5;y<=curY+2;y++){
        const o=document.createElement('option');
        o.value=y; o.textContent=y;
        yearSel.appendChild(o);
    }
})();

function openMonthYearModal(){
    const y=calViewDate.getFullYear(), m=calViewDate.getMonth();
    document.getElementById('myMonthSel').value=m;
    document.getElementById('myYearSel').value=y;
    document.getElementById('modalMonthYear').classList.add('open');
}
function closeMonthYearModal(){
    document.getElementById('modalMonthYear').classList.remove('open');
}
function applyMonthYear(){
    const m=parseInt(document.getElementById('myMonthSel').value);
    const y=parseInt(document.getElementById('myYearSel').value);
    calViewDate=new Date(y,m,1);
    // also update currentDate to 1st of new month
    currentDate=new Date(y,m,currentDate.getDate()>daysInMonth(y,m)?daysInMonth(y,m):currentDate.getDate());
    closeMonthYearModal();
    buildCalendar();
    updateTimelineLabel();
    reload();
}

document.getElementById('btnMonthYear').addEventListener('click',openMonthYearModal);
document.getElementById('modalMonthYear').addEventListener('click',e=>{
    if(e.target.id==='modalMonthYear') closeMonthYearModal();
});

// ── Location Modal ────────────────────────────────────────────────────────────
function openLocationModal(name, lat, lng) {
    locLat = lat; locLng = lng;
    const n = name || 'Lokasi Aktivitas';
    document.getElementById('locModalName').textContent = n;
    document.getElementById('locModalSub').textContent = lat && lng
        ? lat + ', ' + lng
        : 'Lokasi tercatat';

    // Konversi ke desimal
    const latDec = dmsToDecimal(lat);
    const lngDec = dmsToDecimal(lng);

    const mapContainer = document.getElementById('mapPlaceholder');

    if (latDec !== null && lngDec !== null) {
        // Embed Google Maps iframe sungguhan
        mapContainer.innerHTML = `
            <iframe
                width="100%"
                height="180"
                style="border:0;border-radius:14px;display:block;"
                loading="lazy"
                allowfullscreen
                src="https://maps.google.com/maps?q=${latDec},${lngDec}&z=16&output=embed">
            </iframe>`;
    } else {
        // Fallback: placeholder jika koordinat tidak valid
        mapContainer.innerHTML = `
            <div style="width:100%;height:180px;border-radius:14px;background:#e8f4e8;
                        display:flex;align-items:center;justify-content:center;
                        font-size:13px;color:#666;font-weight:600;">
                Koordinat tidak tersedia
            </div>`;
    }

    document.getElementById('modalLocation').classList.add('open');
}
function closeLocationModal(){
    document.getElementById('modalLocation').classList.remove('open');
}
function dmsToDecimal(dms) {
    // Sudah desimal? langsung return
    if (!isNaN(parseFloat(dms)) && dms.indexOf('°') === -1) {
        return parseFloat(dms);
    }
    // Parse DMS: 7°17'10.9"S atau 7 17 10.9 S
    const regex = /(\d+)[°\s]+(\d+)['\s]+(\d+\.?\d*)["″\s]*([NSEW])?/i;
    const m = dms.match(regex);
    if (!m) return null;
    let dd = parseFloat(m[1]) + parseFloat(m[2]) / 60 + parseFloat(m[3]) / 3600;
    if (m[4] && 'SW'.includes(m[4].toUpperCase())) dd = -dd;
    return dd;
}

function openInGMaps() {
    if (locLat && locLng) {
        const lat = dmsToDecimal(locLat);
        const lng = dmsToDecimal(locLng);
        if (lat !== null && lng !== null) {
            window.open('https://www.google.com/maps?q=' + lat + ',' + lng, '_blank');
        } else {
            window.open('https://maps.google.com', '_blank');
        }
    } else {
        window.open('https://maps.google.com', '_blank');
    }
}
document.getElementById('modalLocation').addEventListener('click',e=>{
    if(e.target.id==='modalLocation') closeLocationModal();
});

// ── Image Popup Modal Functions ─────────────────────────────────────────────────────
function openImageModal(imageUrl) {
    if (!imageUrl) return;
    
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    // Set gambar ke modal
    modalImg.src = imageUrl;
    
    // Buka modal
    modal.classList.add('open');
    
    // Mencegah scroll di background
    document.body.style.overflow = 'hidden';
    
    // Optional: tambahkan event untuk close dengan klik di overlay
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeImageModal();
        }
    });
    
    // Optional: close dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
            closeImageModal();
        }
    });
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
</script>
@include('partials.auth-guard')
</body>
</html>