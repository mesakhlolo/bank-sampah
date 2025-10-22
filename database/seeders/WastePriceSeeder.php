<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use App\Models\WastePrice;
use Illuminate\Database\Seeder;

class WastePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prices = [
            'Plastik PET' => 3000,
            'Kertas Campur' => 1500,
            'Kaleng' => 5000,
            'Besi' => 4000,
            'Botol Kaca' => 2000,
        ];

        foreach ($prices as $categoryName => $price) {
            $category = WasteCategory::where('name', $categoryName)->first();

            if ($category) {
                WastePrice::create([
                    'waste_category_id' => $category->id,
                    'price_per_kg' => $price,
                    'effective_from' => '2025-10-01',
                ]);
            }
        }
    }
}
