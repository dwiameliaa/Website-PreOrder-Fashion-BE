<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'order_number' => 'ORD-0001',
            'size' => 'L',
            'total_price' => 190000,
            'customer_id' => 3,
            'product_id' => 1,
        ]);

        Order::create([
            'order_number' => 'ORD-0002',
            'size' => 'M',
            'total_price' => 185000,
            'customer_id' => 3,
            'product_id' => 2,
        ]);
    }
}
