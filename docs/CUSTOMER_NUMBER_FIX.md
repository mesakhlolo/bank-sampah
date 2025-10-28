# Fix: Customer Number Generation

## Problem

-   Nomor customer dimulai dari angka tinggi (9968, 9969, dll) karena Factory menggunakan random number
-   Resiko duplikasi dan konflik setelah nomor 9999
-   Inkonsistensi prefix (NSB-xxxx vs CUST-xxxx)

## Solution Implemented

### 1. CustomerFactory.php

✅ Changed from random `numberBetween(1, 9999)` to sequential numbering
✅ Uses static counter to ensure proper sequence during batch creation
✅ Starts from database's last number + 1

### 2. Customer Model

✅ Added `boot()` method with `creating` event
✅ Auto-generates `member_code` if not provided
✅ Uses database lock (`lockForUpdate()`) to prevent race conditions
✅ Supports expansion beyond 9999 (CUST-10000, CUST-10001, etc.)

### 3. CustomerController

✅ Simplified `store()` method - removed duplicate logic
✅ Removed `generateNextMemberCode()` method (now in Model)
✅ Model handles generation automatically

## Benefits

-   ✅ Sequential numbering from CUST-0001
-   ✅ No duplicate numbers
-   ✅ Automatic expansion beyond 9999
-   ✅ Thread-safe with database locking
-   ✅ Consistent across all creation methods (Controller, Factory, direct Model)

## Testing

```php
// Can create customers without providing member_code
Customer::create([
    'name' => 'John Doe',
    'phone' => '08123456789',
    'address' => 'Jakarta',
    'joined_at' => now(),
]);
// Auto-generates: CUST-0051

// System handles beyond 9999
// After CUST-9999 → CUST-10000, CUST-10001, etc.
```

## Database Reset

```bash
php artisan migrate:fresh --seed
```

This creates 50 customers with proper sequential numbers:

-   CUST-0001 (Budi Setiawan)
-   CUST-0002 (Siti Aminah)
-   ...
-   CUST-0050 (Last seeded customer)
