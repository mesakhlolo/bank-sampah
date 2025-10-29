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

-   **Master Data**: Nasabah, Kategori Sampah, Harga
-   **Transaksi**: Setoran multi-item, Penarikan dengan validasi saldo
-   **Laporan**: Dashboard metrics, Laporan ringkasan, Export PDF
-   **Authorization**: Role-based (Admin & Operator)
-   **Buku Besar**: Tracking mutasi per nasabah

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

## 🛠️ Development Commands

```powershell
# Format code
./vendor/bin/pint

# Clear cache
php artisan optimize:clear

# Reset database
php artisan migrate:fresh --seed

# Run tests
php artisan test
```

---

## 📚 Dokumentasi

-   **[Requirements.md](Requirements.md)** - Requirement aplikasi lengkap
-   **[Implementation_Plan.md](Implementation_Plan.md)** - Rencana implementasi detail

---

## 📄 License

This project is open-sourced software licensed under the MIT license.
