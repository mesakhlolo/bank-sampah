<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Harden financial history.
     *
     * Cascading deletes on financial tables silently destroyed transaction
     * history when a customer or category was removed. Those foreign keys are
     * replaced with RESTRICT so the database refuses the delete as a backstop.
     *
     * `waste_prices` also gets a unique index on
     * (waste_category_id, effective_from). Duplicate rows are removed first,
     * keeping the newest row for each category/date pair.
     */
    public function up(): void
    {
        $this->normalizePriceDates();
        $this->removeDuplicatePrices();

        Schema::table('waste_prices', function (Blueprint $table) {
            $table->unique(
                ['waste_category_id', 'effective_from'],
                'waste_prices_category_effective_unique'
            );
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });

        Schema::table('deposit_items', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->foreign('deposit_id')->references('id')->on('deposits')->restrictOnDelete();

            $table->dropForeign(['waste_category_id']);
            $table->foreign('waste_category_id')->references('id')->on('waste_categories')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_items', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->foreign('deposit_id')->references('id')->on('deposits')->cascadeOnDelete();

            $table->dropForeign(['waste_category_id']);
            $table->foreign('waste_category_id')->references('id')->on('waste_categories')->cascadeOnDelete();
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::table('waste_prices', function (Blueprint $table) {
            $table->dropUnique('waste_prices_category_effective_unique');
        });
    }

    /**
     * Store every effective date as `Y-m-d` before grouping, so rows created with
     * a datetime string and rows created with a plain date compare equal.
     */
    private function normalizePriceDates(): void
    {
        DB::table('waste_prices')->update([
            'effective_from' => DB::raw('DATE(effective_from)'),
        ]);
    }

    /**
     * Keep the newest row (highest id) for each category/date pair.
     */
    private function removeDuplicatePrices(): void
    {
        $duplicates = DB::table('waste_prices')
            ->select('waste_category_id', 'effective_from')
            ->groupBy('waste_category_id', 'effective_from')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('waste_prices')
                ->where('waste_category_id', $duplicate->waste_category_id)
                ->where('effective_from', $duplicate->effective_from)
                ->orderByDesc('id')
                ->pluck('id');

            $staleIds = $ids->slice(1)->all();

            if ($staleIds !== []) {
                DB::table('waste_prices')->whereIn('id', $staleIds)->delete();
            }
        }
    }
};
