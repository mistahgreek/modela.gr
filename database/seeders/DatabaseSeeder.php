<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@modela.gr',
            'password' => Hash::make('password'),
            'username' => 'admin',
            'is_admin' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create demo user
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@modela.gr',
            'password' => Hash::make('password'),
            'username' => 'demo',
            'is_admin' => false,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Seed base data
        $this->call([
            CategorySeeder::class,
            MaterialSeeder::class,
            PrinterSeeder::class,
            PricingRuleSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@modela.gr / password');
        $this->command->info('Demo: demo@modela.gr / password');
    }
}
