<?php

namespace Database\Seeders;

use App\Models\OrderMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh 1: Order Standard (Ukuran berdasarkan size)
        OrderMeasurement::create([
            'customer_id' => 2,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'product_type' => 'shirt',
            'measurement_type' => 'standard',
            'size' => 'L',
            'status' => 'requested',
            'total_price' => 150000,
            'catatan' => 'Warna biru navy, lengan panjang',
            'image' => 'shirt_blue.jpg',
            // 'image' => 'uploads/orders/shirt_blue.jpg',
        ]);

        // Contoh 2: Order Custom Celana
        OrderMeasurement::create([
            'customer_id' => 2,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'product_type' => 'pants',
            'measurement_type' => 'custom',
            'panjang_bahu' => null,
            'panjang_lengan' => null,
            'lingkar_dada' => null,
            'panjang_baju' => null,
            'lingkar_pinggang' => 85.5,
            'panjang_celana' => 100.2,
            'lingkar_paha' => 55.0,
            'lingkar_betis' => 38.0,
            'lingkar_lutut' => 40.5,
            'lingkar_kaki' => 32.0,
            'status' => 'in_progress',
            'total_price' => 200000,
            'catatan' => 'Gunakan bahan katun halus, warna hitam',
            'image' => 'pants_black.jpg',
        ]);

        // Contoh 3: Order Custom Baju
        OrderMeasurement::create([
            'customer_id' => 2,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'product_type' => 'shirt',
            'measurement_type' => 'custom',
            'panjang_bahu' => 45.0,
            'panjang_lengan' => 60.0,
            'lingkar_dada' => 100.0,
            'panjang_baju' => 70.0,
            'lingkar_pinggang' => 90.0,
            'status' => 'done',
            'total_price' => 175000,
            'catatan' => 'Model slim fit, bahan adem',
            'image' => 'shirt_custom.jpg',
        ]);
    }
}
