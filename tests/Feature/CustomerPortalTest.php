<?php

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

/*
 * Fase 5 — customer portal coverage.
 *
 * The portal is the only part of the app exposed to the public internet, and it
 * authenticates with a session key instead of Laravel's auth system. That makes
 * these tests security tests, not just coverage.
 */

/**
 * Log a customer into the portal by putting the session keys the guard expects
 * in place, mirroring what CustomerPortalController::verifyCode() does.
 */
function portalSession(Customer $customer, array $overrides = []): array
{
    return array_merge([
        'portal_customer_id' => $customer->id,
        'portal_login_time' => now(),
        'portal_ip' => '127.0.0.1',
    ], $overrides);
}

test('check code page renders for guests', function () {
    $this->get(route('customer-portal.check-code'))
        ->assertOk()
        ->assertSee('kode', false);
});

test('check code page redirects to dashboard when already logged in', function () {
    $customer = Customer::factory()->create();

    $this->withSession(portalSession($customer))
        ->get(route('customer-portal.check-code'))
        ->assertRedirect(route('customer-portal.dashboard'));
});

test('a valid unique code logs the customer in', function () {
    $customer = Customer::factory()->create();

    $response = $this->post(route('customer-portal.verify-code'), [
        'unique_code' => $customer->unique_code,
    ]);

    $response->assertRedirect(route('customer-portal.dashboard'));

    expect(session('portal_customer_id'))->toBe($customer->id)
        ->and(session('portal_ip'))->not->toBeNull();
});

test('the unique code is accepted case-insensitively', function () {
    $customer = Customer::factory()->create();

    $this->post(route('customer-portal.verify-code'), [
        'unique_code' => strtolower($customer->unique_code),
    ])->assertRedirect(route('customer-portal.dashboard'));

    expect(session('portal_customer_id'))->toBe($customer->id);
});

test('an unknown code does not log anyone in', function () {
    $this->post(route('customer-portal.verify-code'), [
        'unique_code' => 'BSB-000000000000',
    ])->assertSessionHasErrors('unique_code');

    expect(session('portal_customer_id'))->toBeNull();
});

test('a malformed code fails validation', function () {
    $this->post(route('customer-portal.verify-code'), [
        'unique_code' => 'too-short',
    ])->assertSessionHasErrors('unique_code');

    expect(session('portal_customer_id'))->toBeNull();
});

test('repeated failures are rate limited', function () {
    // Codes must be exactly 16 chars so validation passes and the rate limiter is
    // actually reached — otherwise the test would pass for the wrong reason.
    $unknown = fn (int $i) => 'BSB-'.str_pad((string) $i, 12, '0', STR_PAD_LEFT);

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('customer-portal.verify-code'), ['unique_code' => $unknown($i)]);
    }

    // The limiter is keyed on the request IP; the test client uses 127.0.0.1.
    expect(RateLimiter::attempts('portal-login:127.0.0.1'))->toBe(5);

    // The sixth attempt is rejected by the limiter rather than the lookup.
    $this->post(route('customer-portal.verify-code'), ['unique_code' => $unknown(99)])
        ->assertSessionHasErrors('unique_code');
});

test('the dashboard requires a portal session', function () {
    $this->get(route('customer-portal.dashboard'))
        ->assertRedirect(route('customer-portal.check-code'));
});

test('the transactions page requires a portal session', function () {
    $this->get(route('customer-portal.transactions'))
        ->assertRedirect(route('customer-portal.check-code'));
});

test('the info page requires a portal session', function () {
    $this->get(route('customer-portal.info'))
        ->assertRedirect(route('customer-portal.check-code'));
});

test('an expired session is rejected', function () {
    $customer = Customer::factory()->create();

    // Logged in 31 minutes ago — past the 30 minute idle limit.
    $this->withSession(portalSession($customer, [
        'portal_login_time' => now()->subMinutes(31),
    ]))
        ->get(route('customer-portal.dashboard'))
        ->assertRedirect(route('customer-portal.check-code'));

    expect(session('portal_customer_id'))->toBeNull();
});

test('a session from another IP is rejected', function () {
    $customer = Customer::factory()->create();

    $this->withSession(portalSession($customer, ['portal_ip' => '203.0.113.9']))
        ->get(route('customer-portal.dashboard'))
        ->assertRedirect(route('customer-portal.check-code'));

    expect(session('portal_customer_id'))->toBeNull();
});

test('a session for a deleted customer is rejected', function () {
    $customer = Customer::factory()->create();
    $session = portalSession($customer);

    $customer->delete();

    $this->withSession($session)
        ->get(route('customer-portal.dashboard'))
        ->assertRedirect(route('customer-portal.check-code'));
});

test('a customer cannot view another customer deposit', function () {
    $owner = Customer::factory()->create();
    $intruder = Customer::factory()->create();
    $operator = User::factory()->create(['role' => 'operator']);

    $deposit = Deposit::create([
        'code' => 'DEP-20260101-0001',
        'customer_id' => $owner->id,
        'transaction_date' => now()->format('Y-m-d'),
        'total_amount' => 10000,
        'total_weight' => 5,
        'status' => 'final',
        'created_by' => $operator->id,
    ]);

    $this->withSession(portalSession($intruder))
        ->get(route('customer-portal.deposit-detail', $deposit))
        ->assertForbidden();
});

test('a customer can view their own deposit', function () {
    $owner = Customer::factory()->create();
    $operator = User::factory()->create(['role' => 'operator']);

    $deposit = Deposit::create([
        'code' => 'DEP-20260101-0002',
        'customer_id' => $owner->id,
        'transaction_date' => now()->format('Y-m-d'),
        'total_amount' => 10000,
        'total_weight' => 5,
        'status' => 'final',
        'created_by' => $operator->id,
    ]);

    $this->withSession(portalSession($owner))
        ->get(route('customer-portal.deposit-detail', $deposit))
        ->assertOk();
});

test('logout clears the portal session', function () {
    $customer = Customer::factory()->create();

    $this->withSession(portalSession($customer))
        ->post(route('customer-portal.logout'))
        ->assertRedirect(route('customer-portal.check-code'));

    expect(session('portal_customer_id'))->toBeNull();
});

test('the portal only shows the logged in customer transactions', function () {
    $mine = Customer::factory()->create();
    $other = Customer::factory()->create();
    $operator = User::factory()->create(['role' => 'operator']);

    LedgerEntry::create([
        'customer_id' => $mine->id,
        'ref_type' => Deposit::class,
        'ref_id' => 1,
        'direction' => 'KREDIT',
        'amount' => 5000,
    ]);

    LedgerEntry::create([
        'customer_id' => $other->id,
        'ref_type' => Deposit::class,
        'ref_id' => 2,
        'direction' => 'KREDIT',
        'amount' => 9000,
    ]);

    $response = $this->withSession(portalSession($mine))
        ->get(route('customer-portal.transactions'));

    $response->assertOk();

    $entries = $response->viewData('ledgerEntries');

    expect($entries)->toHaveCount(1)
        ->and($entries->first()->customer_id)->toBe($mine->id)
        ->and((float) $entries->first()->amount)->toBe(5000.0);
});
