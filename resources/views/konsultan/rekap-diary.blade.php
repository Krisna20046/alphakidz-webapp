{{-- resources/views/konsultan/rekap-diary.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rekap Diary Nanny</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: {
                            DEFAULT: '#8B46D3',
                            light:   '#9F58F8',
                            dark:    '#1E1B2E',
                            pale:    '#F8F7FF',
                            soft:    '#EDE9FE',
                            muted:   '#8B86A5',
                        }
                    },
                    fontFamily: { sans: ['Nunito', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Nunito', sans-serif; background: #E5E2F5; }

        /* ── Desktop phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex; align-items: flex-start; justify-content: center;
                min-height: 100vh; padding: 32px 0;
                background: #E5E2F5;
            }
            .phone-frame {
                width: 390px; min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(124,58,237,0.28),
                            0 0 0 8px #1a1030, 0 0 0 10px #2d1a50;
                overflow: hidden; position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        .header-bg { background: radial-gradient(circle at top left, rgba(255,255,255,0.14), transparent 34%), linear-gradient(135deg, #A855F7 0%, #8B46D3 40%, #9F58F8 100%); }
        .header-wave { border-radius: 0; }

        /* Skeleton shimmer */
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position:  400px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f0dcea 25%, #fce8f5 50%, #f0dcea 75%);
            background-size: 400px 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 12px;
        }

        /* Slide-up */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up { animation: slideUp 0.35s ease forwards; }
        .anim-up.d1 { animation-delay: 0.05s; opacity: 0; }
        .anim-up.d2 { animation-delay: 0.12s; opacity: 0; }
        .anim-up.d3 { animation-delay: 0.20s; opacity: 0; }
        .anim-up.d4 { animation-delay: 0.28s; opacity: 0; }

        /* Card press */
        .card-press { transition: transform .15s ease, box-shadow .15s ease; }
        .card-press:active { transform: scale(0.97); }

        /* No scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Float empty */
        @keyframes floatAnim {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatAnim 3s ease-in-out infinite; }

        /* Fade in */
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        .fade-in { animation: fadeIn .3s ease forwards; }

        /* Step tabs */
        .tab-btn { transition: all .2s ease; }
        .tab-btn.active {
            background: #7B1E5A;
            color: #fff;
            box-shadow: 0 4px 12px rgba(123,30,90,0.3);
        }

        /* Progress bar */
        @keyframes progressFill {
            from { width: 0%; }
        }
        .progress-bar { animation: progressFill .6s ease forwards; }

        /* Download pulse */
        @keyframes dlPulse {
            0%,100% { opacity:1; }
            50% { opacity:.6; }
        }
        .dl-pulse { animation: dlPulse 1s ease-in-out infinite; }

        /* Modal slide */
        @keyframes modalSlideUp {
            from { transform: translateY(100%); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        .modal-slide { animation: modalSlideUp .3s cubic-bezier(.4,0,.2,1); }

        /* Date picker scroll */
        .picker-col { overflow-y: auto; max-height: 200px; scroll-snap-type: y mandatory; }
        .picker-col::-webkit-scrollbar { display: none; }
        .picker-item { scroll-snap-align: start; }

        /* Badge */
        @keyframes badgePop {
            0%   { transform: scale(0); }
            80%  { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .badge-pop { animation: badgePop .3s ease forwards; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .shell-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,247,255,0.98) 58%, rgba(212,186,239,0.48) 100%);
            border-radius: 50px 50px 0 0;
            box-shadow: 0 -10px 30px rgba(139, 70, 211, 0.08);
        }
        .field-card {
            background: rgba(255,255,255,0.86);
            border: 1px solid #D8CAEF;
            border-radius: 10px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .field-card:focus-within, .field-card.active {
            border-color: #8B46D3;
            box-shadow: 0 0 0 3px rgba(139, 70, 211, 0.10);
        }
        .nanny-card { transition: transform .15s ease; }
        .nanny-card:active { transform: scale(0.98); }
        .category-chip {
            border: 1.5px solid transparent;
            transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
        }
        .category-chip:active { transform: scale(0.96); }
        .category-chip.active {
            border-color: #8B46D3;
            box-shadow: 0 6px 16px rgba(139,70,211,0.14);
        }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="phone-wrapper">
<div class="phone-frame bg-[#F0EDFB] flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-6 pt-[14px] text-white text-xs font-bold bg-[#8B46D3]">
        <span id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg header-wave px-[20px] pt-[54px] pb-[102px] relative shrink-0 overflow-hidden">
        <div class="absolute inset-0 opacity-25 bg-[url('/assets/bg-texture.png')] bg-cover bg-center"></div>
        <div class="relative z-10 flex items-start gap-3">
            <a href="{{ url()->previous() }}"
               class="mt-0.5 flex-shrink-0 w-10 h-10 rounded-full bg-white/20 border border-white/25 flex items-center justify-center">
                <ion-icon name="arrow-back" style="font-size:18px;color:#fff;"></ion-icon>
            </a>
            <div class="min-w-0">
                <h1 class="text-white text-[18px] font-extrabold leading-tight">Diary Recap</h1>
                <p class="text-white/70 text-[11px] font-semibold mt-0.5 leading-[1.35]">Generate Nanny Diary Report</p>
            </div>
        </div>
    </div>

    <!-- SCROLLABLE BODY -->
    <div class="shell-card flex-1 overflow-y-auto hide-scrollbar px-[18px] pt-[18px] pb-28 -mt-[58px] relative z-20" id="mainBody">

        <!-- ── STEP 1: PILIH NANNY ─────────────────────────────────────── -->
        <div id="step1" class="pb-4">

            <!-- Step indicator -->
            <div class="flex items-center justify-between mb-3 anim-up d1">
                <h2 class="text-[#5A556E] text-[18px] font-extrabold">Nanny's Assignment</h2>
                <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                    <span id="nannyCountBadge" class="hidden text-[#8B46D3] text-xs font-bold badge-pop"></span>
                </div>
            </div>

            <!-- Search & info -->
            <div class="anim-up d2 mb-4">
                <div class="flex items-center bg-[#F4F4F4] rounded-full px-4 py-2.5 border border-[#DDD6EF] gap-2 transition-all focus-within:border-[#8B46D3] focus-within:shadow-[0_0_0_3px_rgba(139,70,211,0.14)]">
                    <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                    <input id="searchInput"
                           type="text"
                           placeholder="Search nanny...."
                           class="flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent outline-none"
                           oninput="filterNannies(this.value)"
                    />
                </div>
            </div>

            <!-- Nanny list container -->
            <div id="nannyList" class="anim-up d3 flex flex-col gap-2"></div>

        </div>

        <!-- ── STEP 2: FILTER & GENERATE ─────────────────────────────── -->
        <div id="step2" class="hidden pb-16">

            <!-- Step indicator -->
            <div class="mb-3">
                <button onclick="backToStep1()" class="inline-flex items-center gap-1.5 text-[#8B46D3] text-[12px] font-extrabold">
                    <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
                    Pilih nanny lain
                </button>
            </div>

            <!-- Selected nanny card -->
            <div id="selectedNannyCard" class="bg-white/95 rounded-[14px] px-4 pt-5 pb-4 mb-4 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]">
                <div class="flex flex-col items-center text-center">
                    <div id="selAvatar" class="w-[78px] h-[78px] rounded-full bg-[#F3EEFC] flex items-center justify-center overflow-hidden border-[3px] border-[#D8CAEF] shadow-[0_4px_10px_rgba(139,70,211,0.16)]">
                        <ion-icon name="person" style="font-size:30px;color:#8B46D3;"></ion-icon>
                    </div>
                    <p id="selName" class="text-[#1E1B2E] text-[15px] font-extrabold mt-3 truncate max-w-full">-</p>
                    <span class="mt-1 inline-flex items-center rounded-full bg-[#DCFCE7] px-2 py-0.5 text-[9px] font-black text-[#166534]">ACTIVE</span>
                    <div class="h-px w-full bg-[#E7DCF8] my-4"></div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-[5px] bg-[#EDE9FE] flex items-center justify-center flex-shrink-0"><ion-icon name="card-outline" style="font-size:15px;color:#8B46D3;"></ion-icon></div>
                        <div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">Assignment ID</p><p id="selAssignmentId" class="text-[#1E1B2E] text-[12px] font-extrabold break-words">-</p></div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-[5px] bg-[#FDE7EF] flex items-center justify-center flex-shrink-0"><ion-icon name="mail-outline" style="font-size:15px;color:#EC4899;"></ion-icon></div>
                        <div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">Email</p><p id="selEmail" class="text-[#1E1B2E] text-[12px] font-extrabold break-words">-</p></div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-[5px] bg-[#E8ECFF] flex items-center justify-center flex-shrink-0"><ion-icon name="call-outline" style="font-size:15px;color:#4F46E5;"></ion-icon></div>
                        <div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">Phone Number</p><p id="selPhone" class="text-[#1E1B2E] text-[12px] font-extrabold break-words">-</p></div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white/95 rounded-[14px] border border-[#EAE6F5] px-4 py-4 mb-4 shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
                <div class="flex items-center gap-1.5 mb-3">
                    <ion-icon name="options-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                    <p class="text-[#1E1B2E] font-extrabold text-[13px]">Filter</p>
                </div>

                <!-- Tanggal Mulai -->
                <div class="mb-4">
                    <label class="block text-[#1E1B2E] text-[12px] font-bold mb-1.5">
                        Start Date
                    </label>
                    <div id="startDateWrapper" class="field-card flex items-center gap-2 px-3 py-2.5">
                        <input id="startDate" type="text" readonly
                               placeholder="YYYY-MM-DD"
                               class="flex-1 text-[12px] text-[#7C748F] bg-transparent outline-none font-extrabold cursor-pointer"
                               onclick="openDatePicker('start')"
                        />
                        <ion-icon name="calendar-outline" style="font-size:15px;color:#7C748F;" onclick="openDatePicker('start')" class="cursor-pointer"></ion-icon>
                    </div>
                    <p id="errStart" class="hidden text-red-500 text-xs mt-1 font-medium">Tanggal mulai harus diisi</p>
                </div>

                <!-- Tanggal Selesai -->
                <div class="mb-4">
                    <label class="block text-[#1E1B2E] text-[12px] font-bold mb-1.5">
                        End Date
                    </label>
                    <div id="endDateWrapper" class="field-card flex items-center gap-2 px-3 py-2.5">
                        <input id="endDate" type="text" readonly
                               placeholder="YYYY-MM-DD"
                               class="flex-1 text-[12px] text-[#7C748F] bg-transparent outline-none font-extrabold cursor-pointer"
                               onclick="openDatePicker('end')"
                        />
                        <ion-icon name="calendar-outline" style="font-size:15px;color:#7C748F;" onclick="openDatePicker('end')" class="cursor-pointer"></ion-icon>
                    </div>
                    <p id="errEnd" class="hidden text-red-500 text-xs mt-1 font-medium">Tanggal selesai harus diisi</p>
                </div>

                <!-- Kategori -->
                <div class="mb-5">
                    <label class="block text-[#1E1B2E] text-[12px] font-bold mb-2">
                        Activity Categories
                    </label>
                    <div id="categoryChips" class="flex flex-wrap gap-2"></div>
                    <span id="kategoriLabel" class="hidden">Semua Kategori</span>
                </div>

            </div>

            <!-- Tips Card -->
            <div class="bg-[#FFF7ED] rounded-[10px] border border-[#FED7AA] p-4 mb-5">
                <div class="flex items-center gap-2 mb-3">
                    <ion-icon name="information-circle-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                    <p class="text-[#D97706] font-extrabold text-[11px] uppercase tracking-wide">Information</p>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 text-[11px] text-[#7C748F] font-semibold leading-snug">
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> Laporan mencakup semua diary dalam periode yang dipilih</li>
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> Data dapat difilter berdasarkan kategori aktivitas</li>
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> Pastikan periode tanggal sudah benar sebelum generate</li>
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> File Excel siap digunakan untuk analisis lanjutan</li>
                </ul>
            </div>

            <!-- Generate Button -->
            <button id="generateBtn" onclick="handleGenerate()"
                    class="w-full flex items-center justify-center gap-2 h-[44px] bg-[#8B46D3] text-white rounded-[8px] font-extrabold text-[13px] shadow-[0_8px_18px_rgba(139,70,211,0.32)] active:scale-[0.98] transition-all mb-6">
                <ion-icon name="download-outline" style="font-size:16px;"></ion-icon>
                <span>Generate &amp; Download Now</span>
            </button>

        </div>

    </div><!-- /mainBody -->

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'rekap'])

</div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     DATE PICKER MODAL
═══════════════════════════════════════════════════════════════ -->
<div id="datePickerModal"
     class="fixed inset-0 z-50 flex flex-col justify-end items-center bg-black/50 hidden"
     onclick="closeDatePickerOnOverlay(event)">
    <div class="modal-slide w-full sm:max-w-[390px] bg-white rounded-t-3xl shadow-2xl overflow-hidden">

        <!-- Handle -->
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full bg-plum-soft"></div>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b-2 border-plum-soft">
            <div class="flex items-center gap-2">
                <ion-icon name="calendar" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                <p id="dpTitle" class="text-plum-dark font-bold text-lg">Pilih Tanggal</p>
            </div>
            <button onclick="closeDatePicker()" class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center">
                <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>

        <!-- Preview -->
        <div class="mx-5 mt-4 mb-2 bg-plum-soft rounded-2xl py-3 px-4 flex items-center justify-center gap-2">
            <ion-icon name="calendar-number-outline" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            <span id="dpPreview" class="text-plum font-bold text-base">-</span>
        </div>

        <!-- Picker cols -->
        <div class="flex gap-2 px-5 pt-2 pb-2">
            <!-- Tahun -->
            <div class="flex-1">
                <p class="text-plum-muted text-xs font-bold text-center mb-2 uppercase tracking-wider">Tahun</p>
                <div id="yearCol" class="picker-col bg-plum-pale rounded-2xl border-2 border-plum-soft"></div>
            </div>
            <!-- Bulan -->
            <div class="flex-1">
                <p class="text-plum-muted text-xs font-bold text-center mb-2 uppercase tracking-wider">Bulan</p>
                <div id="monthCol" class="picker-col bg-plum-pale rounded-2xl border-2 border-plum-soft"></div>
            </div>
            <!-- Tanggal -->
            <div class="flex-1">
                <p class="text-plum-muted text-xs font-bold text-center mb-2 uppercase tracking-wider">Tgl</p>
                <div id="dayCol" class="picker-col bg-plum-pale rounded-2xl border-2 border-plum-soft"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 px-5 py-4 border-t-2 border-plum-soft">
            <button onclick="closeDatePicker()"
                    class="flex-1 py-3.5 rounded-xl bg-plum-soft text-plum font-bold text-sm hover:bg-plum/10 transition-colors">
                Batal
            </button>
            <button onclick="confirmDatePicker()"
                    class="flex-1 py-3.5 rounded-xl bg-plum text-white font-bold text-sm flex items-center justify-center gap-2 hover:bg-plum-light transition-colors shadow-lg shadow-plum/30">
                <ion-icon name="checkmark" style="font-size:18px;"></ion-icon>
                Pilih
            </button>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     KATEGORI MODAL
═══════════════════════════════════════════════════════════════ -->
<div id="kategoriModal"
     class="fixed inset-0 z-50 flex flex-col justify-end items-center bg-black/50 hidden"
     onclick="closeKategoriOnOverlay(event)">
    <div class="modal-slide w-full sm:max-w-[390px] bg-white rounded-t-3xl shadow-2xl overflow-hidden">
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full bg-plum-soft"></div>
        </div>
        <div class="flex items-center justify-between px-5 py-4 border-b-2 border-plum-soft">
            <div class="flex items-center gap-2">
                <ion-icon name="filter" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                <p class="text-plum-dark font-bold text-lg">Pilih Kategori</p>
            </div>
            <button onclick="closeKategoriModal()" class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center">
                <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div id="kategoriList" class="px-4 py-4 space-y-2 overflow-y-auto max-h-72"></div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     LOADING / PROGRESS OVERLAY
═══════════════════════════════════════════════════════════════ -->
<div id="loadingOverlay" class="fixed inset-0 z-[60] hidden flex-col items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl p-8 w-72 flex flex-col items-center shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-plum-soft flex items-center justify-center mb-4">
            <ion-icon name="document-text" style="font-size:32px;color:#7B1E5A;" class="dl-pulse"></ion-icon>
        </div>
        <p id="loadingTitle" class="text-plum-dark font-bold text-lg mb-1">Generating...</p>
        <p id="loadingSubtitle" class="text-plum-muted text-sm mb-5 text-center">Mohon tunggu, sedang memproses laporan</p>
        <div class="w-full bg-plum-soft rounded-full h-2 overflow-hidden">
            <div id="progressBar" class="h-full bg-plum rounded-full progress-bar" style="width:0%"></div>
        </div>
        <p id="progressText" class="text-plum text-xs font-bold mt-2">0%</p>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     TOAST NOTIFICATION
═══════════════════════════════════════════════════════════════ -->
<div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[70] hidden max-w-xs w-[calc(100%-2rem)]">
    <div id="toastInner" class="flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-xl">
        <ion-icon id="toastIcon" name="checkmark-circle" style="font-size:20px;" class="flex-shrink-0 mt-0.5"></ion-icon>
        <div>
            <p id="toastTitle" class="font-bold text-sm"></p>
            <p id="toastMsg" class="text-xs mt-0.5 opacity-80"></p>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════════════ -->
<script>
// ── Config ────────────────────────────────────────────────────────────────────
const AUTH_TOKEN = "{{ session('token') }}";
const API_BASE   = "{{ rtrim(env('API_BASE_URL', ''), '/') }}";

// ── State ─────────────────────────────────────────────────────────────────────
let allNannies    = [];
let selectedNanny = null;
let selectedKategori = '';
let dpTarget      = 'start'; // 'start' | 'end'
let dpYear, dpMonth, dpDay;

const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];
const KATEGORI_OPTIONS = [
    { value: '',        label: 'All',         short: 'All',       icon: 'apps-outline',            bg: '#EDE9FE', color: '#8B46D3' },
    { value: 'makan',   label: 'Eat',         short: 'Eat',       icon: 'restaurant-outline',      bg: '#EDE9FE', color: '#8B46D3' },
    { value: 'tidur',   label: 'Sleep',       short: 'Sleep',     icon: 'moon-outline',            bg: '#FDE7EF', color: '#EC4899' },
    { value: 'main',    label: 'Play',        short: 'Play',      icon: 'game-controller-outline', bg: '#E8ECFF', color: '#4F46E5' },
    { value: 'belajar', label: 'Study',       short: 'Study',     icon: 'book-outline',            bg: '#FEF3C7', color: '#D97706' },
    { value: 'mandi',   label: 'Take Bath',   short: 'Take Bath', icon: 'water-outline',           bg: '#DCFCE7', color: '#16A34A' },
];

// ── Clock ─────────────────────────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const el = document.getElementById('statusTime');
    if (el) el.textContent = `${h}:${m}`;
}
updateClock();
setInterval(updateClock, 30000);

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(type, title, msg) {
    const toast    = document.getElementById('toast');
    const inner    = document.getElementById('toastInner');
    const iconEl   = document.getElementById('toastIcon');
    const titleEl  = document.getElementById('toastTitle');
    const msgEl    = document.getElementById('toastMsg');

    const cfg = {
        success: { bg: '#f0fdf4', border: '#bbf7d0', text: '#166534', icon: 'checkmark-circle', iconColor: '#16a34a' },
        error:   { bg: '#fef2f2', border: '#fecaca', text: '#991b1b', icon: 'close-circle',     iconColor: '#dc2626' },
        info:    { bg: '#eff6ff', border: '#bfdbfe', text: '#1e40af', icon: 'information-circle',iconColor: '#2563eb' },
    };
    const c = cfg[type] || cfg.info;

    inner.style.cssText = `background:${c.bg};border:2px solid ${c.border};color:${c.text};`;
    iconEl.name         = c.icon;
    iconEl.style.color  = c.iconColor;
    titleEl.textContent = title;
    msgEl.textContent   = msg || '';

    toast.classList.remove('hidden');
    toast.classList.add('fade-in');

    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('fade-in');
    }, 3500);
}

// ── Loading overlay ───────────────────────────────────────────────────────────
function showLoading(title='Generating...', subtitle='Sedang memproses laporan') {
    document.getElementById('loadingTitle').textContent    = title;
    document.getElementById('loadingSubtitle').textContent = subtitle;
    document.getElementById('progressBar').style.width     = '20%';
    document.getElementById('progressText').textContent    = '0%';
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('loadingOverlay').classList.add('flex');
}
function setProgress(pct) {
    document.getElementById('progressBar').style.width  = pct + '%';
    document.getElementById('progressText').textContent = pct + '%';
}
function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('loadingOverlay').classList.remove('flex');
}

// ── Fetch Nannies ─────────────────────────────────────────────────────────────
async function fetchNannies() {
    renderNannySkeleton();
    try {
        const res  = await fetch(`${API_BASE}/konsultan-nanny`, {
            headers: {
                'Accept':        'application/json',
                'Content-Type':  'application/json',
                'Authorization': `Bearer ${AUTH_TOKEN}`,
            }
        });
        const data = await res.json();

        if (data.status === 'success' && Array.isArray(data.data)) {
            allNannies = data.data.filter(n => n.is_assigned && n.assignment_status === 'active');
        } else {
            allNannies = [];
        }
    } catch (e) {
        console.error('Fetch nannies error:', e);
        allNannies = [];
        showToast('error', 'Gagal memuat', 'Tidak dapat terhubung ke server');
    }

    renderNannies(allNannies);
    updateCountBadge();
}

function renderNannySkeleton() {
    const list = document.getElementById('nannyList');
    list.innerHTML = Array.from({length: 3}).map(() => `
        <div class="bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5] flex items-center gap-3">
            <div class="skeleton w-[50px] h-[50px] rounded-[8px] flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="skeleton h-4 w-32 rounded"></div>
                <div class="skeleton h-3 w-44 rounded"></div>
                <div class="skeleton h-3 w-20 rounded"></div>
            </div>
        </div>
    `).join('');
}

function renderNannies(list) {
    const container = document.getElementById('nannyList');

    if (list.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center py-12 text-center">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                    <ion-icon name="person-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                </div>
                <p class="text-[#1E1B2E] font-extrabold text-lg mb-2">Tidak ada nanny aktif</p>
                <p class="text-[#9CA3AF] text-sm">Nanny aktif bertugas akan muncul di sini</p>
            </div>`;
        return;
    }

    container.innerHTML = list.map((item, i) => `
        <button onclick="selectNanny(${item.id})"
                class="nanny-card w-full text-left bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5] fade-in"
                style="animation-delay:${i * 0.06}s">
            <div class="flex items-center gap-3">
                <div class="w-[50px] h-[50px] rounded-[8px] overflow-hidden flex-shrink-0 bg-[#F3F0FD] flex items-center justify-center">
                    ${item.foto
                        ? `<img src="${item.foto}" class="w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                           <div style="display:none" class="w-full h-full items-center justify-center"><ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon></div>`
                        : `<ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">${item.name || '-'}</p>
                        <span class="bg-[#DCFCE7] text-[#166534] text-[10px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0">ACTIVE</span>
                    </div>
                    <div class="flex items-center gap-1 mt-0.5">
                        <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                        <span class="text-[#1E1B2E] text-[12px] font-extrabold truncate">Nanny</span>
                    </div>
                    <p class="text-[#8B86A5] text-[11px] italic font-semibold mt-0.5 truncate">"${item.email || 'Siap dibuatkan rekap diary'}"</p>
                </div>
            </div>
        </button>
    `).join('');
}

function filterNannies(query) {
    const q = query.toLowerCase().trim();
    const filtered = q ? allNannies.filter(n =>
        String(n.name || '').toLowerCase().includes(q) || String(n.email || '').toLowerCase().includes(q)
    ) : allNannies;
    renderNannies(filtered);
    updateCountBadge(filtered.length);
}

function updateCountBadge(count) {
    const badge = document.getElementById('nannyCountBadge');
    const n = count !== undefined ? count : allNannies.length;
    if (n > 0) {
        badge.textContent = n + ' Nanny';
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function selectNanny(id) {
    selectedNanny = allNannies.find(n => n.id === id);
    if (!selectedNanny) return;
    showStep2();
}

// ── Step Navigation ───────────────────────────────────────────────────────────
function showStep1() {
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('mainBody').scrollTop = 0;
}

function showStep2() {
    // Populate nanny info
    document.getElementById('selName').textContent  = selectedNanny.name;
    document.getElementById('selEmail').textContent = selectedNanny.email || '-';
    document.getElementById('selAssignmentId').textContent = selectedNanny.assignment_id || selectedNanny.id_assignment || selectedNanny.id || '-';
    document.getElementById('selPhone').textContent = selectedNanny.no_hp || selectedNanny.phone || selectedNanny.nanny_no_hp || selectedNanny.nomor_hp || '-';

    const avatarEl = document.getElementById('selAvatar');
    if (selectedNanny.foto) {
        avatarEl.innerHTML = `<img src="${selectedNanny.foto}" class="w-full h-full object-cover" />`;
    } else {
        avatarEl.innerHTML = `<ion-icon name="person" style="font-size:30px;color:#8B46D3;"></ion-icon>`;
    }

    // Default date range: last 30 days
    const now   = new Date();
    const start = new Date(); start.setDate(start.getDate() - 30);
    document.getElementById('startDate').value = formatDate(start);
    document.getElementById('endDate').value   = formatDate(now);
    renderCategoryChips();

    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
    document.getElementById('mainBody').scrollTop = 0;
}

function backToStep1() {
    selectedNanny = null;
    showStep1();
    document.getElementById('searchInput').value = '';
    filterNannies('');
}

// ── Date Utils ────────────────────────────────────────────────────────────────
function formatDate(d) {
    const y  = d.getFullYear();
    const mo = String(d.getMonth() + 1).padStart(2,'0');
    const da = String(d.getDate()).padStart(2,'0');
    return `${y}-${mo}-${da}`;
}

function getDaysInMonth(year, month) {
    return new Date(year, month + 1, 0).getDate();
}

// ── Date Picker ───────────────────────────────────────────────────────────────
function openDatePicker(target) {
    dpTarget = target;
    const val = document.getElementById(target === 'start' ? 'startDate' : 'endDate').value;
    const d   = val ? new Date(val + 'T00:00:00') : new Date();
    dpYear    = d.getFullYear();
    dpMonth   = d.getMonth();
    dpDay     = d.getDate();

    document.getElementById('dpTitle').textContent = target === 'start' ? 'Tanggal Mulai' : 'Tanggal Selesai';

    buildDateCols();
    updateDpPreview();

    document.getElementById('datePickerModal').classList.remove('hidden');
    document.getElementById('datePickerModal').classList.add('flex');
}

function closeDatePicker() {
    document.getElementById('datePickerModal').classList.add('hidden');
    document.getElementById('datePickerModal').classList.remove('flex');
}

function closeDatePickerOnOverlay(e) {
    if (e.target === document.getElementById('datePickerModal')) closeDatePicker();
}

function confirmDatePicker() {
    const str = `${dpYear}-${String(dpMonth+1).padStart(2,'0')}-${String(dpDay).padStart(2,'0')}`;
    if (dpTarget === 'start') {
        document.getElementById('startDate').value = str;
        document.getElementById('errStart').classList.add('hidden');
        document.getElementById('startDateWrapper').classList.remove('border-red-400');
    } else {
        document.getElementById('endDate').value = str;
        document.getElementById('errEnd').classList.add('hidden');
        document.getElementById('endDateWrapper').classList.remove('border-red-400');
    }
    closeDatePicker();
}

function buildDateCols() {
    // Year col
    const yearCol = document.getElementById('yearCol');
    yearCol.innerHTML = '';
    const curYear = new Date().getFullYear();
    for (let y = curYear; y >= 2000; y--) {
        const btn = document.createElement('button');
        btn.className = `picker-item w-full py-2.5 text-sm font-semibold text-center transition-colors ${y === dpYear ? 'bg-plum text-white rounded-xl mx-1' : 'text-plum-dark hover:bg-plum-soft'}`;
        btn.textContent = y;
        btn.onclick = () => { dpYear = y; buildDateCols(); updateDpPreview(); };
        yearCol.appendChild(btn);
        if (y === dpYear) setTimeout(() => btn.scrollIntoView({ block: 'center', behavior: 'smooth' }), 50);
    }

    // Month col
    const monthCol = document.getElementById('monthCol');
    monthCol.innerHTML = '';
    MONTHS_ID.forEach((m, i) => {
        const btn = document.createElement('button');
        btn.className = `picker-item w-full py-2.5 text-xs font-semibold text-center transition-colors ${i === dpMonth ? 'bg-plum text-white rounded-xl mx-1' : 'text-plum-dark hover:bg-plum-soft'}`;
        btn.textContent = m;
        btn.onclick = () => {
            dpMonth = i;
            const maxD = getDaysInMonth(dpYear, dpMonth);
            if (dpDay > maxD) dpDay = maxD;
            buildDateCols(); updateDpPreview();
        };
        monthCol.appendChild(btn);
        if (i === dpMonth) setTimeout(() => btn.scrollIntoView({ block: 'center', behavior: 'smooth' }), 50);
    });

    // Day col
    const dayCol = document.getElementById('dayCol');
    dayCol.innerHTML = '';
    const maxDay = getDaysInMonth(dpYear, dpMonth);
    for (let d = 1; d <= maxDay; d++) {
        const btn = document.createElement('button');
        btn.className = `picker-item w-full py-2.5 text-sm font-semibold text-center transition-colors ${d === dpDay ? 'bg-plum text-white rounded-xl mx-1' : 'text-plum-dark hover:bg-plum-soft'}`;
        btn.textContent = d;
        btn.onclick = () => { dpDay = d; buildDateCols(); updateDpPreview(); };
        dayCol.appendChild(btn);
        if (d === dpDay) setTimeout(() => btn.scrollIntoView({ block: 'center', behavior: 'smooth' }), 50);
    }
}

function updateDpPreview() {
    document.getElementById('dpPreview').textContent =
        `${String(dpDay).padStart(2,'0')} ${MONTHS_ID[dpMonth]} ${dpYear}`;
}

// ── Kategori Modal ────────────────────────────────────────────────────────────
function renderCategoryChips() {
    const wrap = document.getElementById('categoryChips');
    if (!wrap) return;

    wrap.innerHTML = KATEGORI_OPTIONS.map(opt => {
        const active = selectedKategori === opt.value;
        return `
            <button type="button"
                    onclick="selectKategori('${opt.value}')"
                    class="category-chip ${active ? 'active' : ''} inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-extrabold"
                    style="background:${opt.bg};color:${opt.color};">
                <ion-icon name="${opt.icon}" style="font-size:11px;"></ion-icon>
                <span>${opt.short}</span>
            </button>
        `;
    }).join('');
}

function openKategoriModal() {
    const list = document.getElementById('kategoriList');
    list.innerHTML = KATEGORI_OPTIONS.map(opt => `
        <button onclick="selectKategori('${opt.value}')"
                class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl transition-all ${selectedKategori === opt.value ? 'bg-plum text-white shadow-lg shadow-plum/25' : 'bg-plum-pale hover:bg-plum-soft text-plum-dark'}">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl ${selectedKategori === opt.value ? 'bg-white/20' : 'bg-white'} flex items-center justify-center">
                    <ion-icon name="${opt.icon}" style="font-size:16px;color:${selectedKategori === opt.value ? '#fff' : '#7B1E5A'};"></ion-icon>
                </div>
                <span class="font-semibold text-sm">${opt.label}</span>
            </div>
            ${selectedKategori === opt.value
                ? `<div class="w-6 h-6 rounded-full bg-white/25 flex items-center justify-center">
                       <ion-icon name="checkmark" style="font-size:14px;color:#fff;"></ion-icon>
                   </div>`
                : ''}
        </button>
    `).join('');

    document.getElementById('kategoriModal').classList.remove('hidden');
    document.getElementById('kategoriModal').classList.add('flex');
}

function closeKategoriModal() {
    document.getElementById('kategoriModal').classList.add('hidden');
    document.getElementById('kategoriModal').classList.remove('flex');
}

function closeKategoriOnOverlay(e) {
    if (e.target === document.getElementById('kategoriModal')) closeKategoriModal();
}

function selectKategori(val) {
    selectedKategori = val;
    const opt = KATEGORI_OPTIONS.find(o => o.value === val);
    document.getElementById('kategoriLabel').textContent = opt ? opt.label : 'Semua Kategori';
    document.getElementById('kategoriLabel').className = 'hidden';
    renderCategoryChips();
    closeKategoriModal();
}

// ── Validate ──────────────────────────────────────────────────────────────────
function validateForm() {
    let valid = true;
    const start = document.getElementById('startDate').value;
    const end   = document.getElementById('endDate').value;

    if (!start) {
        document.getElementById('errStart').classList.remove('hidden');
        document.getElementById('startDateWrapper').classList.add('border-red-400');
        valid = false;
    } else {
        document.getElementById('errStart').classList.add('hidden');
        document.getElementById('startDateWrapper').classList.remove('border-red-400');
    }

    if (!end) {
        document.getElementById('errEnd').classList.remove('hidden');
        document.getElementById('endDateWrapper').classList.add('border-red-400');
        valid = false;
    } else {
        document.getElementById('errEnd').classList.add('hidden');
        document.getElementById('endDateWrapper').classList.remove('border-red-400');
    }

    if (start && end && new Date(start) > new Date(end)) {
        document.getElementById('errEnd').textContent = 'Tanggal selesai harus setelah tanggal mulai';
        document.getElementById('errEnd').classList.remove('hidden');
        document.getElementById('endDateWrapper').classList.add('border-red-400');
        valid = false;
    }

    return valid;
}

// ── Generate Report ───────────────────────────────────────────────────────────
async function handleGenerate() {
    if (!validateForm()) {
        showToast('error', 'Form tidak lengkap', 'Periksa kembali isian tanggal');
        return;
    }

    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    showLoading('Generating Laporan...', 'Sedang memproses data diary nanny');

    // Animate progress
    let prog = 20;
    const progInterval = setInterval(() => {
        prog = Math.min(prog + Math.random() * 15, 85);
        setProgress(Math.round(prog));
    }, 500);

    try {
        const fd = new FormData();
        fd.append('id_nanny',       selectedNanny.id);
        fd.append('tanggal_mulai',  document.getElementById('startDate').value);
        fd.append('tanggal_selesai',document.getElementById('endDate').value);
        fd.append('export',         'excel');
        if (selectedKategori) fd.append('kategori', selectedKategori);

        const res  = await fetch(`${API_BASE}/diary-for-konsultan`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${AUTH_TOKEN}`, 'Accept': 'application/json' },
            body: fd,
        });
        const data = await res.json();

        clearInterval(progInterval);
        setProgress(100);

        if (data.status === 'success' && data.data?.download_url) {
            setTimeout(() => {
                hideLoading();
                downloadFile(data.data.download_url, data.data.filename || 'rekap-diary.xlsx');
            }, 500);
        } else {
            hideLoading();
            showToast('error', 'Gagal generate', data.message || 'Terjadi kesalahan pada server');
        }
    } catch (e) {
        clearInterval(progInterval);
        hideLoading();
        console.error(e);
        showToast('error', 'Koneksi gagal', 'Tidak dapat terhubung ke server');
    } finally {
        btn.disabled = false;
    }
}

function downloadFile(url, filename) {
    showLoading('Mengunduh File...', 'File Excel sedang diunduh');

    const anchor = document.createElement('a');
    anchor.href     = url;
    anchor.download = filename;
    anchor.target   = '_blank';
    document.body.appendChild(anchor);
    anchor.click();
    document.body.removeChild(anchor);

    setTimeout(() => {
        hideLoading();
        showToast('success', 'Berhasil!', `File "${filename}" berhasil diunduh`);
    }, 1200);
}

// ── Init ──────────────────────────────────────────────────────────────────────
fetchNannies();
</script>
@include('partials.auth-guard')
</body>
</html>
