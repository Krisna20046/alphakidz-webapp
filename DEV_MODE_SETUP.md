# Dev Mode - Alpha Kids Maintenance Bypass

Dev mode untuk **bypass maintenance modal** saat testing fitur live.

## Kegunaan

Saat `MAINTENANCE_MODE=true` di `.env`, aplikasi tampilkan modal maintenance fullscreen. Dev mode bypass modal ini biar developer bisa test.

## Cara Aktifkan

**HANYA 1 CARA:** Copy-paste full command ini ke browser console (F12):

```javascript
(function(){const s='alphakids-dev-2024-secure',k='_ak_cfg',t=Date.now(),h=btoa(s+':'+t);localStorage.setItem(k,t+':'+h);console.log('%c✓ Dev mode enabled','color:#16A34A;font-weight:bold');location.reload()})()
```

Tidak ada shortcut atau method lain. Command panjang ini satu-satunya cara aktifkan.

## Cek Status

```javascript
DevMode.status()
```

Output:
```
Dev mode: ✓ ACTIVE
Expires in 6 days
```

## Nonaktifkan

```javascript
DevMode.disable()
```

## Testing Flow

1. Set `MAINTENANCE_MODE=true` di `.env`
2. Buka aplikasi → maintenance modal muncul
3. Buka console, paste full command di atas
4. Auto-reload → modal hilang, bisa akses normal
5. User biasa tetap liat modal

## Cara Kerja

- Token di localStorage key `_ak_cfg`
- Value: `timestamp:hash` (random string)
- Hash verify dengan secret `alphakids-dev-2024-secure`
- Auto-expire 7 hari
- Check langsung di maintenance modal render
- Tidak ada `DevMode.enable()` - cuma lewat one-liner panjang
- Orang awam tidak bisa aktifkan tanpa command ini

## Security

**Secret:** `alphakids-dev-2024-secure`

Ganti secret di 3 tempat (harus sama):
1. `public/js/dev-mode.js` (line 3)
2. `resources/views/layouts/app.blade.php` (maintenance bypass script)
3. One-liner activation command (ganti nilai `s=`)

## Production

File `public/js/dev-activate.js` opsional (cuma helper console log), boleh dihapus.

Core files:
- `public/js/dev-mode.js` - API utility (DevMode.disable/status saja, NO enable method)
- `resources/views/layouts/app.blade.php` - Bypass logic di maintenance modal

## Simpan Command

Simpan one-liner activation command di password manager atau dokumentasi internal yang aman.
