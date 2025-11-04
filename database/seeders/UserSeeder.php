<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'phone' => '081234567890',
            'address' => 'Jl. Mawar No. 123, Surabaya',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('customer123'),
            'phone' => '081234534520',
            'address' => 'Jakarta Selatan Blok B',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Amelia',
            'email' => 'amelia@example.com',
            'password' => bcrypt('amelia123'),
            'phone' => '081265748765',
            'address' => 'Bali',
            'role' => 'customer',
        ]);
    }
}
