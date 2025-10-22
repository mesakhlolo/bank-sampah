<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use App\Services\DepositService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->operator = User::factory()->create(['role' => 'operator']);
    $this->customer = Customer::factory()->create();

    // Create waste categories with prices
    $this->plastikCategory = WasteCategory::factory()->create(['name' => 'Plastik PET']);
    WastePrice::create([
        'waste_category_id' => $this->plastikCategory->id,
        'price_per_kg' => 3000,
        'effective_from' => now()->subDays(10),
    ]);

    $this->kertasCategory = WasteCategory::factory()->create(['name' => 'Kertas']);
    WastePrice::create([
        'waste_category_id' => $this->kertasCategory->id,
        'price_per_kg' => 1500,
        'effective_from' => now()->subDays(10),
    ]);
});

test('admin can view deposits index', function () {
    $deposit = Deposit::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('deposits.index'));

    $response->assertStatus(200);
    $response->assertSee($deposit->code);
});

test('operator can view deposits index', function () {
    $deposit = Deposit::factory()->create();

    $response = $this->actingAs($this->operator)->get(route('deposits.index'));

    $response->assertStatus(200);
    $response->assertSee($deposit->code);
});

test('admin can create deposit', function () {
    $response = $this->actingAs($this->admin)->post(route('deposits.store'), [
        'customer_id' => $this->customer->id,
        'transaction_date' => now()->format('Y-m-d'),
        'notes' => 'Test deposit',
        'items' => [
            [
                'waste_category_id' => $this->plastikCategory->id,
                'weight_kg' => 5.5,
            ],
            [
                'waste_category_id' => $this->kertasCategory->id,
                'weight_kg' => 3.0,
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check deposit was created
    $this->assertDatabaseHas('deposits', [
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
    ]);

    // Check deposit items were created
    $deposit = Deposit::latest()->first();
    expect($deposit->items)->toHaveCount(2);

    // Check total calculation
    $expectedTotal = (5.5 * 3000) + (3.0 * 1500); // 16500 + 4500 = 21000
    expect((float) $deposit->total_amount)->toBe((float) $expectedTotal);
});

test('operator can create deposit', function () {
    $response = $this->actingAs($this->operator)->post(route('deposits.store'), [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'waste_category_id' => $this->plastikCategory->id,
                'weight_kg' => 2.0,
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('deposit code is generated correctly', function () {
    $depositService = new DepositService;

    $deposit1 = $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 1.0],
        ],
    ]);

    $deposit2 = $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 1.0],
        ],
    ]);

    // Check format DEP-YYYYMMDD-####
    expect($deposit1->code)->toMatch('/^DEP-\d{8}-\d{4}$/');
    expect($deposit2->code)->toMatch('/^DEP-\d{8}-\d{4}$/');

    // Check sequential numbering
    expect($deposit2->code)->not()->toBe($deposit1->code);

    $today = now()->format('Ymd');
    expect($deposit1->code)->toStartWith("DEP-{$today}-");
    expect($deposit2->code)->toStartWith("DEP-{$today}-");
});

test('deposit creates ledger entry automatically', function () {
    $depositService = new DepositService;

    $deposit = $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 2.0],
        ],
    ]);

    // Check ledger entry was created
    $ledgerEntry = LedgerEntry::where('ref_type', 'App\\Models\\Deposit')
        ->where('ref_id', $deposit->id)
        ->first();

    expect($ledgerEntry)->not()->toBeNull();
    expect($ledgerEntry->customer_id)->toBe($this->customer->id);
    expect($ledgerEntry->direction)->toBe('KREDIT');
    expect((int) $ledgerEntry->amount)->toBe(6000); // 2kg * 3000
});

test('customer balance updates after deposit', function () {
    $depositService = new DepositService;

    $initialBalance = $this->customer->balance;

    $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 5.0],
        ],
    ]);

    $newBalance = $this->customer->fresh()->balance;

    expect($newBalance)->toBe($initialBalance + 15000); // 5kg * 3000
});

test('deposit validation requires customer', function () {
    $response = $this->actingAs($this->admin)->post(route('deposits.store'), [
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 1.0],
        ],
    ]);

    $response->assertSessionHasErrors('customer_id');
});

test('deposit validation requires at least one item', function () {
    $response = $this->actingAs($this->admin)->post(route('deposits.store'), [
        'customer_id' => $this->customer->id,
        'items' => [],
    ]);

    $response->assertSessionHasErrors('items');
});

test('deposit validation requires valid waste category', function () {
    $response = $this->actingAs($this->admin)->post(route('deposits.store'), [
        'customer_id' => $this->customer->id,
        'items' => [
            ['waste_category_id' => 99999, 'weight_kg' => 1.0],
        ],
    ]);

    $response->assertSessionHasErrors('items.0.waste_category_id');
});

test('deposit validation requires positive weight', function () {
    $response = $this->actingAs($this->admin)->post(route('deposits.store'), [
        'customer_id' => $this->customer->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 0],
        ],
    ]);

    $response->assertSessionHasErrors('items.0.weight_kg');
});

test('deposit uses correct price based on date', function () {
    // Add new price effective today
    WastePrice::create([
        'waste_category_id' => $this->plastikCategory->id,
        'price_per_kg' => 5000,
        'effective_from' => now(),
    ]);

    $depositService = new DepositService;

    $deposit = $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 1.0],
        ],
    ]);

    // Should use the new price (5000) not the old one (3000)
    expect((int) $deposit->total_amount)->toBe(5000);
    expect((int) $deposit->items->first()->price_per_kg)->toBe(5000);
});

test('deposit service calculates total without saving', function () {
    $depositService = new DepositService;

    $result = $depositService->calculateDepositTotal([
        ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 2.5],
        ['waste_category_id' => $this->kertasCategory->id, 'weight_kg' => 4.0],
    ]);

    $expectedTotal = (2.5 * 3000) + (4.0 * 1500); // 7500 + 6000 = 13500

    expect($result['total'])->toBe($expectedTotal);
    expect($result['items'])->toHaveCount(2);
});

test('admin can view deposit detail', function () {
    $deposit = Deposit::factory()->create([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('deposits.show', $deposit));

    $response->assertStatus(200);
    $response->assertSee($deposit->code);
    $response->assertSee($this->customer->name);
});

test('operator can view deposit detail', function () {
    $deposit = Deposit::factory()->create();

    $response = $this->actingAs($this->operator)->get(route('deposits.show', $deposit));

    $response->assertStatus(200);
});

test('deposit pdf can be downloaded', function () {
    $deposit = Deposit::factory()->create([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('deposits.download-pdf', $deposit));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('multiple deposits update customer balance correctly', function () {
    $depositService = new DepositService;

    $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 2.0],
        ],
    ]);

    $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->kertasCategory->id, 'weight_kg' => 5.0],
        ],
    ]);

    $depositService->createDeposit([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'items' => [
            ['waste_category_id' => $this->plastikCategory->id, 'weight_kg' => 1.0],
        ],
    ]);

    $expectedBalance = (2.0 * 3000) + (5.0 * 1500) + (1.0 * 3000); // 6000 + 7500 + 3000 = 16500

    expect($this->customer->fresh()->balance)->toBe($expectedBalance);
});
