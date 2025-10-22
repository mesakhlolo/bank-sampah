<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('ref_type'); // Deposit or Withdrawal class name
            $table->unsignedBigInteger('ref_id'); // ID of deposit or withdrawal
            $table->enum('direction', ['DEBIT', 'KREDIT']);
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index(['ref_type', 'ref_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
