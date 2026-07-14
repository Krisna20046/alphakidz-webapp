@extends('layouts.app')

@section('title', 'Diary Anak')

@push('styles')
<style>
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

    .filter-chip {
        cursor:pointer; transition:background .15s,color .15s;
        white-space:nowrap; flex-shrink:0;
        padding:7px 14px; border-radius:20px;
        font-size:13px; font-weight:700;
        font-family:'Nunito',sans-serif;
    }
    .filter-chip:active { transform:scale(0.95); }

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

    .akt-card {
        border-radius: 14px;
        padding: 14px;
        border: 1.5px solid transparent;
        position: relative;
        overflow: hidden;
        transition: opacity .15s, transform .15s;
        cursor: pointer;
    }
    .akt-card:hover  { opacity:.9; }
    .akt-card:active { transform:scale(0.98); }
    .akt-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .akt-cat { display:flex; align-items:center; gap:7px; }
    .akt-icon { font-size:18px; }
    .akt-title { font-size:15px; font-weight:900; color:#1E1B2E; }
    .akt-desc  { font-size:13px; font-weight:600; color:#5A556E; line-height:1.5; margin-bottom:8px; }
    .akt-photo { width:100%; height:110px; object-fit:cover; border-radius:10px; margin-bottom:8px; background:#F0EDFB; display:block;
        cursor:pointer; transition:transform 0.2s ease, box-shadow 0.2s ease; }
    .akt-photo:hover { transform:scale(1.02); box-shadow:0 8px 20px rgba(139,70,211,0.2); }
    .tl-duration { font-size:11px; font-weight:700; color:#A8A2C2; margin-top:6px; }
    .akt-footer { display:flex; align-items:center; justify-content:flex-end; margin-top:6px; }
    .loc-btn {
        display:flex; align-items:center; gap:4px;
        font-size:12px; font-weight:700; color:#A8A2C2;
        background:none; border:none; cursor:pointer;
        transition:color .15s; padding:0;
    }
    .loc-btn:hover { color:#8B46D3; }
    .map-placeholder { width:100%; height:180px; border-radius:14px; overflow:hidden; position:relative; }

    .fab {
        position: fixed;
        right: 20px;
        bottom: 90px;
        width: 56px; height: 56px;
        border-radius: 28px;
        background: linear-gradient(135deg, #A855F7 0%, #8B46D3 100%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(139,70,211,.45);
        cursor: pointer; border: none; z-index: 40;
        transition: transform .15s, box-shadow .15s;
        text-decoration: none;  
    }
    .fab:hover { transform:scale(1.07); box-shadow:0 12px 32px rgba(139,70,211,.55); }
    .fab:active { transform:scale(0.95); }
    @keyframes fabIn{0%{transform:scale(0) rotate(-20deg);opacity:0}70%{transform:scale(1.1) rotate(5deg)}100%{transform:scale(1) rotate(0);opacity:1}}
    .fab { animation:fabIn .5s cubic-bezier(.34,1.56,.64,1) .3s both; }

    @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

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

    .modal-box-tall {
        max-height: 82vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

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

    .image-modal-overlay {
        position:fixed; inset:0; background:rgba(0,0,0,.85);
        display:flex; align-items:center; justify-content:center;
        padding:24px; z-index:100;
        opacity:0; pointer-events:none;
        transition:opacity .25s ease;
        backdrop-filter:blur(8px);
    }
    .image-modal-overlay.open { opacity:1; pointer-events:auto; }
    .image-modal-box {
        max-width:90vw; max-height:90vh; background:transparent;
        transform:scale(0.95);
        transition:transform .3s cubic-bezier(.34,1.2,.64,1);
        position:relative;
    }
    .image-modal-overlay.open .image-modal-box { transform:scale(1); }
    .image-frame {
        position:relative; background:linear-gradient(135deg,#fff 0%,#f5f0ff 100%);
        padding:20px; border-radius:32px;
        box-shadow:0 25px 50px -12px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.5);
    }
    .image-frame-inner {
        position:relative; border:8px solid #8B46D3; border-radius:20px;
        overflow:hidden; box-shadow:inset 0 0 0 2px rgba(255,255,255,.3),0 4px 20px rgba(0,0,0,.2);
    }
    .image-frame::before,.image-frame::after,.image-frame-inner::before,.image-frame-inner::after {
        content:''; position:absolute; width:30px; height:30px; pointer-events:none;
    }
    .image-frame::before { top:12px; left:12px; border-top:4px solid #D4BAEF; border-left:4px solid #D4BAEF; border-radius:12px 0 0 0; }
    .image-frame::after  { top:12px; right:12px; border-top:4px solid #D4BAEF; border-right:4px solid #D4BAEF; border-radius:0 12px 0 0; }
    .image-frame-inner::before { bottom:12px; left:12px; border-bottom:4px solid #D4BAEF; border-left:4px solid #D4BAEF; border-radius:0 0 0 12px; }
    .image-frame-inner::after  { bottom:12px; right:12px; border-bottom:4px solid #D4BAEF; border-right:4px solid #D4BAEF; border-radius:0 0 12px 0; }
    .image-modal-img {
        display:block; max-width:100%; max-height:calc(90vh - 80px);
        width:auto; height:auto; object-fit:contain; background:#1E1B2E; cursor:pointer;
    }
    .image-modal-close {
        position:absolute; top:-40px; right:-40px; width:40px; height:40px;
        background:#8B46D3; border:none; border-radius:50%; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        color:white; font-size:24px; font-weight:bold;
        transition:all .2s ease; box-shadow:0 4px 12px rgba(0,0,0,.3); z-index:10;
    }
    .image-modal-close:hover { background:#6a2fb0; transform:scale(1.05); }

    @media (max-width:639px) {
        .image-modal-close { top:-35px; right:-10px; width:36px; height:36px; font-size:20px; }
        .image-frame { padding:12px; }
        .image-frame-inner { border-width:4px; }
    }
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
            <span class="text-white text-[17px] font-extrabold tracking-wide">Diary Anak</span>
            @if(!empty($diaryData['nama_anak']))
            <p style="font-size:13px;color:rgba(255,255,255,0.7);font-weight:600;margin-top:1px;">{{ $diaryData['nama_anak'] }}</p>
            @endif
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Child avatars --}}
    <div style="margin-top:16px;">
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

    {{-- Filter kategori --}}
    <div class="anim d2">
        <div class="hide-scrollbar flex" style="overflow-x:auto;gap:8px;padding-bottom:2px;">
            @php
                $kats = [
                    ['value'=>'','label'=>'Semua'],
                    ['value'=>'makan','label'=>'🍽️ Makan'],
                    ['value'=>'tidur','label'=>'🌙 Tidur'],
                    ['value'=>'main','label'=>'⚽ Main'],
                    ['value'=>'belajar','label'=>'📖 Belajar'],
                    ['value'=>'mandi','label'=>'🛁 Mandi'],
                    ['value'=>'bab','label'=>'🟤 BAB'],
                    ['value'=>'bak','label'=>'💧 BAK'],
                ];
            @endphp
            @foreach($kats as $kat)
            @php $isActive = $activeKat === $kat['value']; @endphp
            <a href="{{ route('nanny-diary', array_merge(['id_anak'=>$idAnak??0], $kat['value']?['kategori'=>$kat['value']]:[], ['tanggal'=>$tanggal??date('Y-m-d')])) }}"
               class="filter-chip"
               style="border:2px solid {{ $isActive?'#8B46D3':'#EDE9FE' }};background:{{ $isActive?'#8B46D3':'#fff' }};color:{{ $isActive?'#fff':'#5A556E' }};">
                {{ $kat['label'] }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Calendar --}}
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

    {{-- Timeline header --}}
    <div class="anim d3" style="padding:20px 0px 12px;">
        <div class="section-header">
            <span class="section-title">Today's Timeline</span>
            <span class="section-date" id="timelineDateLabel">
                {{ \Carbon\Carbon::parse($tanggal ?? now())->locale('en')->isoFormat('dddd, MMMM D') }}
            </span>
        </div>
    </div>

    {{-- Timeline items --}}
    <div class="timeline-section anim d4" id="timelineContainer">
        @php
            $katColors = [
                'makan'   => ['bg'=>'#FFF4EC','border'=>'#FF9A6C','dot'=>'#FF9A6C','icon'=>'🍽️'],
                'tidur'   => ['bg'=>'#EEF4FF','border'=>'#7BB4F0','dot'=>'#7BB4F0','icon'=>'🌙'],
                'main'    => ['bg'=>'#FFFBEE','border'=>'#FFD93D','dot'=>'#FFD93D','icon'=>'⚽'],
                'belajar' => ['bg'=>'#F0F2FF','border'=>'#9BB8FF','dot'=>'#9BB8FF','icon'=>'📖'],
                'mandi'   => ['bg'=>'#FFF0F7','border'=>'#FFB4D6','dot'=>'#FFB4D6','icon'=>'🛁'],
                'bab'     => ['bg'=>'#EFEBE9','border'=>'#8D6E63','dot'=>'#5D4037','icon'=>'🟤'],
                'bak'     => ['bg'=>'#FFF8E1','border'=>'#F9A825','dot'=>'#F9A825','icon'=>'💧'],
            ];
            $warnaColors = [
                'coklat'=>'#6D4C41','hijau'=>'#2E7D32','kuning'=>'#F9A825',
                'hitam'=>'#212121','merah'=>'#C62828','jernih'=>'#CFD8DC','keruh'=>'#8D6E63',
            ];
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
                <div class="tl-right">
                    <div class="akt-card" style="background:{{ $c['bg'] }};border-color:{{ $c['border'] }}40;"
                         onclick='openDetail(@json($item))'>
                        <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:{{ $c['border'] }};border-radius:4px 0 0 4px;"></div>
                        <div class="akt-card-header">
                            <div class="akt-cat">
                                <span style="font-size:18px;">{{ $c['icon'] }}</span>
                                <span class="akt-title">{{ ucfirst($kat) }}</span>
                            </div>
                            @if(!empty($item['mood']) && !in_array($kat, ['bab','bak']))
                            <span style="font-size:20px;">{{ ['senang'=>'😊','sedih'=>'😢','marah'=>'😠','biasa'=>'😐'][$item['mood']] ?? '😊' }}</span>
                            @endif
                        </div>
                        @if(!empty($item['deskripsi']))
                        <p class="akt-desc">{{ $item['deskripsi'] }}</p>
                        @endif
                        @if(in_array($kat, ['bab','bak']))
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px;">
                            @if(!empty($item['warna']))
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;background:#fff;font-size:11px;font-weight:700;color:#5A556E;">
                                <span class="tl-dot" style="width:10px;height:10px;margin:0;background:{{ $warnaColors[$item['warna']] ?? '#999' }};"></span>
                                {{ ucfirst($item['warna']) }}
                            </span>
                            @endif
                            @if(!empty($item['tekstur']))
                            <span style="padding:3px 10px;border-radius:12px;background:#fff;font-size:11px;font-weight:700;color:#5A556E;">
                                {{ ucfirst($item['tekstur']) }}
                            </span>
                            @endif
                            @if(!empty($item['volume']))
                            <span style="padding:3px 10px;border-radius:12px;background:#fff;font-size:11px;font-weight:700;color:#5A556E;">
                                Vol: {{ ucfirst($item['volume']) }}
                            </span>
                            @endif
                            @if(!empty($item['frekuensi']) && $item['frekuensi'] > 0)
                            <span style="padding:3px 10px;border-radius:12px;background:#fff;font-size:11px;font-weight:700;color:#5A556E;">
                                {{ $item['frekuensi'] }}×
                            </span>
                            @endif
                        </div>
                        @endif
                        @if(in_array($kat, ['makan','minum']))
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px;">
                            @if(!empty($item['porsi']))
                            <span style="padding:3px 10px;border-radius:12px;background:#fff;font-size:11px;font-weight:700;color:
                                {{ ['habis'=>'#166534','setengah'=>'#B45309','sedikit'=>'#DC2626','tidak_makan'=>'#DC2626'][$item['porsi']] ?? '#5A556E' }};">
                                {{ str_replace('_',' ',ucfirst($item['porsi'])) }}
                            </span>
                            @endif
                            @if(!empty($item['nafsu_makan']))
                            <span style="padding:3px 10px;border-radius:12px;background:#fff;font-size:11px;font-weight:700;color:#5A556E;">
                                @php $nafsuIcons = ['lapar'=>'🍽️','biasa'=>'😐','tidak_nafsu'=>'😫']; @endphp
                                {{ $nafsuIcons[$item['nafsu_makan']] ?? '' }} {{ str_replace('_',' ',ucfirst($item['nafsu_makan'])) }}
                            </span>
                            @endif
                        </div>
                        @if(!empty($item['foto_sebelum_url']) || !empty($item['foto_sesudah_url']))
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                            @if(!empty($item['foto_sebelum_url']))
                            <img src="{{ $item['foto_sebelum_url'] }}" class="akt-photo" alt="Sebelum"
                                onclick="event.stopPropagation();openImageModal('{{ $item['foto_sebelum_url'] }}')"
                                style="cursor:pointer;">
                            @endif
                            @if(!empty($item['foto_sesudah_url']))
                            <img src="{{ $item['foto_sesudah_url'] }}" class="akt-photo" alt="Sesudah"
                                onclick="event.stopPropagation();openImageModal('{{ $item['foto_sesudah_url'] }}')"
                                style="cursor:pointer;">
                            @endif
                        </div>
                        @endif
                        @endif
                        @if(!empty($item['foto_url']))
                        <img src="{{ $item['foto_url'] }}" class="akt-photo" alt=""
                            onclick="event.stopPropagation();openImageModal('{{ $item['foto_url'] }}')"
                            style="cursor:pointer;">
                        @endif
                        <div class="akt-footer">
                            <button class="loc-btn"
                                onclick="event.stopPropagation(); openLocationModal(
                                    '{{ addslashes($item['lokasi'] ?? '') }}',
                                    '{{ $item['lat'] ?? '' }}',
                                    '{{ $item['lng'] ?? '' }}'
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

</div>

{{-- FAB --}}
@if(isset($idAssignment))
<a href="{{ route('nanny-diary-add', ['id_anak' => $idAnak, 'id_assignment' => $idAssignment]) }}"
   class="fab">
    <ion-icon name="add" style="font-size:28px;color:#fff;"></ion-icon>
</a>
@endif
@endsection

@push('modals')
{{-- Month/Year Modal --}}
<div class="modal-overlay" id="modalMonthYear">
    <div class="modal-box" style="padding:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <ion-icon name="settings-outline" style="font-size:20px;color:#8B46D3;"></ion-icon>
            <span style="font-size:17px;font-weight:800;color:#1E1B2E;">Set Month And Year</span>
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
                <select id="myYearSel" class="my-select"></select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <button onclick="closeMonthYearModal()"
                style="flex:1;padding:10px 20px;border-radius:13px;border:2px solid #FFCACA;background:#FFF9F9;
                       font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#FF0000;cursor:pointer;
                       display:flex;align-items:center;justify-content:center;gap:8px;">
                Cancel
            </button>
            <button onclick="applyMonthYear()"
                style="flex:1;padding:10px 20px;border-radius:13px;border:none;background:#8B46D3;
                       font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#fff;cursor:pointer;
                       display:flex;align-items:center;justify-content:center;gap:8px;
                       box-shadow:0 4px 12px rgba(139,70,211,.3);">
                Apply
            </button>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box modal-box-tall" style="max-width:340px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-shrink:0;padding:20px;border-bottom:2px solid #F0EDFB;">
            <span style="font-size:17px;font-weight:800;color:#1E1B2E;">Detail Aktivitas</span>
            <button onclick="closeDetail()"
                style="width:32px;height:32px;border-radius:16px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;">
                <ion-icon name="close" style="font-size:20px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto hide-scrollbar" style="padding:20px;" id="detailBody"></div>
        <div style="flex-shrink:0;padding:0 20px 20px;">
            <button onclick="closeDetail()"
                style="width:100%;background:#8B46D3;padding:16px;border-radius:16px;font-size:16px;font-weight:800;color:#fff;cursor:pointer;border:none;
                       box-shadow:0 6px 18px rgba(139,70,211,.3);font-family:'Nunito',sans-serif;">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Location Modal --}}
<div class="modal-overlay" id="modalLocation">
    <div class="modal-box" style="padding:0;overflow:hidden;">
        <div class="map-placeholder" id="mapPlaceholder"></div>
        <div style="padding:16px 18px 18px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1.5px solid #F0EDFB;">
                <div style="width:42px;height:42px;border-radius:12px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <ion-icon name="location" style="font-size:20px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p id="locModalName" style="font-size:15px;font-weight:900;color:#1E1B2E;margin-bottom:2px;">Lokasi Aktivitas</p>
                    <p id="locModalSub" style="font-size:12px;font-weight:700;color:#A8A2C2;">Koordinat tersimpan</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <button onclick="closeLocationModal()"
                    style="padding:13px;border-radius:12px;border:2px solid #EDE9FE;background:#fff;
                           font-size:14px;font-weight:800;color:#5A556E;cursor:pointer;">
                    Close
                </button>
                <button onclick="openInGMaps()"
                    style="padding:13px;border-radius:12px;border:none;background:#8B46D3;
                           font-size:14px;font-weight:800;color:#fff;cursor:pointer;
                           box-shadow:0 6px 18px rgba(139,70,211,.35);">
                    Open in GMaps
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
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
@endpush

@push('scripts')
<script>
const MONTHS_EN = ['January','February','March','April','May','June','July','August','September','October','November','December'];

let currentDate = new Date("{{ $tanggal ?? date('Y-m-d') }}");
let calViewDate = new Date(currentDate);
let isFullMonth = false;

function pad(n){ return String(n).padStart(2,'0'); }

function fmtDate(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }
function reload(){
    const p = new URLSearchParams({tanggal: fmtDate(currentDate)});
    const kat = "{{ $activeKat ?? $kategori ?? '' }}";
    if(kat) p.set('kategori', kat);
    const idAssignment = "{{ $idAssignment ?? '' }}";
    if(idAssignment) p.set('id_assignment', idAssignment);
    window.location.href = '{{ route("nanny-diary", ["id_anak" => $idAnak ?? 0]) }}?' + p.toString();
}

function daysInMonth(y,m){ return new Date(y,m+1,0).getDate(); }

function buildCalendar(){
    const y=calViewDate.getFullYear(), m=calViewDate.getMonth();
    document.getElementById('btnMonthYear').textContent = MONTHS_EN[m]+' '+y;

    const grid = document.getElementById('calGrid');
    grid.innerHTML = '';
    const today = new Date();

    if(isFullMonth){
        const first = new Date(y,m,1).getDay();
        const days  = daysInMonth(y,m);
        const prevDays = new Date(y,m,0).getDate();
        for(let i=first-1;i>=0;i--){
            const cell=document.createElement('div');
            cell.className='day-cell other-month';
            cell.textContent=prevDays-i;
            grid.appendChild(cell);
        }
        for(let d=1;d<=days;d++){ grid.appendChild(makeCell(d,y,m,today)); }
        const total=grid.children.length;
        const rem=total%7===0?0:7-(total%7);
        for(let d=1;d<=rem;d++){
            const cell=document.createElement('div');
            cell.className='day-cell other-month';
            cell.textContent=d;
            grid.appendChild(cell);
        }
    } else {
        const sel=new Date(currentDate);
        const dow=sel.getDay();
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

document.getElementById('btnFullMonth').addEventListener('click',()=>{
    isFullMonth=!isFullMonth;
    document.getElementById('btnFullMonth').textContent=isFullMonth?'Week view':'Full month';
    buildCalendar();
});
buildCalendar();

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
function closeMonthYearModal(){ document.getElementById('modalMonthYear').classList.remove('open'); }
function applyMonthYear(){
    const m=parseInt(document.getElementById('myMonthSel').value);
    const y=parseInt(document.getElementById('myYearSel').value);
    calViewDate=new Date(y,m,1);
    currentDate=new Date(y,m,currentDate.getDate()>daysInMonth(y,m)?daysInMonth(y,m):currentDate.getDate());
    closeMonthYearModal();
    buildCalendar();
    updateTimelineLabel();
    reload();
}
document.getElementById('btnMonthYear').addEventListener('click',openMonthYearModal);
document.getElementById('modalMonthYear').addEventListener('click',e=>{ if(e.target.id==='modalMonthYear') closeMonthYearModal(); });

function getKatColor(k){ return{makan:'#FF9A6C',tidur:'#7BB4F0',main:'#FFD93D',belajar:'#9BB8FF',mandi:'#FFB4D6',bab:'#8D6E63',bak:'#F9A825'}[k]||'#8B46D3'; }
function getKatIcon(k) { return{makan:'restaurant',tidur:'moon',main:'football',belajar:'book',mandi:'water',bab:'ellipse',bak:'water'}[k]||'calendar'; }
function getMoodEmoji(m){ return{senang:'😊',sedih:'😢',marah:'😠',biasa:'😐'}[m]||'😊'; }
function getKatBg(k)   { return{makan:'#FFF4EC',tidur:'#EEF4FF',main:'#FFFBEE',belajar:'#F0F2FF',mandi:'#FFF0F7',bab:'#EFEBE9',bak:'#FFF8E1'}[k]||'#F0EDFB'; }
const WARNA_COLORS = {coklat:'#6D4C41',hijau:'#2E7D32',kuning:'#F9A825',hitam:'#212121',merah:'#C62828',jernih:'#CFD8DC',keruh:'#8D6E63'};

function detailRow(iconName,label,value,isLast){
    return `<div style="display:flex;align-items:flex-start;${isLast?'':'margin-bottom:16px;padding-bottom:16px;border-bottom:1.5px solid #F0EDFB;'}">
        <div style="width:36px;height:36px;border-radius:10px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
            <ion-icon name="${iconName}" style="font-size:18px;color:#8B46D3;"></ion-icon>
        </div>
        <div style="flex:1;">
            <p style="font-size:12px;color:#A8A2C2;font-weight:700;margin-bottom:4px;">${label}</p>
            <p style="font-size:15px;color:#1E1B2E;font-weight:600;line-height:1.5;">${value||'-'}</p>
        </div>
    </div>`;
}

function openDetail(item){
    const bg=getKatBg(item.kategori), col=getKatColor(item.kategori), ic=getKatIcon(item.kategori);
    const rows=[
        {icon:'time-outline',label:'Waktu Mulai',value:item.jam_mulai_fmt},
        {icon:'time-outline',label:'Waktu Selesai',value:item.jam_selesai_fmt},
        {icon:'hourglass',label:'Durasi',value:item.durasi_fmt},
    ];
    if(item.mood && item.kategori!=='bab' && item.kategori!=='bak') rows.push({emoji:getMoodEmoji(item.mood),label:'Mood',value:item.mood.charAt(0).toUpperCase()+item.mood.slice(1)});
    if(item.deskripsi) rows.push({icon:'document-text-outline',label:'Deskripsi',value:item.deskripsi});
    // BAB/BAK fields
    if(item.kategori==='bab'||item.kategori==='bak'){
        if(item.warna) rows.push({icon:'color-palette-outline',label:'Warna',value:item.warna.charAt(0).toUpperCase()+item.warna.slice(1)});
        if(item.tekstur && item.kategori==='bab') rows.push({icon:'layers-outline',label:'Tekstur',value:item.tekstur.charAt(0).toUpperCase()+item.tekstur.slice(1)});
        if(item.volume) rows.push({icon:'scale-outline',label:'Volume',value:item.volume.charAt(0).toUpperCase()+item.volume.slice(1)});
        if(item.frekuensi) rows.push({icon:'repeat-outline',label:'Frekuensi',value:item.frekuensi+'×'});
    }
    // Makan/Minum fields
    if(item.kategori==='makan'||item.kategori==='minum'){
        if(item.porsi) rows.push({icon:'fast-food-outline',label:'Porsi',value:item.porsi.charAt(0).toUpperCase()+item.porsi.slice(1)});
        if(item.nafsu_makan) rows.push({icon:'happy-outline',label:'Nafsu Makan',value:item.nafsu_makan.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())});
    }
    if(item.nanny_name) rows.push({icon:'person-outline',label:'Dicatat oleh',value:item.nanny_name});

    let html=`<div style="display:flex;flex-direction:column;align-items:center;padding:20px;border-radius:16px;margin-bottom:20px;background:${bg};border:1.5px solid ${col}40;">
        <ion-icon name="${ic}" style="font-size:36px;color:${col};"></ion-icon>
        <p style="font-size:20px;font-weight:900;color:#1E1B2E;margin-top:10px;">${item.kategori.charAt(0).toUpperCase()+item.kategori.slice(1)}</p>
    </div>`;
    rows.forEach((r,idx)=>{
        const last=idx===rows.length-1&&!item.foto_url;
        if(r.emoji){
            html+=`<div style="display:flex;align-items:flex-start;${last?'':'margin-bottom:16px;padding-bottom:16px;border-bottom:1.5px solid #F0EDFB;'}}">
                <div style="width:36px;height:36px;border-radius:10px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <span style="font-size:20px;">${r.emoji}</span>
                </div>
                <div style="flex:1;"><p style="font-size:12px;color:#A8A2C2;font-weight:700;margin-bottom:4px;">${r.label}</p><p style="font-size:15px;color:#1E1B2E;font-weight:600;">${r.value||'-'}</p></div>
            </div>`;
        } else { html+=detailRow(r.icon,r.label,r.value,last); }
    });
    if(item.foto_url){
        html+=`<div style="margin-top:10px;">
            <p style="font-size:12px;color:#A8A2C2;font-weight:700;margin-bottom:8px;">Foto</p>
            <img src="${item.foto_url}" onclick="closeDetail();openImageModal('${item.foto_url}')"
                 style="width:100%;height:180px;border-radius:14px;object-fit:cover;border:1.5px solid #EDE9FE;cursor:pointer;transition:transform .2s ease;"
                 onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
        </div>`;
    }
    // Foto sebelum/sesudah untuk makan/minum
    if((item.kategori==='makan'||item.kategori==='minum') && (item.foto_sebelum_url||item.foto_sesudah_url)){
        html+=`<div style="margin-top:10px;">
            <p style="font-size:12px;color:#A8A2C2;font-weight:700;margin-bottom:8px;">Foto Makanan</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">`;
        if(item.foto_sebelum_url) html+=`<img src="${item.foto_sebelum_url}" onclick="closeDetail();openImageModal('${item.foto_sebelum_url}')" style="height:160px;border-radius:14px;object-fit:cover;border:1.5px solid #EDE9FE;cursor:pointer;" title="Sebelum">`;
        if(item.foto_sesudah_url) html+=`<img src="${item.foto_sesudah_url}" onclick="closeDetail();openImageModal('${item.foto_sesudah_url}')" style="height:160px;border-radius:14px;object-fit:cover;border:1.5px solid #EDE9FE;cursor:pointer;" title="Sesudah">`;
        html+=`</div></div>`;
    }
    document.getElementById('detailBody').innerHTML=html;
    document.getElementById('modalDetail').classList.add('open');
}
function closeDetail(){ document.getElementById('modalDetail').classList.remove('open'); }
document.getElementById('modalDetail').addEventListener('click',e=>{ if(e.target.id==='modalDetail') closeDetail(); });

// ── Location Modal ──
let locLat = null, locLng = null;

function openLocationModal(name, lat, lng) {
    locLat = lat; locLng = lng;
    document.getElementById('locModalName').textContent = name || 'Lokasi Aktivitas';
    document.getElementById('locModalSub').textContent = lat && lng
        ? lat + ', ' + lng
        : 'Lokasi tercatat';

    const mapContainer = document.getElementById('mapPlaceholder');
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
        mapContainer.innerHTML = `
            <iframe
                width="100%"
                height="180"
                style="border:0;border-radius:14px;display:block;"
                loading="lazy"
                allowfullscreen
                src="https://maps.google.com/maps?q=${lat},${lng}&z=16&output=embed">
            </iframe>`;
    } else {
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
function openInGMaps() {
    if (locLat && locLng) {
        window.open('https://www.google.com/maps?q=' + locLat + ',' + locLng, '_blank');
    } else {
        window.open('https://maps.google.com', '_blank');
    }
}
document.getElementById('modalLocation').addEventListener('click',e=>{
    if(e.target.id==='modalLocation') closeLocationModal();
});

function openImageModal(imageUrl){
    if(!imageUrl) return;
    document.getElementById('modalImage').src=imageUrl;
    document.getElementById('imageModal').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeImageModal(){
    document.getElementById('imageModal').classList.remove('open');
    document.body.style.overflow='';
}
document.getElementById('imageModal').addEventListener('click',e=>{ if(e.target.id==='imageModal') closeImageModal(); });
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeImageModal(); });
</script>
@endpush
