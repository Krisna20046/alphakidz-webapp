{{-- resources/views/konsultan/rekap-diary.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rekap Diary Nanny</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: {
                            DEFAULT: '#7B1E5A',
                            light:   '#9B2E72',
                            dark:    '#4A0E35',
                            pale:    '#FFF9FB',
                            soft:    '#F3E6FA',
                            muted:   '#A2397B',
                        }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

        /* ── Desktop phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper {
                display: flex; align-items: flex-start; justify-content: center;
                min-height: 100vh; padding: 32px 0;
                background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 60%, #e8d5ee 100%);
            }
            .phone-frame {
                width: 390px; min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(123,30,90,0.25),
                            0 0 0 8px #1a0d14, 0 0 0 10px #2d1020;
                overflow: hidden; position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        .header-bg { background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%); }
        .header-wave { border-radius: 0 0 30px 30px; }

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
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg header-wave px-5 pt-10 pb-8 relative shrink-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white/5 translate-y-6 -translate-x-6"></div>
        <div class="relative flex items-center gap-4">
            <a href="{{ url()->previous() }}"
               class="flex-shrink-0 w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
                <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
            </a>
            <div>
                <p class="text-white/60 text-xs font-medium mb-0.5">Konsultan</p>
                <h1 class="text-white text-xl font-extrabold tracking-wide leading-tight">Rekap Diary Nanny</h1>
                <p class="text-white/50 text-xs mt-0.5">Generate laporan aktivitas nanny</p>
            </div>
        </div>
    </div>

    <!-- SCROLLABLE BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar" id="mainBody">

        <!-- ── STEP 1: PILIH NANNY ─────────────────────────────────────── -->
        <div id="step1" class="px-4 pt-5 pb-4">

            <!-- Step indicator -->
            <div class="flex items-center gap-2 mb-5 anim-up d1">
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-full bg-plum flex items-center justify-center">
                        <span class="text-white text-xs font-bold">1</span>
                    </div>
                    <span class="text-plum-dark text-sm font-bold">Pilih Nanny</span>
                </div>
                <div class="flex-1 h-px bg-plum-soft"></div>
                <div class="flex items-center gap-1.5 opacity-40">
                    <div class="w-7 h-7 rounded-full bg-plum-soft flex items-center justify-center">
                        <span class="text-plum text-xs font-bold">2</span>
                    </div>
                    <span class="text-plum-muted text-sm font-semibold">Filter</span>
                </div>
            </div>

            <!-- Search & info -->
            <div class="anim-up d2 mb-4">
                <div class="flex items-center gap-2 bg-white rounded-2xl border-2 border-plum-soft px-4 py-3 shadow-sm">
                    <ion-icon name="search-outline" style="font-size:18px;color:#A2397B;"></ion-icon>
                    <input id="searchInput"
                           type="text"
                           placeholder="Cari nama nanny..."
                           class="flex-1 text-sm text-plum-dark placeholder-plum-muted bg-transparent outline-none font-medium"
                           oninput="filterNannies(this.value)"
                    />
                    <span id="nannyCountBadge" class="hidden bg-plum-soft text-plum text-xs font-bold px-2 py-0.5 rounded-full badge-pop"></span>
                </div>
            </div>

            <!-- Nanny list container -->
            <div id="nannyList" class="anim-up d3 space-y-3"></div>

        </div>

        <!-- ── STEP 2: FILTER & GENERATE ─────────────────────────────── -->
        <div id="step2" class="hidden px-4 pt-5 pb-16">

            <!-- Step indicator -->
            <div class="flex items-center gap-2 mb-5">
                <div class="flex items-center gap-1.5 opacity-40">
                    <div class="w-7 h-7 rounded-full bg-plum-soft flex items-center justify-center">
                        <ion-icon name="checkmark" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <span class="text-plum-muted text-sm font-semibold">Pilih Nanny</span>
                </div>
                <div class="flex-1 h-px bg-plum"></div>
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-full bg-plum flex items-center justify-center">
                        <span class="text-white text-xs font-bold">2</span>
                    </div>
                    <span class="text-plum-dark text-sm font-bold">Filter</span>
                </div>
            </div>

            <!-- Selected nanny card -->
            <div id="selectedNannyCard" class="bg-white rounded-2xl border-2 border-plum-soft p-4 mb-5 flex items-center gap-4 shadow-sm">
                <div id="selAvatar" class="w-14 h-14 rounded-2xl bg-plum-soft flex items-center justify-center overflow-hidden border-2 border-plum-soft flex-shrink-0">
                    <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="selName" class="text-plum-dark text-base font-bold truncate">-</p>
                    <p id="selEmail" class="text-plum-muted text-xs truncate mt-0.5">-</p>
                    <div class="flex items-center gap-1 mt-1.5">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <span class="text-blue-600 text-xs font-semibold">Aktif Bertugas</span>
                    </div>
                </div>
                <button onclick="backToStep1()"
                        class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0 hover:bg-red-50 transition-colors">
                    <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </button>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl border-2 border-plum-soft p-5 mb-4 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                        <ion-icon name="filter" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <p class="text-plum-dark font-bold text-base">Filter Laporan</p>
                </div>

                <!-- Tanggal Mulai -->
                <div class="mb-4">
                    <label class="block text-plum-muted text-xs font-semibold mb-2 uppercase tracking-wider">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <div id="startDateWrapper" class="flex items-center gap-2 border-2 border-plum-soft rounded-xl px-4 py-3 bg-plum-pale focus-within:border-plum transition-colors">
                        <ion-icon name="calendar-outline" style="font-size:18px;color:#A2397B;"></ion-icon>
                        <input id="startDate" type="text" readonly
                               placeholder="YYYY-MM-DD"
                               class="flex-1 text-sm text-plum-dark bg-transparent outline-none font-semibold cursor-pointer"
                               onclick="openDatePicker('start')"
                        />
                        <ion-icon name="chevron-down" style="font-size:16px;color:#A2397B;" onclick="openDatePicker('start')" class="cursor-pointer"></ion-icon>
                    </div>
                    <p id="errStart" class="hidden text-red-500 text-xs mt-1 font-medium">Tanggal mulai harus diisi</p>
                </div>

                <!-- Tanggal Selesai -->
                <div class="mb-4">
                    <label class="block text-plum-muted text-xs font-semibold mb-2 uppercase tracking-wider">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <div id="endDateWrapper" class="flex items-center gap-2 border-2 border-plum-soft rounded-xl px-4 py-3 bg-plum-pale focus-within:border-plum transition-colors">
                        <ion-icon name="calendar-outline" style="font-size:18px;color:#A2397B;"></ion-icon>
                        <input id="endDate" type="text" readonly
                               placeholder="YYYY-MM-DD"
                               class="flex-1 text-sm text-plum-dark bg-transparent outline-none font-semibold cursor-pointer"
                               onclick="openDatePicker('end')"
                        />
                        <ion-icon name="chevron-down" style="font-size:16px;color:#A2397B;" onclick="openDatePicker('end')" class="cursor-pointer"></ion-icon>
                    </div>
                    <p id="errEnd" class="hidden text-red-500 text-xs mt-1 font-medium">Tanggal selesai harus diisi</p>
                </div>

                <!-- Kategori -->
                <div class="mb-5">
                    <label class="block text-plum-muted text-xs font-semibold mb-2 uppercase tracking-wider">
                        Kategori <span class="text-plum-muted font-normal italic normal-case">(opsional)</span>
                    </label>
                    <button id="kategoriBtn" onclick="openKategoriModal()"
                            class="w-full flex items-center justify-between border-2 border-plum-soft rounded-xl px-4 py-3 bg-plum-pale hover:border-plum transition-colors">
                        <div class="flex items-center gap-2">
                            <ion-icon name="list" style="font-size:18px;color:#A2397B;"></ion-icon>
                            <span id="kategoriLabel" class="text-sm text-plum-muted font-medium">Semua Kategori</span>
                        </div>
                        <ion-icon name="chevron-down" style="font-size:16px;color:#A2397B;"></ion-icon>
                    </button>
                </div>

                <!-- Export info -->
                <div class="flex items-start gap-3 bg-blue-50 rounded-xl p-3 border border-blue-100">
                    <ion-icon name="information-circle" style="font-size:20px;color:#3B82F6;" class="mt-0.5 flex-shrink-0"></ion-icon>
                    <p class="text-blue-700 text-xs font-medium leading-relaxed">
                        Laporan akan di-export dalam format <strong>Excel (.xlsx)</strong> dan otomatis terunduh
                    </p>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="bg-amber-50 rounded-2xl border-2 border-amber-100 p-4 mb-5">
                <div class="flex items-center gap-2 mb-3">
                    <ion-icon name="bulb-outline" style="font-size:18px;color:#D97706;"></ion-icon>
                    <p class="text-amber-700 font-bold text-sm">Informasi</p>
                </div>
                <ul class="space-y-1.5 text-xs text-plum-dark leading-relaxed">
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> Laporan mencakup semua diary dalam periode yang dipilih</li>
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> Data dapat difilter berdasarkan kategori aktivitas</li>
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> Pastikan periode tanggal sudah benar sebelum generate</li>
                    <li class="flex gap-2"><span class="text-amber-500 flex-shrink-0">•</span> File Excel siap digunakan untuk analisis lanjutan</li>
                </ul>
            </div>

            <!-- Generate Button -->
            <button id="generateBtn" onclick="handleGenerate()"
                    class="w-full flex items-center justify-center gap-3 bg-plum text-white rounded-2xl py-4 font-bold text-base shadow-lg shadow-plum/30 hover:bg-plum-light active:scale-95 transition-all mb-6">
                <ion-icon name="download-outline" style="font-size:22px;"></ion-icon>
                <span>Generate &amp; Download Laporan</span>
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
    { value: '',        label: 'Semua Kategori',  icon: 'apps-outline'    },
    { value: 'makan',   label: 'Makan',           icon: 'fast-food-outline' },
    { value: 'tidur',   label: 'Tidur',           icon: 'moon-outline'    },
    { value: 'main',    label: 'Main',            icon: 'game-controller-outline' },
    { value: 'belajar', label: 'Belajar',         icon: 'book-outline'    },
    { value: 'mandi',   label: 'Mandi',           icon: 'water-outline'   },
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
        <div class="bg-white rounded-2xl border-2 border-plum-soft p-4 flex items-center gap-4">
            <div class="skeleton w-14 h-14 rounded-2xl flex-shrink-0"></div>
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
                <div class="float-anim w-20 h-20 rounded-full bg-plum-soft flex items-center justify-center mb-4">
                    <ion-icon name="person-outline" style="font-size:34px;color:#E0BBE4;"></ion-icon>
                </div>
                <p class="text-plum-dark font-bold text-base mb-1">Tidak ada nanny aktif</p>
                <p class="text-plum-muted text-sm">Nanny aktif bertugas akan muncul di sini</p>
            </div>`;
        return;
    }

    container.innerHTML = list.map((item, i) => `
        <button onclick="selectNanny(${item.id})"
                class="card-press w-full text-left bg-white rounded-2xl border-2 border-plum-soft p-4 flex items-center gap-4 shadow-sm hover:border-plum/40 hover:shadow-md transition-all fade-in"
                style="animation-delay:${i * 0.06}s">
            <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-plum-soft flex-shrink-0 bg-plum-soft flex items-center justify-center">
                ${item.foto
                    ? `<img src="${item.foto}" class="w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                       <ion-icon name="person" style="font-size:26px;color:#7B1E5A;display:none;" class="w-full h-full items-center justify-center"></ion-icon>`
                    : `<ion-icon name="person" style="font-size:26px;color:#7B1E5A;"></ion-icon>`
                }
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-plum-dark font-bold text-sm truncate">${item.name}</p>
                <p class="text-plum-muted text-xs truncate mt-0.5">${item.email}</p>
                <div class="flex items-center gap-1 mt-1.5">
                    <span class="inline-flex items-center gap-1 bg-blue-50 border border-blue-100 rounded-full px-2 py-0.5 text-blue-600 text-xs font-semibold">
                        <ion-icon name="time-outline" style="font-size:11px;"></ion-icon>
                        Aktif Bertugas
                    </span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                <ion-icon name="chevron-forward" style="font-size:16px;color:#7B1E5A;"></ion-icon>
            </div>
        </button>
    `).join('');
}

function filterNannies(query) {
    const q = query.toLowerCase().trim();
    const filtered = q ? allNannies.filter(n =>
        n.name.toLowerCase().includes(q) || n.email.toLowerCase().includes(q)
    ) : allNannies;
    renderNannies(filtered);
    updateCountBadge(filtered.length);
}

function updateCountBadge(count) {
    const badge = document.getElementById('nannyCountBadge');
    const n = count !== undefined ? count : allNannies.length;
    if (n > 0) {
        badge.textContent = n + ' nanny';
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
    document.getElementById('selEmail').textContent = selectedNanny.email;

    const avatarEl = document.getElementById('selAvatar');
    if (selectedNanny.foto) {
        avatarEl.innerHTML = `<img src="${selectedNanny.foto}" class="w-full h-full object-cover" />`;
    } else {
        avatarEl.innerHTML = `<ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>`;
    }

    // Default date range: last 30 days
    const now   = new Date();
    const start = new Date(); start.setDate(start.getDate() - 30);
    document.getElementById('startDate').value = formatDate(start);
    document.getElementById('endDate').value   = formatDate(now);

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
    document.getElementById('kategoriLabel').className =
        val ? 'text-sm text-plum-dark font-semibold' : 'text-sm text-plum-muted font-medium';
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