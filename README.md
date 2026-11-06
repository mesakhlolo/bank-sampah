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

### 5. Jalankan aplikasi

Pilih salah satu cara di bawah. Jangan jalankan Herd, XAMPP Apache, dan `php artisan serve` bersamaan pada port yang sama.

#### Opsi A — Laravel Herd (Windows/macOS, disarankan)

1. Install [Laravel Herd](https://herd.laravel.com/) dan pastikan **Herd Desktop berjalan**.
2. Letakkan proyek di folder yang diparkir Herd, default:
   `C:\Users\<nama>\Herd\bank-sampah`
3. Jalankan setup dari folder proyek:

   ```powershell
   composer install
   npm install
   copy .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

4. Buka `http://bank-sampah.test`.

Herd otomatis membuat domain `<nama-folder>.test`. Jika situs belum muncul:

- Pastikan folder proyek berada di dalam folder `Herd`.
- Buka **Herd > Sites**, pastikan site `bank-sampah` aktif, lalu restart Herd.
- Jika `php` di terminal bukan PHP Herd, pakai `herd php artisan ...` dan `herd composer ...`.

Saat development frontend, jalankan `npm run dev` di terminal terpisah. Untuk sekadar mencoba aplikasi, `npm run build` sudah cukup.

#### Opsi B — XAMPP (Windows)

1. Install XAMPP dengan PHP 8.2+ (disarankan 8.4). Aktifkan extension berikut di
   `C:\xampp\php\php.ini` dengan menghapus tanda `;` di depannya:

   ```ini
   extension=curl
   extension=fileinfo
   extension=gd
   extension=mbstring
   extension=openssl
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```

2. Copy proyek ke `C:\xampp\htdocs\bank-sampah`.
3. Buka **XAMPP Control Panel**, start **Apache**. MySQL hanya perlu distart jika memakai MySQL.
4. Jalankan setup memakai PHP XAMPP:

   ```powershell
   cd C:\xampp\htdocs\bank-sampah
   C:\xampp\php\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install
   npm install
   copy .env.example .env
   C:\xampp\php\php.exe artisan key:generate
   C:\xampp\php\php.exe artisan migrate --seed
   C:\xampp\php\php.exe artisan storage:link
   npm run build
   ```

   Jika `composer` sudah ada di `PATH`, cukup `composer install`.

5. Akses aplikasi. Ada dua pilihan:

   **Cara cepat — built-in server XAMPP** (tanpa virtual host):

   ```powershell
   C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
   ```

   Buka `http://127.0.0.1:8000`.

   **Cara Apache — virtual host** (disarankan untuk pemakaian harian):

   Tambahkan ke `C:\Windows\System32\drivers\etc\hosts`:

   ```text
   127.0.0.1 bank-sampah.test
   ```

   Tambahkan ke `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

   ```apache
   <VirtualHost *:80>
       ServerName bank-sampah.test
       DocumentRoot "C:/xampp/htdocs/bank-sampah/public"

       <Directory "C:/xampp/htdocs/bank-sampah/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

   Restart Apache, lalu buka `http://bank-sampah.test`.

   **Jika memakai MySQL**, buat database `bank_sampah` lewat
   `http://localhost/phpmyadmin`, lalu ubah `.env`:

   ```env
   APP_URL=http://bank-sampah.test
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bank_sampah
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Setelah itu jalankan `C:\xampp\php\php.exe artisan migrate --seed`.

#### Opsi C — PHP built-in server

Cocok untuk tes cepat tanpa Herd/XAMPP:

```powershell
php artisan serve --no-reload --host=127.0.0.1 --port=8000
```

Buka `http://127.0.0.1:8000`.

`--no-reload` diperlukan di Windows jika muncul error
`Failed to listen on ...` karena `PHP_CLI_SERVER_WORKERS` di `.env`.
Alternatif lain: hapus baris `PHP_CLI_SERVER_WORKERS` dari `.env`, lalu
`php artisan optimize:clear`.

### Login

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@banksampah.test` | `password` |
| Operator | `operator@banksampah.test` | `password` |

Login admin untuk mengelola nasabah, kategori, harga, setoran, penarikan, dan laporan.
Portal nasabah ada di `/nasabah/cek` dan memakai **Unique Code** (`BSB-...`), bukan email.

Semua perintah development sekaligus: `composer run dev` (server + queue + log + Vite).

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
| `could not find driver` (SQLite) | Aktifkan `extension=pdo_sqlite` dan `extension=sqlite3`, restart PHP/Apache |
| `isActive(): Return value must be of type bool, null returned` | Migrasi belum jalan: `php artisan migrate` |
| `database/database.sqlite` tidak ditemukan | Buat file kosong `database\database.sqlite`, lalu `php artisan migrate --seed` |
| `Failed to listen on 127.0.0.1:8000` | Pakai `php artisan serve --no-reload` atau hapus `PHP_CLI_SERVER_WORKERS` dari `.env` |
| Herd menampilkan 404 / site tidak ditemukan | Pastikan Herd Desktop berjalan dan folder proyek ada di `C:\Users\<nama>\Herd` |
| Apache XAMPP gagal start / port 80 bentrok | Hentikan Herd atau Apache yang lain; hanya satu yang boleh memakai port 80 |
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
