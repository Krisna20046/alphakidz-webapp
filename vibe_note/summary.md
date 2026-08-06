# SUMMARY.md — Progress Academic Task, Task Progress & Parent Approval

Tanggal: 2026-08-06
Status: Fungsional — Backend (API) ✅ + Frontend Nanny (input) ✅ + Frontend Majikan (tracking & approval) ✅

---

## 1. Ringkasan

Fitur **Tracking & Input PR/Project/Deadline/Nilai/Foto Tugas** + **Parent Approval** sudah dibangun end-to-end:

| Lapisan | Status | Lokasi |
|---------|--------|--------|
| Backend API (Modul 3, 4, 11) | ✅ | `AlphaKidz-Backend` |
| Frontend input (role Nanny) | ✅ | `Laravel_Web_App/resources/views/nanny/academic-task/` |
| Frontend tracking (role Majikan) | ✅ | `Laravel_Web_App/resources/views/majikan/tracking/` |
| Frontend approval (role Majikan) | ✅ | `Laravel_Web_App/resources/views/majikan/approval/` |
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
| `app/Models/ParentComment.php` | Model approval (relasi anak, task, dailySummary, creator) |
| `app/Http/Controllers/ParentCommentController.php` | approve / reject / comment / index / show + FCM notif nanny |
| `app/Http/Requests/ParentComment/StoreParentCommentRequest.php` | Validasi approval/comment (Form Request) |
| `app/Http/Resources/ParentComment/ParentCommentResource.php` | Resource + derive `decision` (approved/rejected/pending/comment) |
| `database/migrations/2026_08_06_000001_add_action_to_parent_comments.php` | Kolom `action` (enum approve/reject) |
| `db_sql_example/alphakidz-06agustus2026_add_action_parent_comments.sql` | Patch SQL (aturan: setiap update DB ada SQL contoh) |

### Routes backend
```
# Modul 3 & 4 — Academic Task + Progress
apiResource academic-tasks          → CRUD
GET  children/{id_anak}/academic-tasks   → per anak
PATCH academic-tasks/{id}/status
PATCH academic-tasks/{id}/complete
POST  academic-tasks/{id}/upload
GET   academic-tasks-overdue
apiResource task-progress           → CRUD
GET   academic-tasks/{task_id}/progress → per tugas

# Modul 11 — Parent Approval
POST parent-comments/approve  { id_anak, task_id|daily_summary_id, comment? }
POST parent-comments/reject   { id_anak, task_id|daily_summary_id, comment (wajib) }
POST parent-comments          { id_anak, task_id|daily_summary_id?, comment }
GET  parent-comments?per_page&id_anak&task_id
GET  parent-comments/{id}
```

### Detail penting
- Tabel `academic_tasks`, `academic_task_progress`, `parent_comments` **sudah ada di DB**
  (migration `2026_07_13_100000` & `alphakidz-03agustus2026.sql`) — parent_comments bukan polymorphic baru.
- Folder storage dibuat: `storage/app/public/academic_tasks` & `task_progress`.
- Foto diproses `ImageHelper` (dikompres → WebP).
- **Akses role**: read = Admin(1)/Majikan(2)/Nanny(3)/Konsultan(4); write task = Admin/Nanny.
  Nanny dibatasi hanya anak yang di-assign aktif (child access via `nanny_assignment` + `assignment_anak`).
- Menambah/update progres otomatis menyinkronkan status tugas: `progress_percentage >= 100` → `completed`.
- **Approval write** (approve/reject/comment) → role 1 (Admin) & 2 (Majikan); majikan harus **pemilik** anak
  (`user_anak.id_majikan`).
- **REJECT (Opsi B)**: task di-reopen → `status=in_progress`, `completed_at=null`; FCM notif ke nanny
  (resolve via `nanny_assignment.id_nanny` dari `task.id_assignment`), alasan sebagai body.
- **Upsert approval**: satu baris `parent_comments` per target (id_anak + task_id / daily_summary_id),
  reject→revisi→approve meng-*update* baris yang sama, bukan insert baru (`saveDecision`).

---

## 3. Frontend Nanny (input) — `Laravel_Web_App`

### File
| File | Peran |
|------|-------|
| `app/Http/Controllers/AcademicTaskController.php` | Proxy: index/create/store/show/edit/update/destroy/updateStatus/markComplete/storeProgress + fetchLatestRejection |
| `routes/web.php` | Route `academic-task.*` (10 route, authed) |
| `resources/views/nanny/academic-task/index.blade.php` | List + filter status/type (AJAX) + pagination + FAB |
| `resources/views/nanny/academic-task/_list.blade.php` | Partial list (di-swap AJAX) |
| `resources/views/nanny/academic-task/create.blade.php` | Form tambah: child + type (Homework/Project/Exam) + subject + deadline + priority + upload foto |
| `resources/views/nanny/academic-task/show.blade.php` | Detail + badge status + nilai ⭐ + progress bar + modal Update Progress (foto) + Mark Complete + **banner reject** |
| `resources/views/nanny/academic-task/edit.blade.php` | Form ubah + input score (nilai) + status + hapus |

Route names: `academic-task.index`, `.create`, `.store`, `.show`, `.edit`, `.update`, `.destroy`, `.complete`, `.progress`.

### Detail penting
- Dropdown child diambil dari endpoint `nanny-assignments-anak-for-nanny` (pola sama SchoolSchedule);
  hidden `id_assignment` diisi otomatis dari `data-assignment`.
- Progress 100% otomatis menandai tugas selesai.
- **Banner reject**: `show()` fetch riwayat `parent-comments` utk tugas itu; bila ada `action='reject'`
  terbaru → banner merah "Tugas Ditolak oleh Majikan" + alasan + instruksi perbaiki & selesaikan ulang.

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

## 5. Frontend Majikan (approval) — `Laravel_Web_App`

### File
| File | Peran |
|------|-------|
| `app/Http/Controllers/MajikanParentCommentController.php` | Proxy: index/show/approve/reject/comment |
| `routes/web.php` | 5 route prefix `majikan/approval` (authed) |
| `resources/views/majikan/approval/index.blade.php` | Pilih anak (pola sama tracking index) |
| `resources/views/majikan/approval/show.blade.php` | Daftar task + badge approval + modal Approve/Reject + riwayat |

Route names: `majikan-approval`, `majikan-approval-show`, `majikan-approval-approve`, `-reject`, `-comment`.

### Detail penting
- Task hanya bisa di-approve/reject saat status task `completed`.
- **Logika decision per task** (kombinasi status task + riwayat):
  - task `completed` → `approved` bila terakhir di-approve, selain itu `pending` (tombol muncul).
  - task belum selesai → `pending`, atau `rejected` bila pernah ditolak (sedang direvisi nanny).
- Modal reject → `comment` **wajib** (sesuai validasi backend).
- Dropdown ganti anak cepat bila >1 anak.

---

## 6. Perilaku API yang dipelajari

- Backend memakai **`success`** (bool), proxy pakai helper `isSuccess()` yang menerima
  `success === true` ATAU `status === 'success'` (backward-compatible dgn endpoint lama).
- List paginated berbentuk nested `data.data` + `data.meta` (dengan fallback bentuk datar).
- Endpoint `children/{id}/...` mengembalikan array (bukan paginated) → parse `data` langsung.
- `deadline` dikirim format `Y-m-d H:i:s` (dikonversi dari `datetime-local` di proxy).
- Upload foto wajib multipart (`attach()`), terima `jpeg/png/jpg` max 10MB.
- **FCM web**: klien (foreground `onMessage` & background `firebase-messaging-sw.js`) butuh `sender_id`,
  `sender_name`, `url` dlm `data` — tanpa itu notif tidak tampil/routing salah (lihat bug fix).

---

## 7. Bugs yang pernah diperbaiki

### a. (Backend) Baris TODO.md rusak saat edit
Beberapa baris Module 3 TODO sempat terkorupsi (karakter `�`, `[Click ...`, `[x]*`).
Solusi: perbaiki manual via PowerShell (ditulis ulang 3 baris 0-index 236–238 + hapus `*` nyasar). Diperbaiki.

### b. (Frontend) Update tugas pakai PUT multipart
Laravel HTTP client tidak bisa `->put()` dengan `->attach()`. Solusi: kirim `POST {id}/update` dengan
`_method=PUT` via `->post()` (pola di route frontend `academic-task.update`). Diperbaiki sejak awal.

### c. (2026-08-06) FCM notif reject tidak muncul di web
Payload reject tidak punya `sender_id`/`sender_name`/`url`. Solusi: payload FCM reject kini meniru bentuk
data chat agar klien web menampilkan & meroute ke `/academic-task/{id}`. Diperbaiki.

### d. (2026-08-06) Majikan stuck "Rejected" tanpa tombol approve/reject
Decision murni dari riwayat terakhir (selalu reject). Solusi: gabungkan status task + riwayat —
task completed & belum approve → pending (tombol muncul lagi). Diperbaiki.

### e. (2026-08-06) Approve/reject menambah baris baru setiap aksi
Solusi: upsert (`saveDecision`) — satu baris keputusan per target task/summary. Diperbaiki.

---

## 8. Verifikasi

- `php -l` semua controller backend & frontend → tanpa error.
- `php artisan route:list --path=academic` / `--path=academic-task` / `--path=monitoring` / `--path=approval`
  → route tampil benar.
- `php artisan view:cache` (Laravel_Web_App) → semua Blade template berhasil dikompilasi.
- DB migration `academic_tasks` sudah Ran (cek `migrate:status`).
- Kolom `action` di `parent_comments` **belum run migrate** — user run manual.

---

## 9. Belum dikerjakan / Next Tasks

### Done tapi belum dipakai UI
- Endpoint `academic-tasks-overdue` & `updateStatus` sudah ada di backend tapi belum dipakai UI
  (overdue sudah dihitung di halaman tracking majikan).

### Done (2026-08-06) — Task progress detail di halaman approval majikan
- Kartu task di `majikan/approval/show.blade.php` kini bisa diklik → modal detail progress yang
  menampilkan riwayat progres (progress bar, note, foto, tanggal) per task. Memakai data `progress`
  yang sudah ada di response `children/{id_anak}/academic-tasks` (tanpa request tambahan).
- Tombol Approve/Reject & link foto attachment memakai `event.stopPropagation()` agar tidak ikut
  membuka modal detail saat diklik.
- **Fix (2026-08-06)**: `getByChild` backend hanya eager-load `['subject']`, tanpa `progress` → data
  progress kosong di response majikan. Solusi: tambah `'progress'` ke `with(['subject', 'progress'])`
  (`AlphaKidz-Backend/app/Http/Controllers/AcademicTaskController.php:232`). Perlu push/serve ulang
  backend.
- **Lightbox (2026-08-06)**: klik foto progress di modal detail atau thumbnail attachment task →
  popup gambar penuh (`openLightbox`/`closeLightbox`).

### Next Task berikutnya (dari user, 2026-08-06)
- ~~**Lihat task progress di halaman approval majikan**~~ → ✅ selesai (modal detail progress).

### PR (masih terbuka)
- **Summary approval UI**: endpoint backend siap (`daily_summary_id`), tapi Modul 7 (DailyAiSummary)
  belum dibangun → belum ada UI approval summary.
- `parent-comments/{id}` (detail single) belum dipakai UI.
- Indicator "perlu revisi" di list nanny (`_list.blade.php`) belum ada (backend list `academic-tasks`
  belum menyertakan approval state).
