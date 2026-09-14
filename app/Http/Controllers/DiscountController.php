<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discount\StoreDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiscountController extends Controller
{
    /**
     * Display a listing of the discounts.
     */
    public function index(Request $request): Response
    {
        $discounts = Discount::query()
            ->with('product')
            ->withCount('salesDiscounts')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('discounts/index', [
            'discounts' => $discounts->through(fn (Discount $discount) => [
                'id' => $discount->id,
                'product' => $discount->product,
                'discount' => $discount->discount,
                'start_date' => $discount->start_date,
                'end_date' => $discount->end_date,
                'used_count' => $discount->sales_discounts_count,
            ]),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created discount.
     */
    public function store(StoreDiscountRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'discount', 'start_date', 'end_date']);

        Discount::create($data);

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil ditambahkan.');
    }

    /**
     * Update the specified discount.
     */
    public function update(UpdateDiscountRequest $request, Discount $discount): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'discount', 'start_date', 'end_date']);

        $discount->update($data);

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    /**
     * Remove the specified discount.
     */
    public function destroy(Discount $discount): RedirectResponse
    {
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dihapus.');
    }
}
