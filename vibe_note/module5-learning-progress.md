# MODULE 5 — Learning Progress (Backend + Frontend)

Tanggal: 2026-08-07
Status: Fungsional — Backend (API) ✅ + Frontend Nanny (input & chart) ✅ + Frontend Majikan (chart read-only) ✅ + Pagination history ✅ + Tutorial modal ✅

---

## 1. Ringkasan

Fitur **Learning Progress** dibangun end-to-end. Nanny (role 3) merekam skor
perkembangan belajar anak per kategori; Majikan (role 2) melihat grafik
perkembangannya (read-only). Tabel `learning_progress` sudah ada di DB
(tanpa migration baru).

Keputusan desain (dari diskusi, 2026-08-07):
- **Rubrik skor 5 tingkat** disertakan di sistem (panduan antar-pencatat supaya konsisten):
  `0-20 Belum`, `21-40 Perlu Bantuan`, `41-60 Berkembang`, `61-80 Mahir`, `81-100 Menguasai`.
- **Chart agregasi mingguan** (default) + dukung `group_by=month`.

---

## 2. Backend API — `AlphaKidz-Backend`

| File | Peran |
|------|-------|
| `app/Models/LearningProgress.php` | Model → tabel `learning_progress` existing (NO migration) |
| `app/Http/Controllers/LearningProgressController.php` | CRUD + `getByChild` + `learningChart` (agregasi minggu/bulan) |
| `app/Http/Requests/LearningProgress/StoreLearningProgressRequest.php` | Validasi create (`id_anak`, `category` in enum, `score` 0-100, `note`, `recorded_date`) |
| `app/Http/Requests/LearningProgress/UpdateLearningProgressRequest.php` | Validasi update |
| `app/Http/Resources/LearningProgress/LearningProgressResource.php` | Resource + `rubric` (level per skor) + nama anak/pencatat |
| `tests/LearningProgressSelfTest.php` | Self-check rubrik + agregator chart (PASS) |

Routes backend (7):
```
GET    learning-progress?per_page&id_anak&category&recorded_date  → index (paginated)
POST   learning-progress                                          → store
GET    learning-progress/{id}                                     → show
PUT    learning-progress/{id}                                     → update
DELETE learning-progress/{id}                                     → destroy
GET    children/{id_anak}/learning-progress                       → getByChild (array)
GET    learning-progress/chart?id_anak&from&to&group_by=week|month → learningChart
```

Detail penting:
- **Chart** per kategori: `series` (per periode: `{period, avg_score, count}`), `current`,
  `delta` (perubahan vs periode sebelumnya), `attention` (`ok` / `decline` bila ≤ -20 /
  `insufficient_data` / `no_data`). `count` menandakan frekuensi perekaman.
- `periodKey`: `Y-W` (ISO week) utk mingguan, `Y-m` utk bulanan.
- **Akses role**: read = 1-4; write = 1 & 3. Child-access pola sama `AcademicTaskController`
  (Admin/Majikan bypass; Nanny dibatasi anak yang di-assign aktif). `recorded_by = auth()->id()`.
- `learning-progress/chart` didaftarkan **sebelum** `apiResource` agar literal `chart`
  tidak tertelan parameter `{learning_progress}`.

---

## 3. Frontend — `Laravel_Web_App` (proxy controller + Blade)

| File | Peran |
|------|-------|
| `app/Http/Controllers/LearningProgressController.php` | Proxy: nannyIndex/show/create/store/destroy + majikanIndex/show |
| `routes/web.php` | 7 route: `nanny/learning-progress*` (5) + `majikan/learning-progress*` (2) |
| `resources/views/nanny/learning-progress/index.blade.php` | Pilih anak (pola sama tracking/diary) |
| `resources/views/nanny/learning-progress/show.blade.php` | Rubrik legend + Development Overview (bar per kategori) + History + FAB tambah + hapus |
| `resources/views/nanny/learning-progress/create.blade.php` | Form: kategori (grid 6), slider skor 0-100 + level rubrik realtime, **skor terakhir per kategori** sbg pembanding, tanggal, catatan |
| `resources/views/majikan/learning-progress/index.blade.php` | Pilih anak (read-only) |
| `resources/views/majikan/learning-progress/show.blade.php` | **Trend chart line per kategori** (SVG kecil, tooltip per titik, area wash) + tabel History |

Route names: `nanny-learning` / `nanny-learning-show` / `nanny-learning-create` /
`nanny-learning-store` / `nanny-learning-destroy` / `majikan-learning` / `majikan-learning-show`.

Detail penting:
- Proxy controller: token di session, tidak terekspos ke browser (pola sama AcademicTask/tracking).
- **Nanny**: dropdown pilih anak dari `nanny-assignments-anak-for-nanny`; form slider menampilkan
  level rubrik live; kategori menampilkan skor terakhir sbg pembanding (input konsisten).
- **Majikan**: read-only, trend line chart per kategori (chart hanya muncul bila ≥ 2 periode data),
  tabel history utk akses tanpa hover (nilai selalu reachable).
- Chart warna: tiap kategori warna tetap (6 kategori) — identitas dari label card + warna.

---

## 4. Pagination Riwayat (2026-08-07)

History page Nanny & Majikan kini ter-paginate (10/halaman), dengan tombol prev/`current/last`/next
+ swap AJAX — pola sama `nanny/academic-task/_list.blade.php`.

### Backend (`AlphaKidz-Backend`)
- `getByChild` & `index` memakai `->paginate($request->input('per_page', N))`.
- **PENTING (fix meta)**: `LearningProgressResource::collection($paginator)` yang di-nest di dalam
  array JSON `data` TIDAK otomatis menyertakan pagination `meta` (Laravel hanya menambahkannya bila
  resource jadi top-level). Solusi: kembalikan `data` sebagai `['data' => collection, 'meta' =>
  {current_page, last_page, total, per_page}]`. Tanpa ini frontend dapat `pagination: null` →
  tombol tidak render (bug yang baru diperbaiki).

### Frontend (`Laravel_Web_App`)
- `LearningProgressController` (proxy): `fetchRecords($idAnak, $page, $perPage=10)` mengembalikan
  `['records' => [...], 'pagination' => {...}|null]` (deteksi bentuk `data['data']`+`data['meta']`,
  fallback bentuk datar).
- `nannyShow`/`majikanShow` baca `?page=` + teruskan `records` & `pagination` ke view.
- Method baru `nannyHistory`/`majikanHistory`: none-AJAX → redirect ke show; AJAX → render partial.
- Routes baru: `nanny-learning-history` (`nanny/learning-progress/{id_anak}/history`) &
  `majikan-learning-history` (`majikan/learning-progress/{id_anak}/history`), didaftarkan SEBELUM
  route `{id_anak}` (agar literal `history` tidak tertelan).
- Partial `nanny/learning-progress/_history.blade.php` (kartu + hapus) &
  `majikan/learning-progress/_history.blade.php` (tabel), masing-masing dengan baris pagination.
- `show.blade.php` (Nanny & Majikan): `@include` partial; script `lpGoToPage(page)` fetch partial via
  `X-Requested-With: XMLHttpRequest` lalu swap `#historyList` (outerHTML).

---

## 5. Tutorial Modal (2026-08-07)

Penjelasan sistem perhitungan progress tidak dihapus dari badge — dipindah ke **modal tutorial**
yang bisa dibuka kapan saja lewat tombol bantuan `?` di header halaman (Nanny & Majikan).

| File | Peran |
|------|-------|
| `resources/views/learning-progress/_tutorial.blade.php` | Partial modal tutorial (dipakai bersama) |
| `nanny/learning-progress/show.blade.php` | Tombol `?` header + `$steps` 5 langkah (sudut input) |
| `majikan/learning-progress/show.blade.php` | Tombol `?` header + `$steps` 5 langkah (sudut monitoring) |

Perilaku:
- Modal bottom-sheet/sentra (mobile-friendly), header ungu, judul + counter "Langkah X dari N".
- Navigasi: tombol **Sebelumnya / Berikutnya** (jadi **Selesai** di langkah terakhir), dots progress,
  tutup via **X** / klik backdrop / tombol **Escape**.
- `$steps` = array `[icon, color, title, body(HTML)]` — body render `{!! !!}` sehingga bisa `<ul>/<b>`.
- Partial berisi IIFE JS yang bind sekali (`window.__lpTutorialBound`) → aman di-include dua kali.
- **Nanny** (5 langkah): apa itu LP → skor & rubrik → cara mencatat (FAB, slider, skor terakhir
  pembanding) → **skor mingguan rata-rata** (kenapa nambah data di minggu sama tak mengubah grafik,
  butuh 2 minggu berbeda) → badge status.
- **Majikan** (5 langkah): apa itu LP (read-only) → skor & rubrik → membaca grafik tren & delta →
  badge status → riwayat & pagination.

Catatan: tombol `?` diletakkan di header, setelah dropdown ganti anak; badge **"Need more data"**
tetap ada — kini dapat dijelaskan via tombol `?` tanpa menghapus labelnya.

---

## 6. Menu Dashboard

- `database/migration_sql/alphakidz-07agustus2026_learning_progress_menu.sql`
  → tambah menu `Learning Progress` (id 20 utk Nanny route `nanny-learning`, id 21 utk Majikan
  route `majikan-learning`) + role_menu. **Belum di-run** — user run manual (DEVELOPMENT RULES).

---

## 7. Verifikasi

- `php -l` semua controller backend & frontend + routes → tanpa error.
- `php artisan route:list --path=learning-progress` (backend & frontend) → route tampil benar
  (termasuk 2 route `history` baru di frontend).
- `php artisan view:cache` (Laravel_Web_App) → semua Blade berhasil dikompilasi.
- `php tests/LearningProgressSelfTest.php` (backend) → semua PASS.

---

## 8. Belum dikerjakan / Next

- Menu SQL belum di-run user (dashboard belum tampil menu Learning Progress).
- SQL seed `learning_progress_chalista` belum di-run user (dipakai utk tes pagination & chart).
- Endpoint `learning-progress/chart` belum punya preset rentang tanggal di UI (mengambil semua data).
- Bandingkan skor dgn ekspektasi umur (milestone) → Modul 16 (AI Learning Insight).
