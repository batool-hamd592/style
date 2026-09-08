<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'T-Shirts',
            'Shirts',
            'Pants',
            'Dresses',
            'Jackets',
            'Shoes',
            'Accessories',
        ];

        foreach ($categories as $categoryName) {
            Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => "Explore our {$categoryName} collection.",
                    'is_active' => true,
                ]
            );
        }
    }
}