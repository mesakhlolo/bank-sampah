<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\Withdrawal;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->operator = User::factory()->create(['role' => 'operator']);
});

test('operator can access withdrawal create page', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($this->operator)
        ->get(route('withdrawals.create'))
        ->assertOk()
        ->assertViewIs('withdrawals.create');
});

test('operator can create withdrawal within balance', function () {
    $customer = Customer::factory()->create();

    // Create a deposit first to give customer some balance
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 50000,
        'status' => 'final',
        'created_by' => $this->operator->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 50000,
    ]);

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 20000,
        'transaction_date' => now()->format('Y-m-d'),
        'note' => 'Penarikan saldo',
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('withdrawals', [
        'customer_id' => $customer->id,
        'amount' => 20000,
        'created_by' => $this->operator->id,
    ]);
});

test('cannot create withdrawal exceeding balance', function () {
    $customer = Customer::factory()->create();

    // Create a deposit first
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 10000,
        'status' => 'final',
        'created_by' => $this->operator->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 10000,
    ]);

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 15000, // Exceeds balance
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertSessionHasErrors('amount');

    $this->assertDatabaseMissing('withdrawals', [
        'customer_id' => $customer->id,
        'amount' => 15000,
    ]);
});

test('withdrawal creates ledger entry with DEBIT direction', function () {
    $customer = Customer::factory()->create();

    // Give customer balance
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 50000,
        'status' => 'final',
        'created_by' => $this->operator->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 50000,
    ]);

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 20000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData);

    $withdrawal = Withdrawal::where('customer_id', $customer->id)->first();

    expect($withdrawal)->not->toBeNull();

    $this->assertDatabaseHas('ledger_entries', [
        'customer_id' => $customer->id,
        'ref_type' => Withdrawal::class,
        'ref_id' => $withdrawal->id,
        'direction' => 'DEBIT',
        'amount' => 20000,
    ]);
});

test('withdrawal updates customer balance correctly', function () {
    $customer = Customer::factory()->create();

    // Give customer initial balance
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 50000,
        'status' => 'final',
        'created_by' => $this->operator->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 50000,
    ]);

    // Fresh customer to get updated balance
    $customer->refresh();
    expect($customer->balance)->toBe(50000.0);

    // Create withdrawal
    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 20000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData);

    // Check balance after withdrawal
    $customer->refresh();
    expect($customer->balance)->toBe(30000.0); // 50000 - 20000
});

test('withdrawal generates unique code automatically', function () {
    $customer = Customer::factory()->create();

    // Give customer balance
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 100000,
        'status' => 'final',
        'created_by' => $this->operator->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 100000,
    ]);

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 10000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData);

    $withdrawal = Withdrawal::where('customer_id', $customer->id)->first();

    expect($withdrawal->code)
        ->toStartWith('WD-')
        ->toContain(now()->format('Ymd'));
});

test('multiple withdrawals on same day have sequential codes', function () {
    $customer = Customer::factory()->create();

    // Give customer large balance
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 200000,
        'status' => 'final',
        'created_by' => $this->operator->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 200000,
    ]);

    // Create first withdrawal
    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), [
            'customer_id' => $customer->id,
            'amount' => 10000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

    // Create second withdrawal
    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), [
            'customer_id' => $customer->id,
            'amount' => 15000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

    $withdrawals = Withdrawal::orderBy('id')->get();

    expect($withdrawals)->toHaveCount(2);
    expect($withdrawals[0]->code)->toEndWith('0001');
    expect($withdrawals[1]->code)->toEndWith('0002');
});

test('can view withdrawal detail', function () {
    $customer = Customer::factory()->create();

    $withdrawal = Withdrawal::factory()->create([
        'customer_id' => $customer->id,
        'amount' => 25000,
        'created_by' => $this->operator->id,
    ]);

    $this->actingAs($this->operator)
        ->get(route('withdrawals.show', $withdrawal))
        ->assertOk()
        ->assertViewIs('withdrawals.show')
        ->assertViewHas('withdrawal');
});

test('can download withdrawal receipt as PDF', function () {
    $customer = Customer::factory()->create();

    $withdrawal = Withdrawal::factory()->create([
        'customer_id' => $customer->id,
        'amount' => 25000,
        'created_by' => $this->operator->id,
    ]);

    $response = $this->actingAs($this->operator)
        ->get(route('withdrawals.download-pdf', $withdrawal));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('withdrawal requires valid customer', function () {
    $withdrawalData = [
        'customer_id' => 9999, // Non-existent
        'amount' => 10000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertSessionHasErrors('customer_id');
});

test('withdrawal requires positive amount', function () {
    $customer = Customer::factory()->create();

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 0,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertSessionHasErrors('amount');
});

test('withdrawal requires transaction date', function () {
    $customer = Customer::factory()->create();

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 10000,
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertSessionHasErrors('transaction_date');
});

test('admin can create withdrawal', function () {
    $customer = Customer::factory()->create();

    // Give customer balance
    $deposit = Deposit::factory()->create([
        'customer_id' => $customer->id,
        'total_amount' => 50000,
        'status' => 'final',
        'created_by' => $this->admin->id,
    ]);

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => $deposit->id,
        'direction' => 'KREDIT',
        'amount' => 50000,
    ]);

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 20000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->admin)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertRedirect()
        ->assertSessionHas('success');
});

test('cannot create withdrawal for customer with zero balance', function () {
    $customer = Customer::factory()->create();
    // No deposits, so balance is 0

    $withdrawalData = [
        'customer_id' => $customer->id,
        'amount' => 10000,
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($this->operator)
        ->post(route('withdrawals.store'), $withdrawalData)
        ->assertSessionHasErrors('amount');
});
