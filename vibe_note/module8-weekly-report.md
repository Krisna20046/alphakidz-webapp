# MODULE 8 — WEEKLY REPORT (PDF)

Tanggal: 2026-08-21
Status: Backend API ✅ — Frontend Nanny (generate/regenerate/lihat/unduh) ✅ — Frontend Majikan (lihat/unduh read-only) ✅

---

## Backend — `AlphaKidz-Backend`

### Files

| File | Peran |
|------|-------|
| `app/Models/WeeklyReport.php` | Model → tabel `weekly_reports` existing (NO migration) |
| `app/Services/PdfGeneratorService.php` | PDF raw generator minimal (spec 1.4, font Helvetica, render text blocks) |
| `app/Services/WeeklyReportService.php` | Aggregate data minggu + prompt Gemini (konsistensi diary + Note For Nanny) + save PDF + upsert report |
| `app/Jobs/GenerateWeeklyReportJob.php` | Queue job untung generate background |
| `app/Console/Commands/GenerateWeeklyReportCommand.php` | Artisan `weekly:report` (options `--anak`, `--week`, `--sync`) |
| `app/Http/Controllers/WeeklyReportController.php` | generate / regenerate / download / getByChild |
| `app/Http/Requests/WeeklyReport/GenerateWeeklyReportRequest.php` | Validasi id_anak + week_start |
| `app/Http/Resources/WeeklyReport/WeeklyReportResource.php` | Resource + `pdf_url` |
| `app/Console/Kernel.php` | Scheduler: `weekly:report` weeklyOn Saturday 01:00 |
| `database/migration_sql/alphakidz-21agustus2026_weekly_report_module8.sql` | SQL contoh (tanpa skema ubah) |
| `tests/PdfGeneratorSelfTest.php` | Self-check PDF (struktur + origin + Contents/Resources) — 11/11 PASS |

### Routes backend

```
POST weekly-reports/generate                    # on-demand sync (id_anak, week_start)
POST weekly-reports/{id}/regenerate             # bangun ulang report existing
GET  weekly-reports?id_anak&week_start&per_page # daftar report anak (paginated)
GET  weekly-reports/{id}/download               # stream PDF inline
```

### Detail penting

- **Data minggu** = diary aktivitas (`aktivitas_anak`, `whereBetween jam_mulai`) + academic task
  (deadline di minggu, eager `subject`) + learning progress (`recorded_date` di minggu).
- **Upsert**: `WeeklyReport::updateOrCreate(['id_anak','week_start'], [...])` — regenerate meng-update
  baris sama; PDF stale deleted before neof write.
- **PDF**: `Storage::disk('public')->put('weekly_reports/...', $bytes)`. Filename
  `weekly_report_anak{id}_{startYmd}_to_{endYmd}.pdf`.
- **AI**: prompt narrative Bahasa Indonesia via `GeminiAiService` (key `GEMINI_API_KEY`).
  Bila no data → summary "Tidak ada data aktivitas untung minggu ada.", pdf_path null.
- **Konsistensi diary (2026-08-21)**: prompt kini menghitung hari terisi diary dari 7 hari minggu
  (`$filledDays`, `$daysMissing`, label `konsisten`/`cukup konsisten`/`kurang konsisten`). Bila diary
  <6 dari 7 hari, AI WAJIB menulis **paragraf terpisah berlabel "Note For Nanny: "** (jumlah hari kosong
  + imbauan isi diary harian) — TIDAK dicampur ke narasi utama. `buildPdfBlocks` memecah summary via
  regex `Note For Nanny\s*[:.]` → 2 blok body (narasi + note). Bila konsisten → hanya narasi utama.
- **Akses**: read role 1-4; write role 1 & 3. Nanny child-access via `nanny_assignment`+`assignment_anak` active.

### PDF generator (raw, kein library) — fix 2026-08-21

Tabel `weekly_reports` sudah ada → NO migration. `PdfGeneratorService` build PDF text-only
(object Catalog/Pages/Font/Page/Content) — `xref` offset + struktur divalidasi self-test.
Struktur: `render(array $blocks)` → `%PDF-1.4` header → obj → xref → trailer → `%%EOF`.
Blocks: title (18pt) / section (13pt) / body (11pt). Non-ASCII transliterated (WinAnsi).

Bug yang diperbaiki (PDF tampil kosong/putih):
1. **Text origin**: `buildPages` semula `1 0 0 1 0 0 Tm` → baris pertama tergambar di `y=-14` (di bawah
   tepi halaman). Fix: text matrix di-set ke `1 0 0 1 50 791.89 Tm` (margin kiri-atas) → teks terlihat.
2. **Page tidak referensikan content**: objek Page tidak punya `/Contents` → viewer tidak menggambar apa-apa.
   Fix: `pageObject()` kini menyertakan `/Contents {id} 0 R`.
3. **Font tidak dideklarasikan**: `/F1` dipakai tapi `/Resources << /Font << /F1 3 0 R >> >>` tidak ada.
   Fix: objek 3 = Font Helvetica (Type1, WinAnsi), Page menyertakan `/Resources`.
4. Fallback "Tidak ada data" juga diperbaiki koordinatnya (semula `791.89 50 Td` terbalik → di luar halaman).

**Ceiling** (`ponytail:` in update TODO): generator raw = provisorik; bila report need layout advanced
(tabla, foto, header/footer) → install `mpdf/mpdf` (`composer require mpdf/mpdf`) & swap `render()` kal.

## Frontend — `Laravel_Web_App`

Fitur end-to-end (pola modul 6/7: proxy controller + blade + routes web, token tetap server-side).

### Files

| File | Peran |
|------|-------|
| `app/Http/Controllers/WeeklyReportController.php` | Proxy: nannyIndex/Show/History, generate, regenerate, download, viewPdf (inline); majikanIndex/Show/History, majikanDownload, majikanViewPdf; helper `servePdf()` (disposition inline/attachment) + `fetchReports()` (parse meta top-level) |
| `routes/web.php` | Route prefix `weekly-report` di grup `majikan` (read-only) & `nanny` (write): index/show/history/download + view; POST generate/regenerate |
| `resources/views/nanny/weekly-report/index.blade.php` | Pilih anak (assigned) — pola assistant-notes |
| `resources/views/nanny/weekly-report/show.blade.php` | Picker minggu + Generate + riwayat + modal preview PDF + modal konfirmasi regenerate + loader fullscreen |
| `resources/views/nanny/weekly-report/_history.blade.php` | Kartu report: rentang minggu, ringkasan (line-clamp), status Ready/No-data, tombol Lihat + Download + regenerate |
| `resources/views/majikan/weekly-report/index.blade.php` | Pilih anak (milik majikan) |
| `resources/views/majikan/weekly-report/show.blade.php` | Riwayat read-only + modal preview PDF + loader PDF |
| `resources/views/majikan/weekly-report/_history.blade.php` | Kartu read-only: Lihat + Download (tanpa regenerate) |
| `resources/views/weekly-report/_tutorial.blade.php` | Modal Panduan shared (prefix JS `wrTutorial*` — terpisah dari `anTutorial*` modul 6) |
| `database/migration_sql/alphakidz-21agustus2026_weekly_report_menu.sql` | Menu 24 (Majikan) & 25 (Nanny) + role_menu (2/24, 3/25) |

### Routes web

```
GET  majikan/weekly-report               majikan-weekly-report
GET  majikan/weekly-report/{id_anak}     majikan-weekly-report-show
GET  majikan/weekly-report/{id_anak}/history
GET  majikan/weekly-report/{id}/download majikan-weekly-report-download
GET  majikan/weekly-report/{id}/view     majikan-weekly-report-view (inline)
GET  nanny/weekly-report                 nanny-weekly-report
GET  nanny/weekly-report/{id_anak}       nanny-weekly-report-show
GET  nanny/weekly-report/{id_anak}/history
POST nanny/weekly-report/generate        nanny-weekly-report-generate
POST nanny/weekly-report/{id}/regenerate nanny-weekly-report-regenerate
GET  nanny/weekly-report/{id}/download   nanny-weekly-report-download
GET  nanny/weekly-report/{id}/view       nanny-weekly-report-view (inline)
```

### Detail penting

- **Generate** on-demand sync (backend langsung). Form submit → loader fullscreen muncul.
- **Lihat PDF dalam aplikasi**: tombol Lihat → modal `<iframe>` memuat route `view` (proxy web,
  `Content-Disposition: inline`). Token tidak terekspos — iframe lewat session cookie. Loader "Memuat PDF…"
  hilang saat iframe `load`.
- **Download**: route proxy me-stream PDF (`attachment`); filename diambil dari header backend
  `content-disposition` via `$response->header()`.
- **Regenerate**: modal konfirmasi in-app (`#wrRegenModal`) + loader, lalu POST via form dibuat JS
  (CSRF token di-inject) — bukan `confirm()` native.
- **Modal tutorial** `wrTutorial*` per role (Nanny: 5 langkah; Majikan: 4 langkah).

### Bugs frontend yang diperbaiki (2026-08-21)

- `$response->headers()->get(...)` error `Call to a member function get() on array` — klien HTTP Laravel
  mengembalikan array, bukan object. Fix: `$response->header('Content-Disposition')` (dipakai `servePdf`).
- Tombol Lihat memanggil `wrOpenPdf` tapi function bernama `wrPdfOpen` → `ReferenceError`. Fix: samakan nama.
- `alert/confirm` native diganti modal in-app (regenerate) — tidak ada `confirm(`/`alert(` tersisa di modul.
- **405 MethodNotAllowedHttpException** pada regenerate: `wrRegenGo()` memanggil `wrRegenClose()` yang
  me-reset `wrRegenTargetUrl=''` → form POST ke URL halaman (GET-only). Fix: salin URL ke var lokal
  `const url = wrRegenTargetUrl` sebelum close.
- **Loader tidak menghalangi navigasi**: `#wrLoading` ada di dalam kontainer scroll `.relative z-10/20`
  (stacking context) → z-80 terperangkap di bawah bottom-nav. Fix: pindah ke top-level (sibling layout nav),
  `z-[90]` menutupi seluruh viewport termasuk nav.
- **"0 report" padahal kartu muncul**: `fetchReports()` membaca `$meta` dari `$data['meta']` padahal backend
  menaruh `meta` di **top-level** JSON (`$json['meta']`). Fix: `$meta = $json['meta'] ?? []`.

---

## Selftest

```
php tests/PdfGeneratorSelfTest.php   # 11/11 PASS (struktur + origin + /Contents + /Resources font)
```
