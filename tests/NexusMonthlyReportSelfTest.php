<?php

/**
 * Self-check frontend Modul 22 — Nexus Monthly Report (Laravel_Web_App).
 *
 * Verifikasi: controller proxy, route names (cocok seeder menu), view files.
 * Jalankan: php tests/NexusMonthlyReportSelfTest.php
 */

$base = __DIR__ . '/..';

function run(string $cmd): string
{
    $out   = [];
    $code  = 0;
    exec($cmd . ' 2>&1', $out, $code);
    return implode("\n", $out);
}

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

echo "\nController (proxy):\n";
$ctrlPath = $base . '/app/Http/Controllers/NexusMonthlyReportController.php';
$ctrl = @file_get_contents($ctrlPath);
check($ctrl !== false, 'file controller ada');
check(str_contains((string) $ctrl, 'class NexusMonthlyReportController'), 'class NexusMonthlyReportController');
check(str_contains((string) $ctrl, "config('services.api.base_url"), 'pakai base URL config');
check(str_contains((string) $ctrl, "session('token')"), 'token dari session (tidak exposed)');
check(str_contains((string) $ctrl, 'function nexusIndex'), 'method nexusIndex (daftar + filter)');
check(str_contains((string) $ctrl, 'function nexusStore'), 'method nexusStore (multipart upload)');
check(str_contains((string) $ctrl, 'function nexusResend'), 'method nexusResend');
check(str_contains((string) $ctrl, 'function nexusDestroy'), 'method nexusDestroy (delete)');
check(str_contains((string) $ctrl, 'asMultipart'), 'upload via asMultipart + fopen (pola terbukti)');
check(str_contains((string) $ctrl, 'function majikanIndex'), 'method majikanIndex');
check(str_contains((string) $ctrl, 'function majikanShow'), 'method majikanShow');
check(str_contains((string) $ctrl, 'function majikanHistory'), 'method majikanHistory');
check(str_contains((string) $ctrl, 'function majikanDownload'), 'method majikanDownload');
check(str_contains((string) $ctrl, 'function majikanViewPdf'), 'method majikanViewPdf (inline)');
check(str_contains((string) $ctrl, 'function nannyIndex'), 'method nannyIndex');
check(str_contains((string) $ctrl, 'function nannyShow'), 'method nannyShow');
check(str_contains((string) $ctrl, 'function nannyHistory'), 'method nannyHistory');
check(str_contains((string) $ctrl, 'function nannyDownload'), 'method nannyDownload');
check(str_contains((string) $ctrl, 'function nannyViewPdf'), 'method nannyViewPdf');
check(str_contains((string) $ctrl, 'servePdf'), 'servePdf helper (stream PDF)');
check(str_contains((string) $ctrl, '/nexus-monthly-reports'), 'proxy URL backend nexus-monthly-reports');
check(str_contains((string) $ctrl, '/children/'), 'proxy URL per-child');

echo "\nRoutes (web.php):\n";
$web = file_get_contents($base . '/routes/web.php');
check(str_contains($web, 'NexusMonthlyReportController'), 'NexusMonthlyReportController diimport');
foreach ([
    "name('nexus-monthly-reports')",
    "name('nexus-monthly-reports.store')",
    "name('nexus-monthly-reports.resend')",
    "name('nexus-monthly-reports.destroy')",
    "name('nexus-monthly-reports.download')",
    "name('nexus-monthly-reports.view')",
    "name('majikan-monthly-reports')",
    "name('majikan-monthly-reports-show')",
    "name('majikan-monthly-reports-history')",
    "name('majikan-monthly-reports-download')",
    "name('majikan-monthly-reports-view')",
    "name('nanny-monthly-reports')",
    "name('nanny-monthly-reports-show')",
    "name('nanny-monthly-reports-history')",
    "name('nanny-monthly-reports-download')",
    "name('nanny-monthly-reports-view')",
] as $needle) {
    check(str_contains($web, $needle), "route {$needle}");
}
// Route name utk seeder menu: nexus-monthly-reports / majikan-/nanny- prefix
check(str_contains($web, "'nexus-monthly-reports'") || str_contains($web, "name('nexus-monthly-reports')"),
    'nama route cocok kolom menus (seeder modul 22)');

echo "\nViews:\n";
foreach ([
    'resources/views/nexus/monthly-report/index.blade.php' => ['route(\'nexus-monthly-reports.store\'', 'form upload', 'name="pdf"', 'nmrUploadOpen', 'nmrTutorialOpen', 'monthly-report._tutorial', 'nmrPdfModal', 'modal preview PDF', 'nmrPdfFullscreen', 'tombol layar penuh', 'nmrPdfOpen('],
    'resources/views/monthly-report/index.blade.php'       => ['$showRoute', 'selector anak via $showRoute', 'Choose Child'],
    'resources/views/monthly-report/show.blade.php'        => ['$jsPrefix', 'JS prefix dinamis', 'PdfOpen', 'GoToPage', 'nmrTutorialOpen', 'monthly-report._tutorial', 'PdfFullscreen', 'tombol layar penuh'],
    'resources/views/monthly-report/_history.blade.php'    => ['$downloadRoute', 'tombol download', 'viewRoute', 'read-only'],
    'resources/views/monthly-report/_tutorial.blade.php'   => ['nmrTutorialModal', 'modal tutorial', 'nmrTutorialOpen', 'nmrTutorialGo', 'Langkah'],
] as $file => $checks) {
    echo "\n{$file}:\n";
    $v = @file_get_contents($base . '/' . $file);
    check($v !== false, 'file ada');
    if ($v === false) {
        continue;
    }
    foreach ($checks as $needle => $label) {
        check(str_contains((string) $v, $needle), $label);
    }
    check(str_contains((string) $v, "@extends('layouts.app')") || str_contains((string) $v, 'id='),
        str_contains((string) $v, "@extends('layouts.app')") ? 'extend layout app' : 'partial (tanpa layout)');
}

echo "\nLint (php -l):\n";
echo run('php -l ' . escapeshellarg($ctrlPath)) . "\n";
check(str_contains(run('php -l ' . escapeshellarg($ctrlPath)), 'No syntax errors'), 'controller tanpa syntax error');

echo $fail === 0 ? "\nSemua PASS ✅\n" : "\n{$fail} FAIL ❌\n";
exit($fail === 0 ? 0 : 1);
