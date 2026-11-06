<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Support\Facades\Route;

/*
 * Module 2 — regression coverage for broken pages and missing validation.
 */

test('the deposit create page shows member codes instead of a missing code attribute', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();

    $this->actingAs($admin)
        ->get(route('deposits.create'))
        ->assertOk()
        ->assertSee($customer->member_code)
        ->assertDontSee($customer->unique_code);
});

test('the customer show page renders ledger descriptions and pushed scripts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => 999,
        'direction' => 'KREDIT',
        'amount' => 5000,
    ]);

    $this->actingAs($admin)
        ->get(route('customers.show', $customer))
        ->assertOk()
        ->assertSee('Setoran sampah')
        ->assertSee('Rp 5.000')
        ->assertSee('function copyToClipboard', false);
});

test('the portal deposit detail uses the real code and weight attributes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    $deposit = Deposit::create([
        'code' => 'DEP-20260101-0001',
        'customer_id' => $customer->id,
        'transaction_date' => now(),
        'total_amount' => 10000,
        'total_weight' => 5,
        'status' => 'final',
        'created_by' => $admin->id,
    ]);

    $deposit->items()->create([
        'waste_category_id' => $category->id,
        'weight_kg' => 5,
        'price_per_kg' => 2000,
        'subtotal' => 10000,
    ]);

    $this->withSession([
        'portal_customer_id' => $customer->id,
        'portal_login_time' => now(),
        'portal_ip' => '127.0.0.1',
    ])
        ->get(route('customer-portal.deposit-detail', $deposit))
        ->assertOk()
        ->assertSee('DEP-20260101-0001')
        ->assertSee('5.00');
});

test('the waste category show route is no longer registered', function () {
    expect(Route::has('waste-categories.show'))->toBeFalse();
});

test('category price is validated before the category is created', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('waste-categories.store'), [
            'name' => 'Plastik Uji',
            'is_active' => 1,
            'price_per_kg' => 'not-a-number',
            'effective_from' => now()->format('Y-m-d'),
        ])
        ->assertSessionHasErrors('price_per_kg');

    $this->assertDatabaseMissing('waste_categories', ['name' => 'Plastik Uji']);
});

test('an effective date is required when a price is supplied', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('waste-categories.store'), [
            'name' => 'Plastik Uji',
            'is_active' => 1,
            'price_per_kg' => 3000,
        ])
        ->assertSessionHasErrors('effective_from');

    $this->assertDatabaseMissing('waste_categories', ['name' => 'Plastik Uji']);
});

test('category and initial price are saved together', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('waste-categories.store'), [
            'name' => 'Plastik Uji',
            'is_active' => 1,
            'price_per_kg' => 3000,
            'effective_from' => '2026-01-01',
        ])
        ->assertSessionHasNoErrors();

    $category = WasteCategory::where('name', 'Plastik Uji')->firstOrFail();

    expect($category->prices()->count())->toBe(1)
        ->and((float) $category->prices()->first()->price_per_kg)->toBe(3000.0);
});

test('report filters reject invalid dates', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('reports.summary', ['start_date' => '31-12-2026']))
        ->assertSessionHasErrors('start_date');

    $this->actingAs($admin)
        ->get(route('reports.summary', [
            'start_date' => '2026-02-01',
            'end_date' => '2026-01-01',
        ]))
        ->assertSessionHasErrors('end_date');

    $this->actingAs($admin)
        ->get(route('reports.summary', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]))
        ->assertOk();
});

test('flash messages render through the shared component', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->withSession([
            'success' => 'Pesan berhasil',
            'error' => 'Pesan gagal',
        ])
        ->get(route('customers.index'))
        ->assertSee('Pesan berhasil')
        ->assertSee('Pesan gagal');
});
