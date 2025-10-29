<?php

use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin user
    $this->admin = User::factory()->create(['role' => 'admin']);

    // Create operator user
    $this->operator = User::factory()->create(['role' => 'operator']);
});
test('admin can view waste categories index', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('waste-categories.index'));

    $response->assertStatus(200);
    $response->assertSee($category->name);
});

test('operator can view waste categories index', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->operator)->get(route('waste-categories.index'));

    $response->assertStatus(200);
    $response->assertSee($category->name);
});

test('admin can create waste category', function () {
    $response = $this->actingAs($this->admin)->post(route('waste-categories.store'), [
        'name' => 'Plastik HDPE',
        'description' => 'High-Density Polyethylene',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('waste_categories', [
        'name' => 'Plastik HDPE',
        'description' => 'High-Density Polyethylene',
        'is_active' => true,
    ]);
});

test('operator cannot create waste category', function () {
    $response = $this->actingAs($this->operator)->post(route('waste-categories.store'), [
        'name' => 'Plastik HDPE',
        'description' => 'High-Density Polyethylene',
        'is_active' => true,
    ]);

    $response->assertStatus(403);
});

test('admin can update waste category', function () {
    $category = WasteCategory::factory()->create([
        'name' => 'Old Name',
    ]);

    $response = $this->actingAs($this->admin)->put(route('waste-categories.update', $category), [
        'name' => 'New Name',
        'description' => 'Updated description',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('waste_categories', [
        'id' => $category->id,
        'name' => 'New Name',
        'description' => 'Updated description',
    ]);
});

test('admin can deactivate waste category', function () {
    $category = WasteCategory::factory()->create([
        'name' => 'Active Category',
        'is_active' => true,
    ]);

    // Deactivate the category by sending is_active = 0
    $response = $this->actingAs($this->admin)->put(route('waste-categories.update', $category), [
        'name' => 'Active Category',
        'description' => 'Test deactivation',
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('success');

    // Verify category is now inactive
    $this->assertDatabaseHas('waste_categories', [
        'id' => $category->id,
        'is_active' => false,
    ]);

    // Verify the category is actually deactivated
    expect($category->fresh()->is_active)->toBeFalse();
});

test('admin can reactivate waste category', function () {
    $category = WasteCategory::factory()->create([
        'name' => 'Inactive Category',
        'is_active' => false,
    ]);

    // Reactivate the category by sending is_active = 1
    $response = $this->actingAs($this->admin)->put(route('waste-categories.update', $category), [
        'name' => 'Inactive Category',
        'description' => 'Test reactivation',
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('success');

    // Verify category is now active
    $this->assertDatabaseHas('waste_categories', [
        'id' => $category->id,
        'is_active' => true,
    ]);

    // Verify the category is actually activated
    expect($category->fresh()->is_active)->toBeTrue();
});

test('operator cannot update waste category', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->operator)->put(route('waste-categories.update', $category), [
        'name' => 'New Name',
        'is_active' => true,
    ]);

    $response->assertStatus(403);
});

test('admin can delete waste category', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route('waste-categories.destroy', $category));

    $response->assertRedirect(route('waste-categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('waste_categories', [
        'id' => $category->id,
    ]);
});
test('operator cannot delete waste category', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->operator)->delete(route('waste-categories.destroy', $category));

    $response->assertStatus(403);
});

test('admin can add price to waste category', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->admin)->post(route('waste-prices.store'), [
        'waste_category_id' => $category->id,
        'price_per_kg' => 5000,
        'effective_from' => '2025-11-01',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('waste_prices', [
        'waste_category_id' => $category->id,
        'price_per_kg' => 5000,
    ]);

    // Check the date separately (SQLite stores it with time)
    $price = WastePrice::where('waste_category_id', $category->id)->first();
    expect($price->effective_from->format('Y-m-d'))->toBe('2025-11-01');
});
test('operator cannot add price to waste category', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->operator)->post(route('waste-prices.store'), [
        'waste_category_id' => $category->id,
        'price_per_kg' => 5000,
        'effective_from' => '2025-11-01',
    ]);

    $response->assertStatus(403);
});

test('waste category shows current active price', function () {
    $category = WasteCategory::factory()->create();

    // Add multiple prices with different dates
    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 3000,
        'effective_from' => '2025-10-01',
    ]);

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 4000,
        'effective_from' => '2025-11-01',
    ]);

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 5000,
        'effective_from' => '2025-12-01',
    ]);

    // Mock today's date as 2025-11-15
    $this->travelTo(now()->parse('2025-11-15'));

    // Current price should be 4000 (effective from 2025-11-01)
    expect((int) $category->fresh()->current_price)->toBe(4000);
});

test('waste category validation requires name', function () {
    $response = $this->actingAs($this->admin)->post(route('waste-categories.store'), [
        'is_active' => true,
    ]);

    $response->assertSessionHasErrors('name');
});

test('waste price validation requires valid fields', function () {
    $category = WasteCategory::factory()->create();

    $response = $this->actingAs($this->admin)->post(route('waste-prices.store'), [
        'waste_category_id' => $category->id,
        // Missing price_per_kg and effective_from
    ]);

    $response->assertSessionHasErrors(['price_per_kg', 'effective_from']);
});
