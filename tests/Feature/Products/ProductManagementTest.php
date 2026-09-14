<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('shows the stock as the accumulation of acquisitions and sales', function (): void {
    $product = Product::factory()->create(['stock' => 10]);
    Stock::factory()->create(['product_id' => $product->id, 'qty' => 5]);
    Sale::factory()->create(['product_id' => $product->id, 'qty' => 3]);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/index')
            ->where('products.data.0.stock', 12)
        );
});

it('creates a product with validated data', function (): void {
    $response = $this->post(route('products.store'), [
        'name' => 'Kopi Gayo',
        'description' => 'Kopi arabika asal Aceh',
        'price' => 50000,
        'stock' => 20,
    ]);

    $response->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('m_products', [
        'name' => 'Kopi Gayo',
        'price' => 50000,
        'stock' => 20,
    ]);
});

it('rejects an empty product form', function (): void {
    $response = $this->post(route('products.store'), []);

    $response->assertSessionHasErrors(['name', 'price', 'stock']);
});

it('does not allow stock to fall below the quantity already sold', function (): void {
    $product = Product::factory()->create(['stock' => 10]);
    Sale::factory()->create(['product_id' => $product->id, 'qty' => 4]);

    $response = $this->put(route('products.update', $product), [
        'name' => $product->name,
        'description' => $product->description,
        'price' => $product->price,
        'stock' => 3,
    ]);

    $response->assertSessionHasErrors('stock');

    $this->assertDatabaseHas('m_products', ['id' => $product->id, 'stock' => 10]);
});

it('allows stock to be reduced down to the quantity already sold', function (): void {
    $product = Product::factory()->create(['stock' => 10]);
    Sale::factory()->create(['product_id' => $product->id, 'qty' => 4]);

    $response = $this->put(route('products.update', $product), [
        'name' => $product->name,
        'description' => $product->description,
        'price' => $product->price,
        'stock' => 4,
    ]);

    $response->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('m_products', ['id' => $product->id, 'stock' => 4]);
});

it('deletes a product', function (): void {
    $product = Product::factory()->create();

    $this->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'));

    $this->assertSoftDeleted('m_products', ['id' => $product->id]);
});
