<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Report and dashboard queries filter on `status = 'final'` combined with a
     * `transaction_date` range. `status` had no index at all, and the existing
     * `transaction_date` index cannot be used together with it, so the planner fell
     * back to scanning the whole table for every report.
     */
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->index(['status', 'transaction_date'], 'deposits_status_transaction_date_index');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->index(['customer_id', 'transaction_date'], 'withdrawals_customer_transaction_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropIndex('deposits_status_transaction_date_index');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropIndex('withdrawals_customer_transaction_date_index');
        });
    }
};
