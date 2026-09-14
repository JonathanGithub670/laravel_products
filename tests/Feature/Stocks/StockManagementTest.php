<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('creates a stock entry for a product', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('stocks.store'), [
        'product_id' => $product->id,
        'qty' => 25,
    ]);

    $response->assertRedirect(route('stocks.index'));

    $this->assertDatabaseHas('t_stocks', [
        'product_id' => $product->id,
        'qty' => 25,
    ]);
});

it('rejects a stock entry with zero quantity', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('stocks.store'), [
        'product_id' => $product->id,
        'qty' => 0,
    ]);

    $response->assertSessionHasErrors('qty');
});

it('does not allow reducing a stock entry when it would make the remaining stock negative', function (): void {
    $product = Product::factory()->create(['stock' => 5]);
    $stock = Stock::factory()->create(['product_id' => $product->id, 'qty' => 10]);
    Sale::factory()->create(['product_id' => $product->id, 'qty' => 8]);

    // Remaining stock is 7 (5 + 10 - 8); lowering the entry to 2 makes it -1.
    $response = $this->put(route('stocks.update', $stock), [
        'product_id' => $product->id,
        'qty' => 2,
    ]);

    $response->assertSessionHasErrors('qty');

    $this->assertDatabaseHas('t_stocks', ['id' => $stock->id, 'qty' => 10]);
});

it('allows raising a stock entry quantity', function (): void {
    $product = Product::factory()->create(['stock' => 5]);
    $stock = Stock::factory()->create(['product_id' => $product->id, 'qty' => 10]);

    $response = $this->put(route('stocks.update', $stock), [
        'product_id' => $product->id,
        'qty' => 15,
    ]);

    $response->assertRedirect(route('stocks.index'));

    $this->assertDatabaseHas('t_stocks', ['id' => $stock->id, 'qty' => 15]);
});

it('deletes a stock entry', function (): void {
    $stock = Stock::factory()->create();

    $this->delete(route('stocks.destroy', $stock))
        ->assertRedirect(route('stocks.index'));

    $this->assertSoftDeleted('t_stocks', ['id' => $stock->id]);
});
