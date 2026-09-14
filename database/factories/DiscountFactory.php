<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'discount' => fake()->randomFloat(2, 1, 50),
            'start_date' => now()->subDays(7)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
        ];
    }
}
