# Bank Sampah - Waste Bank Management System

Aplikasi manajemen Bank Sampah berbasis web menggunakan Laravel 11, Tailwind CSS, dan Alpine.js.

## 🚀 Tech Stack

-   Laravel 11.x | PHP 8.2+ | MySQL 8.0+
-   Tailwind CSS + Alpine.js
-   DomPDF | Pest PHP | Laravel Pint

---

## 📋 Requirements

1. **XAMPP** (PHP 8.2+, MySQL, Apache) - [Download](https://www.apachefriends.org/)
2. **Composer** - [Download](https://getcomposer.org/download/)
3. **Node.js & NPM** - [Download](https://nodejs.org/)
4. **Git** (optional) - [Download](https://git-scm.com/download/win)

---

## 🛠️ Setup di Windows (XAMPP)

### 1. Install Requirements

-   Install XAMPP → Start Apache & MySQL di Control Panel
-   Install Composer → Arahkan ke `C:\xampp\php\php.exe` saat setup
-   Install Node.js (LTS version)
-   (Optional) Tambahkan `C:\xampp\php` ke PATH environment variable

### 2. Clone Project

```powershell
cd C:\xampp\htdocs
git clone https://github.com/mesakhlolo/bank-sampah-vibecode.git
cd bank-sampah-vibecode
```

Atau download ZIP dan extract ke `C:\xampp\htdocs\bank-sampah-vibecode`

### 3. Install Dependencies

```powershell
composer install
npm install
```

**Jika error extension PHP:**

-   Buka `C:\xampp\php\php.ini`
-   Uncomment (hapus `;`) pada: `extension=fileinfo`, `extension=gd`, `extension=mbstring`, `extension=openssl`, `extension=pdo_mysql`, `extension=zip`
-   Restart Apache di XAMPP

### 4. Setup Database

1. Buka http://localhost/phpmyadmin
2. Buat database baru: `bank_sampah` (collation: utf8mb4_unicode_ci)
3. Copy file environment:
    ```powershell
    copy .env.example .env
    ```
4. Edit `.env`:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=bank_sampah
    DB_USERNAME=root
    DB_PASSWORD=
    ```
5. Generate key:
    ```powershell
    php artisan key:generate
    ```

### 5. Migrasi & Seeding

```powershell
php artisan migrate:fresh --seed
```

**Data yang dibuat:**

-   2 User (Admin & Operator)
-   50 Nasabah
-   5 Kategori Sampah dengan harga
-   ~300 Transaksi sample

### 6. Build Frontend

```powershell
npm run build
```

Atau untuk development:

```powershell
npm run dev
```

### 7. Jalankan Aplikasi

```powershell
php artisan serve
```

Akses: http://127.0.0.1:8000

**Login:**

-   **Admin**: admin@banksampah.test / password
-   **Operator**: operator@banksampah.test / password

---

## ✅ Fitur Utama

### Admin Dashboard

-   **Master Data**: Nasabah, Kategori Sampah, Harga
-   **Transaksi**: Setoran multi-item, Penarikan dengan validasi saldo
-   **Laporan**: Dashboard metrics, Laporan ringkasan, Export PDF
-   **Authorization**: Role-based (Admin & Operator)
-   **Buku Besar**: Tracking mutasi per nasabah

### 🌐 Fitur Publik (NEW!)

#### Landing Page

Akses: http://127.0.0.1:8000/

Halaman depan informatif dengan:

-   Penjelasan tentang Bank Sampah
-   Statistik real-time (nasabah, sampah terkumpul, rupiah tersalur)
-   Daftar harga sampah terkini
-   Cara kerja Bank Sampah (4 langkah mudah)
-   Call-to-action untuk daftar & cek saldo

#### Portal Nasabah

Akses: http://127.0.0.1:8000/nasabah/cek

Nasabah dapat:

-   Cek saldo menggunakan **kode unik** (format: BSB-XXXXXXXXXXXX)
-   Lihat dashboard dengan statistik pribadi
-   Lihat riwayat transaksi lengkap (setoran & penarikan)
-   Lihat detail setoran dengan breakdown item
-   Lihat harga sampah terkini dan informasi bantuan
-   **Tidak perlu akun/login**, cukup kode unik saja

**🔒 Keamanan Portal:**

-   Kode unik cryptographically secure (4.7+ triliun kombinasi)
-   Rate limiting: max 5 percobaan per 5 menit
-   Session timeout: 30 menit
-   IP validation untuk prevent session hijacking
-   Comprehensive security logging

**Demo Access:**

Untuk mendapatkan kode unik nasabah:

1. Login ke admin dashboard sebagai admin/operator
2. Buka menu "Nasabah"
3. Lihat detail nasabah → akan tampil **Member Code** (CUST-XXXX) dan **Unique Code** (BSB-XXXXXXXXXXXX)
4. Gunakan **Unique Code** untuk login ke portal

Atau via command line:

```powershell
php artisan tinker
Customer::first()->unique_code  # Tampilkan kode unik
```

---

## 🐛 Troubleshooting

**Error "Access denied" database:**

```
Periksa .env: DB_USERNAME=root, DB_PASSWORD= (kosong untuk XAMPP default)
```

**Error "Class 'ZipArchive' not found":**

```
Buka php.ini → Uncomment extension=zip → Restart Apache
```

**CSS tidak muncul:**

```powershell
npm run build
```

**Error "Vite manifest not found":**

```powershell
npm install
npm run build
```

---

## 🎬 Demo Scenario (7 Menit)

### Menit 0-1: Landing Page (NEW)

1. Buka http://127.0.0.1:8000/ (landing page)
2. Scroll through sections: Hero → Cara Kerja → Statistik → Harga Sampah
3. Klik tombol "Cek Saldo Saya"

### Menit 1-2: Customer Portal (NEW)

4. Masukkan kode unik nasabah (dapatkan dari admin atau tinker)
5. Dashboard nasabah tampil: saldo, stats, transaksi terbaru
6. Navigasi ke "Riwayat Transaksi" → lihat semua transaksi
7. Klik detail salah satu setoran → tampil breakdown items
8. Klik "Info & Bantuan" → lihat FAQ dan harga terkini
9. Logout dari portal

### Menit 2-3: Admin Login

10. Klik "Masuk Admin" dari landing page
11. Login sebagai Operator (operator@banksampah.test / password)

### Menit 3-5: Core Transactions (Existing)

12. Dashboard admin → lihat metrics dan recent activities
13. Buat setoran baru untuk nasabah
14. Proses penarikan saldo

### Menit 5-6: Reports (Existing)

15. Lihat laporan ringkasan
16. Download PDF laporan

### Menit 6-7: Full Circle

17. Logout dari admin
18. Kembali ke portal nasabah (gunakan kode unik lagi)
19. Verify transaksi baru muncul di riwayat
20. Saldo updated dengan benar ✅

## 🛠️ Development Commands

```powershell
# Format code
./vendor/bin/pint

# Clear cache
php artisan optimize:clear

# Reset database (will generate new unique codes)
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Generate unique code for specific customer
php artisan tinker
$customer = Customer::find(1);
$customer->unique_code;
```

---

## 📚 Dokumentasi

-   **[Requirements.md](Requirements.md)** - Requirement aplikasi lengkap
-   **[Implementation_Plan.md](Implementation_Plan.md)** - Rencana implementasi detail

---

## 📄 License

This project is open-sourced software licensed under the MIT license.
