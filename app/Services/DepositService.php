<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\LedgerEntry;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
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
            // 1. Create deposit header with a unique code, retrying on collision
            $deposit = $this->createWithUniqueCode($data);

            // 2. Load every category and its applicable prices in one query
            // instead of a category + price query per item.
            $transactionDate = $data['transaction_date'] ?? now();
            $categories = $this->loadCategoriesForItems($data['items'], $transactionDate);

            // 3. Process each deposit item
            $totalAmount = 0;
            $totalWeight = 0;

            foreach ($data['items'] as $item) {
                $category = $categories->get($item['waste_category_id']);

                if (! $category) {
                    throw (new ModelNotFoundException)->setModel(
                        WasteCategory::class,
                        [$item['waste_category_id']]
                    );
                }

                // Get the price effective on the transaction date
                $priceRecord = $category->getActivePriceOn($transactionDate);

                if (! $priceRecord) {
                    throw new \Exception("Tidak ada harga aktif untuk kategori: {$category->name}");
                }

                $pricePerKg = $priceRecord->price_per_kg;

                // Calculate subtotal for this item
                $weightKg = (float) $item['weight_kg'];
                $subtotal = $weightKg * $pricePerKg;

                // Create deposit item
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
     * Create the deposit header, retrying when the generated code collides.
     *
     * Sequence generation is read-then-write, so two concurrent requests can
     * compute the same code. The `code` column is unique, which turns that race
     * into a UniqueConstraintViolationException — we catch it and recompute
     * instead of failing the whole deposit.
     *
     * @param  array<string, mixed>  $data
     */
    private function createWithUniqueCode(array $data): Deposit
    {
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return Deposit::create([
                    'code' => $this->generateDepositCode($data['transaction_date'] ?? null),
                    'customer_id' => $data['customer_id'],
                    'created_by' => $data['created_by'],
                    'transaction_date' => $data['transaction_date'] ?? now(),
                    'notes' => $data['notes'] ?? null,
                    'status' => 'final', // Always final for now (no draft support yet)
                ]);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
            }
        }

        // Unreachable: the loop either returns or throws.
        throw new \RuntimeException('Gagal membuat kode setoran unik.');
    }

    /**
     * Generate unique deposit code
     * Format: DEP-YYYYMMDD-####
     */
    private function generateDepositCode(?string $date = null): string
    {
        $date = $date ? Carbon::parse($date) : now();
        $dateStr = $date->format('Ymd');
        $prefix = "DEP-{$dateStr}-";

        // Lock the highest existing code for this day so concurrent transactions
        // serialize instead of computing the same next sequence number.
        // NOTE: when no row exists yet there is nothing to lock — the unique
        // constraint plus the retry in createWithUniqueCode() covers that case.
        $lastDeposit = Deposit::where('code', 'like', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastDeposit) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastDeposit->code, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // First deposit of the day
            $newSequence = 1;
        }

        return sprintf('%s%04d', $prefix, $newSequence);
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
        $transactionDate = $transactionDate ?? now();
        $categories = $this->loadCategoriesForItems($items, $transactionDate);

        foreach ($items as $item) {
            $category = $categories->get($item['waste_category_id']);

            if (! $category) {
                throw (new ModelNotFoundException)->setModel(
                    WasteCategory::class,
                    [$item['waste_category_id']]
                );
            }

            $priceRecord = $category->getActivePriceOn($transactionDate);

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

    /**
     * Load the submitted categories with their applicable prices in one query.
     *
     * The eager-load constraint keeps only prices effective on the transaction
     * date, newest first, so `getActivePriceOn()` can pick the first match from
     * memory instead of issuing a query per item.
     *
     * @param  array<int, array{waste_category_id: int|string, weight_kg: int|float|string}>  $items
     * @return Collection<int, WasteCategory>
     */
    private function loadCategoriesForItems(array $items, mixed $transactionDate): Collection
    {
        $categoryIds = collect($items)
            ->pluck('waste_category_id')
            ->unique()
            ->values();

        return WasteCategory::query()
            ->with(['prices' => function ($query) use ($transactionDate): void {
                $query->where('effective_from', '<=', $transactionDate)
                    ->orderByDesc('effective_from');
            }])
            ->whereIn('id', $categoryIds)
            ->get()
            ->keyBy('id');
    }
}
