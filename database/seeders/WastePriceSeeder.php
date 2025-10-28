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

        // Set effective date to 6 months ago to ensure all historical transactions can use this price
        $effectiveDate = now()->subMonths(6)->format('Y-m-d');
        
        foreach ($prices as $categoryName => $price) {
            $category = WasteCategory::where('name', $categoryName)->first();

            if ($category) {
                WastePrice::create([
                    'waste_category_id' => $category->id,
                    'price_per_kg' => $price,
                    'effective_from' => $effectiveDate,
                ]);
            }
        }
        
        $this->command->info("✓ Waste prices created with effective date: {$effectiveDate}");
    }
}
