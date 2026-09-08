<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::query()
                ->inRandomOrder()
                ->value('id'),

            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(10, 100),
            'image' => null,

            'audience' => fake()->randomElement([
                'men',
                'women',
                'kids',
            ]),

            'is_active' => true,
            'is_featured' => fake()->boolean(30),
        ];
    }
}