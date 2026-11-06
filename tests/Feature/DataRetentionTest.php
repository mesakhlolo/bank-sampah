<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

/*
 * Module 4 — financial history retention, price uniqueness and role gating.
 */

test('admin cannot delete a customer with ledger history', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => 1,
        'direction' => 'KREDIT',
        'amount' => 5000,
    ]);

    $response = $this->actingAs($admin)->delete(route('customers.destroy', $customer));

    $response->assertRedirect(route('customers.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('customers', ['id' => $customer->id]);
});

test('admin can delete a customer without transactions', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();

    $response = $this->actingAs($admin)->delete(route('customers.destroy', $customer));

    $response->assertRedirect(route('customers.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

test('operator cannot delete a customer even when it has no transactions', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();

    $this->actingAs($operator)
        ->delete(route('customers.destroy', $customer))
        ->assertForbidden();

    $this->assertDatabaseHas('customers', ['id' => $customer->id]);
});

test('admin cannot delete a category used by a deposit item', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    $deposit = Deposit::create([
        'code' => 'DEP-20260101-0001',
        'customer_id' => $customer->id,
        'transaction_date' => now(),
        'total_amount' => 5000,
        'total_weight' => 2,
        'status' => 'final',
        'created_by' => $admin->id,
    ]);

    $deposit->items()->create([
        'waste_category_id' => $category->id,
        'weight_kg' => 2,
        'price_per_kg' => 2500,
        'subtotal' => 5000,
    ]);

    $response = $this->actingAs($admin)->delete(route('waste-categories.destroy', $category));

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('waste_categories', ['id' => $category->id]);
});

test('admin can delete an unused category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($admin)->delete(route('waste-categories.destroy', $category));

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('waste_categories', ['id' => $category->id]);
});

test('database rejects deleting a customer that still owns transactions', function () {
    $customer = Customer::factory()->create();

    LedgerEntry::create([
        'customer_id' => $customer->id,
        'ref_type' => Deposit::class,
        'ref_id' => 1,
        'direction' => 'KREDIT',
        'amount' => 5000,
    ]);

    expect(fn () => $customer->delete())->toThrow(QueryException::class);
});

test('duplicate price dates are rejected by validation', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 3000,
        'effective_from' => '2025-11-01',
    ]);

    $response = $this->actingAs($admin)->post(route('waste-prices.store'), [
        'waste_category_id' => $category->id,
        'price_per_kg' => 4000,
        'effective_from' => '2025-11-01',
    ]);

    $response->assertSessionHasErrors('effective_from');
    expect($category->prices()->count())->toBe(1);
});

test('updating a category with a duplicate price date is rejected', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 3000,
        'effective_from' => '2025-11-01',
    ]);

    $response = $this->actingAs($admin)->put(route('waste-categories.update', $category), [
        'name' => $category->name,
        'is_active' => 1,
        'price_per_kg' => 4000,
        'effective_from' => '2025-11-01',
    ]);

    $response->assertSessionHasErrors('effective_from');
    expect($category->fresh()->prices()->count())->toBe(1);
});

test('the database enforces the unique price date index', function () {
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 3000,
        'effective_from' => '2025-11-01',
    ]);

    expect(fn () => WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 4000,
        'effective_from' => '2025-11-01',
    ]))->toThrow(UniqueConstraintViolationException::class);
});

test('an inactive user cannot log in', function () {
    $user = User::factory()->create(['is_active' => false]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('an admin can create an inactive user account', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/register', [
        'name' => 'Non Aktif',
        'email' => 'inactive@example.com',
        'role' => 'operator',
        'is_active' => 0,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'inactive@example.com',
        'is_active' => false,
    ]);
});
