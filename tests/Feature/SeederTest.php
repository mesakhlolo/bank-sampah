<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\Withdrawal;

/*
 * The full seeder used to wrap everything in `WithoutModelEvents`, which silently
 * disabled `Customer::creating`. That hook is the only thing assigning
 * `member_code` and `unique_code`, so `migrate:fresh --seed` crashed with
 * "NOT NULL constraint failed: customers.member_code" while the test suite stayed
 * green — no test exercised the seeder end to end.
 */
test('database seeder produces customers with generated codes', function () {
    $this->seed();

    expect(Customer::query()->count())->toBeGreaterThan(0)
        ->and(Customer::query()->whereNull('member_code')->count())->toBe(0)
        ->and(Customer::query()->whereNull('unique_code')->count())->toBe(0);

    // Codes are sequential with no gaps or duplicates.
    $codes = Customer::query()->orderBy('id')->pluck('member_code');

    expect($codes->unique())->toHaveCount($codes->count())
        ->and($codes->first())->toBe('CUST-0001')
        ->and($codes->last())->toBe('CUST-'.str_pad((string) $codes->count(), 4, '0', STR_PAD_LEFT));
});

test('database seeder writes a balanced ledger', function () {
    $this->seed();

    expect(Deposit::query()->count())->toBeGreaterThan(0)
        ->and(Withdrawal::query()->count())->toBeGreaterThan(0)
        // Every deposit credits the ledger and every withdrawal debits it.
        ->and(LedgerEntry::query()->where('direction', 'KREDIT')->count())
        ->toBe(Deposit::query()->count())
        ->and(LedgerEntry::query()->where('direction', 'DEBIT')->count())
        ->toBe(Withdrawal::query()->count());
});
