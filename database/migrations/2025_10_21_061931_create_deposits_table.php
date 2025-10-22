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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('transaction_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'final'])->default('final');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index('transaction_date');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
