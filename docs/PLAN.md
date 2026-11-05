# FINAL PLAN — Bersihkan & Optimasi `bank-sampah`

**Status:** ✅ FINAL — K1–K4 sudah dijawab, siap eksekusi
**Target repo:** `C:\Users\mesakhlolo\Herd\bank-sampah`
**Basis:** verifikasi langsung ke kode, git, dan filesystem (bukan asumsi)
**Environment:** Laravel 12.x · PHP 8.4.27 · Composer 2.10.3 · Node 26.13.0 · **DB sqlite** · Pest 3

---

## 0. Keputusan final

| # | Pertanyaan | Keputusan |
|---|---|---|
| **K1** | Lokasi skills | **`.agents/skills/`** → hapus `.github/skills/` |
| **K2** | 3 MD usang | **Gabung** ke `docs/PROJECT.md` + **sertakan referensi era awal** (commit pertama 2025-10-22, "iterasi pertama vibe coding") |
| **K3** | Tailwind v4 | **LANJUT** migrasi penuh |
| **K4** | `TransactionHistoryController` | **Perbaiki quote saja**, refactor ke `ledger_entries` jadi item terpisah |

**Keputusan turunan:**
- Plan ini masuk repo sebagai `docs/PLAN.md` (agar jadi konteks agent & tim)
- `.github/copilot-instructions.md` digabung ke `AGENTS.md`, lalu dihapus
- `welcome.blade.php` dihapus (terverifikasi tidak dipakai)
- 3 method mati di `LandingController` dihapus

---

## 1. Status aktual

| Area | Status | Bukti |
|---|---|---|
| Bootstrap | ✅ | `vendor`, `node_modules`, `.env` ada; `71 passed (175 assertions)` |
| MCP + Boost | ✅ | `laravel/boost 2.10`; MCP: `laravel-boost`, `context7`, `github`, `herd` |
| Cleanup repo | ❌ | 288 KB duplikat, 105 KB MD usang, README drift |
| Skills domain | ❌ | `.opencode/skills/` tidak ada |
| Bug produksi | ❌ | 3 bug (§4) |
| Performa | ❌ | 5 isu (§5) |
| Tailwind v4 | ❌ | masih v3 (§6) |
| Test + CI | ❌ | portal/report nol test (§7) |

---

## 2. FASE 0 — Plan masuk repo

**Langkah pertama eksekusi.** Salin plan ini ke repo:

```powershell
New-Item -ItemType Directory -Force docs | Out-Null
# salin isi plan ini ke docs/PLAN.md
```

Alasan: agar agent berikutnya & anggota tim punya konteks tanpa harus buka folder global.
Tambahkan juga rujukan di `AGENTS.md`:

```markdown
## Dokumen Proyek
- `docs/PLAN.md` — rencana optimasi aktif (fase, keputusan, DoD)
- `docs/PROJECT.md` — ringkasan produk + arsitektur + referensi era awal
```

---

## 3. FASE 1 — Cleanup Repo

### 3.1 🔴 288 KB duplikat persis

Boost menulis skills ke **dua lokasi**, keduanya ter-track git:

| Lokasi | File | Ukuran | Aksi |
|---|---|---|---|
| `.agents/skills/` | 35 | 144 KB | **PERTAHANKAN** (K1) |
| `.github/skills/` | 35 | 144 KB | **HAPUS** |

**Terverifikasi:** 35 nama identik, ukuran identik, konten **byte-per-byte 100% identik**.
Ini **58% isi repo** (total ~496 KB tracked).

```powershell
git rm -r .github/skills
```

**Pencegahan agar tidak terulang:** setelah cleanup, jalankan `php artisan boost:update --no-interaction`, lalu cek apakah Boost menulis ulang ke `.github/skills`. Jika ya, tambahkan `.github/skills/` ke `.gitignore` supaya duplikat tidak masuk repo meski ada di disk.

### 3.2 🟠 3 MD usang (105 KB) → `docs/PROJECT.md`

| File | Ukuran | Baris | Masalah |
|---|---|---|---|
| `NEW_FEATURE_IMPLEMENTATION_PLAN.md` | 66 KB | 1570 | Status "Ready for Implementation" — fitur sudah jadi |
| `Implementation_Plan.md` | 26 KB | 750 | Menyebut "Laravel 11"; plan MVP selesai |
| `Requirements.md` | 13 KB | 256 | Requirement MVP |

Ketiganya penuh mojibake (`dY"<`, `dYs?`, `�?"`, `�o.`).

**Struktur `docs/PROJECT.md` (±200 baris):**

```markdown
# Bank Sampah — Project Reference

## 1. Ringkasan Produk
   (dari Requirements.md — bagian yang masih valid)

## 2. Domain Model
   Customer · Deposit · DepositItem · Withdrawal · LedgerEntry · WasteCategory · WastePrice
   - Saldo = agregat ledger_entries (KREDIT +, DEBIT −)
   - Format kode: DEP-YYYYMMDD-#### · WD-YYYYMMDD-#### · CUST-#### · BSB-+12

## 3. Aturan Bisnis
   - Deposit hanya status='final'
   - Harga via getActivePriceOn($date)
   - Penarikan ditolak jika saldo < amount

## 4. Portal Nasabah & Keamanan
   (dari NEW_FEATURE_IMPLEMENTATION_PLAN.md — bagian security)
   - Rate limit 5/5 menit · session 30 menit · IP validation
   - unique_code 16 char, cryptographically secure

## 5. Referensi Era Awal (arsip)
   > Konteks historis — BUKAN panduan aktif.
   - Repo dimulai 2025-10-22, commit 64bd947 "commit iterasi pertama vibe coding"
   - Target awal: Laravel 11, MySQL 8, XAMPP — saat ini sudah Laravel 12 + SQLite
   - 7 fase MVP TDD (Fat Models, Skinny Controllers)
   - Keputusan awal yang masih berlaku: ledger sebagai sumber saldo,
     kode unik acak untuk portal, DomPDF untuk struk
   - Keputusan yang sudah usang: Laravel 11, MySQL, XAMPP-only setup

## 6. Roadmap
   (pointer ke docs/PLAN.md)
```

```powershell
git rm Requirements.md Implementation_Plan.md NEW_FEATURE_IMPLEMENTATION_PLAN.md
```

Riwayat tetap ada di git (19 commit), tidak hilang permanen.

### 3.3 🟠 README drift — tulis ulang

5 salah fakta terverifikasi:

| README bilang | Kenyataan |
|---|---|
| "menggunakan **Laravel 11**" (baris 3) | Laravel **12** |
| `git clone .../bank-sampah-**vibecode**.git` | remote: `.../bank-sampah.git` |
| "MySQL 8.0+" + setup XAMPP/phpMyAdmin | `.env.example` → `DB_CONNECTION=sqlite` |
| Link ke `Requirements.md` & `Implementation_Plan.md` | akan dihapus |
| `php artisan pint` (di copilot-instructions) | **salah** → `vendor/bin/pint` |

Target README (±80 baris): stack aktual · setup SQLite **dan** MySQL · perintah benar · link `docs/` · tanpa mojibake.

### 3.4 🟡 Artefak lain

| Item | Temuan | Aksi |
|---|---|---|
| `welcome.blade.php` (270 baris) | **Terverifikasi tidak dipakai** — route `/` → `LandingController@index` → `landing.index` | **Hapus** |
| `LandingController::about()/prices()/contact()` | **3 method mati** — merender `landing.about`/`prices`/`contact` yang **tidak ada di disk**; route tidak terdaftar | **Hapus** |
| `.github/copilot-instructions.md` (10 KB) | Hanya dibaca Copilot; isi duplikat konvensi | Gabung ke `AGENTS.md`, hapus |
| `AGENTS.md` (12 KB) | **Murni generated Boost** — konvensi proyek (KISS/DRY/refactor >200–300 baris) tidak ada | Tambah bagian proyek |
| `database/database.sqlite` (4.6 MB) | sudah di-ignore | biarkan |
| `storage/logs/laravel.log` (44 KB) | sudah di-ignore | biarkan |

### 3.5 Struktur target

```
bank-sampah/
├── AGENTS.md                  ← Boost guidelines + konvensi proyek
├── README.md                  ← ditulis ulang, akurat
├── docs/
│   ├── PLAN.md                ← plan ini
│   └── PROJECT.md             ← gabungan 3 MD + referensi era awal
├── .agents/skills/            ← SATU lokasi (35 file, 144 KB)
├── .github/
│   └── workflows/ci.yml       ← BARU (Fase 7)
├── app/ config/ database/ resources/ routes/ tests/
└── composer.json  package.json  vite.config.js
```

**Hilang dari root:** 3 MD usang · `.github/skills/` · `.github/copilot-instructions.md` · `resources/views/welcome.blade.php`

**Reduksi:** ~496 KB → ~250 KB (**−50%**), ~230 → ~150 file tracked.

### 3.6 Eksekusi

```powershell
git checkout -b chore/cleanup-repo
git rm -r .github/skills
git rm Requirements.md Implementation_Plan.md NEW_FEATURE_IMPLEMENTATION_PLAN.md
git rm resources/views/welcome.blade.php
git rm .github/copilot-instructions.md
# hapus 3 method mati di LandingController
git commit -m "chore: bersihkan repo - hapus duplikat skills, MD usang, dead code"
```

**Gerbang keluar:** `71 passed` tetap hijau · `npm run build` sukses · landing & portal render.

---

## 4. FASE 2 — 2 Skills domain

Boost sudah menyediakan 5 skill generik (`laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`, `deploying-to-cloud`, `infer-conventions`).
Yang **tidak akan pernah** dibuat Boost karena spesifik domain:

| Skill | Isi |
|---|---|
| `.agents/skills/bank-sampah-domain/SKILL.md` | Saldo = agregat `ledger_entries` (`KREDIT` +, `DEBIT` −). Kode: `DEP-YYYYMMDD-####`, `WD-YYYYMMDD-####`, `CUST-####`, `BSB-`+12. Harga via `getActivePriceOn($date)`. Deposit hanya `status='final'`. `prices()` = `wastePrices()` (alias — jangan duplikasi). |
| `.agents/skills/security-review-portal/SKILL.md` | Rate limit 5/5 menit · session 30 menit · IP validation · `unique_code` `size:16` · jangan log kode penuh (`substr(...,0,6).'******'`) · otorisasi `deposit->customer_id === session('portal_customer_id')`. |

---

## 5. FASE 3 — Bug Produksi

### 5.1 🔴 BUG-1 — DomPDF di `require-dev`

`composer.json:14` → `"barryvdh/laravel-dompdf": "^3.1"` di **require-dev**.
Dipakai runtime: `DepositController.php:119` · `ReportController.php:7` · `WithdrawalController` (downloadPdf).

Deploy `--no-dev` → **fatal error** saat download PDF.

```powershell
composer require barryvdh/laravel-dompdf
```

### 5.2 🔴 BUG-2 — Race condition generate kode

`app/Models/Customer.php:52` — `lockForUpdate()` di event `creating`.

**Dua cacat:**
1. Di luar transaksi eksplisit → no-op. `CustomerSeeder`, `CustomerFactory`, `GenerateUniqueCodesCommand` **tidak** membungkus transaksi.
2. Saat query 0 baris (transaksi pertama hari itu) **tidak ada row untuk dikunci** → tidak melindungi.

Pola sama: `DepositService.php:107-120` (`DEP-`) · `WithdrawalService.php:71-83` (`WD-`) · `Customer.php:49-72` (`CUST-`).

**Perbaikan 3 lapis:** (1) selalu dalam `DB::transaction` · (2) `lockForUpdate()` untuk kasus row ada · (3) **retry loop** saat `QueryException` unique:

```php
for ($attempt = 1; $attempt <= 3; $attempt++) {
    try {
        return Deposit::create(['code' => $code, ...]);
    } catch (\Illuminate\Database\QueryException $e) {
        if ($attempt === 3 || ! $this->isUniqueViolation($e)) {
            throw $e;
        }
        $code = $this->generateDepositCode($date);
    }
}
```

`isUniqueViolation()` cocokkan `UNIQUE constraint failed` (SQLite) **dan** `Duplicate entry` (MySQL).

### 5.3 🔴 BUG-3 — `lockForUpdate()` tanpa transaksi

Tercakup §5.2 butir 1. Audit: `CustomerSeeder`, `CustomerFactory`, `GenerateUniqueCodesCommand`.

---

## 6. FASE 4 — Performa

### 6.1 🟠 PERF-1 — N+1 saldo (paling parah)

`Customer.php:118-125` → `$this->ledgerEntries()->get()->sum(...)` = tarik **semua** row ke memory.
Dipanggil di `customers/index.blade.php:100` **dalam loop** `paginate(15)` → 15 query × seluruh riwayat.

```php
public function getBalanceAttribute(): float
{
    return (float) $this->ledgerEntries()
        ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'KREDIT' THEN amount ELSE -amount END), 0) as balance")
        ->value('balance');
}
```

Plus **memoize** (dipanggil 2–3× per view) + `withSum()` di `CustomerController::index` → 15 nasabah = 1 query.

### 6.2 🟠 PERF-2 — `whereDate()` mematikan index (27 pemakaian)

**Dua kasus BERBEDA — jangan disamakan.**

**Kasus A — `transaction_date` bertipe `DATE`** (schema: `$table->date(...)` + index sudah ada)
`ReportController` (16×) · `DepositController` (2×) · `WithdrawalController` (2×) · `TransactionHistoryController` (2×)

→ **cukup buang `whereDate`**: `->where('transaction_date', '>=', $start)`
⚠️ **Jangan** tambah `' 00:00:00'` — di SQLite kolom DATE dibandingkan sebagai string; suffix jam merusak `<=`.

**Kasus B — `created_at` bertipe timestamp**
`DashboardController` (5×: baris 16, 20, 23, 26, 33)
→ `->whereBetween('created_at', [$today, $today->copy()->endOfDay()])`

**Index baru** (migration): `$table->index(['status', 'transaction_date'])` — `status` **tanpa index**, padahal hampir semua query report memfilternya.

### 6.3 🟠 PERF-3 — Duplikasi `ReportController`

`summary()` baris **13–77** vs `downloadPdf()` baris **80–143** → ~60 baris identik.
Ekstrak ke `app/Services/ReportService.php::buildSummary($start, $end): array`.
**Verifikasi:** snapshot angka via tinker **sebelum** refactor; sesudahnya harus **identik**.

### 6.4 🟠 PERF-4 — Logika di Blade

`customers/show.blade.php:144-152` menghitung `running_balance` di template.
Pindah ke `Customer::ledgerWithRunningBalance(int $limit = 20): Collection`.
Pola sama di `CustomerPortalController::transactions()` baris 140–146 — satukan.

### 6.5 🟡 PERF-5 — `TransactionHistoryController` UNION ALL (K4: quote saja)

`TransactionHistoryController.php:13-35` — derived table `(SELECT ... UNION ALL SELECT ...)` tanpa index → full scan dua tabel.
Perbaikan K4: ganti `"deposit"` / `"withdrawal"` (double quote, rapuh) → `'deposit'` / `'withdrawal'`.
Refactor ke `ledger_entries` dicatat sebagai item terpisah, **tidak** dikerjakan sekarang.

---

## 7. FASE 5 — Tailwind v4 (K3: LANJUT)

### 7.1 ⚠️ Risiko diterima

v4 butuh **Chrome 111+ / Safari 16.4+ / Firefox 128+** (`@property` + `color-mix()`).
Browser lama **tidak render** — bukan degradasi grafis, utility bisa gagal total.
**Keputusan: lanjut**, risiko diterima.

### 7.2 Kondisi saat ini (inkonsisten)

`tailwindcss ^3.1.0` + `@tailwindcss/vite ^4.0.0` (terpasang, **tidak dipakai**) + `postcss.config.js` + `tailwind.config.js` + `@tailwind` directives.

### 7.3 Langkah

```powershell
git checkout -b chore/tailwind-v4
npx @tailwindcss/upgrade
```

Jangan manual — 279 `rounded` + 190 `shadow` terlalu banyak.

| File | Aksi |
|---|---|
| `postcss.config.js` | hapus |
| `tailwind.config.js` | hapus; Figtree → `@theme` |
| `package.json` | hapus `postcss`, `autoprefixer`, `tailwindcss@^3`; naikkan `tailwindcss@^4` |
| `vite.config.js` | tambah `tailwindcss()` dari `@tailwindcss/vite` |

```css
/* resources/css/app.css */
@import 'tailwindcss';
@import 'tom-select/dist/css/tom-select.bootstrap5.css';
@plugin '@tailwindcss/forms';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';

@theme { --font-sans: 'Figtree', ui-sans-serif, system-ui, sans-serif; }

@layer base {
  *, ::after, ::before, ::backdrop, ::file-selector-button {
    border-color: var(--color-gray-200, currentColor);
  }
}
```

**Terlewat mudah:**
- `@source` pagination — v4 auto-detect **tidak** menjangkau `vendor/` → pagination kehilangan style
- `storage/framework/views` di `content[]` lama **harus dibuang** (jangan scan hasil kompilasi)

### 7.4 Perbaikan manual wajib (60 file blade)

| Utility | Jumlah | Perbaikan | Risiko |
|---|---|---|---|
| `bg-opacity-*` | **14** | `bg-white/20` | sedang |
| `flex-shrink-*` | **13** | `shrink-*` | nol |
| `outline-none` | **23** | `outline-hidden` | **makna berubah** |
| `border` tanpa warna | **71** | + `border-gray-200` | **tinggi** |
| `shadow` (bare) | 190 | tool → `shadow-sm` | nol |
| `shadow-sm` | 99 | tool → `shadow-xs` | nol |
| `rounded` (bare) | 279 | tool → `rounded-sm` | nol |
| `space-y-*` / `divide-y-*` | 60 | selector berubah | sedang |

`bg-opacity` ada di: `customer-portal/dashboard` · `customer-portal/deposit-detail` · `customer-portal/info` · `landing/index`.

### 7.5 Verifikasi visual

`hover` v4 hanya `@media (hover: hover)` → **di HP hilang**. `backdrop-blur-sm` → `backdrop-blur-xs`.
Cek: landing · dashboard · 4 halaman portal · `deposits/create` · `withdrawals/create` · `customers/index` · semua PDF.

---

## 8. FASE 6 — Test + CI

Test ada: Deposit, Withdrawal, WasteCategory, Profile, Auth (6 file).
Test **NOL**: `CustomerPortalController` · `CustomerSessionGuard` · `ReportController` · `DepositService` · `WithdrawalService`.

| # | File test | Cakupan |
|---|---|---|
| 1 | `tests/Feature/CustomerPortalTest.php` | verify valid/invalid · rate limit · session timeout · akses deposit orang lain → 403 |
| 2 | `tests/Feature/ReportSummaryTest.php` | angka cocok dengan factory |
| 3 | `tests/Unit/CodeGenerationTest.php` | format `DEP-`/`WD-`/`CUST-` · increment · retry duplikat |

`.github/workflows/ci.yml` (belum ada): `pint --test` → `pest` → `npm run build`.

---

## 9. Urutan eksekusi

| # | Fase | Dampak | Risiko | Branch |
|---|---|---|---|---|
| 0 | Plan masuk `docs/PLAN.md` | 🟢 tinggi | nol | `chore/cleanup-repo` |
| 1 | Cleanup repo | 🟢 tinggi | rendah | `chore/cleanup-repo` |
| 2 | 2 skills domain | 🟢 sedang | nol | `chore/cleanup-repo` |
| 3 | 3 bug produksi | 🔴 kritis | rendah | `fix/production-bugs` |
| 4 | Performa | 🟠 tinggi | sedang | `perf/query-optimization` |
| 5 | Test + CI | 🟠 tinggi | rendah | `test/portal-report-coverage` |
| 6 | Tailwind v4 | 🟡 kosmetik | **tinggi** | `chore/tailwind-v4` |

Fase 6 (Tailwind) **terakhir** dan branch sendiri — agar `git bisect` tidak tercampur perubahan logika bisnis.

---

## 10. Definition of Done

- [ ] `docs/PLAN.md` + `docs/PROJECT.md` ada, dirujuk dari `AGENTS.md`
- [ ] Repo ≤ 250 KB tracked; skills hanya di `.agents/skills/`
- [ ] 3 MD usang terhapus; README akurat (Laravel 12, sqlite, clone URL, tanpa mojibake)
- [ ] `welcome.blade.php` + 3 method mati `LandingController` terhapus
- [ ] `composer install --no-dev` → PDF download **berhasil**
- [ ] 2 deposit paralel tanggal sama → `0001` & `0002`, tanpa exception
- [ ] `customers.index` ≤ 3 query untuk 15 nasabah
- [ ] Angka `reports/summary` identik sebelum vs sesudah refactor
- [ ] Tailwind v4: `npm run build` sukses, semua halaman di §7.5 dicek visual
- [ ] `php artisan test` hijau + ≥3 file test baru
- [ ] `vendor/bin/pint --dirty` bersih
- [ ] CI hijau

---

## 11. Di luar cakupan (dicatat, tidak dikerjakan)

- `TelescopeServiceProvider` unconditional di `bootstrap/providers.php` → aktif di produksi
- `WasteCategory::prices()` vs `wastePrices()` — dua nama untuk relasi sama; kandidat konsolidasi
- Refactor `TransactionHistoryController` ke `ledger_entries` (K4 memilih quote saja)
- `LandingController` hanya punya route `index`; 3 method lain dihapus (bukan diimplementasi)
