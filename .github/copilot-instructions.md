# General Coding Guidance

## General Behavior

- **You are an agent**: Lanjutkan bekerja hingga permintaan pengguna terselesaikan sepenuhnya. Akhiri giliran Anda hanya jika Anda yakin masalahnya telah teratasi dan tidak ada tindakan lebih lanjut yang diperlukan.
- **Your thinking should be thorough**: Tidak apa-apa (dan bahkan dianjurkan) jika penalaran Anda panjang. Berpikirlah langkah demi langkah sebelum dan sesudah setiap tindakan yang Anda ambil.
- **Plan extensively before making any function calls**: Lakukan refleksi kritis setelah setiap panggilan. Hindari merangkai panggilan fungsi tanpa introspeksi di antaranya, karena hal itu dapat mengganggu wawasan dan pengambilan keputusan.
- **If you're unsure about file contents or the codebase structure**: Gunakan tools untuk memeriksa dan membaca file yang relevan. Jangan pernah menebak atau membuat asumsi.
- **Only make necessary, intentional changes**: Lakukan perubahan yang diminta secara langsung atau yang jelas-jelas diperlukan untuk penyelesaian tugas. Hindari mengedit area yang tidak terkait atau tidak jelas.

## Code Quality and Style

- **Prefer simple solutions**: Utamakan solusi yang mudah dipahami dan dipelihara. Ikuti prinsip "Keep It Simple, Stupid" (KISS).
- **Avoid code duplication**: Sebelum menulis logika baru, periksa apakah fungsionalitas serupa sudah ada di dalam codebase (prinsip "Don't Repeat Yourself" - DRY).
- **Keep the codebase clean and organized**: Gunakan pola dan konvensi penamaan yang konsisten yang berlaku di komunitas Laravel. Jalankan `php artisan pint` secara teratur.
- **Refactor files when they exceed 200–300 lines of code**: Terutama untuk Controller dan Model, pertimbangkan untuk mengekstrak logika ke Service Class, Action, atau Trait untuk menjaga modularitas dan kejelasan.
- **Never overwrite the .env file without asking for and receiving explicit confirmation**: Selalu konfirmasikan sebelum membuat perubahan pada file konfigurasi lingkungan.
- **Follow best practices around formatting and consistency**: Gunakan linter, formatter, dan panduan gaya seperti PSR-12. Laravel Pint sudah dikonfigurasi untuk ini secara default.

## Laravel Philosophy and Best Practices

- **Fat Models, Skinny Controllers**: Tempatkan semua logika yang terkait dengan data dan bisnis di dalam Model Eloquent atau Service Class. Controller hanya boleh bertindak sebagai perantara antara request HTTP dan respons, mendelegasikan pekerjaan ke bagian lain dari aplikasi.
- **Use Route Model Binding**: Manfaatkan fitur ini untuk secara otomatis meng-inject instance model ke dalam route Anda, sehingga membuat controller lebih bersih.
- **Validation in Form Requests**: Jangan melakukan validasi di dalam controller. Gunakan Form Request (`php artisan make:request`) untuk menampung semua logika validasi dan otorisasi untuk sebuah request.
- **Leverage the Service Container**: Gunakan dependency injection dan service container Laravel untuk mengelola dependensi class dan membuat kode yang lebih mudah diuji dan dipelihara.
- **Use Queues for Long-Running Tasks**: Untuk tugas yang memakan waktu seperti mengirim email, memproses gambar, atau memanggil API eksternal, pindahkan ke antrean (queue) untuk mencegah pengguna menunggu dan meningkatkan responsivitas aplikasi.
- **Avoid the N+1 Problem**: Selalu gunakan _eager loading_ (`with()`) saat mengambil relasi Eloquent untuk mencegah query database yang berlebihan dan meningkatkan performa.
- **Write Tests**: Tulis Feature Test untuk endpoint API atau route Anda dan Unit Test untuk logika bisnis yang terisolasi. Gunakan Pest atau PHPUnit.

---

# Building a Modern Laravel Application

Ikuti langkah-langkah ini untuk membuat aplikasi Laravel yang lengkap dan modern:

1.  **Project Initialization & Setup** - Siapkan fondasi aplikasi.
2.  **Database & Environment Configuration** - Konfigurasi database dan lingkungan lokal.
3.  **Authentication & Authorization Setup** - Instal dan konfigurasi sistem otentikasi.
4.  **Core Feature Development (TDD Approach)** - Bangun fitur inti dengan Test-Driven Development.
5.  **Frontend Asset Compilation** - Siapkan dan kelola aset frontend dengan Vite.
6.  **Service Integration & Refactoring** - Integrasikan layanan eksternal dan refactor logika kompleks.
7.  **Final Testing, Optimization, and Deployment** - Pengujian akhir, optimisasi, dan deployment.

---

## Step 1: Project Initialization & Setup

Selalu gunakan Composer untuk membuat proyek Laravel baru untuk memastikan semua dependensi terinstal dengan benar.

```bash
composer create-project laravel/laravel my-app
cd my-app
php artisan key:generate
```

Ganti `my-app` dengan nama aplikasi yang diberikan oleh pengguna.

**Laravel Ecosystem Tools**:

- **Pest**: Gunakan sebagai alternatif modern untuk PHPUnit untuk pengalaman testing yang lebih ekspresif.
- **Laravel Pint**: Gunakan untuk memformat kode secara otomatis sesuai standar PSR-12.
- **Laravel Telescope**: Instal di lingkungan lokal untuk debugging dan insight aplikasi.
- **Laravel Herd** (macOS) / **Laragon** (Windows): Gunakan sebagai lingkungan pengembangan lokal yang lengkap.

---

## Step 2: Database & Environment Configuration

### Konfigurasi `.env`

Salin `.env.example` ke `.env` dan konfigurasikan koneksi database Anda.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_app
DB_USERNAME=root
DB_PASSWORD=
```

### Migrasi dan Seeding

Buat migrasi pertama Anda dan jalankan untuk membuat skema database. Gunakan seeder untuk mengisi database dengan data awal.

```bash
php artisan make:migration create_clients_table
# Edit file migrasi yang baru dibuat
php artisan migrate --seed
```

Gunakan **Model Factories** untuk menghasilkan data palsu yang terstruktur untuk pengujian dan seeding.

---

## Step 3: Authentication & Authorization Setup

Gunakan Laravel Starter Kits untuk mempercepat penyiapan otentikasi.

### Laravel Breeze (Rekomendasi Awal)

Sederhana, minimalis, dan mudah dikustomisasi. Pilihan yang bagus untuk sebagian besar aplikasi.

```bash
composer require laravel/breeze --dev
# Pilih stack yang diinginkan (misalnya, Blade dengan Alpine.js)
php artisan breeze:install
npm install
npm run dev
php artisan migrate
```

### Laravel Jetstream (Untuk Aplikasi Kompleks)

Lebih canggih, hadir dengan fitur tim, manajemen profil, dan otentikasi dua faktor.

Gunakan Jetstream jika pengguna secara eksplisit meminta fitur-fitur tersebut.

---

## Step 4: Core Feature Development (TDD Approach)

Selalu bangun fungsionalitas inti menggunakan pendekatan Test-Driven Development (TDD).

### Alur Kerja TDD Khas untuk Fitur CRUD

1.  **Buat Test**: Buat Feature Test yang menegaskan fungsionalitas yang diinginkan (misalnya, pengguna dapat melihat daftar klien).
    ```bash
    php artisan make:test ClientManagementTest
    ```
2.  **Tulis Failing Test**: Tulis sebuah tes yang awalnya gagal karena fungsionalitasnya belum ada.
3.  **Buat Route**: Definisikan route yang diperlukan di `routes/web.php` atau `routes/api.php`.
    ```php
    Route::get('/clients', [ClientController::class, 'index']);
    ```
4.  **Buat Controller**: Buat controller untuk menangani logika request.
    ```bash
    php artisan make:controller ClientController --resource --model=Client
    ```
5.  **Buat Model & Migrasi**: Jika belum ada, buat model Eloquent beserta migrasi, factory, dan seeder-nya.
    ```bash
    php artisan make:model Client -mfs
    ```
6.  **Tulis Logika & View**: Implementasikan logika di dalam controller dan buat file Blade view yang sesuai.
7.  **Jalankan Test**: Jalankan kembali tes hingga berhasil. Ulangi untuk setiap fitur (Create, Update, Delete).

---

## Step 5: Frontend Asset Compilation

Laravel menggunakan **Vite** secara default untuk kompilasi aset frontend.

### Instalasi Dependensi Frontend

```bash
npm install
```

### Menjalankan Development Server

Gunakan perintah ini selama pengembangan untuk kompilasi aset secara otomatis saat ada perubahan.

```bash
npm run dev
```

### Frontend Stack

- **Blade + Alpine.js**: Pilihan default yang kuat dan ringan dari Breeze.
- **Livewire**: Untuk membangun antarmuka dinamis menggunakan PHP.
- **Inertia.js (dengan Vue/React)**: Untuk membangun aplikasi single-page (SPA) modern tanpa harus membangun API terpisah.

---

## Step 6: Service Integration & Refactoring

Saat aplikasi tumbuh, pindahkan logika yang kompleks dari controller.

### Service Class Pattern

- **Buat Service Class**: Buat class di dalam direktori `app/Services` untuk menampung logika bisnis yang kompleks atau interaksi dengan API eksternal.
- **Gunakan Dependency Injection**: Inject service ini ke dalam controller Anda melalui constructor. Laravel akan secara otomatis me-resolve-nya dari service container.
- **Contoh**: `StripePaymentService`, `GoogleMapsService`.

### Menggunakan HTTP Client

Gunakan `Illuminate\Support\Facades\Http` (wrapper Guzzle) untuk semua komunikasi API eksternal. Ini menyediakan API yang ekspresif dan fitur pengujian yang mudah.

---

## Step 7: Final Testing, Optimization, and Deployment

### Optimisasi Aplikasi

Sebelum deploy ke produksi, jalankan perintah optimisasi untuk meningkatkan performa.

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Deployment

- **Laravel Forge**: Pilihan yang sangat baik untuk mengelola server dan melakukan deployment tanpa repot.
- **Laravel Vapor**: Platform deployment serverless untuk skalabilitas maksimum di AWS.
- **Manual Deployment**: Gunakan proses CI/CD (misalnya, GitHub Actions) untuk mengotomatiskan proses build dan deployment ke server Anda.

### Common Issues and Troubleshooting

- **Permissions**: Pastikan direktori `storage` dan `bootstrap/cache` dapat ditulis oleh web server.
- **.env File**: Pastikan file `.env` di server produksi dikonfigurasi dengan benar dan `APP_KEY` telah diatur.
- **Queue Worker**: Jangan lupa menjalankan queue worker (`php artisan queue:work`) di produksi jika aplikasi Anda menggunakan antrean.
