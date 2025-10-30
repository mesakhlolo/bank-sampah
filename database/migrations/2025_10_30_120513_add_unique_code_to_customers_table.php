<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('unique_code', 16)->unique()->nullable()->after('member_code');
            $table->index('unique_code');
            $table->timestamp('last_portal_access')->nullable();
        });

        // Generate unique codes for existing customers
        Customer::chunk(100, function ($customers) {
            foreach ($customers as $customer) {
                do {
                    $code = 'BSB-'.strtoupper(Str::random(12));
                } while (Customer::where('unique_code', $code)->exists());

                $customer->update(['unique_code' => $code]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['unique_code']);
            $table->dropColumn(['unique_code', 'last_portal_access']);
        });
    }
};
