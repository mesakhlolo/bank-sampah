<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use App\Models\Withdrawal;
use App\Services\DepositService;
use App\Services\WithdrawalService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/*
 * Module 5 — balance integrity and data-scale regressions.
 */

/**
 * Give a customer ledger balance through a final deposit plus its KREDIT entry.
 */
function fundLedgerBalance(Customer $customer, User $user, float $amount): Deposit
{
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'total_amount' => $amount,
        'total_weight' => 0,
        'status' => 'final',
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => $amount,
    ]);

    return $deposit;
}

/**
 * Create a category with a price that has been active since last year.
 */
function categoryWithPrice(float $pricePerKg): WasteCategory
{
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => $pricePerKg,
        'effective_from' => now()->subYear(),
    ]);

    return $category;
}

test('competing withdrawal attempts never leave a negative ledger balance', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();

    fundLedgerBalance($customer, $operator, 10000);

    $service = app(WithdrawalService::class);
    $payload = [
        'customer_id' => $customer->id,
        'amount' => 6000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    // First request wins.
    $service->createWithdrawal($payload, $operator);

    // The second would overdraw; it must be rejected by the service itself, not
    // only by the Form Request that normally runs before it.
    expect(fn () => $service->createWithdrawal($payload, $operator))
        ->toThrow(RuntimeException::class, 'Saldo tidak mencukupi');

    $balance = LedgerEntry::query()
        ->where('customer_id', $customer->id)
        ->selectRaw(LedgerEntry::BALANCE_SUM_SQL.' as balance')
        ->value('balance');

    expect((float) $balance)->toBe(4000.0)
        ->and($customer->fresh()->balance)->toBe(4000.0)
        ->and(Withdrawal::where('customer_id', $customer->id)->count())->toBe(1)
        ->and(LedgerEntry::where('customer_id', $customer->id)->where('direction', 'DEBIT')->count())->toBe(1);
});

test('the reconciliation command succeeds when ledger and transactions agree', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();

    fundLedgerBalance($customer, $operator, 50000);

    app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => $customer->id,
        'amount' => 20000,
        'transaction_date' => now()->format('Y-m-d'),
    ], $operator);

    $this->artisan('balances:reconcile')
        ->expectsOutputToContain('seimbang')
        ->assertSuccessful();
});

test('the reconciliation command fails when the ledger does not match transactions', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();

    // A final deposit with no matching ledger entry is exactly the drift the
    // command must surface.
    Deposit::factory()->create([
        'customer_id' => $customer->id,
        'created_by' => $operator->id,
        'total_amount' => 10000,
        'status' => 'final',
    ]);

    $this->artisan('balances:reconcile')
        ->expectsOutputToContain($customer->member_code)
        ->assertFailed();
});

test('the reconciliation command flags negative ledger balances', function () {
    $customer = Customer::factory()->create();

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Withdrawal::class,
        'ref_id' => 999,
        'direction' => 'DEBIT',
        'amount' => 5000,
    ]);

    $this->artisan('balances:reconcile')
        ->expectsOutputToContain('saldo negatif')
        ->assertFailed();
});

test('the ledger page paginates long histories', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();

    foreach (range(1, 25) as $index) {
        LedgerEntry::create([
            'customer_id' => $customer->id,
            'ref_type' => Deposit::class,
            'ref_id' => $index,
            'direction' => 'KREDIT',
            'amount' => 1000,
        ]);
    }

    $response = $this->actingAs($admin)->get(route('ledger.show', $customer));

    $response->assertOk();

    $paginator = $response->viewData('ledgerWithBalance');

    expect($paginator)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($paginator->total())->toBe(25)
        ->and($paginator->perPage())->toBe(20)
        ->and($paginator->count())->toBe(20)
        ->and($paginator->lastPage())->toBe(2);
});

test('the current price accessor reads eager loaded prices without another query', function () {
    $category = categoryWithPrice(3000);

    $loaded = WasteCategory::with('prices')->findOrFail($category->id);

    $queries = [];
    DB::listen(function ($query) use (&$queries): void {
        $queries[] = $query->sql;
    });

    expect((float) $loaded->current_price)->toBe(3000.0)
        ->and($queries)->toBeEmpty();
});

test('deposit creation loads category prices once for all items', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();

    $categories = collect([
        categoryWithPrice(1000),
        categoryWithPrice(2000),
        categoryWithPrice(3000),
    ]);

    $items = $categories->map(fn (WasteCategory $category) => [
        'waste_category_id' => $category->id,
        'weight_kg' => 2,
    ])->all();

    $priceQueries = [];
    DB::listen(function ($query) use (&$priceQueries): void {
        $sql = strtolower($query->sql);

        if (str_starts_with($sql, 'select') && str_contains($sql, 'waste_prices')) {
            $priceQueries[] = $query->sql;
        }
    });

    $deposit = app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $operator->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => $items,
    ]);

    // 2 kg * (1000 + 2000 + 3000) with prices snapshotted per item.
    expect((float) $deposit->total_amount)->toBe(12000.0)
        ->and($deposit->items)->toHaveCount(3)
        ->and($priceQueries)->toHaveCount(1);
});
