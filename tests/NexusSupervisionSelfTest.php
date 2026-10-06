<?php

/**
 * Self-check frontend Modul 21 — Nexus Supervision (Laravel_Web_App).
 *
 * Jalankan: php tests/NexusSupervisionSelfTest.php
 * (Butuh sesi login agar `session('token')` terisi; jika tanpa token proxy
 *  mengembalikan data kosong — itu pun valid.)
 */

$base = __DIR__ . '/..';

define('ARTISAN', $base . '/artisan');

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

echo "\nController & routes:\n";
$ctrl = file_get_contents($base . '/app/Http/Controllers/NexusSupervisionController.php');
check(str_contains($ctrl, 'class NexusSupervisionController'), 'controller ada & extends Controller');
check(str_contains($ctrl, '/nexus/supervision/nanny/'), 'proxy attendance URL benar');
check(str_contains($ctrl, "config('services.api.base_url"), 'pakai base URL config');
check(str_contains($ctrl, "session('token')"), 'token dari session (tidak exposed)');

$web = file_get_contents($base . '/routes/web.php');
check(str_contains($web, "name('nexus-supervision')"), "route name 'nexus-supervision' terdaftar (cocok dengan seeder menu)");
check(str_contains($web, "name('nexus-supervision-nanny')"), "route 'nexus-supervision-nanny' ada");
check(str_contains($web, "name('nexus-supervision-nanny-attendance')"), "route 'nexus-supervision-nanny-attendance' ada");
check(str_contains($web, '->whereNumber(\'id\')'), 'nexus/{id} dibatasi numeric supaya tidak shadow supervision');

echo "\nView files:\n";
foreach ([
    'resources/views/nexus/supervision/index.blade.php'      => ['route(\'nexus-supervision-nanny\'', 'link ke detail nanny', 'count($nannies)'],
    'resources/views/nexus/supervision/nanny.blade.php'      => ['route(\'nexus-supervision-nanny-attendance\'', 'link ke riwayat', '$recentTimeline'],
    'resources/views/nexus/supervision/attendance.blade.php' => ['route(\'nexus-supervision-nanny-attendance\'', 'form filter', '$meta'],
] as $file => $checks) {
    echo "\n{$file}:\n";
    $v = file_get_contents($base . '/' . $file);
    check($v !== false, 'file ada');
    if ($v === false) {
        continue;
    }
    foreach ($checks as $needle => $label) {
        check(str_contains($v, $needle), $label);
    }
    check(str_contains($v, "@extends('layouts.app')"), 'extend layout app');
}

echo $fail === 0 ? "\nSemua PASS ✅\n" : "\n{$fail} FAIL ❌\n";
exit($fail === 0 ? 0 : 1);