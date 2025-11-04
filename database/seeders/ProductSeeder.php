<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Kemeja Batik Kawung Slim Fit',
            'description' => 'Kemeja batik modern lengan pendek dengan motif Kawung. Potongan slim fit pas di badan, cocok untuk acara semi-formal.',
            'price' => 190000,
            'stock' => 15,
            'image' => 'batik_kawung_slim.jpg',
        ]);

        Product::create([
            'name' => 'Kemeja Flanel Kotak',
            'description' => 'Kemeja flanel lengan panjang dengan bahan katun premium yang tebal namun tetap adem. Cocok untuk gaya kasual.',
            'price' => 185000,
            'stock' => 10,
            'image' => 'flanel.jpg',
        ]);

        Product::create([
            'name' => 'Denim Jacket Washed Blue',
            'description' => 'Jaket jeans klasik dengan efek washed (pudar) yang memberikan kesan vintage. Bahan denim tebal dan awet.',
            'price' => 350000,
            'stock' => 7,
            'image' => 'denim_jacket_washed.jpg',
        ]);
    }
}
