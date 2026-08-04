# FLOW.md — Alpha Kids Modules 1 & 2 API Guide

Dokumentasi untuk frontend. Menjelaskan flow API, role yang bisa akses, dan syarat sebelum pakai.

---

## BASE

* Base URL: `http://localhost:8000/api`
* Auth: `Bearer Token` (hasil `POST /login`) untuk semua endpoint di bawah
* Response format umum:

```json
{
  "success": true,
  "message": "Success",
  "data": {}
}
```

---

## ROLE

| id | Role |
|----|------|
| 1  | Admin |
| 2  | Majikan |
| 3  | Nanny |
| 4  | Konsultan |
| 5  | Nexus |

---

# MODULE 1 — SCHOOL SUBJECT

## Intro

Master data mata pelajaran. Merupakan **prasyarat** Module 2 (Semua `subject_id` di `school_schedules` mengarah ke tabel `school_subjects`).

## Endpoints

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET  | `/school-subjects` | List (paginasi 20, urut `name`) |
| POST | `/school-subjects` | Tambah |
| GET  | `/school-subjects/{id}` | Detail |
| PUT  | `/school-subjects/{id}` | Update |
| DELETE | `/school-subjects/{id}` | Hapus |

## Role Access

* **Hanya** role `1 (Admin)` dan `3 (Nanny)` untuk **semua** endpoint.
* Majikan (2) & Konsultan (4) tidak bisa akses modul ini.

## Field / Validation

| Field | Type | Rule |
|-------|------|------|
| `name` | string | **wajib**, unik (`unique:school_subjects,name`) |
| `icon` | string | opsional |
| `color` | string | opsional |

## Contoh Request

### POST /school-subjects (Create)
```json
{
  "name": "Mathematics",
  "icon": "math",
  "color": "#FF5733"
}
```

### PUT /school-subjects/{id} (Update)
```json
{
  "name": "Advanced Mathematics"
}
```

## Syarat Sebelum Pakai

1. Login sebagai **Admin (1)** atau **Nanny (3)**.
2. Field `name` wajib & tidak boleh duplikat.

---

# MODULE 2 — SCHOOL SCHEDULE

## Intro

Jadwal sekolah per anak per hari. Bergantung pada Modul 1 (`subject_id`).

## Endpoints

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET  | `/school-schedules` | List jadwal (paginasi 20). Nanny hanya lihat anak yang di-assign |
| POST | `/school-schedules` | Tambah jadwal |
| GET  | `/school-schedules/{id}` | Detail jadwal |
| PUT  | `/school-schedules/{id}` | Update jadwal |
| DELETE | `/school-schedules/{id}` | Hapus jadwal |
| GET  | `/children/{id_anak}/schedules` | List jadwal per anak |
| GET  | `/children/{id_anak}/today-schedule` | Jadwal hari ini per anak |

## Role Access

| Endpoint | Roles |
|----------|-------|
| Read (`index`, `show`, `getByChild`, `getTodaySchedule`) | `1, 2, 3, 4` |
| Write (`store`, `update`, `destroy`) | `1, 3` |

**Validasi Akses Anak (untuk endpoint yang berbasis anak):**
* **Admin (1) & Majikan (2):** bypass — bisa akses semua anak.
* **Nanny (3):** hanya anak yang ter-assign aktif di `nanny_assignment` (status `active`) lewat tabel `assignment_anak`. Bisa diakses jika ada relasi aktif.
* **Konsultan (4):** bisa panggil `index` lewat role, tapi endpoint per-anak (`getByChild`/`getTodaySchedule`) akan **403** karena bukan 1/2/3 → praktis cuma bisa pakai `index`.

## Request & Validation

### Field (store)
| Field | Type | Notes |
|-------|------|-------|
| `id_anak` | integer | wajib, harus ada di `user_anak` |
| `subject_id` | integer | wajib, harus ada di `school_subjects` |
| `day_of_week` | string | wajib. Contoh `"Monday"`, `"Tuesday"` |
| `start_time` | string | wajib, format `H:i` (contoh `"08:00"`) |
| `end_time` | string | wajib, format `H:i`, harus setelah `start_time` |
| `teacher_name` | string | opsional |
| `notes` | string | opsional |

### Contoh Request

#### POST /school-schedules (Create)
```json
{
  "id_anak": 1,
  "subject_id": 1,
  "day_of_week": "Monday",
  "start_time": "08:00",
  "end_time": "09:00",
  "teacher_name": "Ms. Jane",
  "notes": "Bring textbook"
}
```

#### PUT /school-schedules/{id} (Update)
```json
{
  "start_time": "09:00",
  "end_time": "10:00"
}
```

## Syarat Sebelum Pakai

1. Login.
2. Data **`school_subjects`** harus sudah ada (Modul 1) — `subject_id` di-validate exists.
3. Data **`user_anak`** harus sudah ada — `id_anak` di-validate exists.
4. Untuk **Nanny**: harus ada assignment **aktif** yang menautkan nanny ke anak tsb (`nanny_assignment.status = active`).
5. Field wajib saat create: `id_anak`, `subject_id`, `day_of_week`, `start_time`, `end_time`.

> Urutan pakai: setup `school_subjects` → login (Admin/Nanny) → buat schedule → ambil lewat endpoint per anak.

---

## Catatan Penting untuk Frontend

* Buat subject dulu baru buat schedule.
* `day_of_week` pakai nama hari bahasa Inggris (Monday–Sunday).
* `start_time`/`end_time` kirim sebagai string `HH:MM` (**bukan** `HH:MM:SS`).
* Gunakan `/children/{id}/today-schedule` untuk dashboard "jadwal hari ini".
* Response `422` jika field wajib kosong/salah, `403` jika role tidak berhak / tidak ada akses ke anak, `401` jika tidak login, `404` jika data tidak ditemukan.

---

## Ringkasan

* **Nanny** hanya melihat anak yang ter-assign di `index`, `getByChild`, `getTodaySchedule`; menulis hanya untuk anak yang dia-assign.
* **Admin** bisa read + write untuk semua anak.
* **Majikan** hanya read, tidak bisa menulis di modul ini.