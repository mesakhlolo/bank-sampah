<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Customer;
use Illuminate\Support\Str;

echo "Generating unique codes for customers...\n\n";

$updated = 0;

Customer::whereNull('unique_code')
    ->orWhere('unique_code', '')
    ->chunk(100, function ($customers) use (&$updated) {
        foreach ($customers as $customer) {
            do {
                $code = 'BSB-' . strtoupper(Str::random(12));
            } while (Customer::where('unique_code', $code)->exists());

            $customer->update(['unique_code' => $code]);
            
            echo "✓ {$customer->name} ({$customer->member_code}) -> {$code}\n";
            $updated++;
        }
    });

echo "\n" . str_repeat('=', 50) . "\n";
echo "✅ Done! Generated unique codes for {$updated} customers.\n";
echo str_repeat('=', 50) . "\n";
