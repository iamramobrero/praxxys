<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryIDs = ProductCategory::pluck('id');
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'category_id' => $this->faker->randomElement($categoryIDs),
            'datetime_at' => $this->faker->dateTime(),
        ];
    }
}
