<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    /**
     * Return product details and the currently active discount as JSON.
     */
    public function show(Product $product): JsonResponse
    {
        $discount = $product->activeDiscountOn(Carbon::now());

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stockRemaining(),
                'discount' => $discount === null ? null : [
                    'percent' => $discount->discount,
                    'start_date' => $discount->start_date->toDateString(),
                    'end_date' => $discount->end_date->toDateString(),
                ],
            ],
        ]);
    }
}
