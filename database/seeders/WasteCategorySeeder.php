<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use Illuminate\Database\Seeder;

class WasteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Plastik PET',
                'description' => 'Botol plastik bekas air mineral, minuman ringan',
                'is_active' => true,
            ],
            [
                'name' => 'Kertas Campur',
                'description' => 'Kertas bekas, koran, majalah, kardus',
                'is_active' => true,
            ],
            [
                'name' => 'Kaleng',
                'description' => 'Kaleng bekas minuman, makanan',
                'is_active' => true,
            ],
            [
                'name' => 'Besi',
                'description' => 'Besi tua, paku, kawat',
                'is_active' => true,
            ],
            [
                'name' => 'Botol Kaca',
                'description' => 'Botol kaca bekas',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            WasteCategory::create($category);
        }
    }
}
