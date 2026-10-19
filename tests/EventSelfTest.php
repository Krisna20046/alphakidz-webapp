<?php

/**
 * Self-check frontend Modul 23 — Event Manage (Laravel_Web_App).
 *
 * Verifikasi: controller proxy, route names (cocok seeder menu), view files,
 * slider home (HomeController fetchActiveEvents + home.blade dynamic banners).
 * Jalankan: php tests/EventSelfTest.php
 */

$base = __DIR__ . '/..';

$fail = 0;
function check(bool $cond, string $label): void
{
    global $fail;
    if ($cond) {
        echo "  PASS  {$label}\n";
    } else {
        echo "  FAIL  {$label}\n";
        $fail++;
    }
}

echo "\nController (proxy EventController):\n";
$ctrl = @file_get_contents($base . '/app/Http/Controllers/EventController.php');
check($ctrl !== false, 'file controller ada');
check(str_contains((string) $ctrl, 'class EventController'), 'class EventController');
check(str_contains((string) $ctrl, "config('services.api.base_url"), 'pakai base URL config');
check(str_contains((string) $ctrl, "session('token')"), 'token dari session (tidak exposed)');
check(str_contains((string) $ctrl, 'function nexusIndex'), 'method nexusIndex');
check(str_contains((string) $ctrl, 'function nexusStore'), 'method nexusStore (multipart)');
check(str_contains((string) $ctrl, 'function nexusUpdate'), 'method nexusUpdate (multipart PUT)');
check(str_contains((string) $ctrl, 'function nexusDestroy'), 'method nexusDestroy (delete)');
check(str_contains((string) $ctrl, 'function show'), 'method show (detail event)');
check(str_contains((string) $ctrl, 'asMultipart'), 'upload via asMultipart + fopen (pola terbukti)');
check(str_contains((string) $ctrl, "/events'"), 'proxy URL backend events');
check(str_contains((string) $ctrl, 'banner_image'), 'field banner_image');

echo "\nController (HomeController — slider home):\n";
$home = @file_get_contents($base . '/app/Http/Controllers/HomeController.php');
check($home !== false, 'HomeController ada');
check(str_contains((string) $home, 'fetchActiveEvents'), 'fetchActiveEvents ada');
check(str_contains((string) $home, 'events/active'), 'panggil GET events/active');
check(str_contains((string) $home, "compact('menus', 'artikels', 'riskyTasks', 'events')"), '$events di-pass ke view home');

echo "\nRoutes (web.php):\n";
$web = file_get_contents($base . '/routes/web.php');
check(str_contains($web, 'use App\Http\Controllers\EventController;'), 'EventController diimport');
foreach ([
    "name('nexus-events')",
    "name('nexus-events.store')",
    "name('nexus-events.update')",
    "name('nexus-events.destroy')",
    "name('event-detail')",
] as $needle) {
    check(str_contains($web, $needle), "route {$needle}");
}
check(str_contains($web, "'nexus-events'"), 'nama route cocok kolom menus (seeder modul 23)');

echo "\nViews:\n";
$evIndex = @file_get_contents($base . '/resources/views/nexus/event/index.blade.php');
check($evIndex !== false, 'view nexus/event/index ada');
check(str_contains((string) $evIndex, "route('nexus-events.store'"), 'form action create');
check(str_contains((string) $evIndex, 'EV_UPDATE_URLS'), 'form action update via JS');
check(str_contains((string) $evIndex, 'name="banner_image"'), 'field banner_image');
check(str_contains((string) $evIndex, 'evFormOpen'), 'Ubah prefill via evFormOpen');
check(str_contains((string) $evIndex, 'evTutorialOpen'), 'tombol panduan');
check(str_contains((string) $evIndex, "event._tutorial', ['steps' => \$steps]"), 'tutorial include shared event._tutorial');
check(str_contains((string) $evIndex, 'name="is_active"'), 'toggle is_active');
check(str_contains((string) $evIndex, '@method(\'DELETE\')'), 'method spoofing delete');
check(str_contains((string) $evIndex, 'evDeleteOpen'), 'hapus via modal evDeleteOpen (bukan native confirm)');
check(str_contains((string) $evIndex, 'id="evDelModal"'), 'modal konfirmasi hapus ada');
check(str_contains((string) $evIndex, 'id="evDelForm"'), 'form share utk hapus');
check(!str_contains((string) $evIndex, "confirm('Hapus event"), 'tanpa confirm() native');

echo "\nViews (event/_tutorial — pola shared wizard):\n";
$evTut = @file_get_contents($base . '/resources/views/event/_tutorial.blade.php');
check($evTut !== false, 'view event/_tutorial ada');
check(str_contains((string) $evTut, 'Panduan Event'), 'header panduan');
check(str_contains((string) $evTut, 'ev-step'), 'step wizard .ev-step');
check(str_contains((string) $evTut, 'ev-dot'), 'progress dots .ev-dot');
check(str_contains((string) $evTut, 'evTutorialGo'), 'navigasi evTutorialGo');
check(str_contains((string) $evTut, 'evTutorialClose'), 'evTutorialClose ada');

echo "\nForm adaptif (kategori):\n";
check(str_contains((string) $evIndex, 'id="evTypeSelect"'), 'select kategori evTypeSelect');
foreach (['event', 'pengumuman', 'promo'] as $cat) {
    check(str_contains((string) $evIndex, 'value="' . $cat . '"'), "opsi kategori {$cat}");
}
check(str_contains((string) $evIndex, 'data-cats="event"'), 'field khusus event (lokasi/jadwal/kuota/kontak)');
check(str_contains((string) $evIndex, 'data-cats="event,promo"'), 'field event+promo (harga)');
check(str_contains((string) $evIndex, 'evApplyCats'), 'JS show/hide evApplyCats');
check(str_contains((string) $evIndex, 'catMap'), 'mapping type lama → kategori');

$homeView = @file_get_contents($base . '/resources/views/home.blade.php');
check($homeView !== false, 'view home ada');
check(str_contains((string) $homeView, '$events ?? []'), 'section events baca $events');
check(str_contains((string) $homeView, "route('event-detail'"), 'kartu event → detail event');
check(str_contains((string) $homeView, 'Events &amp; Info'), 'judul section Events & Info');
check(str_contains((string) $homeView, 'Find Trusted Nannies Near You'), 'hero banner statis asli (tidak diubah)');
check(str_contains((string) $homeView, '<!-- PARENTING ARTICLES -->'), 'section Parenting Articles tetap');
check(str_contains((string) $homeView, 'sliderTrack'), 'sliderTrack tetap ada');
check(str_contains((string) $homeView, 'initSlider'), 'initSlider tetap ada');
// Section events harus muncul SEBELUM komentar Parenting Articles
check(
    strpos((string) $homeView, 'EVENTS & INFO') !== false
    && strpos((string) $homeView, 'EVENTS & INFO') < strpos((string) $homeView, '<!-- PARENTING ARTICLES -->'),
    'section events di atas Parenting Articles'
);

echo "\nViews (event/show — detail event):\n";
$evShow = @file_get_contents($base . '/resources/views/event/show.blade.php');
check($evShow !== false, 'view event/show ada');
check(str_contains((string) $evShow, 'Event &amp; Info'), 'header detail event');
check(str_contains((string) $evShow, 'banner_url'), 'banner event');
check(str_contains((string) $evShow, 'event_start_at'), 'jadwal event');
check(str_contains((string) $evShow, 'Buka Tautan'), 'tombol link utama');
check(str_contains((string) $evShow, 'Buka Peta'), 'tombol map');

echo $fail === 0 ? "\nSemua PASS ✅\n" : "\n{$fail} FAIL ❌\n";
exit($fail === 0 ? 0 : 1);
