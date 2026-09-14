<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Keep the seeder idempotent so container restarts never duplicate demo data.
        if (User::where('username', 'admin')->exists() || Product::exists()) {
            $this->command->info('Demo data already seeded, skipping.');

            return;
        }

        $today = Carbon::today();

        $admin = User::factory()->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->command->info('Demo user created: username "admin" / password "password"');

        $products = Product::factory()->count(5)->create();

        // Seed stock so every product has sellable inventory.
        foreach ($products as $product) {
            Stock::factory()->count(2)->create(['product_id' => $product->id]);
        }

        // Give the first product a discount that is currently active.
        $activeDiscount = Discount::factory()->create([
            'product_id' => $products[0]->id,
            'discount' => 10,
            'start_date' => $today->copy()->subDays(3)->toDateString(),
            'end_date' => $today->copy()->addDays(3)->toDateString(),
        ]);

        // Give the second product an expired discount.
        Discount::factory()->create([
            'product_id' => $products[1]->id,
            'discount' => 15,
            'start_date' => $today->copy()->subDays(10)->toDateString(),
            'end_date' => $today->copy()->subDays(5)->toDateString(),
        ]);

        // Create a few sales spread over the current month so the dashboard
        // shows both recent sales and a non-trivial revenue total.
        $sales = Sale::factory()->count(8)->create([
            'product_id' => $products[2]->id,
            'qty' => 1,
            'created_at' => fn () => $today->copy()->subDays(rand(0, 12)),
        ]);

        // Seed the newest sale with the active discount automatically applied,
        // matching what happens when a sale is created through the UI.
        $newestSale = Sale::factory()->create([
            'product_id' => $products[0]->id,
            'qty' => 2,
            'created_at' => $today->copy()->setHour(10),
        ]);

        $newestSale->salesDiscounts()->create([
            'discount_id' => $activeDiscount->id,
            'start_date' => $activeDiscount->start_date,
            'end_date' => $activeDiscount->end_date,
        ]);

        $this->command->info('Demo data seeded: 5 products, stock entries, sales, and discounts.');
    }
}
