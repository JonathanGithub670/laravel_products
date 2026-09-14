<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\Sale;
use App\Models\SalesDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesDiscount>
 */
class SalesDiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_id' => Sale::factory(),
            'discount_id' => Discount::factory(),
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ];
    }
}
