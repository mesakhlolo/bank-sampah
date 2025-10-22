<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/customers/{customer}/balance', function (Customer $customer) {
    return response()->json([
        'balance' => $customer->balance,
        'customer_id' => $customer->id,
        'customer_name' => $customer->name,
    ]);
})->middleware('auth:sanctum');
