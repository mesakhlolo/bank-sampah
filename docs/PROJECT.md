# Bank Sampah — Project Reference

Dokumen referensi tunggal untuk produk, domain, aturan bisnis, dan konteks historis.

> **Dokumen terkait:** [`PLAN.md`](PLAN.md) — rencana optimasi aktif (fase, keputusan, DoD).

---

## 1. Ringkasan Produk

Aplikasi manajemen **Bank Sampah**: mencatat setoran sampah warga (ditimbang & dihargakan per kg), mengakumulasi saldo tabungan nasabah, memproses penarikan, dan menyediakan laporan ringkas.

**Nilai utama:** transparansi (bukti & riwayat transaksi), kemudahan operasional (satu layar untuk setoran), laporan cepat.

### Stakeholder

| Peran | Kebutuhan utama |
|---|---|
| **Admin** | Kelola master data (kategori, harga), kelola nasabah & operator, lihat semua laporan |
| **Operator/Kasir** | Setoran & penarikan cepat, lihat riwayat. **Tidak** bisa ubah master harga |
| **Nasabah** | Lihat saldo & riwayat sendiri via portal (tanpa akun, cukup kode unik) |
| **Pimpinan** | Laporan total setoran, nilai rupiah, jumlah nasabah aktif per periode |

### Cakupan

**In-scope:** CRUD nasabah · master kategori & harga (dengan riwayat) · setoran multi-item · saldo & mutasi · penarikan dengan validasi saldo · laporan periode · bukti PDF · portal nasabah · landing page.

**Out-of-scope:** manajemen stok fisik & penjualan ke pengepul · integrasi payment gateway · aplikasi mobile terpisah · multi-cabang/multi-tenant · notifikasi WA/SMS/email otomatis.

---

## 2. Domain Model

```
Customer 1─┬─< Deposit 1─< DepositItem >─ WasteCategory 1─< WastePrice
           ├─< Withdrawal
           └─< LedgerEntry (polymorphic: ref_type + ref_id)
```

| Model | Tabel | Peran |
|---|---|---|
| `Customer` | `customers` | Nasabah. Punya `member_code` (internal) & `unique_code` (portal) |
| `Deposit` | `deposits` | Header setoran. `status`: `draft` \| `final` |
| `DepositItem` | `deposit_items` | Baris setoran: kategori, berat, harga saat itu, subtotal |
| `Withdrawal` | `withdrawals` | Penarikan saldo |
| `LedgerEntry` | `ledger_entries` | Mutasi saldo. `direction`: `KREDIT` \| `DEBIT` |
| `WasteCategory` | `waste_categories` | Kategori sampah. `is_active` |
| `WastePrice` | `waste_prices` | Riwayat harga per kategori via `effective_from` |
| `User` | `users` | Admin & Operator (`role`) |

### Saldo

**Saldo = agregat `ledger_entries`, bukan kolom tersimpan.**

```
saldo = SUM(KREDIT) − SUM(DEBIT)   untuk customer_id terkait
```

- Setoran → `KREDIT` (menambah saldo nasabah)
- Penarikan → `DEBIT` (mengurangi saldo nasabah)

⚠️ Perhatikan: penamaan `direction` mengikuti sudut pandang **buku besar bank sampah**, sehingga setoran = KREDIT dan penarikan = DEBIT. Ini **kebalikan** dari intuisi "kredit = uang keluar" pada rekening bank konvensional.

### Format kode

| Entitas | Format | Sifat |
|---|---|---|
| Deposit | `DEP-YYYYMMDD-####` | Sekuensial per hari |
| Withdrawal | `WD-YYYYMMDD-####` | Sekuensial per hari |
| Customer (internal) | `CUST-####` | Sekuensial, untuk admin |
| Customer (portal) | `BSB-` + 12 char acak | **Cryptographically secure** |

---

## 3. Aturan Bisnis

| # | Aturan |
|---|---|
| BR-001 | Harga item setoran diambil dari `waste_prices` yang **aktif pada tanggal transaksi** (`effective_from <= transaction_date`, terbaru) |
| BR-002 | Berat minimal 0.01 kg; nominal penarikan ≤ saldo tersedia |
| BR-003 | Transaksi `final` **mengunci** harga & subtotal — tidak berubah walau master harga berganti |
| BR-004 | Koreksi via transaksi penyesuaian (belum diimplementasi); atau batalkan & buat ulang jika belum final |
| BR-005 | Kode transaksi unik, di-generate saat pembuatan |

**Konsekuensi teknis:** `DepositItem` menyimpan **salinan** `price_per_kg` dan `subtotal` saat transaksi — bukan referensi ke `WastePrice`. Ini yang membuat BR-003 bekerja.

---

## 4. Portal Nasabah & Keamanan

Nasabah mengakses portal **tanpa akun/login** — cukup kode unik 16 karakter (`BSB-XXXXXXXXXXXX`).

### 8 lapis proteksi

| # | Lapis | Implementasi |
|---|---|---|
| 1 | Kode acak kriptografis | `Str::random(12)` → keyspace 36¹² ≈ 4,7 triliun kombinasi |
| 2 | Rate limiting | 5 percobaan / 5 menit per IP (`RateLimiter`) |
| 3 | Session server-side | Simpan `portal_customer_id`, `portal_login_time`, `portal_ip` |
| 4 | Session timeout | 30 menit inactivity, dicek tiap request |
| 5 | IP validation | Cegah session hijacking; paksa re-login jika IP berubah |
| 6 | Authorization | Cek `deposit->customer_id === session('portal_customer_id')` → 403 |
| 7 | Audit logging | Log login sukses/gagal, akses tidak sah, timeout, IP mismatch |
| 8 | Input validation | Format tepat 16 char, uppercase, Eloquent ORM, Blade auto-escape |

### Aturan wajib saat menyentuh area portal

- **Jangan pernah** log kode unik secara penuh — gunakan `substr($code, 0, 6).'******'`
- Route `POST /nasabah/cek` **wajib** punya `throttle:5,5`
- Route portal **wajib** di dalam middleware `customer.session`
- Setiap akses data nasabah **wajib** verifikasi kepemilikan

---

## 5. Referensi Era Awal (arsip)

> ⚠️ **Konteks historis — BUKAN panduan aktif.**
> Dipertahankan agar keputusan awal tetap dapat dilacak. Untuk kondisi terkini, lihat bagian 1–4 di atas.

### Garis waktu

| Tanggal | Peristiwa |
|---|---|
| 2025-10-22 | Commit pertama `64bd947` — *"commit iterasi pertama vibe coding"* |
| 2025-10-30 | Dokumen fitur baru: portal nasabah + landing page (security enhancement) |
| — | MVP selesai, 19 commit total |

### Stack yang direncanakan awal vs aktual

| Aspek | Rencana awal | Aktual |
|---|---|---|
| Framework | Laravel 11 | **Laravel 12** |
| Database | MySQL 8.0 / MariaDB | **SQLite** (`.env.example`), MySQL didukung |
| Setup lokal | XAMPP / Laragon | **Laravel Herd** |
| PHP | 8.2+ | **8.4** |
| Frontend | Blade + Tailwind + Alpine | sama (Tailwind masih v3) |
| PDF | `barryvdh/laravel-dompdf` | sama |
| Testing | Pest | sama (Pest 3) |

### Keputusan awal yang **masih berlaku**

- **Ledger sebagai sumber tunggal saldo** — tidak ada kolom `customers.balance`; saldo selalu dihitung dari `ledger_entries`
- **Kode unik acak** untuk portal (bukan sekuensial) — sekuensial dinilai terlalu lemah
- **DomPDF** untuk struk & laporan
- **Fat Models, Skinny Controllers** + Service class untuk logika kompleks
- **Form Request** untuk semua validasi; **Policy** untuk otorisasi
- **TDD** — test sebelum/sesudah implementasi fitur inti
- **7 fase MVP**: Setup → Schema & Models → Authorization → Master Data → Deposits → Withdrawals → Reports → Polish

### Keputusan awal yang **sudah usang**

- ❌ Laravel 11 → sudah 12
- ❌ MySQL 8 wajib → sekarang SQLite untuk dev
- ❌ XAMPP-only → Herd
- ❌ `php artisan pint` → perintah benar: `vendor/bin/pint`
- ❌ Rencana "7 fase" sebagai peta jalan aktif → digantikan [`PLAN.md`](PLAN.md)

### Pitfalls yang sudah diantisipasi sejak awal

Dicatat di `Implementation_Plan.md` dan **masih terbukti relevan** (lihat `PLAN.md` Fase 3–4):

- ❌ N+1 queries di list/report → gunakan `with()` eager loading
- ❌ Race condition di balance update → gunakan DB transaction
- ❌ Tidak validasi input → selalu gunakan Form Request
- ❌ Hardcode role string → gunakan enum/constant

---

## 6. Roadmap

Lihat [`PLAN.md`](PLAN.md) untuk rencana optimasi aktif (cleanup, bug produksi, performa, Tailwind v4, test & CI).

**Kandidat pasca-optimasi** (belum dijadwalkan):

- Penjualan ke pengepul (stok keluar & margin)
- Grafik tren kategori & performa nasabah
- Notifikasi WA/email bukti transaksi
- Multi-cabang & peran auditor
- Refactor `TransactionHistoryController` ke `ledger_entries`
- Konsolidasi relasi `WasteCategory::prices()` vs `wastePrices()`
