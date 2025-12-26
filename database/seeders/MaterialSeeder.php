<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'name' => 'PLA (Standard)',
                'type' => 'PLA',
                'density_g_cm3' => 1.24,
                'available_colors' => ['White', 'Black', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple', 'Gray'],
                'base_cost_per_kg' => 20.00,
                'description' => 'Easy to print biodegradable thermoplastic, perfect for most applications',
                'properties' => [
                    'strength' => 'Medium',
                    'flexibility' => 'Low',
                    'heat_resistance' => 'Low (60°C)',
                    'biodegradable' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'PLA+ (Premium)',
                'type' => 'PLA',
                'density_g_cm3' => 1.24,
                'available_colors' => ['White', 'Black', 'Red', 'Blue', 'Green', 'Silver', 'Gold'],
                'base_cost_per_kg' => 25.00,
                'description' => 'Enhanced PLA with better strength and layer adhesion',
                'properties' => [
                    'strength' => 'High',
                    'flexibility' => 'Low',
                    'heat_resistance' => 'Low (60°C)',
                    'biodegradable' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'PETG',
                'type' => 'PETG',
                'density_g_cm3' => 1.27,
                'available_colors' => ['Clear', 'White', 'Black', 'Red', 'Blue', 'Green', 'Orange'],
                'base_cost_per_kg' => 28.00,
                'description' => 'Durable and flexible with good chemical resistance',
                'properties' => [
                    'strength' => 'High',
                    'flexibility' => 'Medium',
                    'heat_resistance' => 'Medium (80°C)',
                    'food_safe' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'ABS',
                'type' => 'ABS',
                'density_g_cm3' => 1.04,
                'available_colors' => ['White', 'Black', 'Red', 'Blue', 'Yellow', 'Gray'],
                'base_cost_per_kg' => 24.00,
                'description' => 'Strong and impact-resistant, requires heated bed',
                'properties' => [
                    'strength' => 'High',
                    'flexibility' => 'Medium',
                    'heat_resistance' => 'High (100°C)',
                    'acetone_smoothable' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'TPU (Flexible)',
                'type' => 'TPU',
                'density_g_cm3' => 1.21,
                'available_colors' => ['Black', 'Red', 'Blue', 'Clear', 'Green'],
                'base_cost_per_kg' => 35.00,
                'description' => 'Highly flexible and elastic material for rubber-like parts',
                'properties' => [
                    'strength' => 'Medium',
                    'flexibility' => 'Very High',
                    'heat_resistance' => 'Medium',
                    'elastic' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Nylon',
                'type' => 'Nylon',
                'density_g_cm3' => 1.14,
                'available_colors' => ['Natural', 'Black', 'White'],
                'base_cost_per_kg' => 40.00,
                'description' => 'Extremely strong and durable, great for functional parts',
                'properties' => [
                    'strength' => 'Very High',
                    'flexibility' => 'Medium',
                    'heat_resistance' => 'High',
                    'wear_resistant' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}
