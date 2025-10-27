# Bank Sampah - Waste Bank Management System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Aplikasi manajemen Bank Sampah berbasis web yang dibangun menggunakan Laravel 11, Tailwind CSS, dan Alpine.js. Sistem ini memungkinkan pengelolaan setoran sampah, penarikan saldo, dan pelaporan transaksi dengan antarmuka yang mudah digunakan.

---

## 📋 Fitur Utama

### ✅ Manajemen Master Data

-   **Nasabah**: CRUD data nasabah dengan kode anggota unik
-   **Kategori Sampah**: Kelola kategori sampah (Plastik, Kertas, Kaleng, dll.)
-   **Harga Sampah**: History harga dengan effective date

### 💰 Transaksi

-   **Setoran**: Catat setoran sampah multi-item dengan perhitungan otomatis
-   **Penarikan**: Proses penarikan saldo dengan validasi balance
-   **Buku Besar**: Tracking semua mutasi dengan running balance
-   **Struk PDF**: Generate receipt untuk setiap transaksi

### 📊 Pelaporan

-   **Dashboard**: Metrics hari ini (setoran, penarikan, top kategori)
-   **Laporan Ringkasan**: Filter by date range, breakdown per kategori, top customers
-   **Export PDF**: Download laporan dalam format PDF

### 🔐 Authorization

-   **Role-based Access**: Admin & Operator dengan permission berbeda
-   **Policy Protection**: Automatic authorization menggunakan Laravel Policies

---

## 🚀 Tech Stack

-   **Backend**: Laravel 11.x
-   **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
-   **Database**: MySQL 8.0+
-   **PDF Generation**: DomPDF
-   **Testing**: Pest PHP
-   **Code Quality**: Laravel Pint (PSR-12)

---

## 📦 Installation

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   MySQL 8.0 or higher
-   Node.js & NPM

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/bank-sampah.git
cd bank-sampah
```

### Step 2: Install Dependencies

```bash
composer install
npm install
```

### Step 3: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file dan konfigurasikan database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bank_sampah
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Database Migration & Seeding

```bash
php artisan migrate --seed
```

Seeder akan membuat:

-   2 User: Admin & Operator
-   5 Kategori Sampah dengan harga awal
-   10 Nasabah dummy
-   Sample transaksi

### Step 5: Build Frontend Assets

```bash
npm run build
```

Untuk development:

```bash
npm run dev
```

### Step 6: Run Application

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 👤 Default Credentials

### Admin

-   **Email**: `admin@banksampah.test`
-   **Password**: `password`
-   **Akses**: Full access (termasuk master data)

### Operator

-   **Email**: `operator@banksampah.test`
-   **Password**: `password`
-   **Akses**: Transaksi only (setoran & penarikan)

---

## 🎬 Demo Scenario (5 Menit)

### Persiapan

```bash
php artisan migrate:fresh --seed
```

### Flow Demo

**1. Login sebagai Operator** (2 menit)

-   Dashboard: Lihat metrics hari ini
-   Setoran Baru:
    -   Pilih nasabah "Budi Setiawan"
    -   Tambah: Plastik PET 1.2 kg + Kertas 0.5 kg
    -   Total: Rp 4.350
    -   Generate & Download PDF
-   Cek profil nasabah → Saldo bertambah

**2. Transaksi Penarikan** (1 menit)

-   Pilih nasabah "Siti Aminah"
-   Input nominal: Rp 2.000
-   Validasi balance
-   Download receipt

**3. Laporan** (2 menit)

-   Filter by date range
-   Lihat breakdown per kategori
-   Top 10 customers
-   Export PDF

---

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
php artisan test --testsuite=Feature
php artisan test --filter=DepositManagementTest
```

### Test Coverage

```bash
php artisan test --coverage
```

---

## 📂 Project Structure

```
bank-sampah/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # All controllers
│   │   ├── Requests/              # Form validation requests
│   │   └── Middleware/
│   ├── Models/                    # Eloquent models
│   ├── Policies/                  # Authorization policies
│   └── Services/                  # Business logic services
│       ├── DepositService.php
│       └── WithdrawalService.php
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── resources/
│   └── views/                     # Blade templates
│       ├── customers/
│       ├── deposits/
│       ├── withdrawals/
│       ├── waste-categories/
│       ├── reports/
│       └── ledger/
├── routes/
│   └── web.php                    # Web routes
└── tests/
    └── Feature/                   # Feature tests
```

---

## 🛠️ Development

### Code Formatting

```bash
./vendor/bin/pint
```

### Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Fresh Start

```bash
php artisan migrate:fresh --seed
```

---

## 📖 API Endpoints

Semua route memerlukan authentication (`auth` middleware).

### Master Data

-   `GET /customers` - List nasabah
-   `POST /customers` - Create nasabah
-   `GET /customers/{id}` - Detail nasabah
-   `PUT /customers/{id}` - Update nasabah
-   `DELETE /customers/{id}` - Delete nasabah (Admin only)

### Transaksi

-   `GET /deposits/create` - Form setoran
-   `POST /deposits` - Simpan setoran
-   `GET /deposits/{id}` - Detail setoran
-   `GET /deposits/{id}/download-pdf` - Download PDF

-   `GET /withdrawals/create` - Form penarikan
-   `POST /withdrawals` - Simpan penarikan
-   `GET /withdrawals/{id}` - Detail penarikan
-   `GET /withdrawals/{id}/download-pdf` - Download PDF

### Laporan

-   `GET /reports/summary` - Laporan ringkasan
-   `GET /reports/download-pdf` - Download laporan PDF
-   `GET /customers/{id}/ledger` - Buku besar nasabah

---

## 🔧 Configuration

### PDF Settings

Edit `config/dompdf.php` untuk konfigurasi PDF generation.

### Queue Configuration

Untuk production, gunakan queue untuk proses yang berat:

```bash
php artisan queue:work
```

---

