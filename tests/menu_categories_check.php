<?php

require __DIR__ . '/../vendor/autoload.php';

$groups = collect([
    ['nama' => 'Task', 'category' => 'Sekolah', 'urutan' => 40],
    ['nama' => 'Diary', 'category' => 'Utama', 'urutan' => 30],
    ['nama' => 'Legacy', 'urutan' => 60],
    ['nama' => 'Child', 'category' => 'Utama', 'urutan' => 20],
    ['nama' => 'Blank', 'category' => ' ', 'urutan' => 70],
])->sortBy('urutan')->groupBy(fn ($menu) => trim($menu['category'] ?? '') ?: 'Lainnya');

$checks = [
    $groups->keys()->all() === ['Utama', 'Sekolah', 'Lainnya'],
    $groups['Utama']->pluck('nama')->all() === ['Child', 'Diary'],
    $groups['Lainnya']->pluck('nama')->all() === ['Legacy', 'Blank'],
];
if (in_array(false, $checks, true)) {
    throw new RuntimeException('Menu category grouping failed.');
}
echo "Menu category checks passed.\n";
