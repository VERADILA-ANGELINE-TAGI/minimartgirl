<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'barcode' => '899100100001',
            'name' => 'Indomie Goreng',
            'category' => 'Makanan',
            'description' => 'Mi instan goreng',
            'price' => 3500,
            'stock' => 20,
            'image' => null,
            'is_active' => true,
        ]);

        Product::create([
            'barcode' => '899100100002',
            'name' => 'Teh Botol',
            'category' => 'Minuman',
            'description' => 'Minuman teh dalam botol',
            'price' => 5000,
            'stock' => 15,
            'image' => null,
            'is_active' => true,
        ]);

        Product::create([
            'barcode' => '899100100003',
            'name' => 'Roti Coklat',
            'category' => 'Makanan',
            'description' => 'Roti dengan isian coklat',
            'price' => 7000,
            'stock' => 10,
            'image' => null,
            'is_active' => true,
        ]);

        Product::create([
            'barcode' => '899100100004',
            'name' => 'Air Mineral',
            'category' => 'Minuman',
            'description' => 'Air mineral 600 ml',
            'price' => 3000,
            'stock' => 30,
            'image' => null,
            'is_active' => true,
        ]);

        Product::create([
            'barcode' => '899100100005',
            'name' => 'Biskuit Coklat',
            'category' => 'Snack',
            'description' => 'Biskuit rasa coklat',
            'price' => 8000,
            'stock' => 12,
            'image' => null,
            'is_active' => true,
        ]);
    }
}
