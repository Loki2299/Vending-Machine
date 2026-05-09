<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin User',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate(['email' => 'user@example.com'], [
            'name' => 'Regular User',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        collect([
            ['name' => 'Coke', 'price' => 3.990, 'quantity_available' => 25],
            ['name' => 'Pepsi', 'price' => 6.885, 'quantity_available' => 25],
            ['name' => 'Water', 'price' => 0.500, 'quantity_available' => 25],
        ])->each(fn (array $product) => Product::updateOrCreate([
            'name' => $product['name'],
        ], $product));
    }
}
