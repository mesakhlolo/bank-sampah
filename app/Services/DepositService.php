<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\LedgerEntry;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepositService
{
    /**
     * Create a new deposit transaction
     *
     * @param  array  $data  [
     *                       'customer_id' => int,
     *                       'created_by' => int (user ID of creator),
     *                       'items' => [
     *                       ['waste_category_id' => int, 'weight_kg' => float],
     *                       ...
     *                       ],
     *                       'notes' => string|null,
     *                       'transaction_date' => string|null (Y-m-d format, defaults to today)
     *                       ]
     */
    public function createDeposit(array $data): Deposit
    {
        return DB::transaction(function () use ($data) {
            // 1. Generate unique deposit code
            $code = $this->generateDepositCode($data['transaction_date'] ?? null);

            // 2. Create deposit header
            $deposit = Deposit::create([
                'code' => $code,
                'customer_id' => $data['customer_id'],
                'created_by' => $data['created_by'],
                'transaction_date' => $data['transaction_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'status' => 'final', // Always final for now (no draft support yet)
            ]);

            // 3. Process each deposit item
            $totalAmount = 0;
            $totalWeight = 0;

            foreach ($data['items'] as $item) {
                $category = WasteCategory::findOrFail($item['waste_category_id']);

                // Get the price effective on the transaction date
                $priceRecord = $category->getActivePriceOn($data['transaction_date'] ?? now());

                if (! $priceRecord) {
                    throw new \Exception("Tidak ada harga aktif untuk kategori: {$category->name}");
                }

                $pricePerKg = $priceRecord->price_per_kg;

                // Calculate subtotal for this item
                $weightKg = (float) $item['weight_kg'];
                $subtotal = $weightKg * $pricePerKg;                // Create deposit item
                DepositItem::create([
                    'deposit_id' => $deposit->id,
                    'waste_category_id' => $category->id,
                    'weight_kg' => $weightKg,
                    'price_per_kg' => $pricePerKg,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $totalWeight += $weightKg;
            }

            // 4. Update deposit totals
            $deposit->update([
                'total_amount' => $totalAmount,
                'total_weight' => $totalWeight,
            ]);

            // 5. Create ledger entry (credit to customer's balance)
            LedgerEntry::create([
                'customer_id' => $data['customer_id'],
                'ref_type' => 'App\\Models\\Deposit',
                'ref_id' => $deposit->id,
                'direction' => 'KREDIT',
                'amount' => $totalAmount,
            ]);

            // 6. Load relationships for return
            $deposit->load(['customer', 'creator', 'items.wasteCategory']);

            return $deposit;
        });
    }

    /**
     * Generate unique deposit code
     * Format: DEP-YYYYMMDD-####
     */
    private function generateDepositCode(?string $date = null): string
    {
        $date = $date ? Carbon::parse($date) : now();
        $dateStr = $date->format('Ymd');

        // Find the last deposit code for today
        $lastDeposit = Deposit::where('code', 'like', "DEP-{$dateStr}-%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastDeposit) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastDeposit->code, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // First deposit of the day
            $newSequence = 1;
        }

        return sprintf('DEP-%s-%04d', $dateStr, $newSequence);
    }

    /**
     * Calculate deposit total without saving
     *
     * @return array ['total' => float, 'items' => array]
     */
    public function calculateDepositTotal(array $items, ?string $transactionDate = null): array
    {
        $total = 0;
        $calculatedItems = [];

        foreach ($items as $item) {
            $category = WasteCategory::findOrFail($item['waste_category_id']);
            $priceRecord = $category->getActivePriceOn($transactionDate ?? now());

            if (! $priceRecord) {
                throw new \Exception("Tidak ada harga aktif untuk kategori: {$category->name}");
            }

            $pricePerKg = $priceRecord->price_per_kg;
            $weightKg = (float) $item['weight_kg'];
            $subtotal = $weightKg * $pricePerKg;

            $calculatedItems[] = [
                'waste_category_id' => $category->id,
                'category_name' => $category->name,
                'weight_kg' => $weightKg,
                'price_per_kg' => $pricePerKg,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return [
            'total' => $total,
            'items' => $calculatedItems,
        ];
    }
}
