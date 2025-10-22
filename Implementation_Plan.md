# IMPLEMENTATION PLAN — Bank Sampah MVP

> **Berdasarkan:** Requirements.md  
> **Target:** Aplikasi Laravel 11 siap demo dalam 7 fase pengembangan  
> **Pendekatan:** Test-Driven Development (TDD) + Fat Models, Skinny Controllers

---

## 📋 RINGKASAN EKSEKUTIF

Rencana implementasi ini menguraikan langkah-langkah sistematis untuk membangun aplikasi Bank Sampah MVP menggunakan Laravel 11, dengan fokus pada:

- ✅ Kualitas kode (PSR-12, Laravel Pint)
- ✅ Test coverage (Pest PHP)
- ✅ Struktur modular (Service Classes untuk logika kompleks)
- ✅ UI responsif (Blade + Tailwind + Alpine.js via Breeze)
- ✅ Siap demo dalam timeline terkontrol

---

## 🎯 FASE PENGEMBANGAN

### **FASE 0: Project Setup & Environment** ⏱️ 30-45 menit

#### Tasks:

1. ✅ Inisialisasi Laravel project
2. ✅ Konfigurasi environment (.env)
3. ✅ Instal Laravel Breeze (Blade + Alpine stack)
4. ✅ Setup Pest untuk testing
5. ✅ Instal Laravel Pint (code formatting)
6. ✅ Instal dependensi tambahan:
   - `barryvdh/laravel-dompdf` (untuk PDF bukti)
   - `laravel/telescope` (development only)

#### Terminal Commands:

```bash
# Sudah dijalankan: composer create-project laravel/laravel bank-sampah
cd bank-sampah
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require barryvdh/laravel-dompdf
composer require laravel/telescope --dev
php artisan telescope:install
composer require pestphp/pest --dev --with-all-dependencies
composer require pestphp/pest-plugin-laravel --dev
php artisan pest:install
npm install && npm run build
```

#### Deliverables:

- ✅ Laravel project dengan autentikasi dasar
- ✅ Database connection dikonfigurasi
- ✅ Testing framework ready

---

### **FASE 1: Database Schema & Models** ⏱️ 2-3 jam

#### Tasks:

1. Buat migrations untuk semua tabel:

   - `users` (extend default Laravel)
   - `customers`
   - `waste_categories`
   - `waste_prices`
   - `deposits`
   - `deposit_items`
   - `withdrawals`
   - `ledger_entries`

2. Buat Models dengan relationships:

   - `User` (tambah role field)
   - `Customer` (hasMany ledgerEntries, deposits, withdrawals)
   - `WasteCategory` (hasMany wastePrices)
   - `WastePrice` (belongsTo wasteCategory)
   - `Deposit` (belongsTo customer, user; hasMany depositItems)
   - `DepositItem` (belongsTo deposit, wasteCategory)
   - `Withdrawal` (belongsTo customer, user)
   - `LedgerEntry` (belongsTo customer, morphTo ref)

3. Buat Factories untuk seeding & testing

4. Buat Seeders dengan data dummy:
   - Kategori sampah (PET, Kertas, Kaleng)
   - Harga awal
   - 2 nasabah contoh
   - Users (Admin + Operator)

#### File Structure:

```
database/
├── migrations/
│   ├── 2024_01_01_000001_add_role_to_users_table.php
│   ├── 2024_01_01_000002_create_customers_table.php
│   ├── 2024_01_01_000003_create_waste_categories_table.php
│   ├── 2024_01_01_000004_create_waste_prices_table.php
│   ├── 2024_01_01_000005_create_deposits_table.php
│   ├── 2024_01_01_000006_create_deposit_items_table.php
│   ├── 2024_01_01_000007_create_withdrawals_table.php
│   └── 2024_01_01_000008_create_ledger_entries_table.php
├── factories/
│   ├── CustomerFactory.php
│   ├── WasteCategoryFactory.php
│   ├── WastePriceFactory.php
│   └── ... (others)
└── seeders/
    ├── RoleSeeder.php
    ├── WasteCategorySeeder.php
    ├── CustomerSeeder.php
    └── DatabaseSeeder.php
```

#### Key Design Decisions:

**Saldo Calculation:**

- Opsi 1 (Recommended): Computed dari `ledger_entries` via accessor di Model `Customer`
- Opsi 2: Stored field `customers.balance` (perlu trigger/event listener untuk konsistensi)

**Harga Aktif Selection:**

```php
// Di Model WastePrice - scope untuk ambil harga aktif
public function scopeActiveOn($query, $date) {
    return $query->where('effective_from', '<=', $date)
                 ->orderBy('effective_from', 'desc')
                 ->first();
}
```

**Status Enum untuk Deposits:**

```php
enum DepositStatus: string {
    case DRAFT = 'draft';
    case FINAL = 'final';
}
```

#### Deliverables:

- ✅ Semua migrations running
- ✅ Models dengan relationships lengkap
- ✅ Seeder data dummy berjalan
- ✅ Database siap untuk development

---

### **FASE 2: Authorization & Role Management** ⏱️ 1-2 jam

#### Tasks:

1. Setup Gate/Policy untuk role-based access:

   - Admin: full access
   - Operator: tidak bisa kelola master data

2. Buat Middleware untuk role check

3. Update Breeze views dengan conditional menu

4. **Testing:**
   - Unit test untuk Policies
   - Feature test untuk restricted routes

#### Files to Create/Modify:

```
app/
├── Policies/
│   ├── WasteCategoryPolicy.php
│   ├── WastePricePolicy.php
│   └── CustomerPolicy.php
├── Http/Middleware/
│   └── EnsureUserHasRole.php
└── Providers/
    └── AuthServiceProvider.php (register policies)
```

#### Example Policy:

```php
// WasteCategoryPolicy.php
public function update(User $user): bool {
    return $user->role === 'admin';
}

public function delete(User $user): bool {
    return $user->role === 'admin';
}
```

#### Deliverables:

- ✅ Authorization system berfungsi
- ✅ Operator tidak bisa akses master data
- ✅ Tests passing untuk authorization

---

### **FASE 3: Master Data Management (Admin Only)** ⏱️ 3-4 jam

#### Modules:

1. **Customers CRUD** (FR-010 sampai FR-012)
2. **Waste Categories CRUD** (FR-020)
3. **Waste Prices Management** (FR-021, FR-022)

#### TDD Workflow (untuk setiap module):

**A. Customer Management**

1. **Write Tests First:**

```php
// tests/Feature/CustomerManagementTest.php
test('admin can create customer')
test('admin can update customer')
test('admin can delete customer')
test('operator cannot delete customer')
test('can search customer by name or phone')
test('customer profile shows balance and transaction history')
```

2. **Create Routes:**

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/profile', [CustomerController::class, 'profile']);
});
```

3. **Create Controller:**

```bash
php artisan make:controller CustomerController --resource --model=Customer
```

4. **Create Form Requests:**

```bash
php artisan make:request StoreCustomerRequest
php artisan make:request UpdateCustomerRequest
```

5. **Implement Logic:**

   - Controller methods (thin, delegate to model)
   - Validation in Form Requests
   - Blade views dengan Tailwind

6. **Run Tests & Iterate**

**B. Waste Category Management**

- Similar TDD flow
- List view dengan status (active/inactive)
- Toggle active status

**C. Waste Price Management**

- List harga per kategori (dengan history)
- Form tambah harga baru (kategori + price_per_kg + effective_from)
- View: tampilkan harga aktif saat ini highlighted
- Validasi: effective_from tidak boleh sebelum harga terakhir (optional)

#### File Structure:

```
app/Http/
├── Controllers/
│   ├── CustomerController.php
│   ├── WasteCategoryController.php
│   └── WastePriceController.php
├── Requests/
│   ├── StoreCustomerRequest.php
│   ├── UpdateCustomerRequest.php
│   ├── StoreWasteCategoryRequest.php
│   └── StoreWastePriceRequest.php

resources/views/
├── customers/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── profile.blade.php
├── waste-categories/
│   ├── index.blade.php
│   └── form.blade.php
└── waste-prices/
    └── manage.blade.php
```

#### Deliverables:

- ✅ Admin bisa CRUD customers
- ✅ Admin bisa CRUD waste categories
- ✅ Admin bisa manage price history
- ✅ Pencarian customer berfungsi (autocomplete dengan Alpine.js)
- ✅ All feature tests passing

---

### **FASE 4: Core Transaction - Deposits (Setoran)** ⏱️ 4-5 jam

**Ini adalah fitur paling kompleks dan krusial untuk demo.**

#### Tasks:

1. **Create Deposit Service Class**

```bash
mkdir app/Services
# Create DepositService.php manually
```

**DepositService responsibilities:**

- Calculate subtotals & totals
- Validate stock availability (optional for MVP)
- Create deposit header & items in transaction
- Create ledger entry (DEBIT)
- Update customer balance (if using stored balance)
- Generate unique deposit code

2. **TDD for Deposits:**

```php
// tests/Feature/DepositManagementTest.php
test('operator can create deposit with single item')
test('operator can create deposit with multiple items')
test('deposit calculates subtotal correctly using active price')
test('deposit updates customer balance')
test('deposit creates ledger entry with correct direction')
test('cannot create deposit with zero weight')
test('cannot create deposit for non-existent customer')
test('deposit uses correct price based on transaction date')
test('can generate unique deposit code')
```

3. **Create Routes & Controller:**

```php
Route::middleware(['auth'])->group(function () {
    Route::get('deposits/create', [DepositController::class, 'create']);
    Route::post('deposits', [DepositController::class, 'store']);
    Route::get('deposits/{deposit}', [DepositController::class, 'show']);
    Route::get('deposits/{deposit}/receipt', [DepositController::class, 'receipt']);
});
```

4. **Form Request Validation:**

```php
// StoreDepositRequest.php
public function rules() {
    return [
        'customer_id' => 'required|exists:customers,id',
        'items' => 'required|array|min:1',
        'items.*.waste_category_id' => 'required|exists:waste_categories,id',
        'items.*.weight_kg' => 'required|numeric|min:0.01',
    ];
}
```

5. **Deposit Form (Blade + Alpine.js):**

- Autocomplete untuk customer search
- Dynamic form rows untuk items
- Real-time calculation subtotal & total (Alpine.js)
- Display harga aktif saat kategori dipilih

6. **Implement PDF Receipt (Optional but Recommended):**

```php
// DepositController@receipt
public function receipt(Deposit $deposit) {
    $pdf = PDF::loadView('deposits.receipt-pdf', compact('deposit'));
    return $pdf->download("Bukti-Setoran-{$deposit->code}.pdf");
}
```

#### File Structure:

```
app/
├── Services/
│   └── DepositService.php
├── Http/
│   ├── Controllers/
│   │   └── DepositController.php
│   └── Requests/
│       └── StoreDepositRequest.php

resources/views/
└── deposits/
    ├── create.blade.php (form utama)
    ├── show.blade.php (detail)
    └── receipt-pdf.blade.php (template PDF)
```

#### Example DepositService Method:

```php
public function createDeposit(array $data, User $user): Deposit
{
    DB::beginTransaction();
    try {
        // Generate unique code
        $code = $this->generateDepositCode();

        // Create deposit header
        $deposit = Deposit::create([
            'code' => $code,
            'customer_id' => $data['customer_id'],
            'status' => DepositStatus::FINAL,
            'created_by' => $user->id,
            'total_amount' => 0,
            'total_weight' => 0,
        ]);

        $totalAmount = 0;
        $totalWeight = 0;

        // Create deposit items
        foreach ($data['items'] as $item) {
            $activePrice = WastePrice::where('waste_category_id', $item['waste_category_id'])
                ->activeOn(now())
                ->value('price_per_kg');

            $subtotal = $item['weight_kg'] * $activePrice;

            $deposit->items()->create([
                'waste_category_id' => $item['waste_category_id'],
                'weight_kg' => $item['weight_kg'],
                'price_per_kg' => $activePrice,
                'subtotal_amount' => $subtotal,
            ]);

            $totalAmount += $subtotal;
            $totalWeight += $item['weight_kg'];
        }

        // Update totals
        $deposit->update([
            'total_amount' => $totalAmount,
            'total_weight' => $totalWeight,
        ]);

        // Create ledger entry (DEBIT = menambah saldo)
        LedgerEntry::create([
            'customer_id' => $data['customer_id'],
            'ref_type' => Deposit::class,
            'ref_id' => $deposit->id,
            'direction' => 'DEBIT',
            'amount' => $totalAmount,
        ]);

        DB::commit();
        return $deposit;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

#### Deliverables:

- ✅ Form setoran dengan dynamic items
- ✅ Auto-calculation berfungsi
- ✅ Transaksi tersimpan dengan benar
- ✅ Saldo customer bertambah
- ✅ Ledger entry tercatat
- ✅ PDF receipt bisa diunduh
- ✅ All tests passing (AC-001 verified)

---

### **FASE 5: Withdrawals (Penarikan Saldo)** ⏱️ 2-3 jam

#### Tasks:

1. **Create Withdrawal Service**

2. **TDD for Withdrawals:**

```php
// tests/Feature/WithdrawalManagementTest.php
test('operator can create withdrawal within balance')
test('cannot create withdrawal exceeding balance')
test('withdrawal updates customer balance correctly')
test('withdrawal creates ledger entry with KREDIT direction')
test('can generate withdrawal receipt')
```

3. **Routes & Controller:**

```php
Route::middleware(['auth'])->group(function () {
    Route::get('withdrawals/create', [WithdrawalController::class, 'create']);
    Route::post('withdrawals', [WithdrawalController::class, 'store']);
    Route::get('withdrawals/{withdrawal}/receipt', [WithdrawalController::class, 'receipt']);
});
```

4. **Form Features:**

- Select customer (with autocomplete)
- Display current balance
- Input nominal
- Validation: nominal <= balance
- Optional note field

5. **Implement PDF Receipt**

#### WithdrawalService Logic:

```php
public function createWithdrawal(array $data, User $user): Withdrawal
{
    DB::beginTransaction();
    try {
        $customer = Customer::find($data['customer_id']);

        // Validate balance
        if ($customer->balance < $data['amount']) {
            throw new \Exception('Insufficient balance');
        }

        // Create withdrawal
        $withdrawal = Withdrawal::create([
            'code' => $this->generateWithdrawalCode(),
            'customer_id' => $data['customer_id'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'created_by' => $user->id,
        ]);

        // Create ledger entry (KREDIT = mengurangi saldo)
        LedgerEntry::create([
            'customer_id' => $data['customer_id'],
            'ref_type' => Withdrawal::class,
            'ref_id' => $withdrawal->id,
            'direction' => 'KREDIT',
            'amount' => $data['amount'],
        ]);

        DB::commit();
        return $withdrawal;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

#### Deliverables:

- ✅ Form penarikan berfungsi
- ✅ Validasi saldo bekerja
- ✅ Saldo customer berkurang
- ✅ Ledger entry tercatat
- ✅ PDF receipt tersedia
- ✅ Tests passing (AC-002 verified)

---

### **FASE 6: Reports & Dashboard** ⏱️ 3-4 jam

#### Tasks:

1. **Dashboard (Landing after login):**

   - Card metrics: Total setoran hari ini, Total penarikan hari ini, Transaksi hari ini
   - Top 3 kategori hari ini (by weight & amount)
   - Quick actions: Setoran Baru, Penarikan Baru

2. **Transaction History:**

   - Combined view: deposits + withdrawals
   - Filters: date range, customer, type
   - Sortable columns

3. **Customer Ledger View:**

   - Access from customer profile
   - Show all mutations (debit/kredit)
   - Running balance column
   - Export CSV (optional)

4. **Report Summary (FR-060, FR-061):**
   - Date range filter
   - Metrics:
     - Total deposits (Rp & kg)
     - Total withdrawals (Rp)
     - Net balance change
   - Breakdown by waste category:
     - Total weight (kg)
     - Total amount (Rp)
     - Number of transactions
   - Top 10 customers by deposit amount
   - Export PDF/Excel (optional)

#### File Structure:

```
app/Http/Controllers/
├── DashboardController.php
├── ReportController.php
└── LedgerController.php

resources/views/
├── dashboard.blade.php
├── reports/
│   ├── summary.blade.php
│   └── summary-pdf.blade.php
└── ledger/
    └── show.blade.php
```

#### Example Query for Category Summary:

```php
// ReportController
public function summary(Request $request) {
    $start = $request->input('start_date', now()->startOfMonth());
    $end = $request->input('end_date', now()->endOfMonth());

    $categorySummary = DB::table('deposit_items')
        ->join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
        ->join('waste_categories', 'deposit_items.waste_category_id', '=', 'waste_categories.id')
        ->whereBetween('deposits.created_at', [$start, $end])
        ->where('deposits.status', 'final')
        ->select(
            'waste_categories.name',
            DB::raw('SUM(deposit_items.weight_kg) as total_weight'),
            DB::raw('SUM(deposit_items.subtotal_amount) as total_amount'),
            DB::raw('COUNT(DISTINCT deposits.id) as transaction_count')
        )
        ->groupBy('waste_categories.id', 'waste_categories.name')
        ->get();

    // ... other queries for totals

    return view('reports.summary', compact('categorySummary', ...));
}
```

#### Deliverables:

- ✅ Dashboard informatif
- ✅ Ledger view per customer (AC-003 verified)
- ✅ Report summary dengan breakdown (AC-004 verified)
- ✅ Export functionality (PDF minimal)

---

### **FASE 7: UI/UX Polish & Final Testing** ⏱️ 2-3 jam

#### Tasks:

1. **UI Refinements:**

   - Responsive design check (mobile/tablet/desktop)
   - Loading states (Alpine.js)
   - Success/error notifications (Toast/Alert components)
   - Konfirmasi dialogs (Delete, Finalize)
   - Keyboard navigation untuk form setoran (Tab/Enter)

2. **Accessibility:**

   - Form labels proper
   - Error messages clear
   - Contrast ratio check

3. **Performance:**

   - Eager loading untuk relationships (N+1 prevention)
   - Index database columns yang sering di-query
   - Cache configuration settings

4. **Final Integration Testing:**

   - Jalankan semua test suite
   - Manual testing semua flow end-to-end
   - Cross-browser testing (Chrome, Firefox, Edge)

5. **Documentation:**

   - Update README dengan:
     - Installation steps
     - Seeder usage
     - Default credentials
     - Demo scenario script

6. **Demo Preparation:**
   - Fresh migration + seed
   - Prepare demo script sesuai Requirements section 18
   - Screenshoot key screens untuk presentasi

#### Checklist Kriteria Penerimaan:

- [ ] AC-001: Setoran multi-item ✅
- [ ] AC-002: Penarikan dengan validasi ✅
- [ ] AC-003: Riwayat mutasi akurat ✅
- [ ] AC-004: Laporan per kategori ✅
- [ ] AC-005: Harga aktif dynamic ✅
- [ ] AC-006: Role restrictions ✅

#### Deliverables:

- ✅ Aplikasi siap demo
- ✅ All tests passing
- ✅ Documentation complete
- ✅ Demo data ready

---

## 📁 PROJECT STRUCTURE (Final)

```
bank-sampah/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CustomerController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DepositController.php
│   │   │   ├── LedgerController.php
│   │   │   ├── ReportController.php
│   │   │   ├── WasteCategoryController.php
│   │   │   ├── WastePriceController.php
│   │   │   └── WithdrawalController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserHasRole.php
│   │   └── Requests/
│   │       ├── StoreCustomerRequest.php
│   │       ├── StoreDepositRequest.php
│   │       ├── StoreWasteCategoryRequest.php
│   │       ├── StoreWastePriceRequest.php
│   │       └── StoreWithdrawalRequest.php
│   ├── Models/
│   │   ├── Customer.php
│   │   ├── Deposit.php
│   │   ├── DepositItem.php
│   │   ├── LedgerEntry.php
│   │   ├── User.php
│   │   ├── WasteCategory.php
│   │   ├── WastePrice.php
│   │   └── Withdrawal.php
│   ├── Policies/
│   │   ├── CustomerPolicy.php
│   │   ├── WasteCategoryPolicy.php
│   │   └── WastePricePolicy.php
│   └── Services/
│       ├── DepositService.php
│       └── WithdrawalService.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       ├── CustomerSeeder.php
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── WasteCategorySeeder.php
│       └── WastePriceSeeder.php
├── resources/
│   └── views/
│       ├── customers/
│       ├── dashboard.blade.php
│       ├── deposits/
│       ├── layouts/
│       ├── ledger/
│       ├── reports/
│       ├── waste-categories/
│       ├── waste-prices/
│       └── withdrawals/
├── routes/
│   └── web.php
└── tests/
    ├── Feature/
    │   ├── CustomerManagementTest.php
    │   ├── DepositManagementTest.php
    │   ├── ReportTest.php
    │   └── WithdrawalManagementTest.php
    └── Unit/
        ├── DepositServiceTest.php
        └── WithdrawalServiceTest.php
```

---

## 🧪 TESTING STRATEGY

### Unit Tests:

- Service classes logic (DepositService, WithdrawalService)
- Model methods & accessors
- Policies

### Feature Tests:

- Full HTTP request/response cycle
- Authentication & authorization
- CRUD operations
- Transaction flows (deposit, withdrawal)
- Report generation

### Test Coverage Goals:

- Critical paths: 100% (Deposit, Withdrawal)
- Controllers: 80%+
- Services: 90%+

### Running Tests:

```bash
php artisan test
# atau
./vendor/bin/pest
# dengan coverage
./vendor/bin/pest --coverage
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-deployment:

- [ ] Run all tests: `php artisan test`
- [ ] Code formatting: `./vendor/bin/pint`
- [ ] Environment check: `.env.production` ready
- [ ] Database backup strategy
- [ ] Clear config cache: `php artisan config:clear`

### Production Optimization:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Server Requirements:

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & npm (untuk build aset)

### Permissions:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📊 ESTIMASI WAKTU TOTAL

| Fase                      | Waktu Estimasi | Kumulatif  |
| ------------------------- | -------------- | ---------- |
| Fase 0: Setup             | 0.5-1 jam      | 1 jam      |
| Fase 1: Database & Models | 2-3 jam        | 4 jam      |
| Fase 2: Authorization     | 1-2 jam        | 6 jam      |
| Fase 3: Master Data       | 3-4 jam        | 10 jam     |
| Fase 4: Deposits          | 4-5 jam        | 15 jam     |
| Fase 5: Withdrawals       | 2-3 jam        | 18 jam     |
| Fase 6: Reports           | 3-4 jam        | 22 jam     |
| Fase 7: Polish & Testing  | 2-3 jam        | **25 jam** |

**Total: ~20-25 jam pengerjaan efektif** (3-4 hari kerja dengan fokus penuh)

---

## 🎬 DEMO SCENARIO (5 menit)

### Persiapan:

```bash
php artisan migrate:fresh --seed
```

### Script Demo:

**Menit 1-2: Login & Dashboard**

1. Login sebagai Operator (email: `operator@banksampah.test`, password: `password`)
2. Tampilkan dashboard: metrics hari ini, quick actions

**Menit 2-3: Setoran** 3. Klik "Setoran Baru" 4. Pilih nasabah "Budi Setiawan" (autocomplete) 5. Tambah item:

- Plastik PET: 1.2 kg (auto hitung: Rp 3.600)
- Kertas: 0.5 kg (auto hitung: Rp 750)

6. Total: Rp 4.350
7. Simpan & Final → tampilkan bukti/receipt
8. Lihat profil Budi → saldo bertambah Rp 4.350

**Menit 3-4: Penarikan** 9. Klik "Penarikan Baru" 10. Pilih "Siti Aminah" 11. Tampilkan saldo tersedia: Rp 5.000 12. Input nominal: Rp 2.000 13. Simpan → tampilkan bukti penarikan 14. Saldo Siti berkurang menjadi Rp 3.000

**Menit 4-5: Laporan** 15. Akses menu Laporan 16. Filter: hari ini 17. Tampilkan: - Total setoran: 1.7 kg, Rp 4.350 - Breakdown per kategori - Riwayat transaksi 18. (Optional) Download PDF

**Bonus: Master Data (jika Admin login)**

- Tampilkan kelola harga
- Tambah harga baru dengan effective_from

---

## ⚠️ CATATAN PENTING

### Prinsip Development:

1. **Test First**: Tulis test sebelum implementasi
2. **Commit Often**: Commit setiap fitur kecil selesai
3. **Refactor**: Jangan takut refactor setelah test passing
4. **Code Review Self**: Review kode sendiri sebelum lanjut fase baru

### Potential Pitfalls:

- ❌ N+1 queries di list/report → Gunakan `with()` eager loading
- ❌ Race condition di balance update → Gunakan DB transaction
- ❌ Tidak validasi input → Selalu gunakan Form Request
- ❌ Hardcode role string → Gunakan enum/constant

### Best Practices:

- ✅ Fat Models, Skinny Controllers
- ✅ Service classes untuk complex business logic
- ✅ Form Requests untuk validation
- ✅ Policy untuk authorization
- ✅ Events & Listeners untuk decoupling (optional di MVP)

---

## 📚 RESOURCES & REFERENCES

- Laravel 11 Documentation: https://laravel.com/docs/11.x
- Pest PHP: https://pestphp.com
- Laravel Breeze: https://laravel.com/docs/11.x/starter-kits#breeze
- Tailwind CSS: https://tailwindcss.com/docs
- Alpine.js: https://alpinejs.dev
- DomPDF: https://github.com/barryvdh/laravel-dompdf

---

## ✅ NEXT STEPS

Setelah approval Implementation Plan ini:

1. ✅ Konfirmasi stack teknologi (Laravel 11 + Breeze + Blade + Tailwind)
2. ✅ Mulai Fase 0: Project Setup
3. ✅ Jalankan migrasi & seeder (Fase 1)
4. ✅ Iterasi TDD untuk setiap fase
5. ✅ Demo & feedback loop

---

**Siap untuk memulai?** 🚀

Apakah ada perubahan atau penyesuaian yang Anda inginkan pada rencana ini sebelum saya mulai implementasi?
