<?php

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('rejects a sale when the stock is insufficient', function (): void {
    $product = Product::factory()->create(['stock' => 5]);

    $response = $this->post(route('sales.store'), [
        'product_id' => $product->id,
        'qty' => 6,
    ]);

    $response->assertSessionHasErrors('qty');

    $this->assertDatabaseCount('t_sales', 0);
});

it('rejects a sale with a negative quantity', function (): void {
    $product = Product::factory()->create(['stock' => 10]);

    $response = $this->post(route('sales.store'), [
        'product_id' => $product->id,
        'qty' => -1,
    ]);

    $response->assertSessionHasErrors('qty');
});

it('creates a sale and records the valid discount automatically', function (): void {
    $product = Product::factory()->create(['stock' => 50]);
    $discount = Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 10,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    $response = $this->post(route('sales.store'), [
        'product_id' => $product->id,
        'qty' => 2,
        'sale_date' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('sales.index'));

    $sale = Sale::where('product_id', $product->id)->firstOrFail();

    $this->assertDatabaseHas('t_sales_discounts', [
        'sales_id' => $sale->id,
        'discount_id' => $discount->id,
    ]);
});

it('does not apply a discount when no discount is valid on the sale date', function (): void {
    $product = Product::factory()->create(['stock' => 50]);
    Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 10,
        'start_date' => now()->subDays(3)->toDateString(),
        'end_date' => now()->subDays(2)->toDateString(),
    ]);

    $this->post(route('sales.store'), [
        'product_id' => $product->id,
        'qty' => 1,
        'sale_date' => now()->toDateString(),
    ]);

    $this->assertDatabaseCount('t_sales_discounts', 0);
});

it('applies the most recently created discount when several are valid on the sale date', function (): void {
    $product = Product::factory()->create(['stock' => 50]);
    Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 10,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);
    $newestDiscount = Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 20,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    $this->post(route('sales.store'), [
        'product_id' => $product->id,
        'qty' => 1,
        'sale_date' => now()->toDateString(),
    ]);

    $this->assertDatabaseHas('t_sales_discounts', [
        'discount_id' => $newestDiscount->id,
    ]);
});

it('does not apply a discount when the sale date is outside the discount period', function (): void {
    $product = Product::factory()->create(['stock' => 50]);
    Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 10,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    $this->post(route('sales.store'), [
        'product_id' => $product->id,
        'qty' => 1,
        'sale_date' => now()->subDays(5)->toDateString(),
    ]);

    $this->assertDatabaseCount('t_sales_discounts', 0);
});

it('updates a sale and re-applies the discount for the new sale date', function (): void {
    $product = Product::factory()->create(['stock' => 50]);
    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'qty' => 1,
        'created_at' => now()->subDays(5),
    ]);
    $discount = Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 15,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    $response = $this->put(route('sales.update', $sale), [
        'product_id' => $product->id,
        'qty' => 3,
        'sale_date' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('sales.index'));

    $this->assertDatabaseHas('t_sales_discounts', [
        'sales_id' => $sale->id,
        'discount_id' => $discount->id,
    ]);

    $this->assertDatabaseHas('t_sales', [
        'id' => $sale->id,
        'qty' => 3,
    ]);
});

it('rejects updating a sale when the new quantity exceeds the available stock', function (): void {
    $product = Product::factory()->create(['stock' => 10]);
    $sale = Sale::factory()->create(['product_id' => $product->id, 'qty' => 2]);

    // Available stock is 10; the sale's own 2 units are released, so 13 is too many.
    $response = $this->put(route('sales.update', $sale), [
        'product_id' => $product->id,
        'qty' => 13,
        'sale_date' => Carbon::now()->toDateString(),
    ]);

    $response->assertSessionHasErrors('qty');

    $this->assertDatabaseHas('t_sales', ['id' => $sale->id, 'qty' => 2]);
});

it('deletes a sale', function (): void {
    $sale = Sale::factory()->create();

    $this->delete(route('sales.destroy', $sale))
        ->assertRedirect(route('sales.index'));

    $this->assertSoftDeleted('t_sales', ['id' => $sale->id]);
});
