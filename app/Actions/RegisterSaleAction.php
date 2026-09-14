<?php

namespace App\Actions;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDiscount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RegisterSaleAction
{
    /**
     * Register a sale with the given quantity on the given date.
     *
     * When the product has a discount that is valid on the sale date, the most
     * recently created one is applied automatically.
     */
    public function handle(Product $product, int $qty, Carbon $saleDate): Sale
    {
        return DB::transaction(function () use ($product, $qty, $saleDate) {
            $sale = Sale::create([
                'product_id' => $product->id,
                'qty' => $qty,
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            $discount = $product->activeDiscountOn($saleDate);

            if ($discount instanceof Discount) {
                $this->attachDiscount($sale, $discount);
            }

            return $sale;
        });
    }

    /**
     * Update an existing sale: quantity, product, date, and re-applied discount.
     */
    public function update(Sale $sale, Product $product, int $qty, Carbon $saleDate): Sale
    {
        return DB::transaction(function () use ($sale, $product, $qty, $saleDate) {
            $sale->update([
                'product_id' => $product->id,
                'qty' => $qty,
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            $sale->salesDiscounts()->delete();

            $discount = $product->activeDiscountOn($saleDate);

            if ($discount instanceof Discount) {
                $this->attachDiscount($sale, $discount);
            }

            return $sale;
        });
    }

    /**
     * Record that the given discount was applied to the sale, snapshotting
     * its validity period for the "diskon terpakai" (t_sales_discounts) table.
     */
    private function attachDiscount(Sale $sale, Discount $discount): SalesDiscount
    {
        return $sale->salesDiscounts()->create([
            'discount_id' => $discount->id,
            'start_date' => $discount->start_date,
            'end_date' => $discount->end_date,
        ]);
    }
}
