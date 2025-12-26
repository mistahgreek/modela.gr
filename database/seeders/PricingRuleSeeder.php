<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\PricingRule;
use Illuminate\Database\Seeder;

class PricingRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Get materials
        $pla = Material::where('type', 'PLA')->where('name', 'PLA (Standard)')->first();
        $pla_plus = Material::where('type', 'PLA')->where('name', 'PLA+ (Premium)')->first();
        $petg = Material::where('type', 'PETG')->first();
        $abs = Material::where('type', 'ABS')->first();
        $tpu = Material::where('type', 'TPU')->first();
        $nylon = Material::where('type', 'Nylon')->first();

        $rules = [
            // PLA Standard - Small objects
            [
                'rule_name' => 'PLA Small Parts',
                'material_id' => $pla?->id,
                'printer_id' => null,
                'min_volume' => 0,
                'max_volume' => 10000, // 10cm³
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.08,
                'setup_fee' => 2.00,
                'machine_time_rate' => 2.50,
                'margin_percent' => 30.00,
                'min_price' => 5.00,
                'priority' => 10,
                'is_active' => true,
            ],
            // PLA Standard - Medium objects
            [
                'rule_name' => 'PLA Medium Parts',
                'material_id' => $pla?->id,
                'printer_id' => null,
                'min_volume' => 10000,
                'max_volume' => 100000, // 100cm³
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.06,
                'setup_fee' => 3.00,
                'machine_time_rate' => 2.50,
                'margin_percent' => 25.00,
                'min_price' => 10.00,
                'priority' => 9,
                'is_active' => true,
            ],
            // PLA Standard - Large objects
            [
                'rule_name' => 'PLA Large Parts',
                'material_id' => $pla?->id,
                'printer_id' => null,
                'min_volume' => 100000,
                'max_volume' => null,
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.05,
                'setup_fee' => 5.00,
                'machine_time_rate' => 2.50,
                'margin_percent' => 20.00,
                'min_price' => 15.00,
                'priority' => 8,
                'is_active' => true,
            ],
            // PETG pricing
            [
                'rule_name' => 'PETG Standard',
                'material_id' => $petg?->id,
                'printer_id' => null,
                'min_volume' => null,
                'max_volume' => null,
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.10,
                'setup_fee' => 3.50,
                'machine_time_rate' => 3.00,
                'margin_percent' => 30.00,
                'min_price' => 8.00,
                'priority' => 7,
                'is_active' => true,
            ],
            // ABS pricing
            [
                'rule_name' => 'ABS Standard',
                'material_id' => $abs?->id,
                'printer_id' => null,
                'min_volume' => null,
                'max_volume' => null,
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.09,
                'setup_fee' => 3.00,
                'machine_time_rate' => 3.00,
                'margin_percent' => 30.00,
                'min_price' => 8.00,
                'priority' => 6,
                'is_active' => true,
            ],
            // TPU pricing
            [
                'rule_name' => 'TPU Flexible',
                'material_id' => $tpu?->id,
                'printer_id' => null,
                'min_volume' => null,
                'max_volume' => null,
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.15,
                'setup_fee' => 5.00,
                'machine_time_rate' => 4.00,
                'margin_percent' => 35.00,
                'min_price' => 12.00,
                'priority' => 5,
                'is_active' => true,
            ],
            // Nylon pricing
            [
                'rule_name' => 'Nylon High-Strength',
                'material_id' => $nylon?->id,
                'printer_id' => null,
                'min_volume' => null,
                'max_volume' => null,
                'min_weight' => null,
                'max_weight' => null,
                'price_per_gram' => 0.18,
                'setup_fee' => 6.00,
                'machine_time_rate' => 4.50,
                'margin_percent' => 35.00,
                'min_price' => 15.00,
                'priority' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            if ($rule['material_id']) {
                PricingRule::create($rule);
            }
        }
    }
}
