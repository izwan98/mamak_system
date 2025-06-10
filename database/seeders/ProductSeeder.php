<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Seed the products table.
     */
    public function run()
    {
        // Default products from the requirements
        $products = [
            [
                'code' => 'B001',
                'name' => 'Kopi',
                'price' => 2.50,
            ],
            [
                'code' => 'F001',
                'name' => 'Roti Kosong',
                'price' => 1.50,
            ],
            [
                'code' => 'B002',
                'name' => 'Teh Tarik',
                'price' => 2.00,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
