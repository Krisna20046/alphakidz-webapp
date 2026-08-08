# MODULE 6 — Assistant Notes (Frontend UI — Nanny & Majikan)

Tanggal: 2026-08-08
Status: Fungsional — Backend (API) ✅ + Frontend Nanny (input & riwayat) ✅ + Frontend Majikan (read-only) ✅ + Delete modal & fix ikon mood ✅

---

## 1. Ringkasan

Fitur **Assistant Notes** (catatan harian asisten) dibangun end-to-end di frontend.
Nanny (role 3) mencatat mood + highlight + concern + rekomendasi anak per hari;
Majikan (role 2) melihat riwayatnya (read-only). Backend API sudah ada sebelumnya
(Modul 6, branch `feat/assistant-notes` di `AlphaKidz-Backend`).

Keputusan desain:
- **Mood enum** `senang/sedih/marah/biasa` (sama dgn diary) ditampilkan sebagai badge
  berwarna di kartu riwayat.
- **Highlight/Concern/Rekomendasi** tampil sebagai blok berwarna terpisah
  (ungu = highlight, merah = concern, hijau = rekomendasi).
- **Terkait tugas (opsional)**: form bisa mengaitkan catatan ke `academic_tasks` anak
  (dropdown), ditampilkan sebagai baris judul tugas di kartu.
- **Pagination riwayat** (10/halaman, AJAX partial swap) — pola sama Learning Progress.

---

## 2. Frontend — `Laravel_Web_App` (proxy controller + Blade)

| File | Peran |
|------|-------|
| `app/Http/Controllers/AssistantNoteController.php` | Proxy: nannyIndex/show/create/store/destroy + majikanIndex/show + history AJAX |
| `routes/web.php` | 9 route: `nanny/assistant-notes*` (6) + `majikan/assistant-notes*` (3) |
| `resources/views/assistant-notes/_tutorial.blade.php` | Partial modal tutorial (dipakai bersama) |
| `resources/views/nanny/assistant-notes/index.blade.php` | Pilih anak (pola sama tracking/diary) |
| `resources/views/nanny/assistant-notes/show.blade.php` | Riwayat + FAB tambah + dropdown ganti anak + tombol tutorial (?) |
| `resources/views/nanny/assistant-notes/_history.blade.php` | Kartu riwayat (badge mood, task, highlight/concern/rekomendasi) + hapus + pagination |
| `resources/views/nanny/assistant-notes/create.blade.php` | Form: mood (grid 4), task opsional, highlight/concern/rekomendasi |
| `resources/views/majikan/assistant-notes/index.blade.php` | Pilih anak (read-only) |
| `resources/views/majikan/assistant-notes/show.blade.php` | Riwayat read-only + tutorial |
| `resources/views/majikan/assistant-notes/_history.blade.php` | Kartu riwayat (tanpa hapus) + pagination |

Route names: `nanny-notes` / `nanny-notes-show` / `nanny-notes-history` /
`nanny-notes-create` / `nanny-notes-store` / `nanny-notes-destroy` /
`majikan-notes` / `majikan-notes-show` / `majikan-notes-history`.

Detail penting:
- Proxy controller: token di session, tidak terekspos ke browser (pola sama
  LearningProgress/AcademicTask). Endpoint backend: `children/{id_anak}/assistant-notes`
  (paginated `data['data']`+`data['meta']`), `POST assistant-notes`, `DELETE assistant-notes/{id}`.
- **Dropdown task** di form diambil dari `children/{id_anak}/academic-tasks?per_page=100`
  (backend sudah mengizinkan read untuk nanny), parsed ke `[{id, title}]`.
- **Hapus hanya Nanny** (route destroy); Majikan read-only (tidak ada tombol hapus/FAB).
- **Tutorial modal** (5 langkah per role) memakai pola `learning-progress/_tutorial.blade.php`
  tapi dengan prefix JS `anTutorial*` & ID `an*` agar tidak bentrok bila dua tutorial
  dimuat dalam satu halaman.

---

## 3. Menu Dashboard

- `AlphaKidz-Backend/database/migration_sql/alphakidz-08agustus2026_assistant_notes_menu.sql`
  → tambah menu `Catatan Asisten` (id 22 utk Majikan route `majikan-notes`, id 23 utk Nanny
  route `nanny-notes`) + role_menu (Majikan: view; Nanny: view+create+delete).
  **Belum di-run** — user run manual (DEVELOPMENT RULES).

---

## 4. Verifikasi

- `php -l` controller & routes (backend & frontend) → tanpa error.
- `php artisan route:list --path=assistant-notes` (backend & frontend) → route tampil benar
  (backend 6, frontend 9).
- `php artisan view:cache` (Laravel_Web_App) → semua Blade berhasil dikompilasi.
- `php tests/AssistantNoteSelfTest.php` (backend) → 18/18 PASS.

---

## 4b. Polesan & Fix (2026-08-08)

- **Fix ikon mood "marah" tidak tampil**: penyebabnya `angry-outline` TIDAK ada di set Ionicons
  (set hanya punya wajah `happy`/`sad`), sehingga `<ion-icon name="angry-outline">` gagal render.
  Solusi: ganti ke **`flame-outline`** (valid) di ketiga file array `$moodMeta`
  (`nanny/assistant-notes/create.blade.php`, `nanny/assistant-notes/_history.blade.php`,
  `majikan/assistant-notes/_history.blade.php`) agar seragam.
- **Modal konfirmasi hapus in-app**: blok `<form>..onsubmit="confirm()"` di `_history` diganti tombol
  murni yang memanggil `anDeleteConfirm(url)`; modal `#anDeleteModal` + fungsi JS di
  `nanny/assistant-notes/show.blade.php` (sengaja di file show, bukan partial, karena partial
  `_history` di-swap saat pagination → modal di partial akan hilang). Tombol: Batal / Ya, Hapus,
  backdrop + Escape utk tutup.
- **Seeder data** ditambahkan: `AlphaKidz-Backend/database/seeder_sql/alphakidz-08agustus2026_assistant_notes_chalista.sql`
  (nanny id 57, anak 25, 14 catatan, task_id NULL, mood bervariasi).

---
## 5. Belum dikerjakan / Next

- Menu SQL belum di-run user (dashboard belum tampil menu Assistant Notes).
- Tidak ada edit catatan (hanya tambah/hapus) — sesuai scope Modul 6 (belum diminta).
- Konsultan/Nexus belum melihat catatan (backend read role 1-4, konsultan role 4 sudah include).
