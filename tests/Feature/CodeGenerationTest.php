<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use App\Services\DepositService;
use App\Services\WithdrawalService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/*
 * Regression tests for sequential code generation.
 *
 * The codes are produced with a read-then-write (`MAX + 1`), which is not atomic.
 * These tests lock in the format, the increment, and the retry that keeps a
 * concurrent insert from failing the request outright.
 */

test('customer member code follows the CUST format and increments', function () {
    $first = Customer::factory()->create();
    $second = Customer::factory()->create();

    expect($first->member_code)->toMatch('/^CUST-\d{4,}$/')
        ->and($first->member_code)->toBe('CUST-0001')
        ->and($second->member_code)->toBe('CUST-0002');
});

test('customer unique code is a BSB code of the expected length', function () {
    $customer = Customer::factory()->create();

    // BSB- + 12 uppercase alphanumeric characters = 16 chars total.
    expect($customer->unique_code)->toHaveLength(16)
        ->and($customer->unique_code)->toMatch('/^BSB-[A-Z0-9]{12}$/');
});

test('customer unique codes do not repeat across many customers', function () {
    $codes = Customer::factory()->count(25)->create()->pluck('unique_code');

    expect($codes->unique())->toHaveCount(25)
        ->and(Customer::query()->distinct()->count('unique_code'))->toBe(25);
});

test('member code generation retries when the computed code is already taken', function () {
    // Simulate the race: a row with a higher code committed before ours, while the
    // newest row by id holds a lower number. The naive "last id + 1" therefore
    // computes CUST-0010, which already exists.
    DB::table('customers')->insert([
        ['id' => 1, 'member_code' => 'CUST-0010', 'name' => 'Taken', 'joined_at' => now()],
        ['id' => 2, 'member_code' => 'CUST-0009', 'name' => 'Newest', 'joined_at' => now()],
    ]);

    $customer = Customer::factory()->create();

    expect($customer->member_code)->toBe('CUST-0011')
        ->and(Customer::query()->count())->toBe(3);
});

test('a member code supplied by the caller is never silently renumbered', function () {
    Customer::factory()->create(['member_code' => 'CUST-0001']);

    // Without the guard in Customer::save(), the retry loop would bump this to
    // CUST-0002 and hide a genuine duplicate instead of reporting it.
    expect(fn () => Customer::factory()->create(['member_code' => 'CUST-0001']))
        ->toThrow(UniqueConstraintViolationException::class);

    expect(Customer::query()->count())->toBe(1);
});

test('a caller-supplied unique code survives a member code retry', function () {
    DB::table('customers')->insert([
        ['id' => 1, 'member_code' => 'CUST-0010', 'name' => 'Taken', 'joined_at' => now()],
        ['id' => 2, 'member_code' => 'CUST-0009', 'name' => 'Newest', 'joined_at' => now()],
    ]);

    $customer = Customer::factory()->create(['unique_code' => 'BSB-FIXEDFIXED12']);

    expect($customer->member_code)->toBe('CUST-0011')
        ->and($customer->unique_code)->toBe('BSB-FIXEDFIXED12');
});

test('deposit codes follow the DEP format and increment within a day', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 3000,
        'effective_from' => now()->subDay(),
    ]);

    $service = app(DepositService::class);
    $date = now()->format('Y-m-d');

    $payload = [
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => $date,
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 2]],
    ];

    $first = $service->createDeposit($payload);
    $second = $service->createDeposit($payload);

    $prefix = 'DEP-'.now()->format('Ymd').'-';

    expect($first->code)->toBe($prefix.'0001')
        ->and($second->code)->toBe($prefix.'0002');
});

test('withdrawal codes follow the WD format and increment within a day', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 5000,
        'effective_from' => now()->subDay(),
    ]);

    // Fund the account so the withdrawal passes the balance check.
    app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 10]],
    ]);

    $service = app(WithdrawalService::class);

    $payload = [
        'customer_id' => $customer->id,
        'amount' => 1000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $first = $service->createWithdrawal($payload, $user);
    $second = $service->createWithdrawal($payload, $user);

    $prefix = 'WD-'.now()->format('Ymd').'-';

    expect($first->code)->toBe($prefix.'0001')
        ->and($second->code)->toBe($prefix.'0002')
        ->and(Deposit::query()->count())->toBe(1);
});

test('deposit code generation retries when the computed code is already taken', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 3000,
        'effective_from' => now()->subDay(),
    ]);

    $prefix = 'DEP-'.now()->format('Ymd').'-';

    // Reproduce the race deterministically: right before the insert, a competing
    // request commits the very code we computed. The unique index then rejects our
    // insert, and the service must recompute instead of failing the whole deposit.
    $injected = false;

    Deposit::creating(function (Deposit $deposit) use (&$injected, $customer, $user) {
        if ($injected) {
            return;
        }

        $injected = true;

        DB::table('deposits')->insert([
            'code' => $deposit->code,
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'transaction_date' => now()->format('Y-m-d'),
            'total_amount' => 0,
            'total_weight' => 0,
            'status' => 'final',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    $deposit = app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 1]],
    ]);

    // The competing row kept 0001; our deposit moved on to 0002.
    expect($deposit->code)->toBe($prefix.'0002')
        ->and(Deposit::query()->count())->toBe(2)
        ->and(Deposit::query()->where('code', $prefix.'0001')->count())->toBe(1);
});
