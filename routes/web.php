<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AnakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MajikanController;
use App\Http\Controllers\NannyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KonsultanController;
use App\Http\Controllers\KonsultanTugaskanController;


// ─── Guest Routes ─────────────────────────────────────────────────────────────

Route::middleware('guest.api')->group(function () {
    Route::get( '/login',    [AuthController::class, 'showLogin']   )->name('login');
    Route::post('/login',    [AuthController::class, 'login']       )->name('login.post');
    Route::get( '/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']    )->name('register.post');
});

Route::post('/auth/store-token', [AuthController::class, 'storeToken'])->name('auth.store-token');

Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'no-cache, no-store, must-revalidate', // SW selalu fresh
        'Service-Worker-Allowed' => '/',
    ]);
})->name('sw');

Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json',
    ]);
})->name('manifest');

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// ─── Protected Routes ─────────────────────────────────────────────────────────

Route::middleware('auth.api')->group(function () {

    // Home / Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Proxy: unread count (dipanggil JS, agar token tidak exposed ke client)
    Route::get('/api/unread-count', [HomeController::class, 'unreadCount'])->name('api.unread');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // FCM Token management
    Route::post('/fcm/update-token', [FcmController::class, 'updateToken'])->name('fcm.update');
    Route::post('/fcm/remove-token', [FcmController::class, 'removeToken'])->name('fcm.remove');

    // ── Chat ──────────────────────────────────────────────────────────────────
    Route::get('/chat',          [ChatController::class, 'list'] )->name('chat.list');
    Route::get('/chat/{id}',     [ChatController::class, 'room'] )->name('chat.room');

    // ── Chat API Proxy (dipanggil dari JS, token tetap server-side) ───────────
    Route::get( '/api/chat-list', [ChatController::class, 'apiChatList'])->name('api.chat.list');
    Route::get( '/api/chat',      [ChatController::class, 'apiGetChat'] )->name('api.chat.get');
    Route::post('/api/chat',      [ChatController::class, 'apiSendChat'])->name('api.chat.send');

    // Artikel (placeholder)
    Route::get('/artikel', fn() => view('artikel.index'))->name('artikel.index');

    // ── Profil ────────────────────────────────────────────────────────────────
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/',               [ProfileController::class, 'index']    )->name('index');
        Route::get('/detail',         [ProfileController::class, 'detail']   )->name('detail');
        Route::post('/update',        [ProfileController::class, 'update']   )->name('update');
        Route::get('/edit-akun',      [ProfileController::class, 'editAkun'] )->name('edit-akun');
        Route::post('/update-akun',   [ProfileController::class, 'updateAkun'])->name('update-akun');
        Route::get('/data-anak',            [AnakController::class, 'index']  )->name('data-anak');
        Route::get('/data-anak/tambah',     [AnakController::class, 'tambah'] )->name('anak.tambah');
        Route::post('/data-anak/store',     [AnakController::class, 'store']  )->name('anak.store');
        Route::get('/data-anak/{id}',       [AnakController::class, 'detail'] )->name('anak.detail');
        Route::get('/data-anak/{id}/ubah',  [AnakController::class, 'ubah']   )->name('anak.ubah');
        Route::post('/data-anak/update',    [AnakController::class, 'update'] )->name('anak.update');
        Route::delete('/data-anak/{id}',    [AnakController::class, 'hapus']  )->name('anak.hapus');

        // Dropdown AJAX
        Route::get('/provinsi',       [ProfileController::class, 'getProvinsi'])->name('provinsi');
        Route::get('/kota/{id}',      [ProfileController::class, 'getKota']    )->name('kota');
    });

    Route::prefix('majikan')->group(function () {
        Route::get('/nanny',    [MajikanController::class, 'indexNanny'])->name('majikan-nanny-list');
        Route::get('/nanny/{id}', [MajikanController::class, 'showNanny'])->name('majikan-nanny-detail');
        Route::get('/konsultan',    [MajikanController::class, 'indexKonsultan'])->name('majikan-konsultan-list');
        Route::get('/konsultan/{id}', [MajikanController::class, 'showKonsultan'])->name('majikan-konsultan-detail');
        Route::get('/nanny-anda',      [MajikanController::class, 'indexNannyAnda'])->name('majikan-nanny');
        Route::get('/nanny-anda/{id}', [MajikanController::class, 'showNannyAnda'] )->name('majikan-nanny-anda-detail');
        Route::get('/diary',        [MajikanController::class, 'chooseDiary'])->name('majikan-diary-choose');
        Route::get('/diary/{id}',   [MajikanController::class, 'showDiary']  )->name('majikan-diary');
    });

    Route::prefix('nanny')->group(function () {
        Route::get('/diary', [NannyController::class, 'chooseDiary'])->name('nanny-diary-choose');
        Route::get('/diary/{id_anak}', [NannyController::class, 'showDiary'])->name('nanny-diary');
        Route::get('/diary/{id_anak}/tambah', [NannyController::class, 'showAdd'])->name('nanny-diary-add');
        Route::post('/diary/store', [NannyController::class, 'store'])->name('nanny-diary-store');
        Route::get('/data-anak',  [NannyController::class, 'dataAnak'])->name('nanny-anak-list');
        Route::get('/konsultan',  [NannyController::class, 'konsultan'])->name('nanny-konsultan');
        Route::get('/majikan',    [NannyController::class, 'majikan'])->name('nanny-majikan');
    });

    Route::prefix('admin/kelola-akun')->group(function () {
        Route::get('/',            [AdminController::class, 'index']        )->name('admin-kelola-akun');
        Route::get('/create',      [AdminController::class, 'create']       )->name('admin-kelola-akun.create');
        Route::post('/',           [AdminController::class, 'store']        )->name('admin-kelola-akun.store');
        Route::get('/{id}',        [AdminController::class, 'show']         )->name('admin-kelola-akun.show');
        Route::get('/{id}/edit',   [AdminController::class, 'edit']         )->name('admin-kelola-akun.edit');
        Route::put('/{id}',        [AdminController::class, 'update']       )->name('admin-kelola-akun.update');
        Route::post('/{id}/status',[AdminController::class, 'updateStatus'] )->name('admin-kelola-akun.status');
        Route::delete('/{id}',     [AdminController::class, 'destroy']      )->name('admin-kelola-akun.destroy');
    });

    Route::prefix('admin/diary')->group(function () {
        // Step 1 – Pilih Nanny
        Route::get('/', fn() => view('admin.diary-nanny-list'))
            ->name('admin-diary-nanny-list');

        // Step 2 – Pilih Anak (dari nanny yang dipilih)
        Route::get('/{id_nanny}/anak', fn() => view('admin.diary-anak-list'))
            ->name('admin-diary-anak-list');

        // Step 3 – Tampilan Diary
        Route::get('/{id_nanny}/anak/{id_anak}', fn() => view('admin.diary'))
            ->name('admin-diary');
    });

    Route::prefix('konsultan')->group(function () {
        Route::get('/nanny',        [KonsultanController::class, 'indexNanny'])->name('konsultan-nanny-list');
        Route::get('/nanny/{id}',   [KonsultanController::class, 'showNanny']) ->name('konsultan-nanny-detail');
        Route::post('/nanny/add',   [KonsultanController::class, 'addNanny'])  ->name('konsultan-nanny-add');
        Route::get('/nanny-anda',                [KonsultanController::class, 'indexNannyAnda']    )->name('konsultan-nanny-anda');
        Route::get('/nanny-anda/{id}',           [KonsultanController::class, 'showNannyAnda']     )->name('konsultan-nanny-anda-detail');
        Route::post('/nanny-anda/update-status', [KonsultanController::class, 'updateStatusNanny'] )->name('konsultan-nanny-update-status');
        Route::get('/majikan-nanny',        [KonsultanController::class, 'indexMajikanNanny'])->name('konsultan-majikan-nanny');
        Route::get('/majikan-nanny/{id}',   [KonsultanController::class, 'showMajikanNanny']) ->name('konsultan-majikan-nanny-detail');

        // Tugaskan Nanny
        Route::get('/tugaskan-nanny',                      fn()=>view('konsultan.tugaskan-nanny'))->name('konsultan-tugaskan-nanny');
        Route::get('/tugaskan-nanny/{id}/tambah',          fn()=>view('konsultan.tugaskan-nanny-tambah'));
        Route::get('/tugaskan-nanny/assignment/{id}/ubah', fn()=>view('konsultan.tugaskan-nanny-ubah'));
        Route::get('/rekap-diary',               fn()=>view('konsultan.rekap-diary'))->name('konsultan-rekap-diary-nanny-list');
    });
});

// ─── Broadcasting Auth (Pusher private channel) ───────────────────────────────

Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    $token = session('token');
    if (!$token) abort(403, 'Unauthenticated');

    $apiBaseUrl = rtrim(str_replace('/api', '', config('services.api.base_url', env('API_BASE_URL'))), '/');

    $response = \Illuminate\Support\Facades\Http::withToken($token)
        ->asForm()
        ->post("{$apiBaseUrl}/broadcasting/auth", [
            'socket_id'    => $request->socket_id,
            'channel_name' => $request->channel_name,
        ]);

    if ($response->failed()) abort(403, 'Broadcasting auth failed');

    return response()->json($response->json());
})->name('broadcasting.auth');

// Root
Route::get('/', fn() => redirect()->route('login'));