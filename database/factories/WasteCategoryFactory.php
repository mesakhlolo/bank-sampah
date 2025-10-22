<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WasteCategory>
 */
class WasteCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $wasteTypes = [
            'Plastik PET' => 'Botol plastik bening',
            'Plastik HDPE' => 'Botol shampo, detergen',
            'Plastik PVC' => 'Pipa, mainan',
            'Kertas Campur' => 'Kertas bekas',
            'Kardus' => 'Kardus bekas',
            'Kaleng' => 'Kaleng minuman',
            'Besi' => 'Logam besi',
            'Aluminium' => 'Kaleng aluminium',
            'Botol Kaca' => 'Botol kaca bening',
            'Kaca Warna' => 'Botol kaca warna',
        ];

        $type = fake()->randomElement(array_keys($wasteTypes));

        return [
            'name' => $type,
            'description' => $wasteTypes[$type],
            'is_active' => true,
        ];
    }
}
