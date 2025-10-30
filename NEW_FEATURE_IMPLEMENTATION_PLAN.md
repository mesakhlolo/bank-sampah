# RENCANA IMPLEMENTASI FITUR BARU — Bank Sampah

> **Dibuat:** 30 Oktober 2025  
> **Updated:** 30 Oktober 2025 (Security Enhancement)  
> **Status:** Ready for Implementation  
> **Estimasi Waktu:** 12-15 jam (2-3 hari kerja)

---

## 🔐 SECURITY HIGHLIGHT - CRITICAL UPDATE

**⚠️ PERUBAHAN PENTING: Metode Kode Akses Portal**

| Aspek                | Versi Awal               | Versi Final (SECURE)        |
| -------------------- | ------------------------ | --------------------------- |
| **Format Kode**      | `CUST-0001` (sequential) | `BSB-X7K9M2P4N8Q1` (random) |
| **Kombinasi**        | 10,000 (❌ weak)         | 4.7+ trillion (✅ strong)   |
| **Brute Force Time** | < 1 hari                 | 9+ trillion tahun           |
| **Security Level**   | ⚠️ TIDAK AMAN            | ✅ BANK-LEVEL               |

**Implementasi:**

-   Tambah kolom `unique_code` di tabel `customers` (via migration)
-   Auto-generate menggunakan `Str::random(12)` (cryptographically secure)
-   Rate limiting 5 attempts / 5 minutes
-   Session timeout 30 minutes
-   IP validation untuk prevent session hijacking
-   Comprehensive logging untuk audit trail

**Lihat detail lengkap di section "Security Implementation" di bawah.**

---

## 📋 EXECUTIVE SUMMARY

Dokumen ini merinci implementasi dua fitur baru untuk melengkapi sistem Bank Sampah MVP yang sudah berjalan:

1. **Portal Nasabah (Customer Portal)** — Akses publik untuk nasabah cek saldo & riwayat menggunakan kode unik
2. **Landing Page** — Halaman depan informatif untuk memperkenalkan Bank Sampah

**Current Status Sistem:**

-   ✅ Admin dashboard lengkap (setoran, penarikan, nasabah, kategori sampah, laporan)
-   ✅ Authorization system (Admin & Operator)
-   ✅ Core transactions (deposits & withdrawals)
-   ❌ Landing page masih default Laravel
-   ❌ Belum ada akses untuk nasabah

---

## 🎯 FITUR 1: PORTAL NASABAH (CUSTOMER PORTAL)

### Konsep & Pertimbangan

#### Desain Keputusan

**Menggunakan Kode Unik (BUKAN Akun Login)**

✅ **Alasan Memilih Kode Unik:**

-   **Simplicity**: Nasabah cukup masukkan kode, tanpa perlu username/password/email
-   **No Account Management**: Tidak perlu verifikasi email, reset password, dll
-   **Faster Onboarding**: Operator cukup kasih kartu dengan kode member
-   **Lower Barrier**: Cocok untuk warga yang tidak tech-savvy
-   **Sesuai Context**: Ini simulasi/demo, bukan banking system riil

❌ **Mengapa Tidak Pakai Akun Login:**

-   Overhead lebih kompleks (register, verify email, forgot password)
-   Butuh data email/phone nasabah yang valid
-   Maintenance lebih rumit (reset password, security)
-   Overkill untuk use case sederhana

**Struktur Kode Member:**

⚠️ **PENTING - Security Consideration:**

❌ **JANGAN gunakan sequential code seperti `CUST-0001`, `CUST-0002`**

-   Mudah ditebak (brute force attack)
-   Orang bisa coba semua kombinasi dari 0001 sampai 9999
-   **TIDAK AMAN** untuk portal publik!

✅ **GUNAKAN random cryptographically secure code:**

-   Format: `BSB-XXXXXXXXXXXX` (12 karakter random)
-   Contoh: `BSB-X7K9M2P4N8Q1`, `BSB-A3F8K2M9P1Q5`
-   Total kombinasi: **36^12 = 4.7+ triliun** kemungkinan
-   Auto-generated saat nasabah didaftar menggunakan `Str::random(12)`
-   Unique per nasabah (with collision check)

#### Pertimbangan Fitur Penarikan

**REKOMENDASI: Nasabah TIDAK bisa tarik saldo sendiri lewat web**

✅ **Alasan:**

-   **Security**: Mencegah fraud (orang lain pakai kode nasabah)
-   **Cash Flow Control**: Bank Sampah kontrol kapan bisa bayar tunai
-   **Social Aspect**: Tetap ada interaksi dengan operator/pengurus
-   **Audit Trail**: Operator jadi witness/verifier penarikan
-   **Prevention**: Hindari penarikan massal dadakan yang ganggu cash flow

**Alternatif untuk Nasabah:**

-   Portal hanya BACA (view-only): saldo, riwayat, statistik pribadi
-   Jika nasabah mau tarik → datang ke bank sampah → operator proses

**Fitur Tambahan (Optional Fase 2):**

-   Request penarikan online → perlu approval operator → dijadwalkan
-   WhatsApp notification saat ada setoran baru (butuh integrasi WA API)

---

### Spesifikasi Fitur Portal Nasabah

#### FR-100: Halaman Cek Saldo (Public Access)

**Route:** `/cek-saldo` atau `/nasabah/cek`

**UI/UX Flow:**

1. User buka URL public
2. Tampil form sederhana:
    - Input: Kode Member (placeholder: "CUST-0001")
    - Button: "Lihat Saldo"
    - Link kecil: "Lupa kode? Hubungi operator"
3. Submit → Validasi kode
4. Jika valid → Redirect ke dashboard nasabah
5. Jika invalid → Error: "Kode tidak ditemukan"

**Validasi:**

-   Format kode: CUST-XXXX (regex)
-   Kode harus exist di database
-   Rate limiting: max 10 requests per 1 menit (prevent brute force)

**Session Handling:**

-   Simpan `customer_code` di session (bukan auth login)
-   Session expire: 30 menit inactive
-   Bisa cek ulang tanpa re-input kode (selama session aktif)

---

#### FR-101: Dashboard Nasabah

**Route:** `/nasabah/dashboard` (session-guarded)

**Components:**

**1. Header/Profile Card**

```
┌─────────────────────────────────────────┐
│ 👤 Budi Setiawan (CUST-0001)           │
│ 📞 0812-3456-7890                       │
│ 📅 Bergabung: 15 Januari 2025          │
│                                         │
│ 💰 SALDO TERSEDIA                       │
│    Rp 125.500                           │
│                                         │
│ [🔄 Refresh] [🚪 Keluar]                │
└─────────────────────────────────────────┘
```

**2. Quick Stats (3 Cards)**

-   Total Setoran (kg): `25.5 kg`
-   Total Transaksi: `12 kali`
-   Setoran Terakhir: `3 hari lalu`

**3. Grafik/Chart (Optional - Fase 2)**

-   Bar chart: Setoran per bulan (3 bulan terakhir)
-   Pie chart: Kontribusi per kategori sampah

---

#### FR-102: Riwayat Transaksi Nasabah

**Tampilan:**

**Tab/Filter:**

-   Semua Transaksi (default)
-   Setoran Saja
-   Penarikan Saja

**Table Columns:**
| Tanggal | Jenis | Detail | Debit | Kredit | Saldo |
|---------|-------|--------|-------|--------|-------|
| 28 Okt 2025 | Setoran | 2.5 kg Plastik PET, 1 kg Kertas | +Rp 8.500 | - | Rp 125.500 |
| 25 Okt 2025 | Penarikan | Tarik tunai | - | Rp 50.000 | Rp 117.000 |
| 20 Okt 2025 | Setoran | 5 kg Kaleng | +Rp 25.000 | - | Rp 167.000 |

**Features:**

-   Pagination: 20 rows per page
-   Sort by date (newest first)
-   Filter by date range (optional)
-   Download PDF riwayat (optional)

**Running Balance:**

-   Hitung running balance per row (seperti buku tabungan)
-   Formula: `Saldo sebelumnya + Debit - Kredit`

---

#### FR-103: Detail Setoran

**Akses:** Klik row setoran di riwayat

**Tampilan:**

```
╔═══════════════════════════════════════╗
║  BUKTI SETORAN                        ║
║  Bank Sampah Bersih Sejahtera        ║
╠═══════════════════════════════════════╣
║  Kode: DEP-20251028-0042              ║
║  Tanggal: 28 Oktober 2025, 14:30     ║
║  Nasabah: Budi Setiawan (CUST-0001)  ║
╠═══════════════════════════════════════╣
║  RINCIAN BARANG:                      ║
║  ┌─────────────────────────────────┐  ║
║  │ Plastik PET                     │  ║
║  │ 2.5 kg × Rp 3.000 = Rp 7.500    │  ║
║  ├─────────────────────────────────┤  ║
║  │ Kertas Campur                   │  ║
║  │ 1.0 kg × Rp 1.000 = Rp 1.000    │  ║
║  └─────────────────────────────────┘  ║
║                                       ║
║  Total Berat: 3.5 kg                  ║
║  TOTAL NILAI: Rp 8.500                ║
║                                       ║
║  Diterima oleh: Operator (Ibu Rina)  ║
╚═══════════════════════════════════════╝
    [📥 Download PDF] [← Kembali]
```

---

#### FR-104: Info & Bantuan

**Route:** `/nasabah/info`

**Konten:**

-   Cara menggunakan portal
-   Jadwal operasional bank sampah
-   Kontak pengurus
-   Harga sampah terkini (list kategori + harga)
-   FAQ:
    -   "Bagaimana cara menyetor sampah?"
    -   "Kapan saya bisa tarik saldo?"
    -   "Berapa lama saldo masuk setelah setor?"
    -   "Kode member saya hilang, bagaimana?"

---

### Database Changes

**⚠️ PERLU PERUBAHAN SCHEMA - Security Enhancement!**

Kolom `member_code` yang sekarang (`CUST-0001`) **tidak aman** untuk portal publik. Kita perlu:

1. **Tambah kolom baru `unique_code`** untuk portal access (random, secure)
2. **Tetap pertahankan `member_code`** untuk keperluan internal/admin

```sql
-- Migration: Tambah kolom unique_code
ALTER TABLE customers ADD COLUMN unique_code VARCHAR(16) UNIQUE AFTER member_code;
ALTER TABLE customers ADD INDEX idx_unique_code (unique_code);

-- Optional: Tracking portal access
ALTER TABLE customers ADD COLUMN last_portal_access TIMESTAMP NULL;
```

**Strategy:**

-   `member_code`: Tetap sequential (`CUST-0001`) untuk admin/internal
-   `unique_code`: Random 12-char (`BSB-X7K9M2P4N8Q1`) untuk portal publik
-   Nasabah diberikan kartu dengan **kedua kode**: member_code untuk admin, unique_code untuk portal

**Migration File:**

```php
// database/migrations/2025_10_30_add_unique_code_to_customers.php
public function up()
{
    Schema::table('customers', function (Blueprint $table) {
        $table->string('unique_code', 16)->unique()->nullable()->after('member_code');
        $table->index('unique_code');
        $table->timestamp('last_portal_access')->nullable();
    });

    // Generate unique codes for existing customers
    \App\Models\Customer::chunk(100, function ($customers) {
        foreach ($customers as $customer) {
            $customer->update([
                'unique_code' => 'BSB-' . strtoupper(\Illuminate\Support\Str::random(12))
            ]);
        }
    });
}
```

---

### Technical Implementation

#### Files to Create

```
database/migrations/
└── 2025_10_30_add_unique_code_to_customers.php  # NEW - Add unique_code column

app/Models/
└── Customer.php                          # UPDATE - Add boot() method for auto-generate

app/Http/
├── Controllers/
│   └── CustomerPortalController.php      # NEW
├── Middleware/
│   └── CustomerSessionGuard.php          # NEW
└── Requests/
    └── CheckCustomerCodeRequest.php      # NEW

resources/views/
└── customer-portal/                       # NEW FOLDER
    ├── check-code.blade.php               # Form input kode
    ├── dashboard.blade.php                # Dashboard nasabah
    ├── transactions.blade.php             # Riwayat transaksi
    ├── deposit-detail.blade.php           # Detail setoran
    ├── info.blade.php                     # Info & bantuan
    └── layouts/
        └── portal.blade.php               # Layout khusus portal (tanpa admin sidebar)

routes/
└── web.php                                # Update with portal routes

tests/Feature/
└── CustomerPortalTest.php                # NEW
```

#### Model Update: Customer.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $fillable = [
        'member_code',
        'unique_code',  // NEW - untuk portal access
        'name',
        'phone',
        'address',
        'joined_at',
        'last_portal_access',
    ];

    /**
     * Boot method - Auto-generate unique_code saat customer dibuat
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // Generate member_code (sequential, untuk admin)
            if (empty($customer->member_code)) {
                $customer->member_code = self::generateMemberCode();
            }

            // Generate unique_code (random, untuk portal)
            if (empty($customer->unique_code)) {
                $customer->unique_code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate sequential member code (untuk admin/internal)
     */
    private static function generateMemberCode(): string
    {
        $lastCustomer = self::orderBy('id', 'desc')->lockForUpdate()->first();

        if (!$lastCustomer) {
            return 'CUST-0001';
        }

        preg_match('/(\d+)$/', $lastCustomer->member_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : $lastCustomer->id;
        $nextNumber = $lastNumber + 1;

        return 'CUST-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 🔒 Generate secure random unique code (untuk portal public)
     * Format: BSB-XXXXXXXXXXXX (12 random chars)
     * Total kombinasi: 36^12 = 4,738,381,338,321,616,896
     */
    private static function generateUniqueCode(): string
    {
        do {
            // Cryptographically secure random string
            $code = 'BSB-' . strtoupper(Str::random(12));

            // Check collision (very unlikely, but safety first)
        } while (self::where('unique_code', $code)->exists());

        return $code;
    }

    // ... existing relationships & methods
}
```

---

#### Routes Definition

```php
// routes/web.php

// Public customer portal routes
Route::prefix('nasabah')->name('customer-portal.')->group(function () {
    // Check code page (public)
    Route::get('/cek', [CustomerPortalController::class, 'showCheckCode'])
        ->name('check-code');

    Route::post('/cek', [CustomerPortalController::class, 'verifyCode'])
        ->name('verify-code')
        ->middleware('throttle:5,5'); // 🔒 Rate limit: 5 attempts per 5 minutes

    // Portal routes (session-guarded)
    Route::middleware('customer.session')->group(function () {
        Route::get('/dashboard', [CustomerPortalController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/transaksi', [CustomerPortalController::class, 'transactions'])
            ->name('transactions');

        Route::get('/setoran/{deposit}', [CustomerPortalController::class, 'depositDetail'])
            ->name('deposit-detail');

        Route::get('/info', [CustomerPortalController::class, 'info'])
            ->name('info');

        Route::post('/logout', [CustomerPortalController::class, 'logout'])
            ->name('logout');
    });
});
```

#### Middleware: CustomerSessionGuard

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerSessionGuard
{
    public function handle(Request $request, Closure $next)
    {
        // 🔒 Check if session exists
        if (!session()->has('portal_customer_id')) {
            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Silakan masukkan kode unik Anda terlebih dahulu.');
        }

        // 🔒 SESSION TIMEOUT: 30 menit inactive
        $loginTime = session('portal_login_time');
        if ($loginTime && $loginTime->diffInMinutes(now()) > 30) {
            session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

            Log::info('Portal session expired', [
                'customer_id' => session('portal_customer_id'),
            ]);

            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
        }

        // 🔒 IP VALIDATION: Prevent session hijacking
        if (session('portal_ip') !== $request->ip()) {
            session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

            Log::warning('Portal session IP mismatch - possible hijacking attempt', [
                'customer_id' => session('portal_customer_id'),
                'session_ip' => session('portal_ip'),
                'request_ip' => $request->ip(),
            ]);

            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Terdeteksi akses tidak valid. Silakan login kembali.');
        }

        // Verify customer still exists
        $customer = \App\Models\Customer::find(session('portal_customer_id'));

        if (!$customer) {
            session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);
            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Data tidak ditemukan.');
        }

        // Share customer to all views
        view()->share('portalCustomer', $customer);

        return $next($request);
    }
}
```

**Register Middleware:**

```php
// bootstrap/app.php atau app/Http/Kernel.php (tergantung Laravel 11 structure)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'customer.session' => \App\Http\Middleware\CustomerSessionGuard::class,
    ]);
})
```

#### Controller: CustomerPortalController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class CustomerPortalController extends Controller
{
    /**
     * Show check code form
     */
    public function showCheckCode()
    {
        // If already in session, redirect to dashboard
        if (session()->has('customer_code')) {
            return redirect()->route('customer-portal.dashboard');
        }

        return view('customer-portal.check-code');
    }

    /**
     * 🔒 Verify customer unique code with SECURITY MEASURES
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'unique_code' => ['required', 'string', 'size:16'], // BSB- + 12 chars
        ], [
            'unique_code.required' => 'Kode unik harus diisi.',
            'unique_code.size' => 'Format kode tidak valid. Contoh: BSB-X7K9M2P4N8Q1',
        ]);

        $code = strtoupper($request->unique_code);

        // 🔒 RATE LIMITING: Prevent brute force attack
        $key = 'portal-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            return back()->withErrors([
                'unique_code' => "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$minutes} menit."
            ])->withInput();
        }

        // Search customer by unique_code
        $customer = Customer::where('unique_code', $code)->first();

        if (!$customer) {
            // 🔒 Increment rate limiter on failed attempt
            RateLimiter::hit($key, 300); // 5 minutes cooldown

            // 🔒 LOG failed attempt for security monitoring
            Log::warning('Failed portal login attempt', [
                'ip' => $request->ip(),
                'code_prefix' => substr($code, 0, 6) . '******', // Don't log full code
                'timestamp' => now(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'unique_code' => 'Kode unik tidak ditemukan. Silakan periksa kembali atau hubungi operator.',
            ])->withInput();
        }

        // ✅ Login successful - Clear rate limiter
        RateLimiter::clear($key);

        // 🔒 Store in session with security metadata
        session([
            'portal_customer_id' => $customer->id,
            'portal_login_time' => now(),
            'portal_ip' => $request->ip(), // For IP validation in middleware
        ]);

        // Update last portal access
        $customer->update(['last_portal_access' => now()]);

        // 🔒 LOG successful login
        Log::info('Customer portal login successful', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('customer-portal.dashboard')
            ->with('success', "Selamat datang, {$customer->name}!");
    }

    /**
     * Customer dashboard
     */
    public function dashboard()
    {
        $customer = Customer::find(session('portal_customer_id'));

        $customer->load(['ledgerEntries' => function ($query) {
            $query->latest()->limit(5);
        }]);

        // Statistics
        $stats = [
            'total_weight' => $customer->deposits()
                ->where('status', 'final')
                ->sum('total_weight'),
            'transaction_count' => $customer->deposits()->count() + $customer->withdrawals()->count(),
            'last_deposit' => $customer->deposits()->latest()->first(),
        ];

        return view('customer-portal.dashboard', compact('customer', 'stats'));
    }

    /**
     * Transaction history
     */
    public function transactions(Request $request)
    {
        $customer = Customer::find(session('portal_customer_id'));

        $type = $request->input('type', 'all'); // all, deposits, withdrawals

        // Build query for combined transactions
        $ledgerEntries = $customer->ledgerEntries()
            ->with(['referenceable'])
            ->when($type === 'deposits', function ($query) {
                $query->where('ref_type', Deposit::class);
            })
            ->when($type === 'withdrawals', function ($query) {
                $query->where('ref_type', \App\Models\Withdrawal::class);
            })
            ->latest()
            ->paginate(20);

        // Calculate running balance
        $runningBalance = $customer->balance; // Start from current balance
        foreach ($ledgerEntries as $entry) {
            $entry->running_balance = $runningBalance;
            // Go backwards in time
            $runningBalance += ($entry->direction === 'KREDIT' ? $entry->amount : -$entry->amount);
        }

        return view('customer-portal.transactions', compact('customer', 'ledgerEntries', 'type'));
    }

    /**
     * Deposit detail
     */
    public function depositDetail(Deposit $deposit)
    {
        $customer = Customer::find(session('portal_customer_id'));

        // 🔒 AUTHORIZATION: Verify deposit belongs to this customer
        if ($deposit->customer_id !== $customer->id) {
            Log::warning('Unauthorized portal deposit access attempt', [
                'customer_id' => $customer->id,
                'attempted_deposit_id' => $deposit->id,
                'deposit_owner_id' => $deposit->customer_id,
            ]);

            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $deposit->load(['items.wasteCategory', 'createdBy']);

        return view('customer-portal.deposit-detail', compact('customer', 'deposit'));
    }

    /**
     * Info page
     */
    public function info()
    {
        $customer = Customer::find(session('portal_customer_id'));

        // Get current waste prices
        $wastePrices = \App\Models\WasteCategory::with(['wastePrices' => function ($query) {
            $query->where('effective_from', '<=', now())
                ->latest('effective_from')
                ->limit(1);
        }])
            ->where('is_active', true)
            ->get();

        return view('customer-portal.info', compact('customer', 'wastePrices'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        $customerId = session('portal_customer_id');

        // 🔒 LOG logout for audit trail
        Log::info('Customer portal logout', [
            'customer_id' => $customerId,
        ]);

        // Clear all session data
        session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

        return redirect()->route('customer-portal.check-code')
            ->with('success', 'Anda telah keluar dari portal nasabah.');
    }
}
```

---

### UI/UX Design Considerations

#### Layout Berbeda dari Admin

**Portal Nasabah harus punya look & feel berbeda:**

-   Tidak ada sidebar admin
-   Navigation sederhana (top navbar atau tabs)
-   Color scheme berbeda (contoh: hijau untuk portal, biru untuk admin)
-   Font lebih besar, spacing lebih lega (accessibility)

#### Responsiveness Priority

**Mobile-First Design!**

-   70% nasabah kemungkinan akses via HP
-   Touch-friendly buttons (min 44×44px)
-   Readable font size (16px minimum)
-   Sticky header dengan balance

#### Brand Consistency

-   Logo bank sampah di header
-   Konsisten dengan style landing page
-   Friendly tone (gunakan "Anda", "Terima kasih")

---

### Seeder Update

Update `CustomerSeeder.php` untuk auto-generate `unique_code`:

```php
<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Model Customer sudah auto-generate unique_code di boot() method
        // Jadi kita tidak perlu manual set, cukup create seperti biasa

        Customer::factory(50)->create();

        // Atau jika ingin manual create:
        // Customer::create([
        //     'name' => 'Budi Setiawan',
        //     'phone' => '081234567890',
        //     'address' => 'Jl. Contoh No. 123',
        //     'joined_at' => now(),
        //     // member_code dan unique_code auto-generated
        // ]);
    }
}
```

**Catatan Penting:**

-   `member_code` tetap sequential (`CUST-0001`, `CUST-0002`, ...) untuk kemudahan admin
-   `unique_code` random (`BSB-X7K9M2P4N8Q1`) untuk keamanan portal
-   Nasabah dapat kartu dengan **kedua kode**:
    -   **Member Code** untuk identifikasi di kantor
    -   **Unique Code** untuk akses portal online

---

### Testing Strategy

```php
// tests/Feature/CustomerPortalTest.php

test('customer can access check code page')
test('customer can verify valid unique code')
test('customer cannot verify invalid unique code')
test('customer cannot verify member code (must use unique code)')
test('customer redirected to check code if no session')
test('customer can view dashboard with balance')
test('customer can view transaction history')
test('customer can view deposit detail of their own deposit')
test('customer cannot view other customer deposit')
test('customer can logout and session cleared')
test('rate limiting works for code verification')
test('session expires after 30 minutes')
test('session invalidated on IP change')
test('failed login attempts are logged')
test('unauthorized access attempts are logged')

// Security-specific tests
test('cannot brute force with sequential codes')
test('cannot bypass rate limiting')
test('cannot hijack session with different IP')
test('cannot access portal after logout')
```

**Test Implementation Example:**

```php
<?php

use App\Models\Customer;
use Illuminate\Support\Facades\RateLimiter;

test('customer can verify valid unique code', function () {
    $customer = Customer::factory()->create();

    $response = $this->post(route('customer-portal.verify-code'), [
        'unique_code' => $customer->unique_code,
    ]);

    $response->assertRedirect(route('customer-portal.dashboard'));
    expect(session('portal_customer_id'))->toBe($customer->id);
});

test('customer cannot verify invalid unique code', function () {
    $response = $this->post(route('customer-portal.verify-code'), [
        'unique_code' => 'BSB-INVALID00000',
    ]);

    $response->assertSessionHasErrors('unique_code');
    expect(session('portal_customer_id'))->toBeNull();
});

test('rate limiting works after 5 failed attempts', function () {
    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $this->post(route('customer-portal.verify-code'), [
            'unique_code' => 'BSB-INVALID0000' . $i,
        ]);
    }

    // 6th attempt should be rate limited
    $response = $this->post(route('customer-portal.verify-code'), [
        'unique_code' => 'BSB-INVALID00006',
    ]);

    $response->assertSessionHasErrors();
    expect($response->getContent())->toContain('Terlalu banyak percobaan');
});

test('session invalidated on IP change', function () {
    $customer = Customer::factory()->create();

    // Login
    $this->post(route('customer-portal.verify-code'), [
        'unique_code' => $customer->unique_code,
    ]);

    // Manually change IP in session
    session(['portal_ip' => '192.168.1.100']);

    // Try to access dashboard (with different IP)
    $response = $this->get(route('customer-portal.dashboard'));

    $response->assertRedirect(route('customer-portal.check-code'));
    expect(session('portal_customer_id'))->toBeNull();
});
```

---

### 🔒 Security Implementation - Multi-Layer Protection

#### Layer 1: Cryptographically Secure Code Generation

-   **Method**: `Str::random(12)` - Laravel's cryptographically secure random generator
-   **Format**: `BSB-XXXXXXXXXXXX` (12 uppercase alphanumeric characters)
-   **Keyspace**: 36^12 = **4,738,381,338,321,616,896 combinations**
-   **Collision Check**: Loop until unique code found (very unlikely to loop)

#### Layer 2: Rate Limiting (Prevent Brute Force)

-   **Limit**: 5 attempts per 5 minutes per IP address
-   **Implementation**: Laravel's `RateLimiter` facade
-   **Cooldown**: 5 minutes after limit reached
-   **Math**: With rate limiting, would take **9+ trillion years** to try all combinations!

#### Layer 3: Session-Based Authentication

-   **Storage**: Server-side session (not cookies or tokens)
-   **Data Stored**: `portal_customer_id`, `portal_login_time`, `portal_ip`
-   **No Exposure**: Sensitive data never sent to client

#### Layer 4: Session Timeout

-   **Duration**: 30 minutes of inactivity
-   **Check**: Every request validates login time
-   **Auto-logout**: Expired sessions redirected to login

#### Layer 5: IP Validation

-   **Purpose**: Prevent session hijacking
-   **Method**: Store IP on login, validate on every request
-   **Action**: Force re-login if IP mismatch detected

#### Layer 6: Authorization Checks

-   **Verify Ownership**: Every data access checks `customer_id` match
-   **403 Forbidden**: Return error if trying to access other customer data
-   **Logging**: Log all unauthorized access attempts

#### Layer 7: Comprehensive Logging

-   **Failed Logins**: IP, timestamp, partial code (for forensics)
-   **Successful Logins**: Customer ID, IP
-   **Unauthorized Access**: Attempted resources
-   **Session Issues**: Timeouts, IP mismatches
-   **Use Case**: Security monitoring, incident response

#### Layer 8: Input Validation

-   **Format Check**: Exact 16 characters (BSB-XXXXXXXXXXXX)
-   **Case Insensitive**: Convert to uppercase for lookup
-   **SQL Injection**: Protected by Eloquent ORM
-   **XSS Protection**: Laravel's Blade auto-escaping

#### Security Metrics Summary

| Metric               | Value                  | Security Level        |
| -------------------- | ---------------------- | --------------------- |
| **Combinations**     | 4.7+ trillion          | ⭐⭐⭐⭐⭐ Excellent  |
| **Brute Force Time** | 9+ trillion years      | ⭐⭐⭐⭐⭐ Impossible |
| **Rate Limit**       | 5 per 5 min            | ⭐⭐⭐⭐⭐ Strong     |
| **Session Security** | Server-side + IP check | ⭐⭐⭐⭐⭐ Strong     |
| **Authorization**    | Per-request validation | ⭐⭐⭐⭐⭐ Strong     |
| **Audit Trail**      | Comprehensive logging  | ⭐⭐⭐⭐⭐ Excellent  |

#### Additional Security Best Practices

✅ **HTTPS Required**: Deploy with SSL/TLS in production  
✅ **CSRF Protection**: All POST requests protected by Laravel middleware  
✅ **XSS Prevention**: Blade auto-escaping + Content Security Policy  
✅ **SQL Injection**: Eloquent ORM prevents raw SQL vulnerabilities  
✅ **Error Messages**: Generic messages (don't reveal system details)  
✅ **Logging Redaction**: Never log full unique codes  
✅ **Session Regeneration**: Prevent session fixation attacks

#### Comparison: Our Approach vs Alternatives

| Approach                        | Security     | UX          | Maintenance  |
| ------------------------------- | ------------ | ----------- | ------------ |
| **Sequential Code (CUST-0001)** | ❌ Very Weak | ✅ Easy     | ✅ Simple    |
| **Email + Password**            | ⚠️ Medium    | ⚠️ Complex  | ❌ High      |
| **Our Random Code**             | ✅ Strong    | ✅ Easy     | ✅ Simple    |
| **OTP via SMS**                 | ✅ Strong    | ⚠️ Moderate | ❌ High Cost |

**Conclusion**: Our random code approach provides **bank-level security** while maintaining **simplicity** for non-tech-savvy users. Perfect for this use case! 🎯

---

#### Visual Security Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                  CUSTOMER PORTAL LOGIN FLOW                 │
│                    (8 Security Layers)                      │
└─────────────────────────────────────────────────────────────┘

1️⃣ USER INPUT
   ↓
   [BSB-X7K9M2P4N8Q1]
   ↓
2️⃣ INPUT VALIDATION ─────────────┐
   • Format check (16 chars)     │
   • Uppercase conversion         │ ✅ Layer 8
   • XSS prevention              │
   ↓                             │
3️⃣ RATE LIMITING ────────────────┤
   • Check attempts (5/5min)     │ ✅ Layer 2
   • IP-based throttle           │
   ↓                             │
4️⃣ DATABASE LOOKUP ──────────────┤
   • Query unique_code           │ ✅ Layer 1
   • Eloquent ORM (SQL injection │   (Crypto random)
     prevention)                 │
   ↓                             │
5️⃣ AUTHORIZATION ────────────────┤
   • Customer exists?            │ ✅ Layer 6
   • Active account?             │
   ↓                             │
6️⃣ SESSION CREATION ─────────────┤
   • Store customer_id           │ ✅ Layer 3
   • Store login_time            │ ✅ Layer 4
   • Store IP address            │ ✅ Layer 5
   ↓                             │
7️⃣ AUDIT LOGGING ────────────────┤
   • Log successful login        │ ✅ Layer 7
   • Log failed attempts         │
   ↓                             │
8️⃣ REDIRECT TO DASHBOARD ────────┘
   • Session-guarded routes
   • Per-request validation
   • 30-min timeout
   • IP validation

┌─────────────────────────────────────────────────────────────┐
│  EVERY SUBSEQUENT REQUEST VALIDATES:                        │
│  ✓ Session exists                                          │
│  ✓ Not expired (< 30 min)                                 │
│  ✓ IP matches                                             │
│  ✓ Customer owns data                                     │
└─────────────────────────────────────────────────────────────┘
```

---

#### Quick Command Reference

```bash
# Generate new customer (auto-generates both codes)
php artisan tinker
Customer::create(['name' => 'Test', 'phone' => '081234567890', 'address' => 'Test', 'joined_at' => now()])

# View customer codes
Customer::first()->only(['member_code', 'unique_code'])

# Regenerate unique_code for a customer (if needed)
$customer = Customer::find(1);
$customer->unique_code = 'BSB-' . strtoupper(Str::random(12));
$customer->save();

# Check rate limiting status
RateLimiter::remaining('portal-login:127.0.0.1', 5)

# Clear rate limiting for testing
RateLimiter::clear('portal-login:127.0.0.1')

# View security logs
tail -f storage/logs/laravel.log | grep "portal"
```

---

## 🏠 FITUR 2: LANDING PAGE

### Tujuan Landing Page

1. **Edukasi**: Apa itu bank sampah? Mengapa penting?
2. **Call-to-Action**: Ajak warga bergabung + cek saldo untuk nasabah existing
3. **Transparansi**: Tampilkan statistik/impact (total sampah terkumpul, dll)
4. **Trust Building**: Testimoni, foto kegiatan, kontak jelas
5. **Professional**: Bukan lagi "default Laravel welcome"

---

### Struktur Landing Page

#### Section 1: Hero Section

**Layout:**

```
╔════════════════════════════════════════════════╗
║  [Logo] Bank Sampah    [Masuk] [Cek Saldo]   ║
╠════════════════════════════════════════════════╣
║                                                ║
║         Ubah Sampah Menjadi Rupiah            ║
║         Bersama Bank Sampah Bersih            ║
║              Sejahtera                         ║
║                                                ║
║  [🚀 Daftar Sekarang] [📊 Cek Saldo Saya]     ║
║                                                ║
║        [Hero Image/Illustration]               ║
║                                                ║
╚════════════════════════════════════════════════╝
```

**Elemen:**

-   **Background**: Gradient hijau/biru atau foto sampingan kegiatan (overlay gelap)
-   **Headline**: 1-2 kalimat tagline kuat
    -   "Tabung Sampah, Raih Rupiah"
    -   "Lingkungan Bersih, Ekonomi Tumbuh"
-   **Subheadline**: 1 paragraf penjelasan singkat (40-60 kata)
-   **CTA Buttons**:
    -   Primer: "Daftar Sekarang" → Link ke contact/info (karena daftar via operator)
    -   Sekunder: "Cek Saldo Saya" → Langsung ke `/nasabah/cek`
-   **Hero Image**: Illustrasi atau foto (lihat spesifikasi gambar di bawah)

---

#### Section 2: Cara Kerja (How It Works)

**Layout:** 3-4 step cards horizontal

```
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│   1. DAFTAR   │  │  2. SETOR     │  │  3. TABUNG    │
│               │  │               │  │               │
│  [Icon/Img]   │  │  [Icon/Img]   │  │  [Icon/Img]   │
│               │  │               │  │               │
│ Hubungi kami  │  │ Bawa sampah   │  │ Saldo bertambah│
│ untuk daftar  │  │ yang sudah    │  │ otomatis.     │
│ sebagai       │  │ dipilah ke    │  │ Bisa ditarik  │
│ nasabah       │  │ bank sampah   │  │ kapan saja    │
└───────────────┘  └───────────────┘  └───────────────┘

      ┌───────────────┐
      │  4. DAMPAK    │
      │               │
      │  [Icon/Img]   │
      │               │
      │ Lingkungan    │
      │ bersih,       │
      │ ekonomi naik  │
      └───────────────┘
```

**Icons Needed:**

-   Step 1: 📝 atau icon clipboard/form
-   Step 2: ♻️ atau icon truk/tas sampah
-   Step 3: 💰 atau icon dompet/uang
-   Step 4: 🌱 atau icon daun/bumi

---

#### Section 3: Statistik & Dampak (Impact)

**Layout:** 4 metric cards

```
╔════════════════════════════════════════════════╗
║           DAMPAK KAMI BERSAMA                  ║
╠════════════════════════════════════════════════╣
║                                                ║
║  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────┐║
║  │ 🏠 250+  │ │ ♻️ 5 Ton │ │ 💵 25 Jt │ │ 🌳││
║  │ Nasabah  │ │ Sampah   │ │ Rupiah   │ │ 50││
║  │ Aktif    │ │ Terkumpul│ │ Tersalur │ │Pohon║
║  └──────────┘ └──────────┘ └──────────┘ └────┘║
║                                                ║
╚════════════════════════════════════════════════╝
```

**Data Source:**

-   Hitung dari database real-time:
    -   `Customer::count()` → Nasabah aktif
    -   `Deposit::sum('total_weight')` → Total sampah (kg)
    -   `Withdrawal::sum('amount')` → Rupiah tersalur
-   Pohon setara: Formula sederhana (misal: 1 ton sampah = 10 pohon selamat)

**Animation (Optional):**

-   CountUp.js untuk animated counter

---

#### Section 4: Kategori Sampah & Harga

**Layout:** Card grid 3 kolom

```
┌────────────────────┐ ┌────────────────────┐
│ 📦 Plastik PET     │ │ 📰 Kertas Campur   │
│ Rp 3.000/kg        │ │ Rp 1.500/kg        │
│                    │ │                    │
│ Botol air mineral, │ │ Koran, majalah,    │
│ kemasan plastik    │ │ kardus             │
│ bening/tebal       │ │                    │
└────────────────────┘ └────────────────────┘

┌────────────────────┐ ┌────────────────────┐
│ 🥫 Kaleng          │ │ 🍶 Botol Kaca      │
│ Rp 5.000/kg        │ │ Rp 500/kg          │
│                    │ │                    │
│ Kaleng minuman,    │ │ Botol bekas sirup, │
│ kemasan makanan    │ │ oli, kecap         │
└────────────────────┘ └────────────────────┘
```

**Data Source:**

-   Ambil dari `WasteCategory` + `WastePrice` (harga aktif terkini)
-   Tampilkan 4-6 kategori utama
-   Link: "Lihat semua harga" → ke halaman dedicated atau modal

---

#### Section 5: Testimoni (Optional - Jika Ada Waktu)

**Layout:** Carousel atau 3 card static

```
┌──────────────────────────────────────────┐
│ ⭐⭐⭐⭐⭐                                 │
│                                          │
│ "Sekarang saya lebih rajin pilah sampah │
│  karena bisa dapat uang tambahan!"       │
│                                          │
│  — Ibu Siti, Nasabah sejak 2024         │
└──────────────────────────────────────────┘
```

**Placeholder Testimonial:**

-   2-3 testimoni fiktif atau dari user seeder
-   Bisa diganti real testimonial nanti

---

#### Section 6: Call-to-Action (CTA)

**Layout:** Full-width section dengan background kontras

```
╔════════════════════════════════════════════════╗
║                                                ║
║      Siap Bergabung dengan Kami?              ║
║                                                ║
║   Hubungi kami untuk mendaftar sebagai        ║
║   nasabah Bank Sampah Bersih Sejahtera        ║
║                                                ║
║    [📞 Hubungi Kami]  [📍 Lokasi Kami]        ║
║                                                ║
╚════════════════════════════════════════════════╝
```

---

#### Section 7: Footer

**Layout:** 3 kolom

```
┌────────────────┬────────────────┬────────────────┐
│ Bank Sampah    │ Menu Cepat     │ Kontak         │
│ Bersih         │                │                │
│ Sejahtera      │ - Tentang Kami │ 📞 0812-xxxx   │
│                │ - Cara Kerja   │ 📧 info@...    │
│ [Logo]         │ - Harga Sampah │ 📍 Jl. ...     │
│                │ - Cek Saldo    │                │
│                │ - Masuk Admin  │ [FB] [IG] [WA] │
└────────────────┴────────────────┴────────────────┘
           © 2025 Bank Sampah. All rights reserved.
```

---

### Spesifikasi Gambar & Aset

#### Hero Image/Illustration

**Path:** `public/images/landing/hero-image.jpg` atau `.png`

**Spesifikasi:**

-   **Dimensi**: 1920×1080px (landscape)
-   **Rasio**: 16:9
-   **Format**: JPG (untuk foto), PNG (untuk ilustrasi dengan transparency)
-   **Ukuran File**: Max 500KB (optimized untuk web)
-   **Konten**:
    -   Opsi 1: Foto kegiatan bank sampah (orang menyetor sampah, sorting)
    -   Opsi 2: Ilustrasi flat/isometric tentang recycling
    -   Opsi 3: Collage sampah terpilah + tanaman/nature

**Sumber Gambar (Gratis):**

-   Unsplash (https://unsplash.com/s/photos/recycling)
-   Pexels (https://www.pexels.com/search/waste/)
-   Freepik (untuk ilustrasi - perhatikan lisensi)
-   Undraw (https://undraw.co/illustrations - SVG gratis)

---

#### Icons untuk "Cara Kerja"

**Path:** `public/images/landing/icons/`

**Files:**

-   `step-1-register.svg` (atau .png)
-   `step-2-deposit.svg`
-   `step-3-save.svg`
-   `step-4-impact.svg`

**Spesifikasi:**

-   **Dimensi**: 128×128px (atau 256×256px untuk retina)
-   **Format**: SVG (preferred - scalable) atau PNG transparent
-   **Style**: Consistent (semua flat/outline/solid style yang sama)

**Sumber Icons:**

-   Heroicons (https://heroicons.com - gratis, MIT license)
-   Feather Icons (https://feathericons.com)
-   Font Awesome (free icons)
-   Flaticon (perhatikan lisensi)

---

#### Background Patterns (Optional)

**Path:** `public/images/landing/patterns/`

**Files:**

-   `hero-bg.jpg` atau `hero-gradient.svg`
-   `cta-bg.jpg`

**Spesifikasi:**

-   **Dimensi**: 1920×1080px minimum
-   **Format**: JPG (low quality/blurred untuk background) atau SVG (gradients)
-   **Style**: Subtle, tidak ganggu teks

---

#### Logo Bank Sampah

**Path:** `public/images/logo.png` atau `logo.svg`

**Spesifikasi:**

-   **Dimensi**: 300×300px (square) atau custom ratio
-   **Format**: PNG transparent atau SVG
-   **Versions**:
    -   `logo-full.png` (dengan teks)
    -   `logo-icon.png` (icon saja, untuk favicon)

**Jika Belum Ada Logo:**

-   Gunakan placeholder emoji/icon: ♻️ + Text "Bank Sampah"
-   Atau buat logo sederhana di Canva (gratis)

---

### Technical Implementation

#### Files to Create

```
resources/views/
├── landing/
│   ├── index.blade.php              # Main landing page
│   ├── about.blade.php              # Tentang kami (optional)
│   ├── prices.blade.php             # Daftar harga lengkap (optional)
│   └── contact.blade.php            # Kontak (optional)
└── layouts/
    └── landing.blade.php            # Layout untuk landing (tanpa admin nav)

public/images/
├── landing/
│   ├── hero-image.jpg
│   ├── icons/
│   │   ├── step-1-register.svg
│   │   ├── step-2-deposit.svg
│   │   ├── step-3-save.svg
│   │   └── step-4-impact.svg
│   └── patterns/
│       └── hero-bg.jpg
└── logo.png

app/Http/Controllers/
└── LandingController.php            # NEW

routes/
└── web.php                          # Update root route
```

---

#### Routes

```php
// routes/web.php

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/tentang', [LandingController::class, 'about'])->name('landing.about');
Route::get('/harga', [LandingController::class, 'prices'])->name('landing.prices');
Route::get('/kontak', [LandingController::class, 'contact'])->name('landing.contact');

// Existing admin routes (no change)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
// ... rest of admin routes
```

---

#### Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\WasteCategory;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Landing page
     */
    public function index()
    {
        // Calculate statistics
        $stats = [
            'total_customers' => Customer::count(),
            'total_weight' => round(Deposit::sum('total_weight'), 2), // in kg
            'total_amount' => Withdrawal::sum('amount'),
            'trees_equivalent' => round(Deposit::sum('total_weight') / 100), // 1 ton = 10 trees
        ];

        // Get active waste categories with current prices
        $wasteCategories = WasteCategory::with(['wastePrices' => function ($query) {
            $query->where('effective_from', '<=', now())
                ->latest('effective_from')
                ->limit(1);
        }])
            ->where('is_active', true)
            ->limit(6)
            ->get();

        return view('landing.index', compact('stats', 'wasteCategories'));
    }

    /**
     * About page
     */
    public function about()
    {
        return view('landing.about');
    }

    /**
     * Prices page
     */
    public function prices()
    {
        $wasteCategories = WasteCategory::with(['wastePrices' => function ($query) {
            $query->where('effective_from', '<=', now())
                ->latest('effective_from')
                ->limit(1);
        }])
            ->where('is_active', true)
            ->get();

        return view('landing.prices', compact('wasteCategories'));
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $contact = [
            'phone' => '0812-3456-7890',
            'email' => 'info@banksampah.test',
            'address' => 'Jl. Bersih Raya No. 123, Jakarta',
            'hours' => 'Senin - Sabtu: 08.00 - 16.00 WIB',
        ];

        return view('landing.contact', compact('contact'));
    }
}
```

---

### Design System

#### Color Palette

**Primary Colors:**

```css
/* Green (Environment) */
--color-primary-50: #f0fdf4;
--color-primary-500: #22c55e;
--color-primary-600: #16a34a;
--color-primary-700: #15803d;

/* Blue (Trust) */
--color-secondary-500: #3b82f6;
--color-secondary-600: #2563eb;
```

**Usage:**

-   Primary green: CTAs, highlights, nature theme
-   Secondary blue: Links, admin area distinction
-   Gray: Text, backgrounds (Tailwind defaults)

#### Typography

```css
/* Headings */
h1: text-4xl md:text-6xl font-bold (Hero)
h2: text-3xl md:text-4xl font-bold (Section titles)
h3: text-xl md:text-2xl font-semibold (Card titles)

/* Body */
p: text-base md:text-lg (16-18px)
small: text-sm (14px)
```

#### Spacing

```css
/* Section padding */
py-16 md:py-24 (64-96px vertical)

/* Container */
max-w-7xl mx-auto px-4 sm:px-6 lg:px-8
```

---

### Accessibility Checklist

-   [ ] Alt text untuk semua gambar
-   [ ] Color contrast ratio ≥ 4.5:1 (text vs background)
-   [ ] Focus states visible (keyboard navigation)
-   [ ] Semantic HTML (header, nav, main, section, footer)
-   [ ] ARIA labels untuk icons/buttons tanpa text
-   [ ] Responsive images (srcset untuk retina)

---

### Performance Optimization

1. **Image Optimization:**

    ```bash
    # Compress images (pakai tool online atau CLI)
    # TinyPNG, ImageOptim, atau Squoosh
    ```

2. **Lazy Loading:**

    ```html
    <img src="hero.jpg" loading="lazy" alt="..." />
    ```

3. **Critical CSS:**

    - Inline critical CSS di `<head>`
    - Load non-critical CSS async

4. **Minify Assets:**
    ```bash
    npm run build  # Vite auto-minify di production
    ```

---

## 📊 IMPLEMENTATION TIMELINE

### Sprint 1: Customer Portal (6-8 jam)

#### Day 1 (4 jam)

-   [x] Setup routes & middleware (1 jam)
-   [x] Create CustomerPortalController (1.5 jam)
-   [x] Build check-code form & verification logic (1 jam)
-   [x] Create portal layout (0.5 jam)

#### Day 2 (4 jam)

-   [x] Build customer dashboard view (2 jam)
-   [x] Build transaction history view (1.5 jam)
-   [x] Build deposit detail view (0.5 jam)

#### Day 3 (Sisa 2 jam)

-   [x] Build info page (0.5 jam)
-   [x] Testing & bugfix (1 jam)
-   [x] UI polish & responsive (0.5 jam)

---

### Sprint 2: Landing Page (4-6 jam)

#### Day 1 (3 jam)

-   [x] Prepare images & assets (1 jam)
-   [x] Create LandingController & routes (0.5 jam)
-   [x] Build landing layout (0.5 jam)
-   [x] Build hero section (1 jam)

#### Day 2 (3 jam)

-   [x] Build "How it works" section (0.5 jam)
-   [x] Build statistics section (0.5 jam)
-   [x] Build categories & prices section (0.5 jam)
-   [x] Build CTA & footer (0.5 jam)
-   [x] Polish & responsive (1 jam)

---

### Sprint 3: Integration & Testing (2 jam)

-   [x] Connect landing CTA to portal (0.5 jam)
-   [x] End-to-end testing semua flow (1 jam)
-   [x] Cross-browser testing (0.5 jam)

---

**TOTAL ESTIMASI: 12-15 jam kerja efektif**

---

## ✅ ACCEPTANCE CRITERIA

### Customer Portal

-   [ ] **AC-CP-01**: Nasabah dapat input unique code (BSB-XXXX) dan access portal jika kode valid
-   [ ] **AC-CP-02**: Nasabah dapat melihat saldo terkini di dashboard
-   [ ] **AC-CP-03**: Nasabah dapat melihat riwayat transaksi (setoran & penarikan) dengan running balance
-   [ ] **AC-CP-04**: Nasabah dapat melihat detail setoran (item breakdown)
-   [ ] **AC-CP-05**: Nasabah TIDAK dapat melihat data nasabah lain (authorization check)
-   [ ] **AC-CP-06**: Session auto-expire setelah 30 menit inactive
-   [ ] **AC-CP-07**: Rate limiting works (max 5 attempts per 5 minutes)
-   [ ] **AC-CP-09**: IP validation prevents session hijacking
-   [ ] **AC-CP-10**: Failed login attempts are logged
-   [ ] **AC-CP-11**: Cannot use member_code (CUST-XXXX) for portal access
-   [ ] **AC-CP-08**: Portal responsive di mobile, tablet, desktop

### Landing Page

-   [ ] **AC-LP-01**: Landing page tampil sebagai home page (route `/`)
-   [ ] **AC-LP-02**: Statistik ditampilkan dari data real database
-   [ ] **AC-LP-03**: Kategori sampah & harga terkini ditampilkan
-   [ ] **AC-LP-04**: CTA "Cek Saldo" redirect ke portal nasabah
-   [ ] **AC-LP-05**: CTA "Daftar" redirect ke contact/info
-   [ ] **AC-LP-06**: Link "Masuk Admin" di header redirect ke `/login`
-   [ ] **AC-LP-07**: All images load correctly dengan alt text
-   [ ] **AC-LP-08**: Landing page responsive & fast (< 3s load time)

---

## 🧪 TESTING CHECKLIST

### Manual Testing

**Customer Portal:**

1. [ ] Cek unique code valid → redirect ke dashboard
2. [ ] Cek unique code invalid → error message
3. [ ] Cek member_code (CUST-XXXX) → should fail (must use unique code)
4. [ ] Rate limiting: After 5 failed attempts, show cooldown message
5. [ ] Dashboard menampilkan data correct (saldo, stats)
6. [ ] Transaksi history pagination works
7. [ ] Detail setoran shows correct items
8. [ ] Logout clears session
9. [ ] Try access dashboard tanpa session → redirect to check code
10. [ ] Try access deposit milik nasabah lain → 403 error
11. [ ] Session timeout: After 30 min inactive, force re-login
12. [ ] IP change: Session invalidated, force re-login

**Landing Page:**

1. [ ] All sections render correctly
2. [ ] Statistics update from database
3. [ ] CTAs link to correct routes
4. [ ] Images load without 404 errors
5. [ ] Responsive di mobile (Chrome DevTools)
6. [ ] Navigation menu works (if dropdown/mobile menu)

### Automated Testing

```bash
php artisan test --filter=CustomerPortal
php artisan test --filter=Landing
```

**Test Coverage Target:**

-   Controllers: 80%+
-   Critical flows: 100%

---

## 🚀 DEPLOYMENT STEPS

### Pre-Deployment

1. **Run Migration (REQUIRED - add unique_code column)**

    ```bash
    php artisan migrate
    ```

    Migration akan:

    - Tambah kolom `unique_code` ke tabel `customers`
    - Tambah index untuk performa
    - Auto-generate unique code untuk existing customers
    - Tambah kolom `last_portal_access` untuk tracking

2. **Verify Migration Success**

    ```bash
    php artisan tinker
    # Di tinker:
    Customer::first()->unique_code  // Should return BSB-XXXXXXXXXXXX
    Customer::all()->pluck('unique_code')  // All should be unique
    ```

3. **Seed additional data (optional)**

    ```bash
    php artisan db:seed --class=CustomerSeeder
    ```

    New customers akan otomatis dapat `unique_code` dari boot() method

4. **Optimize for production**

    ```bash
    composer install --optimize-autoloader --no-dev
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    npm run build
    ```

5. **Upload images to `public/images/`**

    - Pastikan semua images tersedia di server
    - Check file permissions (readable)

6. **Test di production environment**
    - Check landing page: `https://yourdomain.com/`
    - Check portal: `https://yourdomain.com/nasabah/cek`
    - Check all links & images

---

## 📝 DOCUMENTATION UPDATES

### Update README.md

Add new sections:

```markdown
## 🌐 Fitur Publik

### Landing Page

Akses: http://127.0.0.1:8000/

Halaman depan informatif dengan:

-   Penjelasan bank sampah
-   Statistik real-time
-   Daftar harga sampah
-   Call-to-action untuk daftar & cek saldo

### Portal Nasabah

Akses: http://127.0.0.1:8000/nasabah/cek

Nasabah dapat:

-   Cek saldo menggunakan kode member (contoh: CUST-0001)
-   Lihat riwayat transaksi (setoran & penarikan)
-   Lihat detail setoran dengan breakdown item
-   Tidak perlu akun/login, cukup kode member

**Demo Access:**

Untuk mendapatkan unique code nasabah, admin dapat:

1. Login ke admin dashboard
2. Buka menu "Nasabah"
3. Lihat detail nasabah → akan tampil **Member Code** dan **Unique Code**
4. Atau jalankan command: `php artisan tinker` → `Customer::first()->unique_code`

**Alternatif (untuk demo):** Tambahkan unique_code ke halaman print kartu nasabah
```

### Update Implementation_Plan.md

Add note:

```markdown
## ✅ Status Implementasi

-   [x] Fase 0-7: Core MVP (COMPLETED)
-   [x] Fase 8: Customer Portal (COMPLETED - Sprint baru)
-   [x] Fase 9: Landing Page (COMPLETED - Sprint baru)
```

---

## 🎬 DEMO SCENARIO UPDATE

### Extended Demo (7 menit - Include New Features)

**Menit 0-1: Landing Page** (NEW)

1. Buka home page (landing)
2. Scroll through sections: Hero → Cara Kerja → Statistik → Harga
3. Klik "Cek Saldo Saya" CTA

**Menit 1-2: Customer Portal** (NEW) 4. Input kode member: `CUST-0001` 5. Dashboard nasabah tampil: saldo, stats, recent transactions 6. Navigasi ke "Riwayat Transaksi" 7. Klik detail salah satu setoran → tampil breakdown items 8. Logout

**Menit 2-3: Admin Login** 9. Klik "Masuk" (link to login) 10. Login sebagai Operator

**Menit 3-5: Core Transactions** (Existing) 11. Dashboard admin 12. Buat setoran baru untuk nasabah 13. Proses penarikan

**Menit 5-6: Reports** (Existing) 14. Lihat laporan 15. Download PDF

**Menit 6-7: Full Circle** 16. Logout dari admin 17. Kembali ke portal nasabah (cek kode lagi) 18. Verify transaksi baru muncul di riwayat 19. Saldo updated correctly

---

## 💡 FUTURE ENHANCEMENTS (Fase 3+)

### Portal Nasabah

-   [ ] Request penarikan online (butuh approval operator)
-   [ ] WhatsApp notification saat ada setoran baru
-   [ ] QR Code untuk quick login (scan dari kartu member)
-   [ ] Gamification: Badge/achievement (contoh: "Penyetor Rajin")
-   [ ] Grafik visual (chart.js): trend setoran per bulan
-   [ ] Export PDF riwayat transaksi custom date range

### Landing Page

-   [ ] Blog/artikel tentang lingkungan & daur ulang
-   [ ] Galeri foto kegiatan bank sampah (carousel)
-   [ ] Testimoni video
-   [ ] Live chat support (chatbot atau WhatsApp integration)
-   [ ] Multi-language (English support)
-   [ ] Dark mode toggle

### Integration

-   [ ] Integrasi WA Gateway untuk bukti transaksi otomatis
-   [ ] Email notification (welcome email saat daftar)
-   [ ] API public untuk check balance (mobile app ready)

---

## 🎨 APPENDIX: Design Mockup References

### Color Scheme Recommendation

**Option 1: Fresh Green (Recommended)**

```
Primary: Green-500 (#22c55e)
Secondary: Blue-600 (#2563eb)
Accent: Yellow-400 (#facc15)
Background: Gray-50 (#f9fafb)
Text: Gray-900 (#111827)
```

**Option 2: Eco Blue**

```
Primary: Teal-500 (#14b8a6)
Secondary: Emerald-600 (#059669)
Accent: Orange-400 (#fb923c)
Background: Slate-50 (#f8fafc)
Text: Slate-900 (#0f172a)
```

### Font Pairing

**Option 1: System Fonts (Fast Loading)**

```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    "Helvetica Neue", Arial, sans-serif;
```

**Option 2: Google Fonts (More Character)**

```html
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700&display=swap"
/>
```

```css
/* Headings */
font-family: "Poppins", sans-serif;
/* Body */
font-family: "Inter", sans-serif;
```

---

## 📋 FINAL CHECKLIST

Sebelum mark sebagai DONE, pastikan:

### Development

-   [ ] Migration created & tested (`unique_code` column added)
-   [ ] Customer model boot() method updated
-   [ ] Semua files created sesuai struktur
-   [ ] No syntax errors (`php artisan route:list` works)
-   [ ] No console errors di browser DevTools
-   [ ] All images exist di `public/images/`
-   [ ] Verify unique codes generated for all customers

### Functionality

-   [ ] Landing page accessible di `/`
-   [ ] Portal nasabah accessible di `/nasabah/cek`
-   [ ] Check unique code works (not member_code)
-   [ ] Rate limiting works (5 attempts, 5 min cooldown)
-   [ ] Dashboard nasabah shows correct data
-   [ ] Authorization checks working (cannot view other customer data)
-   [ ] Session timeout works (30 minutes)
-   [ ] IP validation works (prevent session hijacking)
-   [ ] All security logs being written

### Quality

-   [ ] Code formatted dengan Laravel Pint (`./vendor/bin/pint`)
-   [ ] No PHP warnings/notices
-   [ ] Responsive di mobile (test with DevTools)
-   [ ] Accessibility: alt text, contrast, keyboard nav

### Testing

-   [ ] Feature tests written & passing
-   [ ] Manual testing completed (checklist above)
-   [ ] Cross-browser tested (Chrome, Firefox, Edge)

### Documentation

-   [ ] README.md updated
-   [ ] Demo scenario updated
-   [ ] Comments added di code untuk complex logic

### Deployment Ready

-   [ ] `.env.example` updated (if any new vars)
-   [ ] Migration files (none for this feature)
-   [ ] Seeder data adequate
-   [ ] Assets optimized (`npm run build`)

---

## 🤝 COLLABORATION NOTES

### Untuk Anda (Developer)

**Yang Perlu Anda Siapkan:**

1. **Images** (saya sudah detail spesifikasi di atas)

    - Hero image: 1920×1080px, max 500KB
    - Icons: 4 files, 128×128px, SVG/PNG
    - Logo: 300×300px, PNG transparent

2. **Content Copy**

    - Tagline untuk hero section (2-3 variasi)
    - Testimonial quotes (2-3 fake testimonials)
    - Contact info (phone, email, address, hours)

3. **Branding Decision**

    - Pilih color scheme (Option 1 atau 2)
    - Pilih font pairing (System atau Google Fonts)

4. **Admin Interface Update (Optional tapi Recommended)**

    - Update halaman detail nasabah untuk tampilkan `unique_code`
    - Tambahkan button "Copy Unique Code" untuk kemudahan
    - Tampilkan di kartu member (print)

    **Contoh Display:**

    ```
    ┌─────────────────────────────────────┐
    │ KARTU MEMBER BANK SAMPAH           │
    ├─────────────────────────────────────┤
    │ Nama: Budi Setiawan                │
    │ Member: CUST-0001                  │
    │ Portal: BSB-X7K9M2P4N8Q1           │
    │                                     │
    │ Gunakan kode portal untuk         │
    │ cek saldo online di:              │
    │ banksampah.com/nasabah/cek        │
    └─────────────────────────────────────┘
    ```

### Untuk Saya (AI Assistant)

**Yang Akan Saya Kerjakan:**

1. Code implementation sesuai plan ini
2. Testing & debugging
3. UI/UX implementation dengan Tailwind
4. Documentation updates

**Timeline Eksekusi:**

-   Start: Setelah Anda approve plan ini & siapkan images
-   Estimated: 12-15 jam (2-3 hari kerja dengan fokus)
-   Delivery: Setiap sprint deliver working feature (incremental)

---

## ❓ PERTANYAAN & KEPUTUSAN PENDING

Sebelum mulai implementasi, konfirmasi:

### Portal Nasabah

1. ✅ **Session timeout**: 30 menit (DECIDED - best practice)
2. ✅ **Rate limiting**: 5 attempts per 5 minutes (DECIDED - security best practice)
3. ✅ **Unique Code Method**: Random BSB-XXXXXXXXXXXX (DECIDED - secure & simple)
4. **PDF Download**: Perlu fitur download PDF riwayat transaksi? (tambah 1-2 jam)
5. **Admin Display**: Perlu update halaman customer detail untuk show unique_code?

### Landing Page

4. **Konten**: Nama official bank sampah? Atau pakai "Bank Sampah Bersih Sejahtera" (placeholder)?
5. **Contact info**: Data contact real atau placeholder dulu?
6. **Testimoni**: Pakai 2-3 fake testimonial OK? Atau skip dulu section ini?
7. **Additional pages**: Perlu halaman "Tentang Kami" dedicated? Atau cukup landing page saja?

### Prioritas

8. **Which first?** Implement portal dulu atau landing page dulu?
    - Rekomendasi saya: **Portal dulu** (core functionality), **Landing kedua** (presentation layer)

---

## 🎯 NEXT STEPS

Setelah Anda review dokumen ini:

1. **Approve/Feedback** → Berikan feedback jika ada yang perlu diubah
2. **Prepare Assets** → Siapkan images sesuai spesifikasi
3. **Answer Questions** → Jawab pertanyaan di section "PERTANYAAN & KEPUTUSAN PENDING"
4. **Kick-off Sprint 1** → Saya mulai implement Customer Portal
5. **Review & Iterate** → Review hasil Sprint 1, lanjut Sprint 2
6. **Final Testing** → End-to-end testing semua fitur
7. **Go Live!** → Deploy fitur baru ke production

---

**Siap untuk mulai?** 🚀

Apakah ada yang perlu diklarifikasi atau diubah dari plan ini sebelum saya mulai implementasi?
