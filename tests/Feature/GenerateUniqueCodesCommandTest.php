<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

test('the generate unique codes command redacts the generated code', function () {
    $customer = Customer::factory()->create();

    // The model generates a code on create; clear it so the command has work to do.
    DB::table('customers')->where('id', $customer->id)->update(['unique_code' => null]);

    $exitCode = Artisan::call('customers:generate-unique-codes');
    $output = Artisan::output();
    $generatedCode = $customer->refresh()->unique_code;

    expect($exitCode)->toBe(0)
        ->and($generatedCode)->not->toBeNull()
        ->and($output)->toContain('******')
        ->and($output)->not->toContain($generatedCode);
});
