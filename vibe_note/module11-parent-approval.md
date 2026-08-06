# MODULE 11 — Parent Approval (Majikan) — Backend + Frontend

Tanggal: 2026-08-06
Status: Fungsional — Backend (API) ✅ + Frontend Majikan (approve/reject/comment) ✅
Referensi: `vibe_note/summary.md` (Modul 3 & 4, pola proxy controller + majikan tracking)

---

## 1. Ringkasan

Fitur **Parent Approval untuk Academic Task** dibangun end-to-end. Majikan (role 2) bisa meninjau
tugas akademik anaknya yang sudah dikerjakan nanny, lalu **approve / reject / comment**.

### Perilaku REJECT (Opsi B — dipilih user 2026-08-06)
- Saat majikan **reject**: task **di-reopen** → status `academic_tasks` = `in_progress`, `completed_at = null`.
- Backend kirim **FCM notif** ke nanny yg bertanggung jawab (resolve via `nanny_assignment.id_nanny` dari `task.id_assignment`),
  alasan penolakan sebagai body (best-effort, tidak menggagalkan response).
- Frontend nanny (`academic-task/show`) menampilkan **banner merah "Tugas Ditolak"** + alasan + instruksi perbaiki & selesaikan ulang.
- Setelah nanny revisi & selesaikan ulang → task `completed` lagi → majikan kembali bisa approve/reject (tombol muncul lagi).

### Bug fix 2026-08-06 (pasca-uji)
1. **FCM notif tidak muncul di web** → payload reject kini meniru bentuk data chat (tambah
   `sender_id`, `sender_name`, `url`) agar klien web (foreground `onMessage` & background
   `firebase-messaging-sw.js`) menampilkan & meroute ke `/academic-task/{id}` seperti chat.
2. **Majikan stuck "Rejected" tanpa tombol** → logika `decision` di `majikan/approval/show.blade.php`
   sebelumnya murni dari riwayat terakhir (selalu `reject`). Sekarang dikombinasi dgn status task:
   - task `completed` → `approved` bila terakhir di-approve, selain itu `pending` (tombol muncul).
   - task belum selesai → `pending`, atau `rejected` bila pernah ditolak (sedang direvisi nanny).
3. **Approve/reject menambah baris baru setiap aksi** → kini **upsert** (`saveDecision`): cari baris
   `parent_comments` utk (id_anak + task_id) atau (id_anak + daily_summary_id) yang sudah ada,
   update jika ada, insert hanya bila belum ada. Satu baris keputusan per target task/summary —
   reject→revisi→approve meng-*update* baris yang sama, bukan insert baru. (`created_by` tetap Auth::id).

Arsitektur: frontend memakai **proxy controller** (token di session, tidak terekspos ke browser),
sama seperti `MajikanTrackingController` & `AcademicTaskController`.

---

## 2. Backend API — `AlphaKidz-Backend`

### Konteks penting
- Tabel `parent_comments` **SUDAH ADA** di DB (`db_sql_example/alphakidz-03agustus2026.sql`
  & migration `2026_07_13_100000`) — bukan polymorphic baru. Kolom:
  `id, id_anak, task_id, daily_summary_id, comment, is_approved, created_by, created_at, updated_at`.
- `is_approved` boolean tidak bisa bedakan reject vs comment → tambah kolom `action`
  (`enum approve/reject`, nullable) via migration `2026_08_06_000001_add_action_to_parent_comments.php`.
- **`action` belum run migrate** — user run manual (sesuai DEVELOPMENT RULES).
- SQL contoh: `db_sql_example/alphakidz-06agustus2026_add_action_parent_comments.sql`.

### File baru (backend)
| File | Peran |
|------|-------|
| `app/Models/ParentComment.php` | Model (relasi anak, task, dailySummary, creator) |
| `app/Http/Controllers/ParentCommentController.php` | approve / reject / comment / index / show |
| `app/Http/Requests/ParentComment/StoreParentCommentRequest.php` | Validasi (Form Request) |
| `app/Http/Resources/ParentComment/ParentCommentResource.php` | Resource + derive `decision` (approved/rejected/pending/comment) |
| `database/migrations/2026_08_06_000001_add_action_to_parent_comments.php` | Kolom `action` |
| `db_sql_example/alphakidz-06agustus2026_add_action_parent_comments.sql` | Patch SQL |

### Routes backend (5)
```
POST parent-comments/approve  { id_anak, task_id|daily_summary_id, comment? }
POST parent-comments/reject   { id_anak, task_id|daily_summary_id, comment (wajib) }
POST parent-comments          { id_anak, task_id|daily_summary_id?, comment }
GET  parent-comments?per_page&id_anak&task_id
GET  parent-comments/{id}
```

### Akses
- **write** (approve/reject/comment) → role 1 (Admin) & 2 (Majikan); majikan harus **pemilik** anak
  (`user_anak.id_majikan`).
- **read** → + role 3 (Nanny), dibatasi anak yang di-assign aktif (pola sama AcademicTaskController).
- Semua write pakai `DB::transaction` + try/catch + logging.

### Catatan backend
- `DailyAiSummary` model **belum ada** (Modul 7 belum dibangun). Summary approval sudah di-support
  di backend via `DB::table('daily_ai_summaries')` (tanpa dependency class). Belum ada UI summary approval.
- Riwayat approval = baris `parent_comments` (ledger), `decision` di-derive dari `is_approved` + `action`.

---

## 3. Frontend — `Laravel_Web_App` (role Majikan)

### File
| File | Peran |
|------|-------|
| `app/Http/Controllers/MajikanParentCommentController.php` | Proxy: index/show/approve/reject/comment |
| `routes/web.php` | 5 route prefix `majikan/approval` (authed) |
| `resources/views/majikan/approval/index.blade.php` | Pilih anak (pola sama tracking index) |
| `resources/views/majikan/approval/show.blade.php` | Daftar task + badge approval status + modal Approve/Reject + riwayat |

Route names:
- `majikan-approval` (GET `majikan/approval`)
- `majikan-approval-show` (GET `majikan/approval/{id_anak}`)
- `majikan-approval-approve` / `-reject` / `-comment` (POST)

### Detail penting
- Task hanya bisa di-approve/reject saat status task `completed` (tugas belum selesai → pesan
  "tunggu nanny menyelesaikan tugas", tanpa tombol).
- `decision` per task diambil dari riwayat terbaru (`historyByTask`, backend urut desc).
- Modal reject → `comment` **wajib** (sesuai validasi backend).
- Dropdown ganti anak cepat bila >1 anak (pola sama tracking show).

---

## 4. Verifikasi

- `php -l` controller & routes → tanpa error.
- `php artisan view:cache` → Blade berhasil dikompilasi.
- `php artisan route:list --path=approval` → 5 route tampil benar.

---

## 5. Belum dikerjakan / PR

- **Summary approval UI**: endpoint backend siap (`daily_summary_id`), tapi Modul 7 (DailyAiSummary)
  belum dibangun → belum ada UI approval summary.
- Kolom `action` belum di-run migrate & belum masuk dump SQL penuh (`alphakidz-06agustus2026.sql`).
- `parent-comments/{id}` (detail single) belum dipakai UI.
