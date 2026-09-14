<?php

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('shows the discount as a percent with the number of times it was used', function (): void {
    $product = Product::factory()->create();
    $discount = Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 12.5,
    ]);
    $sale = Sale::factory()->create(['product_id' => $product->id]);
    $sale->salesDiscounts()->create([
        'discount_id' => $discount->id,
        'start_date' => $discount->start_date,
        'end_date' => $discount->end_date,
    ]);

    $this->get(route('discounts.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('discounts/index')
            ->where('discounts.data.0.discount', 12.5)
            ->where('discounts.data.0.used_count', 1)
        );
});

it('rejects a discount whose end date is before the start date', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('discounts.store'), [
        'product_id' => $product->id,
        'discount' => 10,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $response->assertSessionHasErrors('end_date');
});

it('rejects a discount above 100 percent', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('discounts.store'), [
        'product_id' => $product->id,
        'discount' => 150,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    $response->assertSessionHasErrors('discount');
});

it('creates a discount', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('discounts.store'), [
        'product_id' => $product->id,
        'discount' => 20,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
    ]);

    $response->assertRedirect(route('discounts.index'));

    $this->assertDatabaseHas('t_discounts', [
        'product_id' => $product->id,
        'discount' => 20,
    ]);
});

it('deletes a discount', function (): void {
    $discount = Discount::factory()->create();

    $this->delete(route('discounts.destroy', $discount))
        ->assertRedirect(route('discounts.index'));

    $this->assertSoftDeleted('t_discounts', ['id' => $discount->id]);
});
