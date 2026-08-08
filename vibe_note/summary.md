# SUMMARY.md — Progress Academic Task, Task Progress, Parent Approval & Learning Progress

Tanggal: 2026-08-08
Status: Fungsional — Backend (API) ✅ + Frontend Nanny (input) ✅ + Frontend Majikan (tracking & approval) ✅ + Modul 7 (Diary AI Summary) ✅ + Modul 5 (Learning Progress) ✅ + Modul 9 (Task Reminder) ✅ + Modul 6 (Assistant Notes) ✅

---

## 0e. Modul 6 — Assistant Notes (2026-08-08)

Fitur **catatan harian asisten** (mood + highlight + concern + rekomendasi) end-to-end:

| Lapisan | Status | Lokasi |
|---------|--------|--------|
| Backend API (Model, Controller, Form Request, Resource, routes, mood enum) | ✅ | `AlphaKidz-Backend` |
| Seeder (nanny id 57, anak 25, 14 catatan) | ✅ | `AlphaKidz-Backend/database/seeder_sql/` |
| Frontend Nanny (pilih anak, input, riwayat + pagination, hapus) | ✅ | `Laravel_Web_App` |
| Frontend Majikan (pilih anak, riwayat read-only) | ✅ | `Laravel_Web_App` |

Detail lengkap di `vibe_note/module6-assistant-notes.md`.

Catatan penting (2026-08-08):
- **Mood enum** `senang/sedih/marah/biasa` (sama diary); ditampilkan sebagai badge berwarna di kartu riwayat.
- **Hapus hanya Nanny** (route destroy); Majikan read-only (tanpa tombol hapus/FAB).
- **Modal konfirmasi hapus in-app** menggantikan `confirm()` native — dipicu tombol trash, form DELETE via modal (`#anDeleteModal` di `nanny/assistant-notes/show.blade.php`), backdrop/Escape utk tutup.
- **Fix ikon mood** (2026-08-08): mood "marah" semula `angry-outline` yang TIDAK ada di Ionicons → diganti `flame-outline` (valid) agar emote tampil; diseragamkan di create + riwayat nanny + riwayat majikan.
- **Tutorial modal** 5 langkah per role (prefix `anTutorial*` biar tak bentrok bila bareng tutorial lain).
- **Menu SQL** `alphakidz-08agustus2026_assistant_notes_menu.sql` (menu 22/23 + role_menu) **belum di-run** user.

---
## 0d. Modul 9 — Task Reminder (2026-08-07)

Fitur **reminder tugas & exam** (deadline ≤ 3 hari, overdue, exam) end-to-end:

| Lapisan | Status | Lokasi |
|---------|--------|--------|
| Backend (migration, model, service, job, command, controller, routes, SQL) | ✅ | `AlphaKidz-Backend` |
| Frontend dashboard (pop-up overlay harian + section "Pengingat Tugas & Ujian" + trigger on-demand) | ✅ | `Laravel_Web_App` |

Catatan penting:
- **Window 3 hari** (`DEADLINE_WINDOW_HOURS=72`): task berisiko = deadline ≤ 3 hari / overdue / exam.
- **Khusus Nanny (role 3)** — Majikan TIDAK dapat reminder (keputusan user 2026-08-07): redirect ke
  halaman update-progress Nanny salah sasaran & reminder berhari-hari mengganggu Majikan.
  Backend `resolveRecipients` hanya Nanny; frontend `riskyTaskList` + trigger hanya role 3.
- **Pop-up muncul tiap hari** mulai H-3 sampai task `completed`/`cancelled`; sekali/hari/task via
  `localStorage` (`taskRiskyShown:{id}:{YYYY-M-D}`). Card punya tombol dismiss (per sesi) + tombol tutorial (?).
- **Tutorial modal** (pola `learning-progress/_tutorial.blade.php`) menjelaskan cara pakai reminder.
- **Pemicu ganda**: scheduler `task:remind` (bila cron ada) ATAU on-demand `POST /api/reminders/check-now`
  (fallback hosting tanpa cron, di-ikat dari dashboard).
- **Anti-spam**: tabel `task_reminders` — 1 notif per task per tipe per hari (unique task_id+type+notif_date).
- **Penerima**: Nanny via `task.id_assignment → nanny_assignment.id_nanny` (status active); hanya jika `fcm_token`.
- **FCM payload**: meniru pola reject task (`type=academic_task_reminder`, `url=/academic-task/{id}`)
  → klien web (`firebase-messaging-sw.js`) tampil & meroute benar.
- **Self-test**: `tests/TaskReminderSelfTest.php` (classify) → 8/8 PASS.
- **Fix stdClass bug (Nanny)**: `childrenByRole` normalisasi `(array)$anak` — endpoint
  `getAnakForNanny` mengembalikan `anak` sebagai stdClass; `$anak['id']` melempar Error → `RISKY_TASKS` kosong.
- **Fix blade script bug**: modal tutorial semula di dalam `<script>` → dipindah ke `<body>`;
  sebelumnya seluruh blok script gagal parse → `RISKY_TASKS is not defined`.
- **Catatan hosting**: untuk pakai scheduler perlu cron `* * * * * php artisan schedule:run`;
  tanpa cron, andalkan on-demand check saat dashboard dimuat.
- **Keterbatasan**: FCM `fcm_options.link` web hardcoded ke `/chat/...`; notif yang diklik mungkin
  tetap ke chat. Perlu edit kecil FCMService bila ingin langsung ke detail tugas.

---

## 0c. Modul 5 — Learning Progress (2026-08-07)

Fitur **learning progress** selesai end-to-end (backend + frontend Nanny input & chart + Majikan
read-only + pagination history). Detail lengkap di
`vibe_note/module5-learning-progress.md`.

| Lapisan | Status | Lokasi |
|---------|--------|--------|
| Backend API (Model, Controller, Request, Resource, routes, chart) | ✅ | `AlphaKidz-Backend` |
| Frontend Nanny (pilih anak, chart, input skor, history + pagination) | ✅ | `Laravel_Web_App` |
| Frontend Majikan (pilih anak, trend chart, history + pagination) | ✅ | `Laravel_Web_App` |
| SQL seed (24 data Chalista, 1 minggu) & menu | ✅ | `AlphaKidz-Backend/database/` |

Catatan penting:
- **Pagination**: `getByChild`/`index` `paginate()`; `meta` dibuat eksplisit karena
  `Resource::collection($paginator)` di-nest tidak otomatis menyertakan meta (fix 2026-08-07).
- Routes history: `nanny-learning-history` / `majikan-learning-history` (AJAX partial swap `#historyList`).
- **Tutorial modal** (2026-08-07): tombol `?` di header Nanny & Majikan → modal multi-langkah
  (Sebelumnya/Berikutnya/Selesai, dots, Escape) menjelaskan skor, rubrik, rata-rata mingguan &
  badge status. Partial bersama `learning-progress/_tutorial.blade.php`; `$steps` beda per role.

---

## 0b. Modul 7 — Diary AI Summary (frontend, 2026-08-06)

Fitur ringkasan AI diary kini tampil di halaman diary **Nanny & Majikan** (`Laravel_Web_App`):

| File | Peran |
|------|-------|
| `app/Http/Controllers/NannyController.php` | Proxy `fetchSummary` (GET) + `generateSummary` (POST) |
| `app/Http/Controllers/MajikanController.php` | Proxy `fetchSummary` (GET, read-only) + `generateSummary` (POST) |
| `routes/web.php` | `nanny-diary-summary`/`-generate`, `majikan-diary-summary`/`-generate` |
| `resources/views/nanny/diary.blade.php` | Kartu "Ringkasan AI Hari Ini" + tombol **Generate Ringkasan** |
| `resources/views/majikan/diary.blade.php` | Kartu sama, **tanpa** tombol generate (read-only) |

Perilaku:
- Kartu muncul hanya jika ada `$idAnak` (halaman diary anak tertentu).
- `loadSummary()` otomatis saat halaman dimuat → GET proxy → tampilkan `ai_summary` bila ada (teks dipecah per baris).
- Nanny: tombol **✨ Generate Ringkasan** → POST proxy → backend `daily-ai-summaries/generate` (role 1,3) — menampilkan `j.message` bila error (mis. tidak ada aktivitas tanggal itu).
- Majikan: **read-only** (backend batch role hanya Admin/Nanny yang boleh generate); tombol generate tidak dirender.
- Tanggal ikut tanggal yang sedang dilihat (`$tanggal`), jadi ringkasan mengikuti pilihan tanggal di kalender.

---

## 0. Modul 7 — Diary AI Summary (backend, 2026-08-06)

Fitur **ringkasan diary aktivitas** (diary summary khusus) dibangun di `AlphaKidz-Backend`:

| File | Peran |
|------|-------|
| `app/Models/DailyAiSummary.php` | Model → tabel `daily_ai_summaries` existing (NO migration) |
| `app/Services/GeminiAiService.php` | Multi API key + rotate model (fallback saat limit), REST tanpa SDK |
| `app/Http/Controllers/DailyAiSummaryController.php` | generate (on-demand) / regenerate / getByChild |
| `app/Http/Requests/DailyAiSummary/GenerateSummaryRequest.php` | Validasi (id_anak, summary_date) |
| `app/Http/Resources/DailyAiSummary/DailyAiSummaryResource.php` | Resource |
| `config/services.php` + `.env` / `.env.example` | Blok `services.gemini` (GEMINI_API_KEY, _2, _3, GEMINI_MODELS) |
| `db_sql_example/alphakidz-06agustus2026_daily_summary.sql` | SQL contoh (tanpa perubahan skema) |
| `tests/GeminiRotateSelfTest.php` + `run_gemini_self_test.php` | Self-check rotate (PASS) |

Routes: `GET daily-ai-summaries? id_anak&summary_date`, `POST daily-ai-summaries/generate`, `POST daily-ai-summaries/{id}/regenerate`.

Detail:
- **Data AI**: diary `aktivitas_anak` per tanggal (kategori, deskripsi, jam, durasi, mood, lokasi, porsi, nafsu) + usia anak dari `tanggal_lahir` (analisis age↔mood ditunda ke summary penuh Modul 7).
- **Multi-API rotate**: model-a 429 → coba model berikutnya; semua model pada satu key gagal → pindah key; semua habis → throw. (Desain sesuai permintaan user.)
- **Upsert**: 1 baris per (anak, summary_date) — regenerate meng-update baris yang sama.
- Role: generate = Admin/Nanny (1,3); read = + Majikan/Konsultan (1,2,3,4); child-access check pola sama AcademicTask.
- **BUTUH AKSI user**: isi `GEMINI_API_KEY` (dan opsional _2/_3 + GEMINI_MODELS) di `.env` backend sebelum endpoint dipakai.

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

### f. (2026-08-07) Input academic task tanpa gambar → error `getRealPath() on null`
Penyebab: `AcademicTaskController@store` (frontend) selalu memanggil
`file_get_contents($request->file('attachment')->getRealPath())` tanpa guard. Fix: tambah
`if ($request->hasFile('attachment'))` sebelum `attach`, pola sama seperti `update()` / `storeProgress()`. Diperbaiki.

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
