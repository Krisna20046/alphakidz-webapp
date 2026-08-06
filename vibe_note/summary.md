# SUMMARY.md — Progress Academic Task & Task Progress (Modul 3 & 4)

Tanggal: 2026-08-05
Status: Fungsional — Backend (API) ✅ + Frontend Nanny (input) ✅ + Frontend Majikan (tracking) ✅

---

## 1. Ringkasan

Fitur **Tracking & Input PR/Project/Deadline/Nilai/Foto Tugas** sudah dibangun end-to-end:

| Lapisan | Status | Lokasi |
|---------|--------|--------|
| Backend API (Modul 3 & 4) | ✅ | `AlphaKidz-Backend` |
| Frontend input (role Nanny) | ✅ | `Laravel_Web_App/resources/views/nanny/academic-task/` |
| Frontend tracking (role Majikan) | ✅ | `Laravel_Web_App/resources/views/majikan/tracking/` |
| Preview jadwal tabel di Majikan | ✅ | modal di `majikan/tracking/show.blade.php` |

Arsitektur frontend tetap memakai **proxy controller**: request diteruskan ke backend via `Http::`
dengan token dari session (token tidak pernah terekspos ke browser).

---

## 2. Backend API — `AlphaKidz-Backend`

### File baru

| File | Peran |
|------|-------|
| `app/Models/AcademicTask.php` | Model tugas (`type`, `deadline`, `score`, `attachment`, relasi anak/assignment/subject/progress) |
| `app/Models/AcademicTaskProgress.php` | Model progres (`progress_percentage`, `photo`, `note`) |
| `app/Http/Controllers/AcademicTaskController.php` | CRUD + getByChild, updateStatus, markCompleted, uploadAttachment, overdueTasks |
| `app/Http/Controllers/AcademicTaskProgressController.php` | CRUD progres + getByTask; sinkron status tugas (100% → completed) |
| `app/Http/Requests/AcademicTask/*` | Validasi Store/Update (Form Request) |
| `app/Http/Requests/AcademicTaskProgress/*` | Validasi Store progres |
| `app/Http/Resources/AcademicTask/*` | Resource + URL foto `attachment` |
| `app/Http/Resources/AcademicTaskProgress/*` | Resource + URL foto `photo` |

### Routes backend (16 route, di `routes/api.php`)
```
apiResource academic-tasks          → CRUD
GET  children/{id_anak}/academic-tasks   → per anak
PATCH academic-tasks/{id}/status
PATCH academic-tasks/{id}/complete
POST  academic-tasks/{id}/upload
GET   academic-tasks-overdue
apiResource task-progress           → CRUD
GET   academic-tasks/{task_id}/progress → per tugas
```

### Detail penting
- Tabel `academic_tasks` & `academic_task_progress` sudah ada di DB (migration
  `2026_07_13_100000_create_remaining_tables` sudah run) — **tidak perlu migrate**.
- Folder storage dibuat: `storage/app/public/academic_tasks` & `task_progress`.
- Foto diproses `ImageHelper` (dikompres → WebP).
- **Akses role**: read = Admin(1)/Majikan(2)/Nanny(3)/Konsultan(4); write = Admin/Nanny.
  Nanny dibatasi hanya anak yang di-assign aktif (child access via `nanny_assignment` + `assignment_anak`).
- Menambah/update progres otomatis menyinkronkan status tugas: `progress_percentage >= 100` → `completed`.

---

## 3. Frontend Nanny (input) — `Laravel_Web_App`

### File
| File | Peran |
|------|-------|
| `app/Http/Controllers/AcademicTaskController.php` | Proxy: index/create/store/show/edit/update/destroy/updateStatus/markComplete/storeProgress |
| `routes/web.php` | Route `academic-task.*` (10 route, authed) |
| `resources/views/nanny/academic-task/index.blade.php` | List + filter status/type (AJAX) + pagination + FAB |
| `resources/views/nanny/academic-task/_list.blade.php` | Partial list (di-swap AJAX) |
| `resources/views/nanny/academic-task/create.blade.php` | Form tambah: child + type (Homework/Project/Exam) + subject + deadline + priority + upload foto |
| `resources/views/nanny/academic-task/show.blade.php` | Detail + badge status + nilai ⭐ + progress bar + modal Update Progress (foto) + Mark Complete |
| `resources/views/nanny/academic-task/edit.blade.php` | Form ubah + input score (nilai) + status + hapus |

Route names: `academic-task.index`, `.create`, `.store`, `.show`, `.edit`, `.update`, `.destroy`, `.complete`, `.progress`.

### Detail penting
- Dropdown child diambil dari endpoint `nanny-assignments-anak-for-nanny` (pola sama SchoolSchedule);
  hidden `id_assignment` diisi otomatis dari `data-assignment`.
- Progress 100% otomatis menandai tugas selesai.

---

## 4. Frontend Majikan (tracking) — `Laravel_Web_App`

### File
| File | Peran |
|------|-------|
| `app/Http/Controllers/MajikanTrackingController.php` | Proxy read-only: daftar anak, tasks & jadwal per anak |
| `routes/web.php` | Route `majikan-tracking` & `majikan-tracking-show` |
| `resources/views/majikan/tracking/index.blade.php` | Pilih anak (pola sama `diary-choose`) |
| `resources/views/majikan/tracking/show.blade.php` | Stat (Total/Doing/Done/Overdue) + School Schedule (per hari + modal preview tabel mingguan) + Academic Tasks (progress bar, nilai, foto) |

Route names: `majikan-tracking` (GET `majikan/monitoring`), `majikan-tracking-show` (GET `majikan/monitoring/{id_anak}`).

### Detail penting
- Backend sudah mengizinkan Majikan (role 2) read task & jadwal anaknya — tidak ada ubah backend.
- **Preview jadwal tabel mingguan** (Monday–Sunday × slot waktu) ditambahkan sebagai modal, meniru preview nanny.
- Dropdown ganti anak cepat bila >1 anak.

---

## 5. Perilaku API yang dipelajari

- Backend memakai **`success`** (bool), proxy pakai helper `isSuccess()` yang menerima
  `success === true` ATAU `status === 'success'` (backward-compatible dgn endpoint lama).
- List paginated berbentuk nested `data.data` + `data.meta` (dengan fallback bentuk datar).
- Endpoint `children/{id}/...` mengembalikan array (bukan paginated) → parse `data` langsung.
- `deadline` dikirim format `Y-m-d H:i:s` (dikonversi dari `datetime-local` di proxy).
- Upload foto wajib multipart (`attach()`), terima `jpeg/png/jpg` max 10MB.

---

## 6. Bugs yang pernah diperbaiki

### a. (Backend) Baris TODO.md rusak saat edit
Beberapa baris Module 3 TODO sempat terkorupsi (karakter `�`, `[Click ...`, `[x]*`).
Solusi: perbaiki manual via PowerShell (ditulis ulang 3 baris 0-index 236–238 + hapus `*` nyasar). Diperbaiki.

### b. (Frontend) Update tugas pakai PUT multipart
Laravel HTTP client tidak bisa `->put()` dengan `->attach()`. Solusi: kirim `POST {id}/update` dengan
`_method=PUT` via `->post()` (pola di route frontend `academic-task.update`). Diperbaiki sejak awal.

---

## 7. Verifikasi

- `php -l` semua controller backend & frontend → tanpa error.
- `php artisan route:list --path=academic` / `--path=academic-task` / `--path=monitoring` → route tampil benar.
- `php artisan view:cache` (Laravel_Web_App) → semua Blade template (termasuk preview modal majikan) berhasil dikompilasi.
- DB migration `academic_tasks` sudah Ran (cek `migrate:status`).

---

## 8. Belum dikerjakan / PR

- Endpoint `academic-tasks-overdue` & `updateStatus` sudah ada di backend tapi belum dipakai UI
  (overdue sudah dihitung di halaman tracking majikan).