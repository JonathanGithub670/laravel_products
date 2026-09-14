<?php

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('shows only the five latest sales', function (): void {
    $product = Product::factory()->create();

    for ($day = 1; $day <= 7; $day++) {
        Sale::factory()->create([
            'product_id' => $product->id,
            'qty' => 1,
            'created_at' => now()->startOfMonth()->addDays($day),
        ]);
    }

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard/index')
            ->has('latestSales', 5)
        );
});

it('totals the revenue for the current month by default', function (): void {
    $product = Product::factory()->create(['price' => 10000]);

    Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 2,
        'created_at' => Carbon::now()->startOfMonth()->addDays(2),
    ]);
    Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 1,
        'created_at' => Carbon::now()->subMonth()->startOfMonth(),
    ]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard/index')
            ->where('totalSales', 20000)
        );
});

it('totals the revenue within the given date range', function (): void {
    $product = Product::factory()->create(['price' => 10000]);

    Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 2,
        'created_at' => Carbon::parse('2026-08-10'),
    ]);
    Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 1,
        'created_at' => Carbon::parse('2026-08-25'),
    ]);
    Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 4,
        'created_at' => Carbon::parse('2026-09-02'),
    ]);

    $this->get(route('dashboard', [
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-31',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard/index')
            ->where('totalSales', 30000)
        );
});

it('includes the discount in the revenue calculation', function (): void {
    $product = Product::factory()->create(['price' => 10000]);
    $discount = Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 10,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);
    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 2,
        'created_at' => now(),
    ]);
    $sale->salesDiscounts()->create([
        'discount_id' => $discount->id,
        'start_date' => $discount->start_date,
        'end_date' => $discount->end_date,
    ]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard/index')
            ->where('totalSales', 18000)
        );
});
