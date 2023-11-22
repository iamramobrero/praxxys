<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
        ];
    }

    protected static function newFactory(): Factory
    {
        return ProductCategoryFactory::new();
    }
}
