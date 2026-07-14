<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inventory Stock</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { screens: { sm: '1024px' } } } };
    </script>
    <script>
        // Force mobile layout on phones even when "Desktop Site" mode is active
        (function() {
            var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            var isPhoneScreen = window.screen.width <= 430 || window.screen.height <= 932;
            if (isTouchDevice && isPhoneScreen && window.innerWidth >= 1024) {
                var meta = document.querySelector('meta[name="viewport"]');
                if (meta) meta.content = 'width=430, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                var s = document.createElement('style');
                s.textContent = '.phone-wrapper{min-height:100vh!important;display:block!important;padding:0!important}.phone-frame{min-height:100vh!important;width:100%!important;border-radius:0!important;box-shadow:none!important}';
                document.head.appendChild(s);
            }
        })();
    </script>
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
        @keyframes tabSlide {
            from { opacity: 0; transform: translateX(10px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .stock-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .stock-card:hover { box-shadow: 0 4px 20px rgba(139,70,211,.13); }

        /* ── Tab Bar ── */
        .tab-bar {
            display: flex; gap: 6px;
            padding: 4px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            backdrop-filter: blur(8px);
        }
        .tab-btn {
            flex: 1; padding: 9px 8px;
            border-radius: 12px; border: none;
            font-family: 'Nunito', sans-serif;
            font-size: 12px; font-weight: 800;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 5px;
            transition: all 0.22s cubic-bezier(0.34,1.2,0.64,1);
            color: rgba(255,255,255,0.65);
            background: transparent;
            white-space: nowrap;
        }
        .tab-btn.active {
            background: white; color: #6C3FC5;
            box-shadow: 0 4px 16px rgba(108,63,197,0.22);
        }
        .tab-badge {
            background: #F97316; color: white;
            font-size: 9px; font-weight: 900;
            border-radius: 999px; padding: 1px 5px;
            min-width: 16px; text-align: center; line-height: 14px;
        }
        .tab-btn.active .tab-badge { background: #6C3FC5; }

        /* ── Toggle Switch ── */
        .toggle-switch {
            width: 48px; height: 26px;
            background: #D1D5DB; border-radius: 999px;
            position: relative; cursor: pointer;
            transition: background 0.25s ease; flex-shrink: 0;
        }
        .toggle-switch.active { background: #2635DA; }
        .toggle-switch.tog-disabled { opacity: 0.35; pointer-events: none; }
        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 20px; height: 20px;
            background: white; border-radius: 50%;
            top: 3px; left: 3px;
            transition: left 0.25s ease;
            box-shadow: 0 1px 4px rgba(0,0,0,0.18);
        }
        .toggle-switch.active::after { left: 25px; }

        /* ── Qty Stepper ── */
        .qty-btn {
            width: 32px; height: 32px; border-radius: 50%; border: none;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 20px; font-weight: 800;
            transition: transform 0.13s ease, opacity 0.13s ease;
        }
        .qty-btn:active { transform: scale(0.88); opacity: 0.7; }
        .qty-btn.btn-disabled { opacity: 0.3; pointer-events: none; }
        .qty-minus { background: #F97316; color: white; }
        .qty-plus  { background: #F97316; color: white; }
        .qty-val   { font-size: 18px; font-weight: 800; color: #1F2937; min-width: 28px; text-align: center; }

        /* ── Range Slider ── */
        .alert-slider {
            -webkit-appearance: none; width: 100%; height: 4px;
            border-radius: 999px; background: #E5E7EB; outline: none; cursor: pointer;
        }
        .alert-slider:disabled { opacity: 0.3; cursor: default; }
        .alert-slider::-webkit-slider-thumb {
            -webkit-appearance: none; width: 22px; height: 22px;
            border-radius: 50%; background: #F97316; cursor: pointer;
            box-shadow: 0 2px 8px rgba(249,115,22,.35);
            transition: transform 0.15s ease;
        }
        .alert-slider::-webkit-slider-thumb:active { transform: scale(1.2); }
        .alert-slider.active-track { background: linear-gradient(to right, #F97316 var(--val,10%), #E5E7EB var(--val,10%)); }

        /* ── Partner Pills ── */
        .partners-scroll { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px; }
        .partners-scroll::-webkit-scrollbar { display: none; }
        .assign-pill {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 14px; border-radius: 14px;
            background: white; cursor: pointer;
            transition: all 0.18s ease;
            border: 2px solid transparent; flex-shrink: 0;
        }
        .assign-pill.active { border-color: #6C3FC5; box-shadow: 0 4px 14px rgba(108,63,197,0.18); }
        .assign-pill:active { transform: scale(0.97); }
        .assign-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #EDE9FB; overflow: hidden;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* ── Readonly Banner ── */
        .readonly-banner {
            background: linear-gradient(135deg,#FEF3C7,#FDE68A);
            border-radius: 14px; padding: 10px 14px;
            display: flex; align-items: center; gap: 10px;
            font-size: 12px; font-weight: 700; color: #92400E;
        }

        /* ── Nanny Badge ── */
        .nanny-badge {
            display: inline-flex; align-items: center; gap: 3px;
            background: #EDE9FB; color: #6C3FC5;
            font-size: 10px; font-weight: 800;
            border-radius: 8px; padding: 2px 7px;
        }

        /* ── Add Item Dashed Button ── */
        .add-item-btn {
            border: 2px dashed #ffffff; border-radius: 18px;
            background: rgba(139,70,211,0.07);
            transition: background 0.18s ease, border-color 0.18s ease; cursor: pointer;
        }
        .add-item-btn:hover { background: rgba(139,70,211,0.12); border-color: #6C3FC5; }
        .add-item-btn:active { transform: scale(0.98); }

        /* ── Save Button ── */
        .btn-save-all {
            width: 100%; padding: 15px; border-radius: 16px;
            background: #ffffff; color: #6C3FC5;
            font-size: 16px; font-weight: 800;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            cursor: pointer; transition: opacity 0.18s ease, transform 0.15s ease; border: none;
        }
        .btn-save-all:active { opacity: 0.88; transform: scale(0.98); }

        /* ── Modal ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 50;
            display: flex; align-items: center; justify-content: center;
            animation: fadeIn 0.2s ease;
        }
        .modal-box {
            background: white; border-radius: 20px; padding: 28px 24px;
            width: calc(100% - 64px); max-width: 340px;
            animation: modalIn 0.28s cubic-bezier(0.34,1.4,0.64,1);
        }

        /* ── Bottom Sheet ── */
        .bottom-sheet-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 50;
            animation: fadeIn 0.2s ease;
        }
        .bottom-sheet-wrapper {
            position: fixed; bottom: 0; left: 0; right: 0;
            display: flex; justify-content: center; z-index: 51;
            pointer-events: none;
        }
        .bottom-sheet {
            width: 100%; max-width: 390px; background: white;
            border-radius: 28px 28px 0 0; padding: 24px 24px 40px;
            animation: sheetIn 0.32s cubic-bezier(0.32,0.72,0,1);
            max-height: 85vh; overflow-y: auto; pointer-events: auto;
        }
        .bottom-sheet::-webkit-scrollbar { display: none; }

        /* ── Input ── */
        .stock-input {
            width: 100%; border: 1.5px solid #E5E7EB; border-radius: 12px;
            padding: 12px 14px; font-size: 15px; font-family: 'Nunito', sans-serif;
            font-weight: 600; color: #1F2937; outline: none;
            transition: border-color 0.18s ease;
        }
        .stock-input:focus { border-color: #8B46D3; }
        .stock-input::placeholder { color: #9CA3AF; font-weight: 500; }

        /* ── Skeleton ── */
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position:  400px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
            background-size: 400px 100%; animation: shimmer 1.4s ease infinite; border-radius: 12px;
        }

        /* ── Banner ── */
        @keyframes bannerSlide {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notif-banner {
            animation: bannerSlide 0.3s ease; border-radius: 12px;
            padding: 12px 16px; font-size: 14px; font-weight: 600; margin-bottom: 12px;
        }

        /* ── Misc ── */
        .product-icon-wrap {
            width: 48px; height: 48px; border-radius: 12px; overflow: hidden;
            background: #FEF3C7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stock-row {
            background: #F9F8FF; border-radius: 12px; padding: 10px 14px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .empty-state {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 48px 24px; gap: 10px; text-align: center;
        }
        .tab-panel { animation: tabSlide 0.25s ease; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#8B46D3] bg-[url('/assets/bg-texture-full.png')] bg-cover bg-center min-h-screen flex flex-col relative">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <span class="iconify" data-icon="material-symbols:signal-cellular-alt" style="font-size:16px;color:white;"></span>
            <span class="iconify" data-icon="material-symbols:wifi"               style="font-size:16px;color:white;"></span>
            <span class="iconify" data-icon="material-symbols:battery-full"       style="font-size:16px;color:white;"></span>
        </div>
    </div>

    <!-- PURPLE HEADER -->
    <div class="anim delay-1 relative z-10 px-[24px] pt-[55px] pb-[20px]">
        <!-- Back + Title -->
        <div class="flex items-center gap-3 mb-5">
            <a href="{{ route('profil.index') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <span class="iconify" data-icon="material-symbols:arrow-back-rounded" style="font-size:18px;color:white;"></span>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Expense Tracking</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">Transparent recording of children's expenses</p>
            </div>
        </div>

        <!-- TAB BAR -->
        <div class="tab-bar anim delay-2">
            <button class="tab-btn active" id="tabMyStock" onclick="switchTab('my')">
                <span class="iconify" data-icon="material-symbols:inventory-2-rounded" style="font-size:14px;"></span>
                My Stock
            </button>
            <button class="tab-btn" id="tabSharedStock" onclick="switchTab('shared')">
                <span class="iconify" data-icon="material-symbols:people-rounded" style="font-size:14px;"></span>
                Shared Stock
                <span class="tab-badge" id="sharedBadge" style="display:none">0</span>
            </button>
            <button class="tab-btn" id="tabExpiry" onclick="switchTab('expiry')">
                <span class="iconify" data-icon="material-symbols:warning-outline-rounded" style="font-size:14px;"></span>
                Expiry Alerts
                <span class="tab-badge" id="expiryBadge" style="display:none">0</span>
            </button>
        </div>
    </div>

    <!-- SCROLLABLE BODY -->
    <div class="flex-1 overflow-y-auto px-[20px] pb-28 relative z-20 flex flex-col gap-4 hide-scrollbar" id="mainBody">

        <div id="notifBanner" class="hidden"></div>

        <!-- ══════════════════════════════════════════════ -->
        <!--  PANEL A — MY STOCK                           -->
        <!-- ══════════════════════════════════════════════ -->
        <div id="panelMyStock" class="tab-panel flex flex-col gap-4">

            <div class="bg-white/90 backdrop-blur-sm rounded-[18px] px-4 py-4 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-full bg-[#EDE9FB] flex items-center justify-center shrink-0">
                    <span class="iconify" data-icon="material-symbols:inventory-2-rounded" style="font-size:22px;color:#6C3FC5;"></span>
                </div>
                <div>
                    <p class="text-[#1F2937] text-[16px] font-extrabold">Inventory Status</p>
                    <p class="text-gray-400 text-xs font-semibold mt-0.5">Update the latest stock for children's needs.</p>
                </div>
            </div>

            <p class="text-white/80 text-[13px] font-bold px-1">Child Essentials Inventory</p>

            <div id="stockList" class="flex flex-col gap-3">
                <div class="bg-white rounded-[18px] p-4 skeleton h-[180px]"></div>
                <div class="bg-white rounded-[18px] p-4 skeleton h-[180px]"></div>
            </div>

            <div id="emptyMyStock" class="hidden empty-state">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:inventory-2-outline-rounded" style="font-size:32px;color:white;"></span>
                </div>
                <p class="text-white/80 font-bold text-base">No stock items yet</p>
                <p class="text-white/50 text-sm font-medium">Tap "Add New Item" to get started</p>
            </div>

            <div class="add-item-btn py-4 flex items-center justify-center gap-2" onclick="openAddSheet()">
                <span class="iconify" data-icon="material-symbols:add-circle-outline-rounded" style="font-size:20px;color:#ffffff;"></span>
                <span class="text-[#ffffff] text-[14px] font-extrabold">Add New Item</span>
            </div>

            <button class="btn-save-all" id="saveAllBtn" onclick="saveAllChanges()">
                <span class="iconify" data-icon="material-symbols:save-rounded" style="font-size:20px;"></span>
                Save All Changes
            </button>
        </div>

        <!-- ══════════════════════════════════════════════ -->
        <!--  PANEL B — SHARED STOCK                       -->
        <!-- ══════════════════════════════════════════════ -->
        <div id="panelSharedStock" class="tab-panel hidden flex flex-col gap-4">

            <!-- Loading skeleton -->
            <div id="sharedLoading" class="flex flex-col gap-3">
                <div class="bg-white rounded-[18px] p-4 skeleton h-[80px]"></div>
                <div class="bg-white rounded-[18px] p-4 skeleton h-[180px]"></div>
            </div>

            <!-- No connections -->
            <div id="sharedNoConn" class="hidden empty-state">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:link-off-rounded" style="font-size:32px;color:white;"></span>
                </div>
                <p class="text-white/80 font-bold text-base">No connections yet</p>
                <p class="text-white/50 text-sm text-center">Shared stock appears when you're connected with a nanny or majikan</p>
            </div>

            <!-- Connected content -->
            <div id="sharedContent" class="hidden flex flex-col gap-4">

                <!-- Info card -->
                <div class="bg-white/90 backdrop-blur-sm rounded-[18px] px-4 py-4 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-[#ede9fb] flex items-center justify-center shrink-0">
                        <span class="iconify" data-icon="material-symbols:group-rounded" style="font-size:22px;color:#8B46D3;"></span>
                    </div>
                    <div>
                        <p class="text-[#1F2937] text-[16px] font-extrabold">Shared Inventory</p>
                        <p class="text-gray-400 text-xs font-semibold mt-0.5" id="sharedInfoSub">Stock managed together</p>
                    </div>
                </div>

                <!-- Partner pills (horizontal scroll) -->
                <div id="partnerScroll" class="partners-scroll"></div>

                <!-- Read-only notice for majikan -->
                <div id="readonlyBanner" class="readonly-banner hidden">
                    <span class="iconify shrink-0" data-icon="material-symbols:visibility-rounded" style="font-size:18px;"></span>
                    <span>You can view this stock. Only the nanny can make changes.</span>
                </div>

                <p class="text-white/80 text-[13px] font-bold px-1">Shared Items</p>

                <div id="sharedStockList" class="flex flex-col gap-3">
                    <div class="bg-white rounded-[18px] p-4 skeleton h-[180px]"></div>
                </div>

                <div id="emptySharedItems" class="hidden empty-state">
                    <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="iconify" data-icon="material-symbols:inbox-rounded" style="font-size:28px;color:white;"></span>
                    </div>
                    <p class="text-white/70 font-bold text-sm">No shared items yet</p>
                    <p class="text-white/40 text-xs">Nanny can add items here</p>
                </div>

                <!-- Nanny-only actions -->
                <div id="nannyActions" class="hidden flex flex-col gap-3">
                    <div class="add-item-btn py-4 flex items-center justify-center gap-2" onclick="openSharedAddSheet()">
                        <span class="iconify" data-icon="material-symbols:add-circle-outline-rounded" style="font-size:20px;color:#ffffff;"></span>
                        <span class="text-[#ffffff] text-[14px] font-extrabold">Add Shared Item</span>
                    </div>
                    <button class="btn-save-all" id="saveSharedBtn" onclick="saveSharedChanges()">
                        <span class="iconify" data-icon="material-symbols:save-rounded" style="font-size:20px;"></span>
                        Save Shared Changes
                    </button>
                </div>

            </div><!-- /sharedContent -->
        </div><!-- /panelSharedStock -->

        <!-- ══════════════════════════════════════════════ -->
        <!--  PANEL C — EXPIRY ALERTS                      -->
        <!-- ══════════════════════════════════════════════ -->
        <div id="panelExpiry" class="tab-panel hidden flex flex-col gap-4">

            <!-- Loading -->
            <div id="expiryLoading" class="flex flex-col gap-3">
                <div class="bg-white rounded-[18px] p-4 skeleton h-[100px]"></div>
                <div class="bg-white rounded-[18px] p-4 skeleton h-[80px]"></div>
            </div>

            <!-- ═══ EXPIRED ═══ -->
            <div id="expiredSection" class="hidden flex flex-col gap-3">
                <div class="flex items-center gap-2 px-1">
                    <span class="w-3 h-3 rounded-full bg-red-500 shrink-0"></span>
                    <span class="text-white font-extrabold text-sm tracking-wide">Expired</span>
                    <span class="text-white/50 text-xs font-bold ml-auto" id="expiredCount">0 items</span>
                </div>
                <div id="expiredList" class="flex flex-col gap-2"></div>
            </div>

            <!-- ═══ EXPIRING SOON ═══ -->
            <div id="expiringSection" class="hidden flex flex-col gap-3">
                <div class="flex items-center gap-2 px-1">
                    <span class="w-3 h-3 rounded-full bg-amber-400 shrink-0"></span>
                    <span class="text-white font-extrabold text-sm tracking-wide">Expiring Soon</span>
                    <span class="text-white/50 text-xs font-bold ml-auto" id="expiringCount">0 items</span>
                </div>
                <div id="expiringList" class="flex flex-col gap-2"></div>
            </div>

            <!-- Empty state -->
            <div id="emptyExpiry" class="hidden empty-state">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:check-circle-outline-rounded" style="font-size:32px;color:white;"></span>
                </div>
                <p class="text-white/80 font-bold text-base">All items are fresh</p>
                <p class="text-white/50 text-sm font-medium">No expired or expiring items found</p>
            </div>

        </div><!-- /panelExpiry -->

    </div><!-- /mainBody -->

    @include('partials.bottom-nav', ['active' => 'profil'])
</div>
</div>

<!-- ============================================================ -->
<!--  MODAL: Renew Confirmation                                    -->
<!-- ============================================================ -->
<div id="updateModal" class="modal-overlay hidden">
    <div class="modal-box">
        <h2 class="text-[17px] font-extrabold text-gray-900 mb-2">Update Stock</h2>
        <p class="text-gray-500 text-sm font-medium leading-relaxed mb-6" id="updateMsg">Are you sure?</p>
        <div class="flex gap-3">
            <button onclick="closeUpdateModal()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold text-sm">
                <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:12px;color:white;"></span>
                </span>Cancel
            </button>
            <button onclick="confirmUpdate()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl text-white font-bold text-sm"
                style="background:linear-gradient(135deg,#22C55E,#16A34A);">
                <span class="iconify" data-icon="material-symbols:autorenew-rounded" style="font-size:16px;"></span>Update
            </button>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!--  MODAL: Delete Confirmation                                   -->
<!-- ============================================================ -->
<div id="deleteModal" class="modal-overlay hidden">
    <div class="modal-box">
        <h2 class="text-[17px] font-extrabold text-gray-900 mb-2">Delete Item</h2>
        <p class="text-gray-500 text-sm font-medium leading-relaxed mb-6" id="deleteMsg">Are you sure?</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()"
                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold text-sm">
                <span class="w-5 h-5 rounded-full bg-gray-400 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:12px;color:white;"></span>
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
<!--  BOTTOM SHEET: Add My Stock                                   -->
<!-- ============================================================ -->
<div id="sheetOverlay" class="bottom-sheet-overlay hidden" onclick="closeSheet()"></div>
<div class="bottom-sheet-wrapper hidden" id="sheetWrapper">
<div id="stockSheet" class="bottom-sheet">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="iconify" data-icon="material-symbols:add-circle-outline-rounded" style="font-size:20px;color:#6C3FC5;"></span>
            <h3 class="text-[16px] font-extrabold text-gray-900">Add Stock</h3>
        </div>
        <button onclick="closeSheet()" class="w-8 h-8 flex items-center justify-center text-gray-400">
            <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:20px;"></span>
        </button>
    </div>
    <div class="border-b border-gray-100 mb-5"></div>
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Children's Needs</label>
    <input type="text"    id="itemName" class="stock-input mb-5" placeholder="e.g. Diapers (Size 4)">
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Quantity</label>
    <input type="number"  id="itemQty"  class="stock-input mb-6" placeholder="e.g. 4" min="0">

    <!-- ═══ Expiry Fields ═══ -->
    <div class="border-t border-gray-100 pt-4 mb-4">
        <div class="flex items-center gap-1 mb-4">
            <span class="iconify" data-icon="material-symbols:calendar-clock-rounded" style="font-size:16px;color:#6C3FC5;"></span>
            <span class="text-[13px] font-extrabold text-gray-800">Expiry Info (optional)</span>
        </div>
        <label class="text-[13px] font-bold text-gray-700 mb-2 block">Product Description</label>
        <textarea id="itemDeskripsi" class="stock-input mb-4" placeholder="e.g. Size 4, 64 pcs per pack" rows="2" style="resize:none;"></textarea>
        <label class="text-[13px] font-bold text-gray-700 mb-2 block">Expiry Date</label>
        <input type="date" id="itemExpiryDate" class="stock-input mb-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-[13px] font-extrabold text-gray-800">Expiry Alert</p>
                <p class="text-[11px] text-gray-400 font-semibold">Notify me before expiry</p>
            </div>
            <div class="toggle-switch" id="expiryAlertToggle" onclick="toggleExpiryAlert()"></div>
        </div>
        <div id="expiryAlertSliderWrap" class="hidden">
            <label class="text-[13px] font-bold text-gray-700 mb-2 block">Notify <span id="expiryAlertDaysLabel">7</span> days before</label>
            <input type="range" id="expiryAlertDays"
                   class="alert-slider active-track"
                   min="1" max="30" value="7"
                   style="--val:23.3%;"
                   oninput="updateExpiryAlertDays(this)">
            <div class="flex justify-between text-[10px] text-gray-400 font-bold mt-1 px-0.5">
                <span>1 day</span>
                <span id="expiryAlertDaysVal">7 days</span>
            </div>
        </div>
    </div>

    <button class="btn-save-all" id="addStockBtn" onclick="submitAddStock()">
        <span class="iconify" data-icon="material-symbols:add-circle-rounded" style="font-size:18px;"></span>
        Add Stock
    </button>
</div>
</div>

<!-- ============================================================ -->
<!--  BOTTOM SHEET: Add Shared Stock (nanny only)                 -->
<!-- ============================================================ -->
<div id="sharedSheetOverlay" class="bottom-sheet-overlay hidden" onclick="closeSharedSheet()"></div>
<div class="bottom-sheet-wrapper hidden" id="sharedSheetWrapper">
<div id="sharedStockSheet" class="bottom-sheet">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="iconify" data-icon="material-symbols:people-rounded" style="font-size:20px;color:#F59E0B;"></span>
            <h3 class="text-[16px] font-extrabold text-gray-900">Add Shared Item</h3>
        </div>
        <button onclick="closeSharedSheet()" class="w-8 h-8 flex items-center justify-center text-gray-400">
            <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:20px;"></span>
        </button>
    </div>
    <div class="border-b border-gray-100 mb-5"></div>
    <div class="bg-[#FEF9EC] rounded-[12px] px-3 py-2 flex items-center gap-2 mb-5">
        <span class="iconify" data-icon="material-symbols:link-rounded" style="font-size:16px;color:#F59E0B;"></span>
        <span class="text-[12px] font-bold text-amber-700" id="sharedSheetInfo">Shared with —</span>
    </div>
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Item Name</label>
    <input type="text"   id="sharedItemName" class="stock-input mb-5" placeholder="e.g. Baby Formula">
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Quantity</label>
    <input type="number" id="sharedItemQty"  class="stock-input mb-6" placeholder="e.g. 2" min="0">
    <button class="btn-save-all" id="addSharedBtn" onclick="submitAddSharedStock()"
        style="background:linear-gradient(135deg,#F59E0B,#F97316);">
        <span class="iconify" data-icon="material-symbols:add-circle-rounded" style="font-size:18px;"></span>
        Add Shared Item
    </button>
</div>
</div>

<!-- ============================================================ -->
<!--  BOTTOM SHEET: Edit Stock (My Stock)                         -->
<!-- ============================================================ -->
<div id="editSheetOverlay" class="bottom-sheet-overlay hidden" onclick="closeEditSheet()"></div>
<div class="bottom-sheet-wrapper hidden" id="editSheetWrapper">
<div id="editStockSheet" class="bottom-sheet">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="iconify" data-icon="material-symbols:edit-outline-rounded" style="font-size:20px;color:#3B82F6;"></span>
            <h3 class="text-[16px] font-extrabold text-gray-900">Edit Stock</h3>
        </div>
        <button onclick="closeEditSheet()" class="w-8 h-8 flex items-center justify-center text-gray-400">
            <span class="iconify" data-icon="material-symbols:close-rounded" style="font-size:20px;"></span>
        </button>
    </div>
    <div class="border-b border-gray-100 mb-5"></div>
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Children's Needs</label>
    <input type="text"    id="editItemName" class="stock-input mb-5" placeholder="e.g. Diapers (Size 4)">
    <label class="text-[13px] font-bold text-gray-700 mb-2 block">Quantity</label>
    <input type="number"  id="editItemQty"  class="stock-input mb-6" placeholder="e.g. 4" min="0">

    <!-- ═══ Expiry Fields ═══ -->
    <div class="border-t border-gray-100 pt-4 mb-4">
        <div class="flex items-center gap-1 mb-4">
            <span class="iconify" data-icon="material-symbols:calendar-clock-rounded" style="font-size:16px;color:#3B82F6;"></span>
            <span class="text-[13px] font-extrabold text-gray-800">Expiry Info (optional)</span>
        </div>
        <label class="text-[13px] font-bold text-gray-700 mb-2 block">Product Description</label>
        <textarea id="editItemDeskripsi" class="stock-input mb-4" placeholder="e.g. Size 4, 64 pcs per pack" rows="2" style="resize:none;"></textarea>
        <label class="text-[13px] font-bold text-gray-700 mb-2 block">Expiry Date</label>
        <input type="date" id="editItemExpiryDate" class="stock-input mb-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-[13px] font-extrabold text-gray-800">Expiry Alert</p>
                <p class="text-[11px] text-gray-400 font-semibold">Notify me before expiry</p>
            </div>
            <div class="toggle-switch" id="editExpiryAlertToggle" onclick="toggleEditExpiryAlert()"></div>
        </div>
        <div id="editExpiryAlertSliderWrap" class="hidden">
            <label class="text-[13px] font-bold text-gray-700 mb-2 block">Notify <span id="editExpiryAlertDaysLabel">7</span> days before</label>
            <input type="range" id="editExpiryAlertDays"
                   class="alert-slider active-track"
                   min="1" max="30" value="7"
                   style="--val:23.3%;"
                   oninput="updateEditExpiryAlertDays(this)">
            <div class="flex justify-between text-[10px] text-gray-400 font-bold mt-1 px-0.5">
                <span>1 day</span>
                <span id="editExpiryAlertDaysVal">7 days</span>
            </div>
        </div>
    </div>

    <button class="btn-save-all" id="editStockBtn" onclick="submitEditStock()"
        style="background:linear-gradient(135deg,#3B82F6,#2563EB);color:white;">
        <span class="iconify" data-icon="material-symbols:save-rounded" style="font-size:18px;"></span>
        Save Changes
    </button>
</div>
</div>

<script>
// ================================================================
// CONFIG & STATE
// ================================================================
const API_BASE  = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "https://api.alpha-kidz.com/api")), "/") }}';
@php
    $resolvedUserId = session('user_id') ?: data_get(session('user'), 'id_user');
@endphp
const USER_ID   = @json($resolvedUserId);
const API_TOKEN = '{{ session("token") }}';

let activeTab        = 'my';

// My Stock state
let stockItems       = [];
let localQty         = {};
let localAlert       = {};
let localExpiry      = {}; // { [id]: { tanggal_expired, expiry_alert, expiry_alert_days } }

// Shared Stock state
let assignments      = [];
let activeAssignId   = null;
let activeAssignRole = null;
let sharedItems      = [];
let sharedLocalQty   = {};
let sharedLocalAlert = {};
let sharedLocalExpiry = {}; // { [id]: { tanggal_expired, expiry_alert, expiry_alert_days } }

// Expiry Alert state
let expiredItems     = [];
let expiringItems    = [];

// Add Stock sheet expiry state
let expiryAlertEnabled = false;
let expiryAlertDays    = 7;

// Modal state (tracks which mode: 'my' | 'shared')
let modalMode        = 'my';
let pendingUpdateId  = null;
let pendingDeleteId  = null;

// Edit state
let editMode         = false;
let editItemId       = null; // null = add mode, number = edit mode

// Status bar clock
setInterval(() => {
    const n = new Date(), el = document.getElementById('statusTime');
    if (el) el.textContent = `${n.getHours()}:${String(n.getMinutes()).padStart(2,'0')}`;
}, 10000);

// ================================================================
// TAB SWITCH
// ================================================================
function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tabMyStock').classList.toggle('active',      tab === 'my');
    document.getElementById('tabSharedStock').classList.toggle('active',  tab === 'shared');
    document.getElementById('tabExpiry').classList.toggle('active',       tab === 'expiry');
    document.getElementById('panelMyStock').classList.toggle('hidden',    tab !== 'my');
    document.getElementById('panelSharedStock').classList.toggle('hidden',tab !== 'shared');
    document.getElementById('panelExpiry').classList.toggle('hidden',     tab !== 'expiry');

    if (tab === 'shared' && assignments.length === 0) fetchAssignments();
    if (tab === 'expiry') fetchExpiryData();
}

// ================================================================
// ── MY STOCK ─────────────────────────────────────────────────────
// ================================================================
async function fetchStock() {
    try {
        const res  = await fetch(`${API_BASE}/stock/${USER_ID}`, {
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        stockItems = data.data || [];
        localQty = {}; localAlert = {}; localExpiry = {};
        stockItems.forEach(item => {
            localQty[item.id]   = item.quantity ?? 0;
            localAlert[item.id] = { enabled: item.low_stock_alert ?? false, threshold: item.alert_threshold ?? 1 };
            localExpiry[item.id] = {
                tanggal_expired: item.tanggal_expired ?? '',
                expiry_alert: item.expiry_alert ?? false,
                expiry_alert_days: item.expiry_alert_days ?? 7
            };
        });
        renderMyList();
    } catch {
        showBanner('Failed to load stock. Please try again.', 'error');
        document.getElementById('stockList').innerHTML = '';
        document.getElementById('emptyMyStock').classList.remove('hidden');
    }
}

function renderMyList() {
    const list = document.getElementById('stockList');
    const empty = document.getElementById('emptyMyStock');
    if (stockItems.length === 0) { list.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    list.innerHTML = stockItems.map((item, i) => buildCard(item, i, false)).join('');
    if (window.Iconify) Iconify.scan();
}

function changeQty(id, delta) {
    localQty[id] = Math.max(0, (localQty[id] ?? 0) + delta);
    const el = document.getElementById(`qty-my-${id}`);
    if (el) el.textContent = localQty[id];
}
function toggleAlert(id) {
    if (!localAlert[id]) localAlert[id] = { enabled: false, threshold: 1 };
    localAlert[id].enabled = !localAlert[id].enabled;
    document.getElementById(`alertToggle-my-${id}`)?.classList.toggle('active', localAlert[id].enabled);
}
function updateSlider(id, input) {
    const val = parseInt(input.value);
    if (!localAlert[id]) localAlert[id] = { enabled: false, threshold: 1 };
    localAlert[id].threshold = val;
    input.style.setProperty('--val', Math.round(val / parseInt(input.max) * 100) + '%');
    const lbl = document.getElementById(`sliderVal-my-${id}`);
    if (lbl) lbl.textContent = `${val} UNIT`;
}

// ── Expiry Accordion Toggle ───────────────────────────────────
function toggleExpiryAccordion(id) {
    if (!window._expOpen) window._expOpen = {};
    window._expOpen[id] = !window._expOpen[id];
    const body = document.getElementById(`expBody-my-${id}`);
    if (body) body.classList.toggle('hidden');
}
function toggleExpiryAccordionShared(id) {
    if (!window._expOpenShared) window._expOpenShared = {};
    window._expOpenShared[id] = !window._expOpenShared[id];
    const body = document.getElementById(`expBody-shared-${id}`);
    if (body) body.classList.toggle('hidden');
}

// ── Low Stock Accordion Toggle ────────────────────────────────
function toggleLsAccordion(id) {
    if (!window._lsOpen) window._lsOpen = {};
    window._lsOpen[id] = !window._lsOpen[id];
    const body = document.getElementById(`lsBody-my-${id}`);
    if (body) body.classList.toggle('hidden');
}
function toggleLsAccordionShared(id) {
    if (!window._lsOpenShared) window._lsOpenShared = {};
    window._lsOpenShared[id] = !window._lsOpenShared[id];
    const body = document.getElementById(`lsBody-shared-${id}`);
    if (body) body.classList.toggle('hidden');
}

// ── Inline Expiry Edit (My Stock) ─────────────────────────────
function updateExpiryDate(id, val) {
    if (!localExpiry[id]) localExpiry[id] = { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 };
    localExpiry[id].tanggal_expired = val;
    // Re-render the card to show/hide expiry alert toggle + slider section
    renderMyList();
}
function toggleExpiryAlert(id) {
    if (!localExpiry[id]) localExpiry[id] = { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 };
    localExpiry[id].expiry_alert = !localExpiry[id].expiry_alert;
    renderMyList();
}
function updateExpiryDays(id, input) {
    const val = parseInt(input.value);
    if (!localExpiry[id]) localExpiry[id] = { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 };
    localExpiry[id].expiry_alert_days = val;
    input.style.setProperty('--val', Math.round(val / parseInt(input.max) * 100) + '%');
    const lbl = document.getElementById(`expDaysVal-my-${id}`);
    if (lbl) lbl.textContent = `${val} days`;
}

// ── Inline Expiry Edit (Shared Stock) ─────────────────────────
function updateExpiryDateShared(id, val) {
    if (!sharedLocalExpiry[id]) sharedLocalExpiry[id] = { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 };
    sharedLocalExpiry[id].tanggal_expired = val;
    renderSharedList();
}
function toggleExpiryAlertShared(id) {
    if (!sharedLocalExpiry[id]) sharedLocalExpiry[id] = { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 };
    sharedLocalExpiry[id].expiry_alert = !sharedLocalExpiry[id].expiry_alert;
    renderSharedList();
}
function updateExpiryDaysShared(id, input) {
    const val = parseInt(input.value);
    if (!sharedLocalExpiry[id]) sharedLocalExpiry[id] = { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 };
    sharedLocalExpiry[id].expiry_alert_days = val;
    input.style.setProperty('--val', Math.round(val / parseInt(input.max) * 100) + '%');
    const lbl = document.getElementById(`expDaysVal-shared-${id}`);
    if (lbl) lbl.textContent = `${val} days`;
}

async function saveAllChanges() {
    const btn = document.getElementById('saveAllBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';
    try {
        const payload = stockItems.map(item => ({
            id: item.id, quantity: localQty[item.id] ?? 0,
            low_stock_alert: localAlert[item.id]?.enabled ?? false,
            alert_threshold: localAlert[item.id]?.threshold ?? 1,
            expiry_alert: localExpiry[item.id]?.expiry_alert ?? item.expiry_alert ?? false,
            expiry_alert_days: localExpiry[item.id]?.expiry_alert_days ?? item.expiry_alert_days ?? 7,
            tanggal_expired: localExpiry[item.id]?.tanggal_expired ?? item.tanggal_expired ?? null,
        }));
        const res  = await fetch(`${API_BASE}/stock/batch-update`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: USER_ID, items: payload })
        });
        const data = await res.json();
        if (data.success || res.ok) { showBanner('All changes saved!', 'success'); fetchStock(); }
        else showBanner(data.message || 'Failed to save.', 'error');
    } catch { showBanner('Network error.', 'error'); }
    finally { btn.style.opacity = ''; btn.style.pointerEvents = ''; }
}

// ── My Stock sheet ─────────────────────────────────────────────
function openAddSheet() {
    document.getElementById('itemName').value = '';
    document.getElementById('itemQty').value  = '';
    document.getElementById('sheetOverlay').classList.remove('hidden');
    document.getElementById('sheetWrapper').classList.remove('hidden');
}
function closeSheet() {
    document.getElementById('sheetOverlay').classList.add('hidden');
    document.getElementById('sheetWrapper').classList.add('hidden');
    // Reset expiry fields
    expiryAlertEnabled = false;
    expiryAlertDays = 7;
    document.getElementById('expiryAlertToggle').classList.remove('active');
    document.getElementById('expiryAlertSliderWrap').classList.add('hidden');
}

// ── Edit Stock sheet ──────────────────────────────────────────
let editModeIsShared = false;

function openEditSheet(id, mode) {
    editModeIsShared = (mode === 'shared');
    const list = editModeIsShared ? sharedItems : stockItems;
    const item = list.find(x => x.id === id);
    if (!item) return;

    editItemId = id;
    editMode = true;

    document.getElementById('editItemName').value = item.name || '';
    document.getElementById('editItemQty').value  = item.quantity ?? 0;
    document.getElementById('editItemDeskripsi').value = item.deskripsi_produk || '';
    document.getElementById('editItemExpiryDate').value = item.tanggal_expired || '';

    // Expiry alert toggle state
    const hasExpiry = !!item.tanggal_expired;
    const expAlert  = item.expiry_alert ?? false;
    const expDays   = item.expiry_alert_days ?? 7;

    const toggleEl = document.getElementById('editExpiryAlertToggle');
    const sliderWrap = document.getElementById('editExpiryAlertSliderWrap');
    if (hasExpiry && expAlert) {
        toggleEl.classList.add('active');
        sliderWrap.classList.remove('hidden');
    } else {
        toggleEl.classList.remove('active');
        sliderWrap.classList.add('hidden');
    }

    document.getElementById('editExpiryAlertDays').value = expDays;
    document.getElementById('editExpiryAlertDaysLabel').textContent = expDays;
    document.getElementById('editExpiryAlertDaysVal').textContent = expDays + ' days';
    const pct = Math.round(expDays / 30 * 100);
    document.getElementById('editExpiryAlertDays').style.setProperty('--val', pct + '%');

    window._editExpiryAlertEnabled = hasExpiry && expAlert;
    window._editExpiryAlertDays = expDays;

    document.getElementById('editSheetOverlay').classList.remove('hidden');
    document.getElementById('editSheetWrapper').classList.remove('hidden');
}
function closeEditSheet() {
    document.getElementById('editSheetOverlay').classList.add('hidden');
    document.getElementById('editSheetWrapper').classList.add('hidden');
    editItemId = null;
    editMode = false;
}
function toggleEditExpiryAlert() {
    window._editExpiryAlertEnabled = !window._editExpiryAlertEnabled;
    document.getElementById('editExpiryAlertToggle').classList.toggle('active', window._editExpiryAlertEnabled);
    document.getElementById('editExpiryAlertSliderWrap').classList.toggle('hidden', !window._editExpiryAlertEnabled);
}
function updateEditExpiryAlertDays(input) {
    window._editExpiryAlertDays = parseInt(input.value);
    input.style.setProperty('--val', Math.round(window._editExpiryAlertDays / parseInt(input.max) * 100) + '%');
    document.getElementById('editExpiryAlertDaysLabel').textContent = window._editExpiryAlertDays;
    document.getElementById('editExpiryAlertDaysVal').textContent = window._editExpiryAlertDays + ' days';
}
async function submitEditStock() {
    if (editItemId === null) return;
    const name = document.getElementById('editItemName').value.trim();
    const qty  = parseInt(document.getElementById('editItemQty').value) || 0;
    if (!name) { flashBorder('editItemName'); return; }

    const deskripsi = document.getElementById('editItemDeskripsi').value.trim();
    const tanggalExpired = document.getElementById('editItemExpiryDate').value || null;

    const btn = document.getElementById('editStockBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';

    try {
        const body = { name, quantity: qty };
        body.deskripsi_produk = deskripsi || null;
        body.tanggal_expired = tanggalExpired;
        if (tanggalExpired) {
            body.expiry_alert = window._editExpiryAlertEnabled ?? false;
            body.expiry_alert_days = window._editExpiryAlertDays ?? 7;
        } else {
            body.expiry_alert = false;
            body.expiry_alert_days = 7;
        }

        const url = editModeIsShared
            ? `${API_BASE}/shared-stock/${editItemId}`
            : `${API_BASE}/stock/${editItemId}`;

        if (!editModeIsShared) {
            body.user_id = USER_ID;
        }

        const res  = await fetch(url, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.success) {
            showBanner('Stock updated!', 'success');
            if (editModeIsShared) {
                // Update local shared state
                const idx = sharedItems.findIndex(x => x.id === editItemId);
                if (idx !== -1) sharedItems[idx] = data.data;
                sharedLocalQty[editItemId] = qty;
                renderSharedList();
            } else {
                const idx = stockItems.findIndex(x => x.id === editItemId);
                if (idx !== -1) stockItems[idx] = data.data;
                localQty[editItemId] = qty;
                renderMyList();
            }
            closeEditSheet();
        } else {
            showBanner(data.message || 'Failed to update.', 'error');
        }
    } catch { showBanner('Network error.', 'error'); }
    finally { btn.style.opacity = ''; btn.style.pointerEvents = ''; }
}
async function submitAddStock() {
    const name = document.getElementById('itemName').value.trim();
    const qty  = parseInt(document.getElementById('itemQty').value) || 0;
    if (!name) { flashBorder('itemName'); return; }
    const deskripsi = document.getElementById('itemDeskripsi').value.trim();
    const tanggalExpired = document.getElementById('itemExpiryDate').value || null;
    const btn = document.getElementById('addStockBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';
    try {
        const body = { user_id: USER_ID, name, quantity: qty };
        if (deskripsi) body.deskripsi_produk = deskripsi;
        if (tanggalExpired) {
            body.tanggal_expired = tanggalExpired;
            body.expiry_alert = expiryAlertEnabled;
            body.expiry_alert_days = expiryAlertDays;
        }
        const res  = await fetch(`${API_BASE}/stock`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.success) {
            stockItems.push(data.data);
            localQty[data.data.id] = qty;
            localAlert[data.data.id] = { enabled: false, threshold: 1 };
            renderMyList(); showBanner('Item added!', 'success'); closeSheet();
        } else showBanner(data.message || 'Failed to add.', 'error');
    } catch { showBanner('Network error.', 'error'); }
    finally { btn.style.opacity = ''; btn.style.pointerEvents = ''; }
}

// ================================================================
// ── SHARED STOCK ─────────────────────────────────────────────────
// ================================================================
async function fetchAssignments() {
    document.getElementById('sharedLoading').classList.remove('hidden');
    document.getElementById('sharedNoConn').classList.add('hidden');
    document.getElementById('sharedContent').classList.add('hidden');
    try {
        const res  = await fetch(`${API_BASE}/shared-stock/my-assignments?user_id=${USER_ID}`, {
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        assignments = data.data || [];
    } catch { assignments = []; }

    document.getElementById('sharedLoading').classList.add('hidden');

    // Badge on tab
    const total = assignments.reduce((s, a) => s + (a.shared_stocks_count || 0), 0);
    const badge = document.getElementById('sharedBadge');
    badge.textContent = total;
    badge.style.display = assignments.length ? '' : 'none';

    if (!assignments.length) {
        document.getElementById('sharedNoConn').classList.remove('hidden'); return;
    }
    document.getElementById('sharedContent').classList.remove('hidden');
    renderPartnerPills();
    selectAssignment(assignments[0].assignment_id);
}

function renderPartnerPills() {
    const scroll = document.getElementById('partnerScroll');
    scroll.innerHTML = assignments.map(a => {
        const partner = a.role === 'nanny' ? a.majikan : a.nanny;
        const roleLabel = a.role === 'nanny' ? 'Majikan' : 'Nanny';
        const count = a.shared_stocks_count || 0;
        const avatarHtml = partner?.avatar
            ? `<img src="${escHtml(partner.avatar)}" class="w-full h-full object-cover" alt="">`
            : `<span class="iconify" data-icon="material-symbols:person-rounded" style="font-size:18px;color:#8B46D3;"></span>`;
        return `
        <div class="assign-pill" id="pill-${a.assignment_id}" onclick="selectAssignment(${a.assignment_id})">
            <div class="assign-avatar">${avatarHtml}</div>
            <div>
                <p class="text-[13px] font-extrabold text-gray-900 whitespace-nowrap">${escHtml(partner?.name ?? '—')}</p>
                <p class="text-[10px] font-bold text-[#8B46D3]">${roleLabel} · ${count} item${count !== 1 ? 's' : ''}</p>
            </div>
        </div>`;
    }).join('');
    if (window.Iconify) Iconify.scan();
}

async function selectAssignment(id) {
    activeAssignId = id;
    document.querySelectorAll('.assign-pill').forEach(p => p.classList.remove('active'));
    document.getElementById(`pill-${id}`)?.classList.add('active');

    const a = assignments.find(x => x.assignment_id === id);
    activeAssignRole = a?.role ?? null;
    const partner = a?.role === 'nanny' ? a.majikan : a.nanny;

    document.getElementById('sharedInfoSub').textContent = a?.role === 'nanny'
        ? `Stock you manage for ${partner?.name ?? 'Majikan'}`
        : `Stock managed by ${partner?.name ?? 'Nanny'}`;

    document.getElementById('readonlyBanner').classList.toggle('hidden', a?.role !== 'majikan');
    document.getElementById('nannyActions').classList.toggle('hidden',   a?.role !== 'nanny');

    await fetchSharedItems(id);
}

async function fetchSharedItems(assignId) {
    const list = document.getElementById('sharedStockList');
    list.innerHTML = '<div class="bg-white rounded-[18px] p-4 skeleton h-[180px]"></div>';
    document.getElementById('emptySharedItems').classList.add('hidden');
    try {
        const res  = await fetch(`${API_BASE}/shared-stock/assignment/${assignId}?user_id=${USER_ID}`, {
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        sharedItems      = data.data || [];
        sharedLocalQty   = {}; sharedLocalAlert = {}; sharedLocalExpiry = {};
        sharedItems.forEach(item => {
            sharedLocalQty[item.id]   = item.quantity ?? 0;
            sharedLocalAlert[item.id] = { enabled: item.low_stock_alert ?? false, threshold: item.alert_threshold ?? 1 };
            sharedLocalExpiry[item.id] = {
                tanggal_expired: item.tanggal_expired ?? '',
                expiry_alert: item.expiry_alert ?? false,
                expiry_alert_days: item.expiry_alert_days ?? 7
            };
        });
        renderSharedList();
    } catch {
        list.innerHTML = '';
        document.getElementById('emptySharedItems').classList.remove('hidden');
        showBanner('Failed to load shared stock.', 'error');
    }
}

function renderSharedList() {
    const list  = document.getElementById('sharedStockList');
    const empty = document.getElementById('emptySharedItems');
    if (!sharedItems.length) { list.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    list.innerHTML = sharedItems.map((item, i) => buildCard(item, i, true)).join('');
    if (window.Iconify) Iconify.scan();
}

function changeQtyShared(id, delta) {
    sharedLocalQty[id] = Math.max(0, (sharedLocalQty[id] ?? 0) + delta);
    const el = document.getElementById(`qty-shared-${id}`);
    if (el) el.textContent = sharedLocalQty[id];
}
function toggleAlertShared(id) {
    if (!sharedLocalAlert[id]) sharedLocalAlert[id] = { enabled: false, threshold: 1 };
    sharedLocalAlert[id].enabled = !sharedLocalAlert[id].enabled;
    document.getElementById(`alertToggle-shared-${id}`)?.classList.toggle('active', sharedLocalAlert[id].enabled);
}
function updateSliderShared(id, input) {
    const val = parseInt(input.value);
    if (!sharedLocalAlert[id]) sharedLocalAlert[id] = { enabled: false, threshold: 1 };
    sharedLocalAlert[id].threshold = val;
    input.style.setProperty('--val', Math.round(val / parseInt(input.max) * 100) + '%');
    const lbl = document.getElementById(`sliderVal-shared-${id}`);
    if (lbl) lbl.textContent = `${val} UNIT`;
}

async function saveSharedChanges() {
    const btn = document.getElementById('saveSharedBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';
    try {
        const payload = sharedItems.map(item => ({
            id: item.id, quantity: sharedLocalQty[item.id] ?? 0,
            low_stock_alert: sharedLocalAlert[item.id]?.enabled ?? false,
            alert_threshold: sharedLocalAlert[item.id]?.threshold ?? 1,
            expiry_alert: sharedLocalExpiry[item.id]?.expiry_alert ?? item.expiry_alert ?? false,
            expiry_alert_days: sharedLocalExpiry[item.id]?.expiry_alert_days ?? item.expiry_alert_days ?? 7,
            tanggal_expired: sharedLocalExpiry[item.id]?.tanggal_expired ?? item.tanggal_expired ?? null,
        }));
        const res  = await fetch(`${API_BASE}/shared-stock/batch-update`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: USER_ID, assignment_id: activeAssignId, items: payload })
        });
        const data = await res.json();
        if (data.success || res.ok) { showBanner('Shared changes saved!', 'success'); fetchSharedItems(activeAssignId); }
        else showBanner(data.message || 'Failed to save.', 'error');
    } catch { showBanner('Network error.', 'error'); }
    finally { btn.style.opacity = ''; btn.style.pointerEvents = ''; }
}

// ── Shared sheet ───────────────────────────────────────────────
function openSharedAddSheet() {
    const a = assignments.find(x => x.assignment_id === activeAssignId);
    const partner = a?.majikan;
    document.getElementById('sharedSheetInfo').textContent = `Shared with ${partner?.name ?? 'Majikan'}`;
    document.getElementById('sharedItemName').value = '';
    document.getElementById('sharedItemQty').value  = '';
    document.getElementById('sharedSheetOverlay').classList.remove('hidden');
    document.getElementById('sharedSheetWrapper').classList.remove('hidden');
}
function closeSharedSheet() {
    document.getElementById('sharedSheetOverlay').classList.add('hidden');
    document.getElementById('sharedSheetWrapper').classList.add('hidden');
}
async function submitAddSharedStock() {
    const name = document.getElementById('sharedItemName').value.trim();
    const qty  = parseInt(document.getElementById('sharedItemQty').value) || 0;
    if (!name) { flashBorder('sharedItemName'); return; }
    const btn = document.getElementById('addSharedBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';
    try {
        const res  = await fetch(`${API_BASE}/shared-stock`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: USER_ID, assignment_id: activeAssignId, name, quantity: qty })
        });
        const data = await res.json();
        if (data.success) {
            sharedItems.push(data.data);
            sharedLocalQty[data.data.id]   = qty;
            sharedLocalAlert[data.data.id] = { enabled: false, threshold: 1 };
            renderSharedList();
            const a = assignments.find(x => x.assignment_id === activeAssignId);
            if (a) { a.shared_stocks_count = (a.shared_stocks_count || 0) + 1; renderPartnerPills(); }
            showBanner('Shared item added!', 'success'); closeSharedSheet();
        } else showBanner(data.message || 'Failed to add.', 'error');
    } catch { showBanner('Network error.', 'error'); }
    finally { btn.style.opacity = ''; btn.style.pointerEvents = ''; }
}

// ================================================================
// SHARED CARD BUILDER
// ================================================================
function buildCard(item, i, isShared) {
    const id        = item.id;
    const readonly  = isShared && activeAssignRole === 'majikan';
    const pfx       = isShared ? 'shared' : 'my';
    const qty       = isShared ? (sharedLocalQty[id] ?? 0) : (localQty[id] ?? 0);
    const alert     = isShared ? (sharedLocalAlert[id] ?? { enabled: false, threshold: 1 })
                               : (localAlert[id] ?? { enabled: false, threshold: 1 });
    const maxT      = Math.max(20, qty + 5);
    const pct       = Math.round(alert.threshold / maxT * 100);
    const updAt     = item.updated_at ? timeAgo(item.updated_at) : '—';

    const qCls  = readonly ? 'qty-btn qty-minus btn-disabled' : 'qty-btn qty-minus';
    const qCls2 = readonly ? 'qty-btn qty-plus btn-disabled'  : 'qty-btn qty-plus';
    const tCls  = `toggle-switch${alert.enabled ? ' active' : ''}${readonly ? ' tog-disabled' : ''}`;

    const minusClick  = readonly ? '' : (isShared ? `onclick="changeQtyShared(${id},-1)"` : `onclick="changeQty(${id},-1)"`);
    const plusClick   = readonly ? '' : (isShared ? `onclick="changeQtyShared(${id}, 1)"` : `onclick="changeQty(${id}, 1)"`);
    const togClick    = readonly ? '' : (isShared ? `onclick="toggleAlertShared(${id})"` : `onclick="toggleAlert(${id})"`);
    const sliderEvent = readonly ? '' : (isShared ? `oninput="updateSliderShared(${id},this)"` : `oninput="updateSlider(${id},this)"`);
    const renewClick  = readonly ? '' : (isShared ? `onclick="openRenewModal(${id},'shared')"` : `onclick="openRenewModal(${id},'my')"`);
    const delClick    = readonly ? '' : (isShared ? `onclick="openDeleteMod(${id},'shared')"` : `onclick="openDeleteMod(${id},'my')"`);
    const editClick   = readonly ? '' : (isShared ? `onclick="openEditSheet(${id},'shared')"` : `onclick="openEditSheet(${id},'my')"`);

    // Inline expiry state
    const expiry     = isShared ? (sharedLocalExpiry[id] ?? { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 })
                                 : (localExpiry[id] ?? { tanggal_expired: '', expiry_alert: false, expiry_alert_days: 7 });
    const expToggle  = `toggle-switch${expiry.expiry_alert ? ' active' : ''}${readonly ? ' tog-disabled' : ''}`;
    const expSliderWrap = expiry.expiry_alert && expiry.tanggal_expired ? '' : 'hidden';
    const expDateVal = expiry.tanggal_expired || '';
    const expDaysVal = expiry.expiry_alert_days || 7;
    const expPct     = Math.round(expDaysVal / 30 * 100);

    // Accordion open state per card
    const expOpen    = (isShared ? (window._expOpenShared && window._expOpenShared[id]) : (window._expOpen && window._expOpen[id])) ?? false;
    const lsOpen    = (isShared ? (window._lsOpenShared && window._lsOpenShared[id]) : (window._lsOpen && window._lsOpen[id])) ?? false;

    const nannyBadge = isShared && item.nanny
        ? `<span class="nanny-badge">
               <span class="iconify" data-icon="material-symbols:person-rounded" style="font-size:10px;"></span>
               ${escHtml(item.nanny?.name ?? 'Nanny')}
           </span>`
        : '';

    return `
    <div class="stock-card bg-white rounded-[18px] px-4 pt-4 pb-3 shadow-sm anim" style="animation-delay:${0.05 + i * 0.08}s">
        <div class="flex items-center gap-3 mb-3">
            <div class="product-icon-wrap">
                ${item.image_url
                    ? `<img src="${escHtml(item.image_url)}" alt="" class="w-full h-full object-cover">`
                    : `<span class="iconify" data-icon="material-symbols:child-care-rounded" style="font-size:26px;color:#F59E0B;"></span>`
                }
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-[15px] font-extrabold text-gray-900 truncate">${escHtml(item.name)}</p>
                    ${nannyBadge}
                </div>
                <div class="flex items-center gap-1 mt-0.5 text-gray-400 text-[11px] font-semibold">
                    <span class="iconify" data-icon="material-symbols:schedule-rounded" style="font-size:12px;"></span>
                    <span>${updAt}</span>
                </div>
            </div>
            ${!readonly ? `
            <div class="flex items-center gap-1.5 shrink-0">
                <button ${editClick}
                    class="w-8 h-8 rounded-full bg-blue-50 border-2 border-blue-400 text-blue-500 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:edit-rounded" style="font-size:15px;"></span>
                </button>
                <button ${renewClick}
                    class="w-8 h-8 rounded-full bg-green-50 border-2 border-green-400 text-green-500 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:autorenew-rounded" style="font-size:15px;"></span>
                </button>
                <button ${delClick}
                    class="w-8 h-8 rounded-full bg-red-50 border-2 border-red-400 text-red-400 flex items-center justify-center">
                    <span class="iconify" data-icon="material-symbols:delete-rounded" style="font-size:15px;"></span>
                </button>
            </div>` : `
            <div class="shrink-0">
                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 rounded-lg px-2 py-1">View only</span>
            </div>`}
        </div>

        <div class="border-t border-gray-100 mb-3"></div>

        <div class="stock-row mb-3">
            <span class="text-[13px] font-bold text-gray-600">Current Stock</span>
            <div class="flex items-center gap-3">
                <button class="${qCls}" ${minusClick}>−</button>
                <span class="qty-val" id="qty-${pfx}-${id}">${qty}</span>
                <button class="${qCls2}" ${plusClick}>+</button>
            </div>
        </div>

        <!-- ═══ INLINE LOW STOCK (ACCORDION) ═══ -->
        <div class="border-t border-gray-100 mt-3 pt-2">
            <div class="flex items-center justify-between cursor-pointer select-none"
                 onclick="${readonly ? '' : (isShared ? `toggleLsAccordionShared(${id})` : `toggleLsAccordion(${id})`)}">
                <div class="flex items-center gap-2">
                    <span class="iconify" data-icon="material-symbols:notifications-active-rounded" style="font-size:16px;color:#F59E0B;"></span>
                    <span class="text-[13px] font-extrabold text-gray-700">Low Stock Alerts</span>
                    ${alert.enabled ? `<span class="text-[10px] font-bold text-green-600 bg-green-100 rounded-full px-2 py-0.5">ON</span>` : ''}
                </div>
                <div class="flex items-center gap-2">
                    <span class="iconify transition-transform duration-200" style="font-size:18px;color:#9CA3AF;${lsOpen ? 'transform:rotate(180deg)' : ''}"
                          data-icon="material-symbols:expand-more-rounded"></span>
                </div>
            </div>
            <div id="lsBody-${pfx}-${id}" class="${lsOpen ? '' : 'hidden'} mt-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#FEF3C7] flex items-center justify-center shrink-0">
                        <span class="iconify" data-icon="material-symbols:notifications-active-rounded" style="font-size:18px;color:#F59E0B;"></span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[12px] font-bold text-gray-600">Alert when stock below</p>
                            <div class="${tCls}" id="alertToggle-${pfx}-${id}" ${togClick}></div>
                        </div>
                        <input type="range" id="slider-${pfx}-${id}"
                               class="alert-slider active-track"
                               min="1" max="${maxT}" value="${alert.threshold}"
                               style="--val:${pct}%;"
                               ${readonly ? 'disabled' : ''}
                               ${sliderEvent}>
                        <div class="flex justify-between text-[10px] text-gray-400 font-bold mt-1 px-0.5">
                            <span>1 UNIT</span>
                            <span id="sliderVal-${pfx}-${id}">${alert.threshold} UNIT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ INLINE EXPIRY EDIT (ACCORDION) ═══ -->
        <div class="border-t border-gray-100 mt-3 pt-2">
            <div class="flex items-center justify-between cursor-pointer select-none"
                 onclick="${readonly ? '' : (isShared ? `toggleExpiryAccordionShared(${id})` : `toggleExpiryAccordion(${id})`)}">
                <div class="flex items-center gap-2">
                    <span class="iconify" data-icon="material-symbols:calendar-clock-rounded" style="font-size:16px;color:#0284C7;"></span>
                    <span class="text-[13px] font-extrabold text-gray-700">Expiry Date</span>
                    ${expDateVal ?
                        (expiry.expiry_alert
                            ? `<span class="text-[10px] font-bold text-green-600 bg-green-100 rounded-full px-2 py-0.5">ON</span>`
                            : `<span class="text-[10px] font-bold text-gray-400 bg-gray-200 rounded-full px-2 py-0.5">OFF</span>`)
                    : ''}
                </div>
                <span class="iconify transition-transform duration-200" style="font-size:18px;color:#9CA3AF;${expOpen ? 'transform:rotate(180deg)' : ''}"
                      data-icon="material-symbols:expand-more-rounded"></span>
            </div>
            <div id="expBody-${pfx}-${id}" class="${expOpen ? '' : 'hidden'} mt-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#E0F2FE] flex items-center justify-center shrink-0">
                        <span class="iconify" data-icon="material-symbols:calendar-clock-rounded" style="font-size:18px;color:#0284C7;"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-bold text-gray-600 mb-1">Expiry Date</p>
                        <input type="date" id="expDate-${pfx}-${id}"
                               class="stock-input mb-2" value="${expDateVal}"
                               ${readonly ? 'disabled' : ''}
                               onchange="${readonly ? '' : (isShared ? `updateExpiryDateShared(${id},this.value)` : `updateExpiryDate(${id},this.value)`)}"
                               style="font-size:13px;padding:8px 12px;">
                        ${expDateVal ? `
                        <div class="flex items-center justify-between mt-2">
                            <div>
                                <p class="text-[12px] font-extrabold text-gray-700">Expiry Alert</p>
                                <p class="text-[10px] text-gray-400 font-semibold">Notify before expiry</p>
                            </div>
                            <div class="${expToggle}" id="expToggle-${pfx}-${id}"
                                 ${readonly ? '' : (isShared ? `onclick="toggleExpiryAlertShared(${id})"` : `onclick="toggleExpiryAlert(${id})"`)}></div>
                        </div>
                        <div class="mt-2 ${expSliderWrap}" id="expSliderWrap-${pfx}-${id}">
                            <input type="range" id="expSlider-${pfx}-${id}"
                                   class="alert-slider active-track"
                                   min="1" max="30" value="${expDaysVal}"
                                   style="--val:${expPct}%;"
                                   ${readonly ? 'disabled' : ''}
                                   ${readonly ? '' : (isShared ? `oninput="updateExpiryDaysShared(${id},this)"` : `oninput="updateExpiryDays(${id},this)"`)}>
                            <div class="flex justify-between text-[10px] text-gray-400 font-bold mt-1 px-0.5">
                                <span>1 day</span>
                                <span id="expDaysVal-${pfx}-${id}">${expDaysVal} days</span>
                            </div>
                        </div>` : `<p class="text-[11px] text-gray-400 font-semibold italic">Set a date to enable expiry alerts</p>`}
                    </div>
                </div>
            </div>
        </div>

    </div>`;
}

// ================================================================
// MODALS — unified (mode: 'my' | 'shared')
// ================================================================
function openRenewModal(id, mode) {
    modalMode = mode; pendingUpdateId = id;
    const list = mode === 'shared' ? sharedItems : stockItems;
    const item = list.find(x => x.id === id);
    document.getElementById('updateMsg').textContent =
        `Are you sure you want to renew "${item?.name || ''}"?`;
    document.getElementById('updateModal').classList.remove('hidden');
}
function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
    pendingUpdateId = null;
}
async function confirmUpdate() {
    if (pendingUpdateId === null) return;
    const id = pendingUpdateId;
    const qty = modalMode === 'shared' ? (sharedLocalQty[id] ?? 0) : (localQty[id] ?? 0);
    const url = modalMode === 'shared' ? `${API_BASE}/shared-stock/${id}/renew` : `${API_BASE}/stock/${id}/renew`;
    const body = modalMode === 'shared' ? { user_id: USER_ID, quantity: qty } : { quantity: qty };
    try {
        const res  = await fetch(url, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.success) {
            const list = modalMode === 'shared' ? sharedItems : stockItems;
            const item = list.find(x => x.id === id);
            if (item) item.updated_at = new Date().toISOString();
            showBanner('Stock renewed!', 'success');
            modalMode === 'shared' ? renderSharedList() : renderMyList();
        } else showBanner(data.message || 'Failed to renew.', 'error');
    } catch { showBanner('Network error.', 'error'); }
    closeUpdateModal();
}

function openDeleteMod(id, mode) {
    modalMode = mode; pendingDeleteId = id;
    const list = mode === 'shared' ? sharedItems : stockItems;
    const item = list.find(x => x.id === id);
    document.getElementById('deleteMsg').textContent =
        `Delete "${item?.name || ''}"? This cannot be recovered.`;
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    pendingDeleteId = null;
}
async function confirmDelete() {
    if (pendingDeleteId === null) return;
    const id = pendingDeleteId;
    const url = modalMode === 'shared' ? `${API_BASE}/shared-stock/${id}` : `${API_BASE}/stock/${id}`;
    const opts = {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' }
    };
    if (modalMode === 'shared') opts.body = JSON.stringify({ user_id: USER_ID });
    try {
        const res  = await fetch(url, opts);
        const data = await res.json();
        if (data.success || res.ok) {
            if (modalMode === 'shared') {
                sharedItems = sharedItems.filter(x => x.id !== id);
                delete sharedLocalQty[id]; delete sharedLocalAlert[id];
                renderSharedList();
                const a = assignments.find(x => x.assignment_id === activeAssignId);
                if (a) { a.shared_stocks_count = Math.max(0, (a.shared_stocks_count||1) - 1); renderPartnerPills(); }
            } else {
                stockItems = stockItems.filter(x => x.id !== id);
                delete localQty[id]; delete localAlert[id];
                renderMyList();
            }
            showBanner('Item deleted.', 'success');
        } else showBanner(data.message || 'Failed to delete.', 'error');
    } catch {
        if (modalMode === 'shared') { sharedItems = sharedItems.filter(x => x.id !== id); renderSharedList(); }
        else { stockItems = stockItems.filter(x => x.id !== id); renderMyList(); }
    }
    closeDeleteModal();
}

// ================================================================
// HELPERS
// ================================================================
function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60)    return `${diff}s ago`;
    if (diff < 3600)  return `${Math.floor(diff/60)} min ago`;
    if (diff < 86400) return `${Math.floor(diff/3600)} hours ago`;
    return `${Math.floor(diff/86400)} days ago`;
}
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function showBanner(msg, type) {
    const el = document.getElementById('notifBanner');
    el.className = `notif-banner ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
    el.textContent = msg; el.classList.remove('hidden');
    clearTimeout(el._timer);
    el._timer = setTimeout(() => el.classList.add('hidden'), 3500);
}
function flashBorder(elId) {
    const el = document.getElementById(elId);
    el.style.borderColor = '#EF4444'; el.focus();
    setTimeout(() => el.style.borderColor = '', 1200);
}

// ================================================================
// ── EXPIRY ALERTS ────────────────────────────────────────────────
// ================================================================
function toggleExpiryAlert() {
    expiryAlertEnabled = !expiryAlertEnabled;
    document.getElementById('expiryAlertToggle').classList.toggle('active', expiryAlertEnabled);
    document.getElementById('expiryAlertSliderWrap').classList.toggle('hidden', !expiryAlertEnabled);
}
function updateExpiryAlertDays(input) {
    expiryAlertDays = parseInt(input.value);
    input.style.setProperty('--val', Math.round(expiryAlertDays / parseInt(input.max) * 100) + '%');
    document.getElementById('expiryAlertDaysLabel').textContent = expiryAlertDays;
    document.getElementById('expiryAlertDaysVal').textContent = expiryAlertDays + ' days';
}

async function fetchExpiryData() {
    const loading = document.getElementById('expiryLoading');
    const expiredSection = document.getElementById('expiredSection');
    const expiringSection = document.getElementById('expiringSection');
    const empty = document.getElementById('emptyExpiry');

    loading.classList.remove('hidden');
    expiredSection.classList.add('hidden');
    expiringSection.classList.add('hidden');
    empty.classList.add('hidden');

    try {
        const [expiredRes, expiringRes] = await Promise.all([
            fetch(`${API_BASE}/stock/${USER_ID}/expired`, {
                headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
            }),
            fetch(`${API_BASE}/stock/${USER_ID}/expiring`, {
                headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
            })
        ]);
        const expiredData  = await expiredRes.json();
        const expiringData = await expiringRes.json();
        expiredItems  = expiredData.data || [];
        expiringItems = expiringData.data || [];
    } catch {
        expiredItems  = [];
        expiringItems = [];
        showBanner('Failed to load expiry data.', 'error');
    }

    loading.classList.add('hidden');

    // Update badge on tab
    const total = expiredItems.length + expiringItems.length;
    const badge = document.getElementById('expiryBadge');
    badge.textContent = total;
    badge.style.display = total ? '' : 'none';

    if (!total) {
        empty.classList.remove('hidden');
        return;
    }

    // Render expired
    if (expiredItems.length) {
        document.getElementById('expiredCount').textContent = expiredItems.length + ' item' + (expiredItems.length > 1 ? 's' : '');
        document.getElementById('expiredList').innerHTML = expiredItems.map((item, i) => buildExpiryCard(item, i, 'expired')).join('');
        expiredSection.classList.remove('hidden');
    }

    // Render expiring
    if (expiringItems.length) {
        document.getElementById('expiringCount').textContent = expiringItems.length + ' item' + (expiringItems.length > 1 ? 's' : '');
        document.getElementById('expiringList').innerHTML = expiringItems.map((item, i) => buildExpiryCard(item, i, 'expiring')).join('');
        expiringSection.classList.remove('hidden');
    }

    if (window.Iconify) Iconify.scan();
}

function buildExpiryCard(item, i, type) {
    const expired = type === 'expired';
    const expiryDate = item.tanggal_expired ? new Date(item.tanggal_expired + 'T00:00:00') : null;
    const now = new Date();
    now.setHours(0,0,0,0);

    let daysRemaining = 0;
    if (expiryDate) {
        daysRemaining = Math.floor((expiryDate - now) / (1000 * 60 * 60 * 24));
        if (expired) daysRemaining = Math.abs(daysRemaining);
    }

    const daysText = expired
        ? `<span class="font-extrabold text-red-600">${daysRemaining} day${daysRemaining > 1 ? 's' : ''} overdue</span>`
        : `<span class="font-extrabold text-amber-600">${daysRemaining} day${daysRemaining > 1 ? 's' : ''} remaining</span>`;

    const borderCls = expired ? 'border-l-red-500' : 'border-l-amber-400';
    const iconColor = expired ? '#EF4444' : '#F59E0B';

    return `
    <div class="stock-card bg-white rounded-[18px] px-4 py-4 shadow-sm border-l-4 ${borderCls} anim" style="animation-delay:${0.05 + i * 0.08}s">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                <span class="iconify" data-icon="material-symbols:inventory-2-rounded" style="font-size:20px;color:${iconColor};"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[15px] font-extrabold text-gray-900 truncate">${escHtml(item.name)}</p>
                <div class="flex items-center gap-2 mt-0.5 text-gray-400 text-[11px] font-semibold">
                    <span class="iconify" data-icon="material-symbols:calendar-today-rounded" style="font-size:12px;"></span>
                    <span>Exp: ${item.tanggal_expired || '—'}</span>
                </div>
            </div>
            <div class="shrink-0 text-right">
                ${daysText}
            </div>
        </div>
        ${item.deskripsi_produk ? `
        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-100">
            <span class="iconify" data-icon="material-symbols:description-outline-rounded" style="font-size:14px;color:#9CA3AF;"></span>
            <span class="text-[12px] text-gray-500 font-semibold">${escHtml(item.deskripsi_produk)}</span>
        </div>` : ''}
        ${expired ? `
        <div class="mt-2 pt-2 border-t border-gray-100">
            <span class="text-[11px] font-bold text-red-500 bg-red-50 rounded-lg px-2 py-1 inline-flex items-center gap-1">
                <span class="iconify" data-icon="material-symbols:warning-rounded" style="font-size:12px;"></span>
                EXPIRED — discard or replace
            </span>
        </div>` : `
        <div class="mt-2 pt-2 border-t border-gray-100">
            <span class="text-[11px] font-bold text-amber-500 bg-amber-50 rounded-lg px-2 py-1 inline-flex items-center gap-1">
                <span class="iconify" data-icon="material-symbols:schedule-rounded" style="font-size:12px;"></span>
                EXPIRING SOON — restock in time
            </span>
        </div>`}
    </div>`;
}

// ================================================================
// INIT
// ================================================================
fetchStock();
</script>

@include('partials.auth-guard')
</body>
</html>