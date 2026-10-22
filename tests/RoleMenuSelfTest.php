<?php

/**
 * Self-check frontend — Role Menu Management (Admin, role 1) di Laravel_Web_App.
 *
 * Verifikasi: controller proxy, route names, view, guard role 1, format boolean.
 * Jalankan: php tests/RoleMenuSelfTest.php
 */

$base = __DIR__ . '/..';

function run(string $cmd): string
{
    $out  = [];
    $code = 0;
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

echo "\nController (proxy RoleMenuController):\n";
$ctrlPath = $base . '/app/Http/Controllers/RoleMenuController.php';
$ctrl = @file_get_contents($ctrlPath);
check($ctrl !== false, 'file controller ada');
check(str_contains((string) $ctrl, 'class RoleMenuController'), 'class RoleMenuController');
check(str_contains((string) $ctrl, "config('services.api.base_url"), 'pakai base URL config');
check(str_contains((string) $ctrl, "session('token')"), 'token dari session (tidak exposed)');
check(str_contains((string) $ctrl, 'function index'), 'method index');
check(str_contains((string) $ctrl, 'function update'), 'method update (PUT /role-menu)');
check(str_contains((string) $ctrl, 'function storeMenu'), 'method storeMenu (POST /menu)');
check(str_contains((string) $ctrl, 'function storeRoleMenu'), 'method storeRoleMenu (POST /role-menu)');
check(str_contains((string) $ctrl, "'/role-menu'"), 'proxy URL backend /role-menu');
check(str_contains((string) $ctrl, "'/menu'"), 'proxy URL backend /menu');
check(str_contains((string) $ctrl, "->put("), 'update pakai PUT');
check(str_contains((string) $ctrl, "->post("), 'create pakai POST');

echo "\nGuard admin (role 1):\n";
check(str_contains((string) $ctrl, "session('user')['id_role']"), 'baca id_role dari session');
check(str_contains((string) $ctrl, "!== 1"), 'hanya role 1 yang boleh akses');
check(str_contains((string) $ctrl, "requireAdmin"), 'helper requireAdmin dipakai');
check(substr_count((string) $ctrl, '$this->requireAdmin()') >= 3, 'guard di semua method (index/update/store*)');

echo "\nFormat boolean (backend hanya terima true/false/0/1):\n";
check(!str_contains((string) $ctrl, "'true'"), "tidak kirim string 'true'");
check(!str_contains((string) $ctrl, "'false'"), "tidak kirim string 'false'");

echo "\nRoutes (web.php):\n";
$web = file_get_contents($base . '/routes/web.php');
check(str_contains($web, 'RoleMenuController'), 'RoleMenuController diimport');
foreach ([
    "name('admin-role-menu')",
    "name('admin-role-menu.store-menu')",
    "name('admin-role-menu.store-role-menu')",
    "name('admin-role-menu.update')",
] as $needle) {
    check(str_contains($web, $needle), "route {$needle}");
}
check(str_contains($web, "Route::prefix('admin/role-menu')"), 'prefix admin/role-menu');
// Route literal harus didaftarkan sebelum /{id} supaya tidak tertangkap.
$idxPost = strpos($web, "name('admin-role-menu.store-role-menu')");
$idxPut  = strpos($web, "name('admin-role-menu.update')");
check($idxPost !== false && $idxPut !== false && $idxPost < $idxPut,
    'route literal (/role-menu) didaftarkan sebelum /{id}');

echo "\nViews:\n";
foreach ([
    'resources/views/admin/role-menu/index.blade.php' => [
        '@extends(\'layouts.app\')',
        'Tambah Menu Baru',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
        'data-id-role',
        'data-field',
        'savePermission',
        'admin-role-menu.update',
        'admin-role-menu.store-role-menu',
        'admin-role-menu.store-menu',
        'showAppAlert',
    ],
] as $file => $checks) {
    echo "\n{$file}:\n";
    $v = @file_get_contents($base . '/' . $file);
    check($v !== false, 'file ada');
    if ($v === false) {
        continue;
    }
    foreach ($checks as $needle) {
        check(str_contains((string) $v, $needle), "mengandung: {$needle}");
    }
    // Guard: halaman hanya untuk admin
    check(!str_contains((string) $v, 'nexus-monthly-reports.store'), 'tidakMixed view nexus (tidak ada form upload nexus)');
}

echo "\nLint (php -l):\n";
echo run('php -l ' . escapeshellarg($ctrlPath)) . "\n";
check(str_contains(run('php -l ' . escapeshellarg($ctrlPath)), 'No syntax errors'), 'controller tanpa syntax error');
echo run('php -l ' . escapeshellarg($base . '/routes/web.php')) . "\n";
check(str_contains(run('php -l ' . escapeshellarg($base . '/routes/web.php')), 'No syntax errors'), 'routes/web.php tanpa syntax error');

echo $fail === 0 ? "\nSemua PASS ✅\n" : "\n{$fail} FAIL ❌\n";
exit($fail === 0 ? 0 : 1);
