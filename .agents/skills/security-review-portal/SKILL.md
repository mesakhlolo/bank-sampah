---
name: security-review-portal
description: Checklist keamanan portal nasabah Bank Sampah — rate limiting, session timeout, IP validation, otorisasi kepemilikan data, dan redaksi logging kode unik. Gunakan skill ini setiap kali mengubah CustomerPortalController, CustomerSessionGuard, route /nasabah/*, atau kode apa pun yang menyentuh session portal_customer_id dan unique_code.
---

# Security Review — Portal Nasabah

Portal nasabah adalah area **paling sensitif** di aplikasi ini: akses ke saldo & riwayat keuangan, **tanpa login/password** — hanya kode unik.

Referensi lengkap: `docs/PROJECT.md` §4.

## Model ancaman

| Ancaman | Mitigasi |
|---|---|
| Brute force kode unik | Rate limit 5/5 menit per IP + keyspace 36¹² (4,7 triliun) |
| Session hijacking | IP validation tiap request |
| Session fixation | Session server-side, regenerate saat login |
| Akses data nasabah lain | Cek kepemilikan per request → 403 |
| Kebocoran kode via log | Redaksi: `substr($code, 0, 6).'******'` |
| Enumerasi kode | Pesan error generik, jangan bedakan "tidak ada" vs "format salah" |

## Checklist wajib

Saat mengubah apa pun di area portal, verifikasi semua poin ini:

- [ ] Route `POST /nasabah/cek` punya middleware `throttle:5,5`
- [ ] Semua route portal (selain `cek`) di dalam grup middleware `customer.session`
- [ ] `RateLimiter::hit()` dipanggil saat login **gagal**; `RateLimiter::clear()` saat **berhasil**
- [ ] Validasi input: `['required', 'string', 'size:16']` — format `BSB-` + 12 char
- [ ] Kode di-`strtoupper()` sebelum lookup
- [ ] Session menyimpan `portal_customer_id`, `portal_login_time`, `portal_ip`
- [ ] Setiap akses data nasabah cek `$deposit->customer_id === session('portal_customer_id')` → `abort(403)`
- [ ] Log **tidak pernah** memuat kode unik penuh
- [ ] Logout menghapus ketiga key session

## Middleware `customer.session`

Urutan pengecekan yang benar:

1. Session ada? → jika tidak, redirect ke `customer-portal.check-code`
2. Session expired? (`> 30 menit`) → hapus session, redirect dengan pesan
3. IP cocok? → jika tidak, hapus session + log, redirect

Jangan mengubah urutan ini. Cek IP sebelum cek expiry membocorkan informasi; cek expiry setelah akses data membuka celah.

## Redaksi logging — pola yang benar

```php
// ✅ benar
Log::warning('Failed portal login attempt', [
    'ip' => $request->ip(),
    'code_prefix' => substr($code, 0, 6).'******',
    'user_agent' => $request->userAgent(),
]);

// ❌ salah — kode penuh masuk log
Log::warning('Failed login', ['code' => $code]);
```

Alasan: log bisa diakses operator, terkirim ke layanan monitoring, atau bocor lewat error reporting. Kode unik = kredensial.

## Pesan error

Gunakan pesan generik yang tidak membocorkan keberadaan kode:

```php
// ✅
'Kode unik tidak ditemukan. Silakan periksa kembali atau hubungi operator.'

// ❌ membocorkan
'Kode BSB-ABC123 tidak terdaftar di sistem.'
```

## Yang sering terlewat saat refactor

1. **Menambah route baru tanpa `customer.session`** — paling sering. Setiap route yang menampilkan data nasabah harus di dalam grup.
2. **Route model binding melewati otorisasi.** `depositDetail(Deposit $deposit)` otomatis mengambil deposit berdasarkan ID — **wajib** cek kepemilikan secara eksplisit setelah binding.
3. **Menghapus `throttle` saat debugging** lalu lupa mengembalikan.
4. **Menambah field ke log context** tanpa memeriksa apakah itu sensitif.
5. **Mengubah `size:16`** — format kode terkunci 16 char; mengubahnya merusak validasi.

## Test yang harus ada untuk setiap perubahan

Minimal cakup:

- Kode valid → redirect ke dashboard, session terisi
- Kode tidak valid → error, `RateLimiter` bertambah
- Percobaan ke-6 dalam 5 menit → diblokir
- Session > 30 menit → redirect ke login
- IP berbeda → session dihapus
- Akses deposit milik nasabah lain → 403
- Logout → session bersih
