<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inventory Stock</title>
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
        .bottom-sheet {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 390px; background: white;
            border-radius: 28px 28px 0 0; padding: 24px 24px 40px; z-index: 51;
            animation: sheetIn 0.32s cubic-bezier(0.32,0.72,0,1);
            max-height: 85vh; overflow-y: auto;
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
<div id="stockSheet" class="bottom-sheet hidden">
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
    <button class="btn-save-all" id="addStockBtn" onclick="submitAddStock()">
        <span class="iconify" data-icon="material-symbols:add-circle-rounded" style="font-size:18px;"></span>
        Add Stock
    </button>
</div>

<!-- ============================================================ -->
<!--  BOTTOM SHEET: Add Shared Stock (nanny only)                 -->
<!-- ============================================================ -->
<div id="sharedSheetOverlay" class="bottom-sheet-overlay hidden" onclick="closeSharedSheet()"></div>
<div id="sharedStockSheet" class="bottom-sheet hidden">
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

<script>
// ================================================================
// CONFIG & STATE
// ================================================================
const API_BASE  = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "http://127.0.0.1:8000/api")), "/") }}';
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

// Shared Stock state
let assignments      = [];
let activeAssignId   = null;
let activeAssignRole = null;
let sharedItems      = [];
let sharedLocalQty   = {};
let sharedLocalAlert = {};

// Modal state (tracks which mode: 'my' | 'shared')
let modalMode        = 'my';
let pendingUpdateId  = null;
let pendingDeleteId  = null;

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
    document.getElementById('panelMyStock').classList.toggle('hidden',    tab !== 'my');
    document.getElementById('panelSharedStock').classList.toggle('hidden',tab !== 'shared');

    if (tab === 'shared' && assignments.length === 0) fetchAssignments();
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
        localQty = {}; localAlert = {};
        stockItems.forEach(item => {
            localQty[item.id]   = item.quantity ?? 0;
            localAlert[item.id] = { enabled: item.low_stock_alert ?? false, threshold: item.alert_threshold ?? 1 };
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

async function saveAllChanges() {
    const btn = document.getElementById('saveAllBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';
    try {
        const payload = stockItems.map(item => ({
            id: item.id, quantity: localQty[item.id] ?? 0,
            low_stock_alert: localAlert[item.id]?.enabled ?? false,
            alert_threshold: localAlert[item.id]?.threshold ?? 1,
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
    document.getElementById('stockSheet').classList.remove('hidden');
}
function closeSheet() {
    document.getElementById('sheetOverlay').classList.add('hidden');
    document.getElementById('stockSheet').classList.add('hidden');
}
async function submitAddStock() {
    const name = document.getElementById('itemName').value.trim();
    const qty  = parseInt(document.getElementById('itemQty').value) || 0;
    if (!name) { flashBorder('itemName'); return; }
    const btn = document.getElementById('addStockBtn');
    btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none';
    try {
        const res  = await fetch(`${API_BASE}/stock`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: USER_ID, name, quantity: qty })
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
        sharedLocalQty   = {}; sharedLocalAlert = {};
        sharedItems.forEach(item => {
            sharedLocalQty[item.id]   = item.quantity ?? 0;
            sharedLocalAlert[item.id] = { enabled: item.low_stock_alert ?? false, threshold: item.alert_threshold ?? 1 };
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
    document.getElementById('sharedStockSheet').classList.remove('hidden');
}
function closeSharedSheet() {
    document.getElementById('sharedSheetOverlay').classList.add('hidden');
    document.getElementById('sharedStockSheet').classList.add('hidden');
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

        <div class="flex items-start gap-3 mb-2">
            <div class="w-10 h-10 rounded-full bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <span class="iconify" data-icon="material-symbols:notifications-active-rounded" style="font-size:18px;color:#F59E0B;"></span>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[13px] font-extrabold text-gray-800">Low Stock Alerts</p>
                        <p class="text-[11px] text-gray-400 font-semibold">Notify me when items run low</p>
                    </div>
                    <div class="${tCls}" id="alertToggle-${pfx}-${id}" ${togClick}></div>
                </div>
                <div class="mt-3">
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
// INIT
// ================================================================
fetchStock();
</script>

@include('partials.auth-guard')
</body>
</html>