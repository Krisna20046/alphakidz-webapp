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
use App\Http\Controllers\NexusController;
use App\Http\Controllers\KonsultanTugaskanController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\SchoolSubjectController;
use App\Http\Controllers\SchoolScheduleController;
use App\Http\Controllers\AcademicTaskController;
use App\Http\Controllers\MajikanTrackingController;


// â”€â”€â”€ Guest Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

Route::middleware('guest.api')->group(function () {
    Route::get( '/login',    [AuthController::class, 'showLogin']   )->name('login');
    Route::post('/login',    [AuthController::class, 'login']       )->name('login.post');
    Route::get( '/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']    )->name('register.post');
    Route::get( '/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
    Route::get( '/reset-password',  [AuthController::class, 'showResetPassword'] )->name('reset.password');
});

Route::post('/auth/store-token', [AuthController::class, 'storeToken'])->name('auth.store-token');

/**
 * GET /force-logout
 * Endpoint khusus dipanggil oleh auth-guard JS saat token expired.
 * BERBEDA dengan POST /logout (yang butuh CSRF & method POST):
 *   - Ini GET, jadi gak gagal gara-gara sendBeacon/hang
 *   - Guaranteed hapus semua session
 *   - Redirect ke login page
 */
Route::get('/force-logout', function () {
    session()->flush();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login')->with('auth_flash', 'Sesi berakhir. Silakan login kembali.');
})->name('force-logout')->withoutMiddleware(['auth.api', 'guest.api']);

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

Route::get('/auth/google/callback', function () {
    return view('auth.google-callback');
})->name('google.callback');

// â”€â”€â”€ Protected Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

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

    // â”€â”€ Chat â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get('/chat',          [ChatController::class, 'list'] )->name('chat.list');
    Route::get('/chat/{id}',     [ChatController::class, 'room'] )->name('chat.room');

    // â”€â”€ Chat API Proxy (dipanggil dari JS, token tetap server-side) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get( '/api/chat-list', [ChatController::class, 'apiChatList'])->name('api.chat.list');
    Route::get( '/api/chat',      [ChatController::class, 'apiGetChat'] )->name('api.chat.get');
    Route::post('/api/chat',      [ChatController::class, 'apiSendChat'])->name('api.chat.send');

    // â”€â”€ Online Status API Proxy â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::post('/api/user/ping',         [ChatController::class, 'apiPing'])->name('api.user.ping');
    Route::post('/api/user/offline',      [ChatController::class, 'apiOffline'])->name('api.user.offline');
    Route::get('/api/user/{id}/status',   [ChatController::class, 'apiUserStatus'])->name('api.user.status');

    // Artikel
    Route::get('/artikel', [ArtikelController::class, 'index'])->name('artikel.index');
    Route::get('/artikel/{id}', [ArtikelController::class, 'show'])->name('artikel.show');

    // â”€â”€ Nexus â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::prefix('nexus')->name('nexus.')->group(function () {
        Route::get('/',           [NexusController::class, 'index'] )->name('nexus-index');
        Route::get('/create',     [NexusController::class, 'create'])->name('create');
        Route::post('/',          [NexusController::class, 'store'] )->name('store');
        Route::get('/{id}',       [NexusController::class, 'show']  )->name('show');
    });

    // â”€â”€ Profil â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

        // Medical CRUD (RS, Dokter, Vaksin) â€” proxy via AnakController
        Route::post('/data-anak/medical/{type}',                [AnakController::class, 'medicalStore'])->name('anak.medical.store');
        Route::post('/data-anak/medical/{type}/update',         [AnakController::class, 'medicalUpdate'])->name('anak.medical.update');
        Route::post('/data-anak/medical/{type}/delete',         [AnakController::class, 'medicalDelete'])->name('anak.medical.delete');

        // Dropdown AJAX
        Route::get('/provinsi',       [ProfileController::class, 'getProvinsi'])->name('provinsi');
        Route::get('/kota/{id}',      [ProfileController::class, 'getKota']    )->name('kota');
    });
    Route::prefix('reminder')->name('reminder.')->group(function () {
        Route::get('/',               fn() => view('profil.reminder.index'))->name('index');
    });

    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/',               fn() => view('profil.stock.index'))->name('index');
    });

    Route::prefix('majikan')->group(function () {
        Route::get('/nanny',    [MajikanController::class, 'indexNanny'])->name('majikan-nanny-list');
        Route::get('/nanny/{id}', [MajikanController::class, 'showNanny'])->name('majikan-nanny-detail');
        Route::get('/konsultan',    [MajikanController::class, 'indexKonsultan'])->name('majikan-konsultan-list');
        Route::get('/konsultan/{id}', [MajikanController::class, 'showKonsultan'])->name('majikan-konsultan-detail');
        Route::get('/konsultan-anda', [MajikanController::class, 'indexKonsultanAnda'])->name('majikan-konsultan-anda');
        Route::get('/nanny-anda',      [MajikanController::class, 'indexNannyAnda'])->name('majikan-nanny');
        Route::get('/nanny-anda/{id}', [MajikanController::class, 'showNannyAnda'] )->name('majikan-nanny-anda-detail');
        Route::get('/diary',        [MajikanController::class, 'chooseDiary'])->name('majikan-diary-choose');
        Route::get('/diary/{id}',   [MajikanController::class, 'showDiary']  )->name('majikan-diary');

        // Tracking (monitor tasks & schedule yg diisi nanny)
        Route::get('/monitoring',        [MajikanTrackingController::class, 'index'])->name('majikan-tracking');
        Route::get('/monitoring/{id_anak}', [MajikanTrackingController::class, 'show'])->name('majikan-tracking-show');
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

    Route::prefix('admin/school-subject')->group(function () {
        Route::get('/',            [SchoolSubjectController::class, 'index']  )->name('admin-school-subject');
        Route::get('/create',      [SchoolSubjectController::class, 'create'] )->name('admin-school-subject.create');
        Route::post('/',           [SchoolSubjectController::class, 'store']  )->name('admin-school-subject.store');
        Route::get('/{id}',        [SchoolSubjectController::class, 'show']   )->name('admin-school-subject.show');
        Route::get('/{id}/edit',   [SchoolSubjectController::class, 'edit']   )->name('admin-school-subject.edit');
        Route::put('/{id}',        [SchoolSubjectController::class, 'update'] )->name('admin-school-subject.update');
        Route::delete('/{id}',     [SchoolSubjectController::class, 'destroy'])->name('admin-school-subject.destroy');
    });

    // Module 2 — School Schedule (role Nanny untuk sekarang)
    Route::prefix('school-schedule')->name('school-schedule.')->group(function () {
        Route::get('/',            [SchoolScheduleController::class, 'index']  )->name('index');
        Route::get('/create',      [SchoolScheduleController::class, 'create'] )->name('create');
        Route::post('/',           [SchoolScheduleController::class, 'store']  )->name('store');
        Route::get('/{id}',        [SchoolScheduleController::class, 'show']   )->name('show');
        Route::get('/{id}/edit',   [SchoolScheduleController::class, 'edit']   )->name('edit');
        Route::put('/{id}',        [SchoolScheduleController::class, 'update'] )->name('update');
        Route::delete('/{id}',     [SchoolScheduleController::class, 'destroy'])->name('destroy');
    });

    // Module 3 & 4 — Academic Task + Task Progress (role Nanny)
    Route::prefix('academic-task')->name('academic-task.')->group(function () {
        Route::get('/',                [AcademicTaskController::class, 'index']        )->name('index');
        Route::get('/create',          [AcademicTaskController::class, 'create']       )->name('create');
        Route::post('/',               [AcademicTaskController::class, 'store']        )->name('store');
        Route::get('/{id}',            [AcademicTaskController::class, 'show']         )->name('show');
        Route::get('/{id}/edit',       [AcademicTaskController::class, 'edit']         )->name('edit');
        Route::post('/{id}/update',    [AcademicTaskController::class, 'update']       )->name('update');
        Route::delete('/{id}',         [AcademicTaskController::class, 'destroy']      )->name('destroy');
        Route::post('/{id}/status',    [AcademicTaskController::class, 'updateStatus'] )->name('update-status');
        Route::post('/{id}/complete',  [AcademicTaskController::class, 'markComplete'] )->name('complete');
        Route::post('/{id}/progress',  [AcademicTaskController::class, 'storeProgress'])->name('progress');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/diary', fn() => view('admin.diary-nanny-list'))
            ->name('admin-diary-nanny-list');

        Route::get('/diary/{id_nanny}/anak', fn() => view('admin.diary-anak-list'))
            ->name('admin-diary-anak-list');

        Route::get('/diary/{id_nanny}/anak/{id_anak}', fn() => view('admin.diary'))
            ->name('admin-diary');

        Route::get('/rekap-diary', fn() => view('admin.rekap-diary'))
            ->name('admin-rekap-diary-nanny-list');
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
        Route::get('/diary',                [KonsultanController::class, 'diaryIndex'])->name('konsultan-diary');
        Route::get('/nanny/{id}/diary', [KonsultanController::class, 'showDiary'])->name('konsultan-nanny-diary');
    });
});

// â”€â”€â”€ Broadcasting Auth (Pusher private channel) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    $token = session('token');
    if (!$token) abort(403, 'Unauthenticated');

    $apiBaseUrl = rtrim(
        config('services.api.base_url', env('API_BASE_URL')),
        '/'
    );

    if (str_ends_with($apiBaseUrl, '/api')) {
        $apiBaseUrl = substr($apiBaseUrl, 0, -4);
    }

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

