# SUMMARY.md — Implementasi School Subject (Modul 1) di Frontend Web App

Tanggal: 2026-08-03
Status: Fungsional (list/create/edit/show/delete sudah jalan di proxy controller)

---

## 1. Konteks

Modul 1 (School Subject) dari `vibe_note/school_subject_&_school_schedule/FLOW.md`
diimplementasikan ke frontend Laravel blade (`Laravel_Web_App`), dimulai dari role **Admin**.

Arsitektur frontend ini memakai **proxy controller**: semua request backend diteruskan lewat
HTTP client (`Http::`) dengan token dari session, jadi token tidak pernah terekspos ke browser.

---

## 2. File yang dibuat/diubah

| File | Peran |
|------|-------|
| `app/Http/Controllers/SchoolSubjectController.php` | Proxy CRUD ke API `/school-subjects` |
| `routes/web.php` | Route + import controller (`admin-school-subject.*`) |
| `resources/views/admin/school-subject/index.blade.php` | List + search + pagination + FAB + detail modal |
| `resources/views/admin/school-subject/create.blade.php` | Form tambah (ikons, warna, preview) |
| `resources/views/admin/school-subject/edit.blade.php` | Form ubah (prefilled) |
| `resources/views/admin/school-subject/show.blade.php` | Detail full-page |

Route names yang dihasilkan:

| Route name | Method | URI |
|------------|--------|-----|
| `admin-school-subject` | GET | `admin/school-subject` |
| `admin-school-subject.create` | GET | `admin/school-subject/create` |
| `admin-school-subject.store` | POST | `admin/school-subject` |
| `admin-school-subject.show` | GET | `admin/school-subject/{id}` |
| `admin-school-subject.edit` | GET | `admin/school-subject/{id}/edit` |
| `admin-school-subject.update` | PUT | `admin/school-subject/{id}` |
| `admin-school-subject.destroy` | DELETE | `admin/school-subject/{id}` |

Route prefix: `admin/school-subject`.
**Penting untuk menu**: nama route yang harus didaftarkan menu di backend = **`admin-school-subject`**.

---

## 3. Perilaku API yang telah dipelajari (dari backend lama)

Sumber: `AlphaKidz-Backend/app/Http/Controllers/SchoolSubjectController.php`

- Backend memakai **`success`** (bool), **bukan** `status`. Cek sukses pada proxy memakai helper
  `isSuccess()` yang menerima `success === true` ATAU `status === 'success'`.
- Endpoint list memakai pagination `paginate(20)`. Bentuk response-nya **nested**:
  ```
  {
    success: true,
    data: {
      data: [ ...items ],
      meta: { current_page, last_page, total, per_page }
    }
  }
  ```
  Index mem-baca item dari `data.data` & pagination dari `data.meta` (dengan fallback bentuk datar).
- Field model: `id`, `name`, `icon`, `color`.

---

## 4. Bugs yang pernah diperbaiki

### a. List tampil "Belum ada mata pelajaran" padahal datanya ada
Penyebab: cek response salah (`status` vs `success`) + pagination bersarang.
Solusi: helper `isSuccess()` + parsing `data.data` / `data.meta`. (Diperbaiki)

---

## 5. Belum dikerjakan / PR

- **Menu di dashboard belum muncul** — itu diisi dari backend external (endpoint `role-menu-user`),
  tabel menu di **backend**, bukan migration lokal. Backend harus punya record menu:
  - `nama` = "Mata Pelajaran"
  - `icon` = `book`
  - `route` = **`admin-school-subject`** ← wajib pas dengan route name.
- Perlu memeriksa nama tabel/kolom menu di `AlphaKidz-Backend` kalau mau isi manual.
- Role lain (nanny dsb) belum dibuat — fokus saat ini Admin.

---

## 6. Verifikasi

- `php artisan route:list --name=school-subject` → 7 route tampil.
- `php -l app/Http/Controllers/SchoolSubjectController.php` → tanpa error.
- `php artisan view:cache` → Blade sudah compile.
- Manual: muat `/admin/school-subject` → list bermunculan benar.

---

## 7. Catatan untuk lanjutan (Module 2 – School Schedule)

- Prasyarat: `school_subjects` harus ada dulu (Modul 1).
- Schedule butuh `id_anak` (dari `user_anak`) & `subject_id`.
- Endpoint per anak tersedia: `/children/{id}/schedules` & `/children/{id}/today-schedule`.
- Validasi: `day_of_week` (Monday–Sunday), `start_time`/`end_time` string `HH:MM`.
- Admin (1) & Nanny (3) bisa write; Majikan (2)/Konsultan (4) read hanya index.