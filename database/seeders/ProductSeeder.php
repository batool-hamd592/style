<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::factory()
            ->count(18)
            ->create();

        foreach ($products as $product) {
            $sizes = $product->audience === 'kids'
                ? ['4Y', '6Y', '8Y', '10Y']
                : ['S', 'M', 'L', 'XL'];

            $color = fake()->randomElement([
                'Black',
                'White',
                'Blue',
                'Beige',
                'Green',
            ]);

            foreach ($sizes as $size) {
                ProductVariant::create([
                    'product_id' => $product->id,

                    'sku' => strtoupper(
                        'SH-' .
                        $product->id . '-' .
                        $size . '-' .
                        Str::random(4)
                    ),

                    'size' => $size,
                    'color' => $color,
                    'stock' => fake()->numberBetween(3, 25),
                ]);
            }
        }
    }
}