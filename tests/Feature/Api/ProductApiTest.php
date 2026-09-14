<?php

use App\Models\Discount;
use App\Models\Product;
use App\Models\User;

it('returns 401 when the client is not authenticated', function (): void {
    $product = Product::factory()->create();

    $this->getJson("/api/products/{$product->id}")
        ->assertUnauthorized();
});

it('returns the product description and the active discount as JSON', function (): void {
    $this->actingAs(User::factory()->create());

    $product = Product::factory()->create([
        'description' => 'Deskripsi produk uji coba',
    ]);
    Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 25,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    $this->getJson("/api/products/{$product->id}")
        ->assertOk()
        ->assertJson([
            'data' => [
                'id' => $product->id,
                'description' => 'Deskripsi produk uji coba',
                'discount' => [
                    'percent' => 25,
                    'start_date' => now()->subDay()->toDateString(),
                    'end_date' => now()->addDay()->toDateString(),
                ],
            ],
        ]);
});

it('returns null for the discount when none is active', function (): void {
    $this->actingAs(User::factory()->create());

    $product = Product::factory()->create();
    Discount::factory()->create([
        'product_id' => $product->id,
        'discount' => 25,
        'start_date' => now()->subDays(5)->toDateString(),
        'end_date' => now()->subDays(3)->toDateString(),
    ]);

    $this->getJson("/api/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('data.discount', null);
});

it('returns 404 for a product that does not exist', function (): void {
    $this->actingAs(User::factory()->create());

    $this->getJson('/api/products/9999')
        ->assertNotFound();
});
