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

        $products = collect([
            ['name' => 'Aki GS Astra NS40 12V 35Ah', 'description' => 'Aki kering maintenance free untuk mobil kecil, kapasitas 12V 35Ah.', 'price' => 650_000, 'stock' => 20],
            ['name' => 'Filter Oli Denso 115010-5021', 'description' => 'Filter oli orisinal untuk Toyota Avanza dan Xenia.', 'price' => 45_000, 'stock' => 50],
            ['name' => 'Filter Solar Sakura FS-2012', 'description' => 'Filter solar untuk mesin diesel common rail.', 'price' => 85_000, 'stock' => 30],
            ['name' => 'Busi Iridium NGK BKR6EIX', 'description' => 'Busi iridium performa tinggi dengan umur pakai lebih lama.', 'price' => 95_000, 'stock' => 40],
            ['name' => 'Oli Mesin Castrol Magnatec 10W-40 1L', 'description' => 'Oli mesin semi-sintetik dengan perlindungan ekstra saat start dingin.', 'price' => 120_000, 'stock' => 35],
        ])->map(fn (array $data) => Product::create($data));

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
