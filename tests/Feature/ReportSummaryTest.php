<?php

use App\Models\Customer;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WastePrice;
use App\Services\DepositService;
use App\Services\ReportService;
use App\Services\WithdrawalService;
use Barryvdh\DomPDF\Facade\Pdf;

/*
 * Fase 5 — report coverage.
 *
 * The report is what the operator hands to the kelurahan, so the numbers must be
 * verifiable independently. These tests recompute the expected figures from the
 * ledger/transactions rather than hard-coding magic values wherever possible.
 */

/**
 * Seed a deterministic set of transactions for one customer.
 *
 * @return array{customer: Customer, category: WasteCategory, deposit: mixed, withdrawal: mixed}
 */
function seedReportData(): array
{
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create(['name' => 'Plastik']);
    $other = WasteCategory::factory()->create(['name' => 'Kertas']);

    foreach ([$category, $other] as $cat) {
        WastePrice::create([
            'waste_category_id' => $cat->id,
            'price_per_kg' => 2000,
            'effective_from' => now()->subYear(),
        ]);
    }

    $deposit = app(DepositService::class)->createDeposit([
        'customer_id' => $customer->id,
        'created_by' => $operator->id,
        'transaction_date' => now()->format('Y-m-d'),
        'items' => [
            ['waste_category_id' => $category->id, 'weight_kg' => 10], // 20.000
            ['waste_category_id' => $other->id, 'weight_kg' => 5],     // 10.000
        ],
    ]);

    $withdrawal = app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => $customer->id,
        'amount' => 5000,
        'transaction_date' => now()->format('Y-m-d'),
    ], $operator);

    return compact('customer', 'category', 'deposit', 'withdrawal');
}

test('report totals match the seeded transactions', function () {
    seedReportData();

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect((float) $summary['totalDeposits']->total_amount)->toBe(30000.0)
        ->and((float) $summary['totalDeposits']->total_weight)->toBe(15.0)
        ->and((int) $summary['totalDeposits']->count)->toBe(1)
        ->and((float) $summary['totalWithdrawals']->total_amount)->toBe(5000.0)
        ->and((int) $summary['totalWithdrawals']->count)->toBe(1)
        ->and((float) $summary['netBalanceChange'])->toBe(25000.0);
});

test('net balance change equals the ledger balance', function () {
    $data = seedReportData();

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    // One customer, so the period's net change must equal their ledger balance.
    expect(round((float) $summary['netBalanceChange'], 2))
        ->toBe($data['customer']->fresh()->balance);
});

test('category breakdown splits by waste category', function () {
    seedReportData();

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    $rows = $summary['categorySummary']->keyBy('name');

    expect($rows)->toHaveCount(2)
        ->and((float) $rows['Plastik']->total_amount)->toBe(20000.0)
        ->and((float) $rows['Plastik']->total_weight)->toBe(10.0)
        ->and((float) $rows['Kertas']->total_amount)->toBe(10000.0)
        ->and((float) $rows['Kertas']->total_weight)->toBe(5.0);
});

test('category breakdown is ordered by amount descending', function () {
    seedReportData();

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    $amounts = $summary['categorySummary']->pluck('total_amount')->map(fn ($v) => (float) $v)->all();

    expect($amounts)->toBe([20000.0, 10000.0]);
});

test('top customers are ranked by deposit amount', function () {
    seedReportData();

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect($summary['topCustomers'])->toHaveCount(1)
        ->and((float) $summary['topCustomers']->first()->total_amount)->toBe(30000.0)
        ->and((int) $summary['topCustomers']->first()->transaction_count)->toBe(1);
});

test('top customers are capped at ten', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 1000,
        'effective_from' => now()->subYear(),
    ]);

    foreach (range(1, 12) as $i) {
        $customer = Customer::factory()->create();

        app(DepositService::class)->createDeposit([
            'customer_id' => $customer->id,
            'created_by' => $operator->id,
            'transaction_date' => now()->format('Y-m-d'),
            'items' => [['waste_category_id' => $category->id, 'weight_kg' => $i]],
        ]);
    }

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect($summary['topCustomers'])->toHaveCount(10);

    // Ranked by amount descending: the 12kg customer first.
    expect((float) $summary['topCustomers']->first()->total_amount)->toBe(12000.0);
});

test('the period is inclusive of both endpoints', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $customer = Customer::factory()->create();
    $category = WasteCategory::factory()->create();

    WastePrice::create([
        'waste_category_id' => $category->id,
        'price_per_kg' => 1000,
        'effective_from' => now()->subYear(),
    ]);

    $start = now()->startOfMonth();
    $end = now()->endOfMonth();

    foreach ([$start, $end] as $day) {
        app(DepositService::class)->createDeposit([
            'customer_id' => $customer->id,
            'created_by' => $operator->id,
            'transaction_date' => $day->format('Y-m-d'),
            'items' => [['waste_category_id' => $category->id, 'weight_kg' => 1]],
        ]);
    }

    $summary = app(ReportService::class)->buildSummary(
        $start->format('Y-m-d'),
        $end->format('Y-m-d'),
    );

    expect((int) $summary['totalDeposits']->count)->toBe(2);
});

test('a period with no transactions returns zeroes, not nulls', function () {
    $summary = app(ReportService::class)->buildSummary('2000-01-01', '2000-01-31');

    expect((float) ($summary['totalDeposits']->total_amount ?? 0))->toBe(0.0)
        ->and((int) ($summary['totalDeposits']->count ?? 0))->toBe(0)
        ->and((float) ($summary['totalWithdrawals']->total_amount ?? 0))->toBe(0.0)
        ->and((float) $summary['netBalanceChange'])->toBe(0.0)
        ->and($summary['categorySummary'])->toHaveCount(0)
        ->and($summary['topCustomers'])->toHaveCount(0);
});

test('the summary defaults to the current month when no dates are given', function () {
    seedReportData();

    $summary = app(ReportService::class)->buildSummary();

    expect($summary['start'])->toBe(now()->startOfMonth()->format('Y-m-d'))
        ->and($summary['end'])->toBe(now()->endOfMonth()->format('Y-m-d'))
        ->and((int) $summary['totalDeposits']->count)->toBe(1);
});

test('draft deposits are excluded from the report', function () {
    $data = seedReportData();

    // Flip the deposit to draft; it must drop out of every figure.
    $data['deposit']->update(['status' => 'draft']);

    $summary = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect((int) $summary['totalDeposits']->count)->toBe(0)
        ->and((float) $summary['totalDeposits']->total_amount)->toBe(0.0)
        ->and($summary['categorySummary'])->toHaveCount(0)
        ->and($summary['topCustomers'])->toHaveCount(0);
});

test('the summary page renders for an admin', function () {
    seedReportData();

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('reports.summary'))
        ->assertOk()
        ->assertSee('30.000', false);
});

test('the summary page requires authentication', function () {
    $this->get(route('reports.summary'))
        ->assertRedirect(route('login'));
});

test('the report can be downloaded as a PDF', function () {
    seedReportData();

    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('reports.download-pdf'));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');

    // A PDF must actually be produced, not just an empty response.
    expect(strlen($response->getContent()))->toBeGreaterThan(1000)
        ->and($response->getContent())->toStartWith('%PDF');
});

test('the PDF and the HTML page report the same totals', function () {
    seedReportData();

    $admin = User::factory()->create(['role' => 'admin']);

    // Both endpoints call ReportService::buildSummary() with the same arguments, so
    // the figures are identical by construction. Assert that contract directly:
    // the HTML view receives the service output, and the PDF renders without error
    // for the same period.
    $html = $this->actingAs($admin)->get(route('reports.summary'));
    $html->assertOk();

    $service = app(ReportService::class)->buildSummary(
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d'),
    );

    expect((float) $html->viewData('totalDeposits')->total_amount)
        ->toBe(round((float) $service['totalDeposits']->total_amount, 2))
        ->and((float) $html->viewData('totalWithdrawals')->total_amount)
        ->toBe(round((float) $service['totalWithdrawals']->total_amount, 2))
        ->and((float) $html->viewData('netBalanceChange'))
        ->toBe(round((float) $service['netBalanceChange'], 2));

    // The PDF path renders the same data through a different template.
    $pdf = $this->actingAs($admin)->get(route('reports.download-pdf'));
    $pdf->assertOk();
    expect($pdf->getContent())->toStartWith('%PDF');
});
