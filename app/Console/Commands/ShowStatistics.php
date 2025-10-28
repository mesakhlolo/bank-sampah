<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\Withdrawal;
use Illuminate\Console\Command;

class ShowStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menampilkan statistik data Bank Sampah untuk testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->line(str_repeat('=', 70));
        $this->info('📊 STATISTIK DATA BANK SAMPAH VIBECODE');
        $this->line(str_repeat('=', 70));
        $this->newLine();

        // User Statistics
        $this->info('👥 PENGGUNA');
        $this->line('- Total Admin: '.User::where('role', 'admin')->count());
        $this->line('- Total Operator: '.User::where('role', 'operator')->count());
        $this->newLine();

        // Customer Statistics
        $this->info('👨‍👩‍👧‍👦 NASABAH');
        $this->line('- Total Nasabah: '.Customer::count());
        $this->line('- Nasabah Aktif (punya transaksi): '.Customer::has('deposits')->count());
        $this->newLine();

        // Waste Category Statistics
        $this->info('♻️ KATEGORI SAMPAH');
        foreach (WasteCategory::all() as $category) {
            $totalWeight = DepositItem::where('waste_category_id', $category->id)->sum('weight_kg');
            $activePrice = $category->getActivePriceOn();
            $price = $activePrice ? 'Rp '.number_format($activePrice->price_per_kg, 0, ',', '.') : 'N/A';
            $this->line("- {$category->name}: {$price}/kg | Total: ".number_format($totalWeight, 2).' kg');
        }
        $this->newLine();

        // Deposit Statistics
        $this->info('📥 TRANSAKSI SETORAN');
        $this->line('- Total Transaksi: '.Deposit::count());
        $this->line('- Total Berat Sampah: '.number_format(Deposit::sum('total_weight'), 2).' kg');
        $this->line('- Total Nilai Setoran: Rp '.number_format(Deposit::sum('total_amount'), 0, ',', '.'));
        $this->line('- Rata-rata per Transaksi: Rp '.number_format(Deposit::avg('total_amount'), 0, ',', '.'));
        $this->line('- Setoran Tertinggi: Rp '.number_format(Deposit::max('total_amount'), 0, ',', '.'));
        $this->line('- Setoran Terendah: Rp '.number_format(Deposit::min('total_amount'), 0, ',', '.'));
        $this->newLine();

        // Withdrawal Statistics
        $this->info('📤 TRANSAKSI PENARIKAN');
        $this->line('- Total Transaksi: '.Withdrawal::count());
        $this->line('- Total Nilai Penarikan: Rp '.number_format(Withdrawal::sum('amount'), 0, ',', '.'));
        $this->line('- Rata-rata per Transaksi: Rp '.number_format(Withdrawal::avg('amount'), 0, ',', '.'));
        $this->line('- Penarikan Tertinggi: Rp '.number_format(Withdrawal::max('amount'), 0, ',', '.'));
        $this->line('- Penarikan Terendah: Rp '.number_format(Withdrawal::min('amount'), 0, ',', '.'));
        $this->newLine();

        // Overall Statistics
        $totalIn = Deposit::sum('total_amount');
        $totalOut = Withdrawal::sum('amount');
        $totalBalance = $totalIn - $totalOut;

        $this->info('💰 RINGKASAN KEUANGAN');
        $this->line('- Total Pemasukan (Setoran): Rp '.number_format($totalIn, 0, ',', '.'));
        $this->line('- Total Pengeluaran (Penarikan): Rp '.number_format($totalOut, 0, ',', '.'));
        $this->line('- Total Saldo Sistem: Rp '.number_format($totalBalance, 0, ',', '.'));
        $this->newLine();

        // Top Customers
        $this->info('🏆 TOP 5 NASABAH (Berdasarkan Total Setoran)');
        $topCustomers = Customer::withSum('deposits', 'total_amount')
            ->orderByDesc('deposits_sum_total_amount')
            ->take(5)
            ->get();

        $tableData = [];
        foreach ($topCustomers as $index => $customer) {
            $balance = $customer->balance;
            $totalDeposit = $customer->deposits_sum_total_amount ?? 0;
            $tableData[] = [
                '#'.($index + 1),
                $customer->name,
                'Rp '.number_format($totalDeposit, 0, ',', '.'),
                'Rp '.number_format($balance, 0, ',', '.'),
            ];
        }

        $this->table(
            ['Rank', 'Nama', 'Total Setoran', 'Saldo'],
            $tableData
        );

        $this->newLine();
        $this->line(str_repeat('=', 70));
        $this->info('✅ Data siap untuk testing!');
        $this->line(str_repeat('=', 70));
        $this->newLine();

        return Command::SUCCESS;
    }
}
