<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AnakController;
use App\Http\Controllers\ProfileController;


// ─── Guest Routes ─────────────────────────────────────────────────────────────

Route::middleware('guest.api')->group(function () {
    Route::get( '/login',    [AuthController::class, 'showLogin']   )->name('login');
    Route::post('/login',    [AuthController::class, 'login']       )->name('login.post');
    Route::get( '/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']    )->name('register.post');
});

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
    Route::get('/artikel', fn() => view('coming-soon'))->name('artikel.index');

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