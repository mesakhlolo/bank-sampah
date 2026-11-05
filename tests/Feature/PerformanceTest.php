<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use App\Services\DepositService;
use App\Services\ReportService;
use App\Services\WithdrawalService;
use Illuminate\Support\Facades\DB;

/*
 * Fase 4 — performance regressions.
 *
 * These tests lock in behaviour that the optimisation work could easily break:
 * balance maths, running balances, and report numbers. They assert values, not
 * query counts, because query counts are a property of the ORM and change with
 * framework versions; value correctness is what actually matters.
 */

/**
 * Create a customer funded with a deposit.
 *
 * @return array{0: Customer, 1: Deposit, 2: WasteCategory}
 */
function fundCustomer(int $weightKg = 10, int $pricePerKg = 3000): array
{
    $user = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    // Effective from a year ago so tests can backdate deposits without hitting
    // "Tidak ada harga aktif untuk kategori". `getActivePriceOn()` picks the
    // newest price whose `effective_from` is <= the transaction date.
    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => $pricePerKg,
        'effective_from' => now()->subYear(),
    ]);

    $deposit = app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => $weightKg]],
    ]);

    return [$customer->fresh(), $deposit, $category];
}

test('balance is derived from the ledger with deposit as KREDIT', function () {
    [$customer] = fundCustomer(10, 3000);

    expect($customer->balance)->toBe(30000.0);

    // A withdrawal must reduce the balance, not increase it.
    $user = User::factory()->create(['role' => 'operator']);

    app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => $customer->id,
        'amount' => 12000,
        'transaction_date' => now()->format('Y-m-d'),
    ], $user);

    expect($customer->fresh()->balance)->toBe(18000.0);
});

test('balance is memoised per instance and refreshed on refresh()', function () {
    [$customer] = fundCustomer(5, 2000);

    expect($customer->balance)->toBe(10000.0);

    // Add a ledger entry behind the cached instance.
    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => 999,
        'direction' => 'KREDIT',
        'amount' => 5000,
    ]);

    // Memoised value is still the old one.
    expect($customer->balance)->toBe(10000.0);

    // refresh() clears the cache and picks up the new entry.
    expect($customer->refresh()->balance)->toBe(15000.0);
});

test('balance survives when eager loaded with the aggregate alias', function () {
    [$a] = fundCustomer(2, 3000);
    [$b] = fundCustomer(4, 3000);

    $loaded = Customer::query()
        ->withAggregate('ledgerEntries as ledger_sum_signed', LedgerEntry::BALANCE_SUM_SQL)
        ->orderBy('id')
        ->get();

    expect((float) $loaded->firstWhere('id', $a->id)->balance)->toBe(6000.0)
        ->and((float) $loaded->firstWhere('id', $b->id)->balance)->toBe(12000.0);
});

test('running balance is correct and ends at the current balance', function () {
    [$customer, , $category] = fundCustomer(10, 3000); // +30000

    $user = User::factory()->create(['role' => 'operator']);

    app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => $customer->id,
        'amount' => 5000,
        'transaction_date' => now()->format('Y-m-d'),
    ], $user);

    app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 2]],
    ]);

    $entries = $customer->ledgerWithRunningBalance()->get();

    // Chronological: +30000 (balance 30000), -5000 (25000), +6000 (31000).
    // Displayed newest first, each row showing the balance after that entry.
    $running = $entries->pluck('running_balance')->map(fn ($v) => round((float) $v, 2))->all();

    expect($running)->toBe([31000.0, 25000.0, 30000.0])
        ->and(round((float) $entries->first()->running_balance, 2))->toBe($customer->balance);
});

test('running balance ignores the type filter when accumulating', function () {
    [$customer] = fundCustomer(10, 3000); // +30000

    $user = User::factory()->create(['role' => 'operator']);

    app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => $customer->id,
        'amount' => 10000,
        'transaction_date' => now()->format('Y-m-d'),
    ], $user);

    // Filtering to deposits only must still show the balance after each deposit,
    // i.e. it must not silently ignore the withdrawal that happened between them.
    $depositsOnly = $customer->ledgerWithRunningBalance(Deposit::class)->get();

    expect($depositsOnly)->toHaveCount(1)
        ->and(round((float) $depositsOnly->first()->running_balance, 2))->toBe(30000.0);
});

test('report summary counts only final deposits and matches the ledger', function () {
    [$customer] = fundCustomer(10, 3000);

    $user = User::factory()->create(['role' => 'operator']);

    app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => $customer->id,
        'amount' => 5000,
        'transaction_date' => now()->format('Y-m-d'),
    ], $user);

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect((float) $summary['totalDeposits']->total_amount)->toBe(30000.0)
        ->and((int) $summary['totalDeposits']->count)->toBe(1)
        ->and((float) $summary['totalWithdrawals']->total_amount)->toBe(5000.0)
        ->and((int) $summary['totalWithdrawals']->count)->toBe(1)
        ->and((float) $summary['netBalanceChange'])->toBe(25000.0)
        ->and($summary['categorySummary'])->toHaveCount(1)
        ->and($summary['topCustomers'])->toHaveCount(1);

    // Net change must equal the customer's balance when there is a single customer.
    expect(round((float) $summary['netBalanceChange'], 2))->toBe($customer->balance);
});

test('report summary excludes transactions outside the period', function () {
    [$customer, , $category] = fundCustomer(10, 3000);

    $user = User::factory()->create(['role' => 'operator']);

    // A deposit well outside the reporting window.
    app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => now()->subMonths(3)->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 1]],
    ]);

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect((int) $summary['totalDeposits']->count)->toBe(1)
        ->and((float) $summary['totalDeposits']->total_amount)->toBe(30000.0);
});

test('date range boundaries include both first and last day', function () {
    [$customer, , $category] = fundCustomer(10, 3000);

    $user = User::factory()->create(['role' => 'operator']);
    $firstDay = now()->startOfMonth();
    $lastDay = now()->endOfMonth();

    // A deposit exactly on the first day and one exactly on the last day.
    foreach ([$firstDay, $lastDay] as $day) {
        app(DepositService::class)->createDeposit([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'transaction_date' => $day->format('Y-m-d'),
            'items' => [['waste_category_id' => $category->id, 'weight_kg' => 1]],
        ]);
    }

    $summary = app(ReportService::class)->buildSummary(
        $firstDay->format('Y-m-d'),
        $lastDay->format('Y-m-d'),
    );

    // 1 from fundCustomer + 2 boundary deposits = 3.
    expect((int) $summary['totalDeposits']->count)->toBe(3);
});

test('a single-day range includes transactions on that day', function () {
    [$customer, , $category] = fundCustomer(10, 3000);

    $user = User::factory()->create(['role' => 'operator']);
    $day = now()->startOfMonth();

    app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $user->id,
        'transaction_date' => $day->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 1]],
    ]);

    // Regression: `transaction_date` is cast to datetime, so it is stored as
    // 'Y-m-d 00:00:00'. Comparing against a bare 'Y-m-d' end bound excluded the
    // whole day. See App\Support\DateRange.
    $summary = app(ReportService::class)->buildSummary(
        $day->format('Y-m-d'),
        $day->format('Y-m-d'),
    );

    expect((int) $summary['totalDeposits']->count)->toBe(1)
        ->and((float) $summary['totalDeposits']->total_amount)->toBe(3000.0);
});

test('dashboard aggregates today using a timestamp range', function () {
    [$customer, , $category] = fundCustomer(10, 3000);

    // The dashboard reports today's *activity*, which is keyed on `created_at`,
    // not on `transaction_date`. Backdate `created_at` on a second deposit to
    // prove it falls outside the window.
    $yesterday = app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => User::where('role', 'operator')->first()->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => [['waste_category_id' => $category->id, 'weight_kg' => 1]],
    ]);

    Deposit::whereKey($yesterday->id)->update([
        'created_at' => now()->subDay()->startOfDay(),
    ]);

    $admin = User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();

    // Only today's deposit counts, not the one created yesterday.
    expect((float) $response->viewData('todayDeposits'))->toBe(30000.0);
});

test('withdrawal create page shows member codes and ledger balances', function () {
    [$customer] = fundCustomer(10, 3000);

    $operator = User::factory()->create(['role' => 'operator']);

    $response = $this->actingAs($operator)->get(route('withdrawals.create'));

    $response->assertOk();

    $customers = collect($response->viewData('customers'));
    $row = $customers->firstWhere('id', $customer->id);

    // `code` used to read a non-existent `customers.code` column and came back null.
    expect($row)->not->toBeNull()
        ->and($row['code'])->toBe($customer->member_code)
        ->and($row['balance'])->toBe(30000.0);
});

test('transaction history page renders and resolves relations', function () {
    [$customer] = fundCustomer(10, 3000);

    $admin = User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('transactions.index'));

    $response->assertOk();

    $transactions = $response->viewData('transactions');

    expect($transactions->count())->toBe(1)
        ->and($transactions->first()->customer->id)->toBe($customer->id)
        ->and($transactions->first()->creator)->not->toBeNull();
});

test('report summary page renders with totals', function () {
    [$customer] = fundCustomer(10, 3000);

    $admin = User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('reports.summary'))
        ->assertOk()
        ->assertSee($customer->name);
});

test('ledger page shows a running balance column', function () {
    [$customer] = fundCustomer(10, 3000);

    $admin = User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('ledger.show', $customer));

    $response->assertOk();

    $entries = $response->viewData('ledgerWithBalance');

    expect($entries)->toHaveCount(1)
        ->and(round((float) $entries->first()->running_balance, 2))->toBe(30000.0);
});

test('portal transactions page shows a running balance', function () {
    [$customer] = fundCustomer(10, 3000);

    $response = $this->withSession([
        'portal_customer_id' => $customer->id,
        'portal_login_time' => now(),
        'portal_ip' => '127.0.0.1',
    ])->get(route('customer-portal.transactions'));

    $response->assertOk();

    $entries = $response->viewData('ledgerEntries');

    expect(round((float) $entries->first()->running_balance, 2))->toBe(30000.0);
});

test('customers index does not issue one query per customer', function () {
    // Five customers, each with a ledger entry. Without the aggregate this page
    // ran one ledger query per row.
    foreach (range(1, 5) as $i) {
        fundCustomer(1, 1000);
    }

    $admin = User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $queries = [];
    DB::listen(function ($q) use (&$queries) {
        $queries[] = $q->sql;
    });

    $this->actingAs($admin)->get(route('customers.index'))->assertOk();

    $ledgerQueries = array_filter(
        $queries,
        fn ($sql) => str_contains($sql, 'ledger_entries')
    );

    // One aggregate sub-select, not one per customer.
    expect(count($ledgerQueries))->toBe(1);
});
