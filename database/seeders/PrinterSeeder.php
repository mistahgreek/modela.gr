<?php

namespace Database\Seeders;

use App\Models\Printer;
use Illuminate\Database\Seeder;

class PrinterSeeder extends Seeder
{
    public function run(): void
    {
        $printers = [
            [
                'name' => 'Prusa i3 MK3S+',
                'manufacturer' => 'Prusa Research',
                'build_volume_x' => 250,
                'build_volume_y' => 210,
                'build_volume_z' => 210,
                'nozzle_sizes' => [0.4, 0.6, 0.8],
                'supported_materials' => ['PLA', 'PETG', 'ABS', 'TPU', 'Nylon'],
                'hourly_rate' => 3.50,
                'is_active' => true,
            ],
            [
                'name' => 'Creality Ender 3 V2',
                'manufacturer' => 'Creality',
                'build_volume_x' => 220,
                'build_volume_y' => 220,
                'build_volume_z' => 250,
                'nozzle_sizes' => [0.4],
                'supported_materials' => ['PLA', 'PETG', 'ABS'],
                'hourly_rate' => 2.50,
                'is_active' => true,
            ],
            [
                'name' => 'Artillery Sidewinder X2',
                'manufacturer' => 'Artillery',
                'build_volume_x' => 300,
                'build_volume_y' => 300,
                'build_volume_z' => 400,
                'nozzle_sizes' => [0.4, 0.6],
                'supported_materials' => ['PLA', 'PETG', 'ABS', 'TPU'],
                'hourly_rate' => 4.00,
                'is_active' => true,
            ],
        ];

        foreach ($printers as $printer) {
            Printer::create($printer);
        }
    }
}
