# Bank Sampah — Waste Bank Management System

Aplikasi manajemen Bank Sampah berbasis web: pencatatan setoran sampah, akumulasi saldo tabungan nasabah, penarikan saldo, laporan, dan portal nasabah.

**Stack:** Laravel 12.x · PHP 8.4 · SQLite (default) / MySQL · Blade + Alpine.js + Tailwind CSS · DomPDF · Pest 3 · Laravel Pint

> Dokumentasi lengkap: [`docs/PROJECT.md`](docs/PROJECT.md) (domain & aturan bisnis) · [`docs/PLAN.md`](docs/PLAN.md) (roadmap optimasi)

---

## Requirements

| Tool | Versi |
|---|---|
| PHP | 8.2+ (teruji di 8.4) |
| Composer | 2.x |
| Node.js | 20+ (teruji di 26) |
| Database | SQLite (default) atau MySQL 8 / MariaDB |

Lingkungan lokal yang disarankan: **Laravel Herd** (Windows/macOS) atau XAMPP/Laragon.

---

## Setup

### 1. Clone & install

```powershell
git clone https://github.com/mesakhlolo/bank-sampah.git
cd bank-sampah
composer install
npm install
```

### 2. Environment

```powershell
copy .env.example .env
php artisan key:generate
```

`.env.example` memakai **SQLite** secara default — tidak perlu konfigurasi database tambahan.

<details>
<summary>Jika ingin memakai MySQL</summary>

Buat database `bank_sampah` (collation `utf8mb4_unicode_ci`), lalu ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bank_sampah
DB_USERNAME=root
DB_PASSWORD=
```

</details>

### 3. Migrasi & seed

```powershell
php artisan migrate:fresh --seed
```

Data yang dibuat: 2 user (admin & operator), 50 nasabah, 5 kategori sampah + harga, ~300 transaksi sample.

### 4. Build frontend

```powershell
npm run build
```

Untuk development: `npm run dev`

### 5. Jalankan

```powershell
php artisan serve
```

Buka http://127.0.0.1:8000

**Login:**

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@banksampah.test` | `password` |
| Operator | `operator@banksampah.test` | `password` |

Atau jalankan semuanya sekaligus: `composer run dev` (server + queue + log + Vite).

---

## Fitur

### Admin / Operator

- **Master data** — nasabah, kategori sampah, harga (dengan riwayat `effective_from`)
- **Transaksi** — setoran multi-item, penarikan dengan validasi saldo
- **Buku besar** — mutasi saldo per nasabah
- **Laporan** — ringkasan periode, akumulasi per kategori, top 10 nasabah, ekspor PDF
- **Bukti PDF** — struk setoran & penarikan
- **Authorization** — role-based (Admin & Operator) via Policy

### Publik

**Landing page** (`/`) — penjelasan, statistik real-time, daftar harga terkini, cara kerja.

**Portal nasabah** (`/nasabah/cek`) — tanpa akun, cukup kode unik:

- Cek saldo & statistik pribadi
- Riwayat transaksi lengkap (setoran & penarikan)
- Detail setoran dengan breakdown item
- Harga sampah terkini & info bantuan

**Keamanan portal (8 lapis):** kode acak kriptografis (4,7 triliun kombinasi) · rate limit 5 percobaan/5 menit · session server-side · timeout 30 menit · IP validation · authorization per-request · audit logging · input validation. Detail di [`docs/PROJECT.md` §4](docs/PROJECT.md).

**Mendapatkan kode unik nasabah:**

1. Login admin → menu **Nasabah** → detail nasabah menampilkan **Member Code** (`CUST-XXXX`) dan **Unique Code** (`BSB-XXXXXXXXXXXX`)
2. Gunakan **Unique Code** untuk masuk portal

Atau via tinker:

```powershell
php artisan tinker --execute 'echo App\Models\Customer::first()->unique_code;'
```

---

## Perintah Development

```powershell
# Format code (PSR-12)
vendor/bin/pint
vendor/bin/pint --dirty        # hanya file berubah

# Test
php artisan test
php artisan test --compact
php artisan test --filter=NamaTest

# Clear cache
php artisan optimize:clear

# Reset database (kode unik akan di-generate ulang)
php artisan migrate:fresh --seed

# Statistik
php artisan stats:show
```

---

## Troubleshooting

| Gejala | Penyebab & solusi |
|---|---|
| `Failed opening required vendor/autoload.php` | `composer install` belum dijalankan |
| `Vite manifest not found` | `npm install && npm run build` |
| CSS tidak muncul | `npm run build` (atau `npm run dev`) |
| `npm.ps1 cannot be loaded` (PowerShell) | `Set-ExecutionPolicy -Scope Process Bypass` atau pakai `npm.cmd` |
| `Class 'ZipArchive' not found` | Aktifkan `extension=zip` di `php.ini`, restart web server |
| `Access denied` database | Periksa `DB_USERNAME` / `DB_PASSWORD` di `.env` |

---

## Struktur Repo

```
bank-sampah/
├── AGENTS.md              # Konvensi untuk AI agent & kontributor
├── docs/
│   ├── PROJECT.md         # Domain, aturan bisnis, referensi historis
│   └── PLAN.md            # Rencana optimasi aktif
├── .agents/skills/        # Skill domain untuk AI agent (Laravel Boost)
├── app/                   # Controllers, Models, Services, Policies, Requests
├── database/              # Migrations, seeders, factories
├── resources/views/       # Blade templates
├── routes/                # web.php, auth.php, console.php
└── tests/                 # Pest feature & unit tests
```

---

## License

MIT
