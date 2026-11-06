<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\LedgerEntry;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class ReconcileBalancesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balances:reconcile
                            {--customer= : Periksa satu nasabah berdasarkan ID atau kode anggota}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membandingkan saldo ledger dengan total setoran final dikurangi penarikan';

    /**
     * Execute the console command.
     *
     * Balance is never stored, so the ledger is the source of truth. A second
     * figure is derived from the transaction tables: final deposits minus
     * withdrawals. Both must agree for every customer. A negative ledger balance
     * is always a data-integrity failure, even when it happens to match.
     */
    public function handle(): int
    {
        $customers = $this->customersQuery()
            ->get();

        if ($customers->isEmpty()) {
            $this->warn('Tidak ada nasabah yang cocok dengan filter.');

            return self::SUCCESS;
        }

        $mismatches = [];
        $negatives = [];

        foreach ($customers as $customer) {
            $ledgerBalance = round((float) ($customer->ledger_balance ?? 0), 2);
            $expectedBalance = round(
                (float) ($customer->deposits_total ?? 0) - (float) ($customer->withdrawals_total ?? 0),
                2
            );
            $difference = round($ledgerBalance - $expectedBalance, 2);

            if ($ledgerBalance < 0) {
                $negatives[] = [
                    $customer->member_code,
                    $customer->name,
                    $this->rupiah($ledgerBalance),
                ];
            }

            if (abs($difference) >= 0.01) {
                $mismatches[] = [
                    $customer->member_code,
                    $customer->name,
                    $this->rupiah($ledgerBalance),
                    $this->rupiah($expectedBalance),
                    $this->rupiah($difference),
                ];
            }
        }

        if ($mismatches === [] && $negatives === []) {
            $this->info("Rekonsiliasi selesai: {$customers->count()} nasabah seimbang, tidak ada saldo negatif.");

            return self::SUCCESS;
        }

        if ($mismatches !== []) {
            $this->error('Ditemukan saldo ledger yang tidak cocok dengan total transaksi:');
            $this->table(
                ['Kode', 'Nama', 'Saldo Ledger', 'Setoran - Penarikan', 'Selisih'],
                $mismatches
            );
        }

        if ($negatives !== []) {
            $this->error('Ditemukan saldo negatif:');
            $this->table(
                ['Kode', 'Nama', 'Saldo Ledger'],
                $negatives
            );
        }

        $this->newLine();
        $this->line('Periksa ledger_entries, deposits, dan withdrawals untuk nasabah di atas.');

        return self::FAILURE;
    }

    /**
     * Build the aggregate query once so the optional filter applies to all sums.
     */
    private function customersQuery(): Builder
    {
        $query = Customer::query()
            ->select('id', 'member_code', 'name')
            ->withAggregate('ledgerEntries as ledger_balance', LedgerEntry::BALANCE_SUM_SQL)
            ->withSum(
                ['deposits as deposits_total' => fn ($deposits) => $deposits->where('status', 'final')],
                'total_amount'
            )
            ->withSum('withdrawals as withdrawals_total', 'amount')
            ->orderBy('id');

        $term = $this->option('customer');

        if ($term === null || $term === '') {
            return $query;
        }

        return $query->where(function ($scoped) use ($term): void {
            $scoped->whereKey($term)
                ->orWhere('member_code', $term);
        });
    }

    /**
     * Format a number for the console tables.
     */
    private function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 2, ',', '.');
    }
}
