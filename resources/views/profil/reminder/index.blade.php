<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Alarm & Notification</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }
        .delay-4 { animation-delay: 0.30s; }
        .delay-5 { animation-delay: 0.38s; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes sheetIn {
            from { transform: translateY(100%); }
            to   { transform: translateY(0); }
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .alarm-card { transition: transform 0.15s ease; }
        .alarm-card:active { transform: scale(0.98); }

        /* Toggle Switch */
        .toggle-switch {
            width: 48px; height: 26px;
            background: #D1D5DB;
            border-radius: 999px;
            position: relative;
            cursor: pointer;
            transition: background 0.25s ease;
            flex-shrink: 0;
        }
        .toggle-switch.active { background: #6C3FC5; }
        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 20px; height: 20px;
            background: white;
            border-radius: 50%;
            top: 3px; left: 3px;
            transition: left 0.25s ease;
            box-shadow: 0 1px 4px rgba(0,0,0,0.18);
        }
        .toggle-switch.active::after { left: 25px; }

        /* Modal overlay */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 50;
            display: flex; align-items: center; justify-content: center;
            animation: fadeIn 0.2s ease;
        }
        .modal-box {
            background: white;
            border-radius: 20px;
            padding: 28px 24px;
            width: calc(100% - 64px);
            max-width: 340px;
            animation: modalIn 0.28s cubic-bezier(0.34,1.4,0.64,1);
        }

        /* Bottom Sheet */
        .bottom-sheet-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 50;
            animation: fadeIn 0.2s ease;
        }
        .bottom-sheet {
            position: fixed;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 100%; max-width: 390px;
            background: white;
            border-radius: 28px 28px 0 0;
            padding: 24px 24px 40px;
            z-index: 51;
            animation: sheetIn 0.32s cubic-bezier(0.32,0.72,0,1);
            max-height: 85vh;
            overflow-y: auto;
        }
        .bottom-sheet::-webkit-scrollbar { display: none; }

        /* ── Time Picker ── */
        .time-input-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        /* Editable circle: shows input on focus, number display normally */
        .time-circle-display {
            width: 52px; height: 52px;
            border: 2px solid #E5E7EB;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800;
            color: #1F2937;
            background: white;
            cursor: text;
            user-select: none;
            position: relative;
            transition: border-color 0.18s;
        }
        .time-circle-display:focus-within {
            border-color: #8B46D3;
            box-shadow: 0 0 0 3px rgba(139,70,211,.15);
        }
        /* The actual hidden-ish input inside the circle */
        .time-circle-input {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            border: none; outline: none;
            background: transparent;
            text-align: center;
            font-size: 20px; font-weight: 800;
            color: #1F2937;
            font-family: 'Nunito', sans-serif;
            border-radius: 50%;
            padding: 0;
            caret-color: #8B46D3;
        }
        /* Arrow button above/below */
        .time-arrow-btn {
            width: 28px; height: 22px;
            display: flex; align-items: center; justify-content: center;
            color: #D1D5DB;
            cursor: pointer;
            border-radius: 6px;
            transition: color 0.15s, background 0.15s;
        }
        .time-arrow-btn:hover { color: #8B46D3; background: #F3F0FF; }
        .time-arrow-btn:active { transform: scale(0.9); }

        /* AM/PM Toggle */
        .ampm-btn {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px; font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }
        .ampm-btn.active { background: #2635DA; color: white; }
        .ampm-btn.inactive { color: #9CA3AF; }

        /* Day selector */
        .day-btn {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
            color: #6B7280;
        }
        .day-btn.selected { background: #6C3FC5; color: white; }

        /* Calendar */
        .cal-wrap { background: #fff; border-radius: 18px; padding: 16px; box-shadow: 0 2px 14px rgba(139,70,211,.08); }
        .cal-month-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
        .cal-month-btn { font-size:16px; font-weight:900; color:#1E1B2E; background:none; border:none; cursor:pointer; padding:0; font-family:'Nunito',sans-serif; }
        .cal-month-btn:hover { color:#8B46D3; }
        .full-month-btn { font-size:13px; font-weight:800; color:#8B46D3; background:none; border:none; cursor:pointer; padding:0; font-family:'Nunito',sans-serif; }
        .weekdays { display:grid; grid-template-columns:repeat(7,1fr); text-align:center; margin-bottom:4px; }
        .weekday { font-size:11px; font-weight:800; color:#A8A2C2; padding:2px 0; }
        .days-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
        .day-cell { display:flex; align-items:center; justify-content:center; height:34px; border-radius:10px; font-size:14px; font-weight:700; color:#1E1B2E; cursor:pointer; transition:background .12s; font-family:'Nunito',sans-serif; position:relative; }
        .day-cell:hover { background:#EDE9FE; }
        .day-cell.today { background:transparent; color:#1E1B2E; border:1.5px solid #8B46D3; }
        .day-cell.selected { background:#8B46D3; color:#fff; }
        .day-cell.other-month { color:#C4B5FD; font-weight:600; cursor:default; }
        .day-cell.other-month:hover { background:transparent; }
        .day-cell.has-dot::after { content:''; width:4px; height:4px; border-radius:50%; background:#8B46D3; position:absolute; bottom:2px; left:50%; transform:translateX(-50%); }
        .day-cell.today.has-dot::after,
        .day-cell.selected.has-dot::after { background:#fff; }

        /* Save button */
        .btn-save {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, #8B46D3, #6C3FC5);
            color: white;
            font-size: 15px;
            font-weight: 800;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            cursor: pointer;
            transition: opacity 0.18s ease, transform 0.15s ease;
        }
        .btn-save:active { opacity: 0.88; transform: scale(0.98); }

        /* Input field */
        .alarm-input {
            width: 100%;
            border: 1.5px solid #E5E7EB;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 15px;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
            color: #1F2937;
            outline: none;
            transition: border-color 0.18s ease;
        }
        .alarm-input:focus { border-color: #8B46D3; }
        .alarm-input::placeholder { color: #9CA3AF; font-weight: 500; }

        /* Action icon buttons */
        .icon-btn {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 2px solid currentColor;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .icon-btn:active { transform: scale(0.9); opacity: 0.7; }

        .fab-in { animation: fabIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s both; }

        @keyframes shimmer {
            0% { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 400px 100%;
            animation: shimmer 1.4s ease infinite;
            border-radius: 8px;
        }

        @keyframes bannerSlide {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .notif-banner {
            animation: bannerSlide 0.3s ease;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .sub-modal {
            position: fixed; inset: 0;
            display: flex; align-items: center; justify-content: center;
            z-index: 60;
            background: rgba(0,0,0,0.3);
            animation: fadeIn 0.15s ease;
        }
        .sub-modal-box {
            background: white;
            border-radius: 20px;
            padding: 24px;
            width: calc(100% - 80px);
            max-width: 300px;
            animation: modalIn 0.22s ease;
        }
        .my-select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1.5px solid #EDE9FE;
            background: #F8F7FF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%238B46D3' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #1E1B2E;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            outline: none;
        }
        .my-select:focus { border-color: #8B46D3; box-shadow: 0 0 0 3px rgba(139,70,211,.12); }

        /* repeat days badge on alarm card */
        .repeat-badge {
            display: inline-flex; gap: 3px; flex-wrap: wrap;
        }
        .repeat-badge span {
            font-size: 10px; font-weight: 800;
            background: #EDE9FB; color: #6C3FC5;
            border-radius: 6px;
            padding: 1px 5px;
        }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#8B46D3] bg-[url('/assets/bg-texture-full.png')] bg-cover bg-center min-h-screen flex flex-col relative">

    <!-- STATUS BAR (desktop only) -->
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <span class="iconify" data-icon="material-symbols:signal-cellular-alt" style="font-size:16px; color:white;"></span>
            <span class="iconify" data-icon="material-symbols:wifi" style="font-size:16px; color:white;"></span>
            <span class="iconify" data-icon="material-symbols:battery-full" style="font-size:16px; color:white;"></span>
        </div>
    </div>

    <!-- PURPLE HEADER -->
    <div class="anim delay-1 relative z-10 px-[24px] py-[55px]">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('profil.index') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <span class="iconify" data-icon="material-symbols:arrow-back-rounded" style="font-size:18px; color:white;"></span>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Alarm & Notification</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">Set alarms and notifications as reminders of your needs</p>
            </div>
        </div>
    </div>

    <!-- WHITE BODY -->
    <div class="flex-1 backdrop-blur-sm overflow-y-auto px-[20px] pb-28 relative z-20 flex flex-col gap-4 hide-scrollbar" id="mainBody">

        <div id="notifBanner" class="hidden"></div>

        <!-- Summary Card -->
        <div class="anim delay-2 bg-white rounded-[18px] px-4 py-4 flex items-center gap-4 shadow-sm" id="summaryCard">
            <div class="w-12 h-12 rounded-full bg-[#EDE9FB] flex items-center justify-center shrink-0">
                <span class="iconify" data-icon="material-symbols:notifications-rounded" style="font-size:22px; color:#2635DA;"></span>
            </div>
            <div>
                <p class="text-[#2635DA] text-[16px] font-extrabold" id="summaryCount">Loading...</p>
                <p class="text-gray-400 text-xs font-semibold mt-0.5" id="summaryNext">—</p>
            </div>
        </div>

        <p class="text-white/80 text-[13px] font-bold px-1 anim delay-3">Upcoming Alarm</p>

        <div id="alarmList" class="flex flex-col gap-3">
            <div class="bg-white rounded-[18px] p-4 skeleton h-[88px]"></div>
            <div class="bg-white rounded-[18px] p-4 skeleton h-[88px]"></div>
            <div class="bg-white rounded-[18px] p-4 skeleton h-[88px]"></div>
        </div>

        <div id="emptyState" class="hidden flex flex-col items-center justify-center py-16 gap-3">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                <span class="iconify" data-icon="material-symbols:alarm-outline-rounded" style="font-size:32px; color:white;"></span>
            </div>
            <p class="text-white/80 font-bold text-base">No alarms yet</p>
            <p class="text-white/50 text-sm font-medium">Tap + to create your first alarm</p>
        </div>

    </div>

    <!-- FAB -->
    <button class="fab-in fixed sm:absolute bottom-24 right-5 w-14 h-14 rounded-full bg-[#ffffff] shadow-[0_8px_24px_rgba(139,70,211,0.45)] flex items-center justify-center z-30" onclick="openCreateSheet()" id="fabBtn">
        <span class="iconify" data-icon="material-symbols:add-rounded" style="font-size:30px; color:#6C3FC5;"></span>
    </button>

    @include('partials.bottom-nav', ['active' => 'profil'])
</div>
</div>

<!-- ============================================================ -->
<!--  MODAL: Nonactive Confirmation                                -->
<!-- ============================================================ -->
<div id="nonactiveModal" class="modal-overlay hidden">
    <div class="modal-box">
        <h2 class="text-[17px] font-extrabold text-gray-900 mb-2">Nonactive Alarm & Notification</h2>
        <p class="text-gray-500 text-sm font-medium leading-relaxed mb-6" id="nonactiveMsg">Are you sure?</p>
        <div class="flex gap-3">
            <button onclick="closeNonactiveModal()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold text-sm transition hover:bg-gray-50">
                <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:12px; color:white;"></span>
                </span>Cancel
            </button>
            <button onclick="confirmNonactive()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl text-white font-bold text-sm"
                style="background: linear-gradient(135deg,#F59E0B,#F97316);">
                <span class="w-5 h-5 rounded-full bg-white/30 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:remove-rounded" style="font-size:12px; color:white;"></span>
                </span>Nonactive
            </button>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!--  MODAL: Delete Confirmation                                   -->
<!-- ============================================================ -->
<div id="deleteModal" class="modal-overlay hidden">
    <div class="modal-box">
        <h2 class="text-[17px] font-extrabold text-gray-900 mb-2">Delete Alarm & Notification</h2>
        <p class="text-gray-500 text-sm font-medium leading-relaxed mb-6" id="deleteMsg">Are you sure?</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold text-sm transition hover:bg-gray-50">
                <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:12px; color:white;"></span>
                </span>Cancel
            </button>
            <button onclick="confirmDelete()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl bg-red-500 text-white font-bold text-sm">
                <span class="iconify" data-icon="material-symbols:delete-rounded" style="font-size:16px;"></span>Delete
            </button>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!--  BOTTOM SHEET: Create / Edit Alarm                           -->
<!-- ============================================================ -->
<div id="sheetOverlay" class="bottom-sheet-overlay hidden" onclick="closeSheet()"></div>
<div id="alarmSheet" class="bottom-sheet hidden">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="iconify" data-icon="material-symbols:add-circle-outline-rounded" style="font-size:20px; color:#6C3FC5;"></span>
            <h3 class="text-[16px] font-extrabold text-gray-900" id="sheetTitle">Create Alarm & Notification</h3>
        </div>
        <button onclick="closeSheet()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600">
            <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:20px;"></span>
        </button>
    </div>
    <div class="border-b border-gray-100 mb-5"></div>

    <!-- Label -->
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Alarm Label</label>
    <input type="text" id="alarmLabel" class="alarm-input mb-5" placeholder="e.g. Hepatitis B-1 vaccine">

    <!-- ── Time Picker ── -->
    <label class="text-[13px] font-bold text-gray-700 mb-3 block">
        Time
        <span class="text-[11px] text-gray-400 font-normal ml-1">(tap number to type, or use arrows)</span>
    </label>
    <div class="flex items-center gap-3 mb-5">
        <div class="flex items-center gap-2">

            <!-- Hour -->
            <div class="time-input-wrap">
                <button class="time-arrow-btn" onclick="adjustTime('hour',1)">
                    <span class="iconify" data-icon="material-symbols:keyboard-arrow-up-rounded" style="font-size:20px;"></span>
                </button>
                <div class="time-circle-display" id="hourCircle">
                    <input type="text"
                           id="inputHour"
                           class="time-circle-input"
                           inputmode="numeric"
                           maxlength="2"
                           value="07"
                           onFocus="selectAll(this)"
                           onInput="handleTimeInput(this,'hour')"
                           onBlur="commitTimeInput(this,'hour')"
                           onKeyDown="handleTimeKey(event,this,'hour')"
                    >
                </div>
                <button class="time-arrow-btn" onclick="adjustTime('hour',-1)">
                    <span class="iconify" data-icon="material-symbols:keyboard-arrow-down-rounded" style="font-size:20px;"></span>
                </button>
            </div>

            <span class="text-2xl font-black text-gray-300 select-none">:</span>

            <!-- Minute -->
            <div class="time-input-wrap">
                <button class="time-arrow-btn" onclick="adjustTime('minute',1)">
                    <span class="iconify" data-icon="material-symbols:keyboard-arrow-up-rounded" style="font-size:20px;"></span>
                </button>
                <div class="time-circle-display" id="minuteCircle">
                    <input type="text"
                           id="inputMinute"
                           class="time-circle-input"
                           inputmode="numeric"
                           maxlength="2"
                           value="30"
                           onFocus="selectAll(this)"
                           onInput="handleTimeInput(this,'minute')"
                           onBlur="commitTimeInput(this,'minute')"
                           onKeyDown="handleTimeKey(event,this,'minute')"
                    >
                </div>
                <button class="time-arrow-btn" onclick="adjustTime('minute',-1)">
                    <span class="iconify" data-icon="material-symbols:keyboard-arrow-down-rounded" style="font-size:20px;"></span>
                </button>
            </div>
        </div>

        <!-- AM/PM -->
        <div class="ml-auto flex gap-1 bg-gray-100 rounded-xl p-1">
            <button class="ampm-btn active" id="btnAM" onclick="setAmPm('AM')">AM</button>
            <button class="ampm-btn inactive" id="btnPM" onclick="setAmPm('PM')">PM</button>
        </div>
    </div>

    <!-- Calendar (shown when repeat is OFF) -->
    <div id="calendarSection" class="mb-4">
        <div class="cal-wrap">
            <div class="cal-month-row">
                <button onclick="prevMonth()" class="text-gray-400 hover:text-gray-600 flex items-center">
                    <span class="iconify" data-icon="material-symbols:chevron-left-rounded" style="font-size:20px;"></span>
                </button>
                <button class="cal-month-btn" id="calMonthYearBtn" onclick="openMonthYearPicker()">March 2026</button>
                <div class="flex items-center gap-2">
                    <button onclick="nextMonth()" class="text-gray-400 hover:text-gray-600 flex items-center">
                        <span class="iconify" data-icon="material-symbols:chevron-right-rounded" style="font-size:20px;"></span>
                    </button>
                    <button class="full-month-btn" id="fullMonthBtn" onclick="goFullMonth()">Full month</button>
                </div>
            </div>
            <div class="weekdays">
                <div class="weekday">SUN</div><div class="weekday">MON</div><div class="weekday">TUE</div>
                <div class="weekday">WED</div><div class="weekday">THU</div><div class="weekday">FRI</div>
                <div class="weekday">SAT</div>
            </div>
            <div class="days-grid" id="calGrid"></div>
        </div>
    </div>

    <!-- Repeat Weekly -->
    <div class="border-t border-gray-100 pt-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="iconify" data-icon="material-symbols:repeat-rounded" style="font-size:18px; color:#6C3FC5;"></span>
                <span class="text-[14px] font-bold text-gray-700">Repeatly Weekly</span>
            </div>
            <div class="toggle-switch" id="repeatToggle" onclick="toggleRepeat()"></div>
        </div>

        <!-- Day selector (shown when repeat is ON) -->
        <div id="daySelector" class="hidden">
            <p class="text-[11px] text-gray-400 font-semibold mb-2">Select day(s) to repeat</p>
            <div class="flex justify-between px-1">
                <div class="day-btn" data-day="0" onclick="toggleDay(this)">S</div>
                <div class="day-btn" data-day="1" onclick="toggleDay(this)">M</div>
                <div class="day-btn" data-day="2" onclick="toggleDay(this)">T</div>
                <div class="day-btn" data-day="3" onclick="toggleDay(this)">W</div>
                <div class="day-btn selected" data-day="4" onclick="toggleDay(this)">TH</div>
                <div class="day-btn" data-day="5" onclick="toggleDay(this)">F</div>
                <div class="day-btn" data-day="6" onclick="toggleDay(this)">SA</div>
            </div>
            <p id="repeatDayError" class="hidden text-[11px] text-red-500 font-semibold mt-2 px-1">
                Please select at least one day.
            </p>
        </div>
    </div>

    <!-- Save Button -->
    <button class="btn-save" id="saveBtn" onclick="saveAlarm()">
        <span class="iconify" data-icon="material-symbols:save-rounded" style="font-size:18px;"></span>
        <span id="saveBtnText">Create New Alarm</span>
    </button>
</div>

<!-- ============================================================ -->
<!--  SUB-MODAL: Month & Year Picker                              -->
<!-- ============================================================ -->
<div id="monthYearModal" class="sub-modal hidden">
    <div class="sub-modal-box">
        <div class="flex items-center gap-2 mb-4">
            <span class="iconify" data-icon="material-symbols:settings-rounded" style="font-size:20px; color:#6C3FC5;"></span>
            <h4 class="text-[15px] font-extrabold text-gray-900">Set Month And Year</h4>
        </div>
        <div class="flex gap-4 mb-5">
            <div class="flex-1">
                <label class="text-[11px] font-bold text-gray-500 mb-1.5 block">Select Month</label>
                <select id="pickerMonth" class="my-select">
                    <option value="0">January</option><option value="1">February</option>
                    <option value="2">March</option><option value="3">April</option>
                    <option value="4">May</option><option value="5">June</option>
                    <option value="6">July</option><option value="7">August</option>
                    <option value="8">September</option><option value="9">October</option>
                    <option value="10">November</option><option value="11">December</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="text-[11px] font-bold text-gray-500 mb-1.5 block">Select Year</label>
                <select id="pickerYear" class="my-select"></select>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="closeMonthYearPicker()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold text-sm">
                <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:12px; color:white;"></span>
                </span>Cancel
            </button>
            <button onclick="applyMonthYear()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl text-white font-bold text-sm"
                style="background:#6C3FC5;">
                <span class="iconify" data-icon="material-symbols:check-circle-rounded" style="font-size:16px;"></span>Apply
            </button>
        </div>
    </div>
</div>

<script>
// ================================================================
// CONFIG
// ================================================================
const API_BASE  = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "http://127.0.0.1:8000/api")), "/") }}';
@php
    $resolvedUserId = session('user_id') ?: data_get(session('user'), 'id_user');
@endphp
const USER_ID   = @json($resolvedUserId);
const API_TOKEN = '{{ session("token") }}';

// ================================================================
// STATE
// ================================================================
let reminders           = [];
let editingId           = null;
let pendingDeleteId     = null;
let pendingToggleId     = null;
let isRepeat            = false;
let ampm                = 'AM';
let hour                = 7;
let minute              = 30;
let calYear             = new Date().getFullYear();
let calMonth            = new Date().getMonth();
let selectedDate        = null;
let isFullMonth         = false;
let selectedDays        = new Set([4]); // 0=Sun..6=Sat

const DAY_NAMES_SHORT   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const DAY_LABELS        = ['S','M','T','W','TH','F','SA'];

// Status bar clock
setInterval(() => {
    const n = new Date();
    document.querySelectorAll('#statusTime').forEach(el =>
        el.textContent = `${n.getHours()}:${String(n.getMinutes()).padStart(2,'0')}`
    );
}, 10000);

// ================================================================
// FETCH
// ================================================================
async function fetchReminders() {
    try {
        const endpoint = USER_ID
            ? `${API_BASE}/reminders/${USER_ID}`
            : `${API_BASE}/reminders`;
        const res  = await fetch(endpoint, {
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        reminders  = data.data || [];
        renderList();
    } catch (e) {
        showBanner('Failed to load reminders. Please try again.', 'error');
        document.getElementById('alarmList').innerHTML = '';
        document.getElementById('emptyState').classList.remove('hidden');
    }
}

// ================================================================
// RENDER
// ================================================================
function repeatLabel(r) {
    if (!r.is_repeat_weekly) return 'One time';
    const days = Array.isArray(r.repeat_days) ? r.repeat_days : [];
    if (days.length === 0) return 'Weekly';
    // Show badges
    return days.sort((a,b)=>a-b).map(d => `<span>${DAY_NAMES_SHORT[d]}</span>`).join('');
}

function renderList() {
    const list  = document.getElementById('alarmList');
    const empty = document.getElementById('emptyState');

    document.getElementById('summaryCount').textContent =
        `${reminders.length} active reminder${reminders.length !== 1 ? 's' : ''}`;

    const now = new Date();
    const upcoming = reminders
        .filter(r => r.date && r.time && !r.is_repeat_weekly)
        .map(r => ({ r, dt: new Date(`${r.date}T${r.time}`) }))
        .filter(x => x.dt > now)
        .sort((a, b) => a.dt - b.dt);

    const repeatOnes = reminders.filter(r => r.is_repeat_weekly);

    const nextLabel = upcoming.length
        ? `Next: ${formatTime12(upcoming[0].r.time)} — ${formatDate(upcoming[0].r.date)}`
        : repeatOnes.length
            ? `${repeatOnes.length} weekly repeat alarm${repeatOnes.length > 1 ? 's' : ''} active`
            : 'No upcoming alarms';
    document.getElementById('summaryNext').textContent = nextLabel;

    if (reminders.length === 0) {
        list.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }
    empty.classList.add('hidden');

    list.innerHTML = reminders.map((r, i) => {
        const t12      = formatTime12(r.time);
        const dateStr  = r.is_repeat_weekly ? '' : (r.date ? formatDate(r.date) : '');
        const isActive = r.is_active !== false;
        const rLabel   = repeatLabel(r);
        const isRepeatCard = r.is_repeat_weekly;

        return `
        <div class="alarm-card bg-white rounded-[18px] p-4 shadow-sm anim" style="animation-delay:${0.05 + i * 0.07}s">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-widest text-[#2635DA] mb-1 truncate">${escHtml(r.label)}</p>
                    <p class="text-[22px] font-bold text-gray-900 leading-tight">${t12}</p>
                </div>
                <div class="toggle-switch mt-1 ${isActive ? 'active' : ''}" onclick="onToggleActive(${r.id}, ${isActive})"></div>
            </div>
            <div class="flex items-center justify-between mt-3">
                <div class="text-gray-400 text-[12px] font-semibold flex items-start gap-1.5">
                    <span class="iconify mt-0.5" data-icon="${isRepeatCard ? 'material-symbols:repeat-rounded' : 'material-symbols:calendar-month-outline-rounded'}" style="font-size:13px; flex-shrink:0;"></span>
                    ${isRepeatCard
                        ? `<div class="repeat-badge">${rLabel}</div>`
                        : `<span>${dateStr}</span>`
                    }
                </div>
                <div class="flex items-center gap-2 ml-2 shrink-0">
                    <button class="icon-btn text-green-500" onclick="openEditSheet(${r.id})">
                        <span class="iconify" data-icon="material-symbols:edit-rounded" style="font-size:13px;"></span>
                    </button>
                    <button class="icon-btn text-red-400" onclick="openDeleteModal(${r.id})">
                        <span class="iconify" data-icon="material-symbols:delete-rounded" style="font-size:13px;"></span>
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');

    if (window.Iconify) Iconify.scan();
}

// ================================================================
// TOGGLE ACTIVE
// ================================================================
async function onToggleActive(id, currentlyActive) {
    if (currentlyActive) {
        pendingToggleId = id;
        const r = reminders.find(x => x.id === id);
        document.getElementById('nonactiveMsg').textContent =
            `Are you sure you want to nonactive "${r?.label || ''}" alarm?`;
        document.getElementById('nonactiveModal').classList.remove('hidden');
    } else {
        try {
            const res = await fetch(`${API_BASE}/reminders/${id}/update-status`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_active: true })
            });
            const data = await res.json();
            if (data.success) {
                const r = reminders.find(x => x.id === id);
                if (r) { r.is_active = true; renderList(); }
            } else {
                showToast('Terjadi kesalahan. Coba lagi.');
            }
        } catch (e) {
            showToast('Terjadi kesalahan. Coba lagi.');
        }
    }
}
function closeNonactiveModal() {
    document.getElementById('nonactiveModal').classList.add('hidden');
    pendingToggleId = null;
}
async function confirmNonactive() {
    if (pendingToggleId !== null) {
        try {
            const res = await fetch(`${API_BASE}/reminders/${pendingToggleId}/update-status`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_active: false })
            });
            const data = await res.json();
            if (data.success) {
                const r = reminders.find(x => x.id === pendingToggleId);
                if (r) { r.is_active = false; renderList(); }
            } else {
                showToast('Terjadi kesalahan. Coba lagi.');
            }
        } catch (e) {
            showToast('Terjadi kesalahan. Coba lagi.');
        }
    }
    closeNonactiveModal();
}

// ================================================================
// DELETE
// ================================================================
function openDeleteModal(id) {
    pendingDeleteId = id;
    const r = reminders.find(x => x.id === id);
    document.getElementById('deleteMsg').textContent =
        `Are you sure you want to delete "${r?.label || ''}" alarm? This cannot be recovered.`;
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    pendingDeleteId = null;
}
async function confirmDelete() {
    if (pendingDeleteId === null) return;
    try {
        const res  = await fetch(`${API_BASE}/reminders/${pendingDeleteId}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${API_TOKEN}` }
        });
        const data = await res.json();
        if (data.success) {
            reminders = reminders.filter(r => r.id !== pendingDeleteId);
            renderList();
            showBanner('Alarm deleted successfully.', 'success');
        } else {
            showBanner('Failed to delete alarm.', 'error');
        }
    } catch {
        reminders = reminders.filter(r => r.id !== pendingDeleteId);
        renderList();
    }
    closeDeleteModal();
}

// ================================================================
// BOTTOM SHEET
// ================================================================
function openCreateSheet() {
    editingId    = null;
    document.getElementById('sheetTitle').textContent  = 'Create Alarm & Notification';
    document.getElementById('saveBtnText').textContent = 'Create New Alarm';
    document.getElementById('alarmLabel').value        = '';
    hour = 7; minute = 30; ampm = 'AM'; isRepeat = false;
    selectedDays = new Set([4]);
    calYear      = new Date().getFullYear();
    calMonth     = new Date().getMonth();
    selectedDate = null;
    isFullMonth  = false;
    syncSheet();
    showSheet();
}

function openEditSheet(id) {
    const r = reminders.find(x => x.id === id);
    if (!r) return;
    editingId = id;
    document.getElementById('sheetTitle').textContent  = 'Edit Alarm & Notification';
    document.getElementById('saveBtnText').textContent = 'Save Change';
    document.getElementById('alarmLabel').value        = r.label || '';

    if (r.time) {
        const [hStr, mStr] = r.time.split(':');
        let h = parseInt(hStr);
        if (h >= 12) { ampm = 'PM'; h = h === 12 ? 12 : h - 12; }
        else          { ampm = 'AM'; h = h === 0  ? 12 : h; }
        hour = h; minute = parseInt(mStr);
    }

    isRepeat = r.is_repeat_weekly || false;

    // Load repeat_days
    if (isRepeat && Array.isArray(r.repeat_days) && r.repeat_days.length > 0) {
        selectedDays = new Set(r.repeat_days);
    } else {
        selectedDays = new Set([4]);
    }

    if (!isRepeat && r.date) {
        const d  = new Date(r.date);
        calYear  = d.getFullYear();
        calMonth = d.getMonth();
        selectedDate = d.getDate();
    } else {
        calYear  = new Date().getFullYear();
        calMonth = new Date().getMonth();
        selectedDate = null;
    }
    isFullMonth = true;
    syncSheet();
    showSheet();
}

function showSheet() {
    document.getElementById('sheetOverlay').classList.remove('hidden');
    document.getElementById('alarmSheet').classList.remove('hidden');
}
function closeSheet() {
    document.getElementById('sheetOverlay').classList.add('hidden');
    document.getElementById('alarmSheet').classList.add('hidden');
}

function syncSheet() {
    updateTimeDisplay();

    document.getElementById('btnAM').className = 'ampm-btn ' + (ampm === 'AM' ? 'active' : 'inactive');
    document.getElementById('btnPM').className = 'ampm-btn ' + (ampm === 'PM' ? 'active' : 'inactive');

    document.getElementById('repeatToggle').classList.toggle('active', isRepeat);
    document.getElementById('daySelector').classList.toggle('hidden', !isRepeat);
    document.getElementById('calendarSection').classList.toggle('hidden', isRepeat);

    document.querySelectorAll('.day-btn').forEach(btn => {
        const d = parseInt(btn.dataset.day);
        btn.classList.toggle('selected', selectedDays.has(d));
    });

    document.getElementById('repeatDayError').classList.add('hidden');
    buildCalendar();
}

function updateTimeDisplay() {
    document.getElementById('inputHour').value   = String(hour).padStart(2,'0');
    document.getElementById('inputMinute').value = String(minute).padStart(2,'0');
}

// ================================================================
// TIME PICKER — Arrow buttons
// ================================================================
function adjustTime(part, delta) {
    if (part === 'hour') {
        hour = ((hour - 1 + delta + 12) % 12) + 1;
    } else {
        minute = (minute + delta + 60) % 60;
    }
    updateTimeDisplay();
}

// ================================================================
// TIME PICKER — Keyboard / direct input
// ================================================================
function selectAll(input) {
    requestAnimationFrame(() => input.select());
}

function handleTimeInput(input, part) {
    // Strip non-digits
    let val = input.value.replace(/\D/g,'');
    if (val.length > 2) val = val.slice(-2);
    input.value = val;
}

function commitTimeInput(input, part) {
    let val = parseInt(input.value, 10);
    if (isNaN(val)) { val = part === 'hour' ? 12 : 0; }
    if (part === 'hour') {
        if (val < 1)  val = 12;
        if (val > 12) val = 12;
        hour = val;
    } else {
        if (val < 0)  val = 0;
        if (val > 59) val = 59;
        minute = val;
    }
    updateTimeDisplay();
}

function handleTimeKey(event, input, part) {
    if (event.key === 'Enter' || event.key === 'Tab') {
        input.blur();
        return;
    }
    if (event.key === 'ArrowUp')   { event.preventDefault(); adjustTime(part,  1); }
    if (event.key === 'ArrowDown') { event.preventDefault(); adjustTime(part, -1); }
}

// ================================================================
// AM/PM
// ================================================================
function setAmPm(val) {
    ampm = val;
    document.getElementById('btnAM').className = 'ampm-btn ' + (ampm === 'AM' ? 'active' : 'inactive');
    document.getElementById('btnPM').className = 'ampm-btn ' + (ampm === 'PM' ? 'active' : 'inactive');
}

// ================================================================
// REPEAT TOGGLE & DAYS
// ================================================================
function toggleRepeat() {
    isRepeat = !isRepeat;
    document.getElementById('repeatToggle').classList.toggle('active', isRepeat);
    document.getElementById('daySelector').classList.toggle('hidden', !isRepeat);
    document.getElementById('calendarSection').classList.toggle('hidden', isRepeat);
    document.getElementById('repeatDayError').classList.add('hidden');
}

function toggleDay(el) {
    const d = parseInt(el.dataset.day);
    if (selectedDays.has(d)) {
        selectedDays.delete(d);
        el.classList.remove('selected');
    } else {
        selectedDays.add(d);
        el.classList.add('selected');
    }
    document.getElementById('repeatDayError').classList.add('hidden');
}

// ================================================================
// CALENDAR
// ================================================================
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function daysInMonth(y, m) { return new Date(y, m + 1, 0).getDate(); }

function makeCell(d, y, m, today) {
    const el      = document.createElement('div');
    const isToday = d === today.getDate() && m === today.getMonth() && y === today.getFullYear();
    const isSel   = selectedDate === d && calMonth === m && calYear === y && !isRepeat;
    el.className  = 'day-cell' + (isToday ? ' today' : '') + (isSel ? ' selected' : '');
    el.textContent = d;
    el.onclick = () => {
        selectedDate = d;
        calMonth = m;
        calYear  = y;
        buildCalendar();
    };
    return el;
}

function buildCalendar() {
    const y   = calYear;
    const m   = calMonth;
    const btn = document.getElementById('calMonthYearBtn');
    if (btn) btn.textContent = MONTHS[m] + ' ' + y;

    const grid = document.getElementById('calGrid');
    if (!grid) return;
    grid.innerHTML = '';

    const today = new Date();

    if (isFullMonth) {
        const first    = new Date(y, m, 1).getDay();
        const days     = daysInMonth(y, m);
        const prevDays = new Date(y, m, 0).getDate();

        for (let i = first - 1; i >= 0; i--) {
            const el = document.createElement('div');
            el.className   = 'day-cell other-month';
            el.textContent = prevDays - i;
            grid.appendChild(el);
        }
        for (let d = 1; d <= days; d++) {
            grid.appendChild(makeCell(d, y, m, today));
        }
        const rem = grid.children.length % 7 === 0 ? 0 : 7 - (grid.children.length % 7);
        for (let d = 1; d <= rem; d++) {
            const el = document.createElement('div');
            el.className   = 'day-cell other-month';
            el.textContent = d;
            grid.appendChild(el);
        }
    } else {
        const target    = selectedDate || today.getDate();
        const dow       = new Date(y, m, target).getDay();
        const weekStart = target - dow;
        const maxDay    = daysInMonth(y, m);

        for (let i = 0; i < 7; i++) {
            const day = weekStart + i;
            if (day < 1 || day > maxDay) {
                const el = document.createElement('div');
                el.className   = 'day-cell other-month';
                el.textContent = '';
                grid.appendChild(el);
            } else {
                grid.appendChild(makeCell(day, y, m, today));
            }
        }
    }

    const fbBtn = document.getElementById('fullMonthBtn');
    if (fbBtn) fbBtn.textContent = isFullMonth ? 'Week view' : 'Full month';
}

function goFullMonth() { isFullMonth = !isFullMonth; buildCalendar(); }
function prevMonth()   { calMonth--; if (calMonth < 0)  { calMonth = 11; calYear--; } buildCalendar(); }
function nextMonth()   { calMonth++; if (calMonth > 11) { calMonth = 0;  calYear++; } buildCalendar(); }

// ================================================================
// MONTH/YEAR PICKER
// ================================================================
function openMonthYearPicker() {
    const pickerYear = document.getElementById('pickerYear');
    pickerYear.innerHTML = '';
    for (let y = 2020; y <= 2035; y++) {
        const opt = document.createElement('option');
        opt.value      = y;
        opt.textContent = y;
        if (y === calYear) opt.selected = true;
        pickerYear.appendChild(opt);
    }
    document.getElementById('pickerMonth').value = calMonth;
    document.getElementById('monthYearModal').classList.remove('hidden');
}
function closeMonthYearPicker() {
    document.getElementById('monthYearModal').classList.add('hidden');
}
function applyMonthYear() {
    calMonth     = parseInt(document.getElementById('pickerMonth').value);
    calYear      = parseInt(document.getElementById('pickerYear').value);
    selectedDate = null;
    isFullMonth  = true;
    buildCalendar();
    closeMonthYearPicker();
}

// ================================================================
// SAVE ALARM
// ================================================================
async function saveAlarm() {
    const label = document.getElementById('alarmLabel').value.trim();
    if (!label) {
        document.getElementById('alarmLabel').style.borderColor = '#EF4444';
        document.getElementById('alarmLabel').focus();
        setTimeout(() => document.getElementById('alarmLabel').style.borderColor = '', 1200);
        return;
    }

    // Validate repeat days
    if (isRepeat && selectedDays.size === 0) {
        document.getElementById('repeatDayError').classList.remove('hidden');
        document.getElementById('daySelector').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Commit any in-progress input values
    commitTimeInput(document.getElementById('inputHour'),   'hour');
    commitTimeInput(document.getElementById('inputMinute'), 'minute');

    let h24 = hour;
    if (ampm === 'AM' && hour === 12) h24 = 0;
    else if (ampm === 'PM' && hour !== 12) h24 = hour + 12;
    const timeStr = `${String(h24).padStart(2,'0')}:${String(minute).padStart(2,'0')}:00`;

    // Payload
    const payload = {
        user_id          : USER_ID,
        label,
        time             : timeStr,
        is_repeat_weekly : isRepeat,
    };

    if (isRepeat) {
        // Send repeat_days as sorted array; no date needed (backend calculates it)
        payload.repeat_days = Array.from(selectedDays).sort((a,b)=>a-b);
    } else {
        // One-time: send specific date or today
        payload.date = selectedDate
            ? `${calYear}-${String(calMonth + 1).padStart(2,'0')}-${String(selectedDate).padStart(2,'0')}`
            : new Date().toISOString().split('T')[0];
    }

    const btn = document.getElementById('saveBtn');
    btn.style.opacity       = '0.6';
    btn.style.pointerEvents = 'none';

    try {
        let res, data;
        if (editingId) {
            res  = await fetch(`${API_BASE}/reminders/${editingId}`, {
                method  : 'PUT',
                headers : { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
                body    : JSON.stringify(payload)
            });
            data = await res.json();
            if (data.success) {
                const idx = reminders.findIndex(r => r.id === editingId);
                if (idx !== -1) reminders[idx] = { ...reminders[idx], ...data.data };
                showBanner('Alarm updated successfully!', 'success');
            } else {
                showBanner(data.message || 'Failed to update alarm.', 'error');
            }
        } else {
            res  = await fetch(`${API_BASE}/reminders`, {
                method  : 'POST',
                headers : { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
                body    : JSON.stringify(payload)
            });
            data = await res.json();
            if (data.success) {
                reminders.push(data.data);
                showBanner('Alarm created successfully!', 'success');
            } else {
                showBanner(data.message || 'Failed to create alarm.', 'error');
            }
        }
        renderList();
        closeSheet();
    } catch (err) {
        showBanner('Network error. Please try again.', 'error');
    } finally {
        btn.style.opacity       = '';
        btn.style.pointerEvents = '';
    }
}

// ================================================================
// HELPERS
// ================================================================
function formatTime12(timeStr) {
    if (!timeStr) return '—';
    const [hStr, mStr] = timeStr.split(':');
    let h = parseInt(hStr), m = parseInt(mStr);
    const period = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${String(h).padStart(2,'0')} : ${String(m).padStart(2,'0')} ${period}`;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d      = new Date(dateStr);
    const days   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${days[d.getDay()]}, ${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
}

function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showBanner(msg, type) {
    const el = document.getElementById('notifBanner');
    el.className = `notif-banner ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
    el.textContent = msg;
    el.classList.remove('hidden');
    clearTimeout(el._timer);
    el._timer = setTimeout(() => el.classList.add('hidden'), 3500);
}

// ================================================================
// INIT
// ================================================================
fetchReminders();
</script>

@include('partials.auth-guard')
</body>
</html>