<?php

namespace App\Http\Controllers;

use App\Models\SalesDiscount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SalesDiscountController extends Controller
{
    /**
     * Display a listing of the used discounts.
     */
    public function index(Request $request): Response
    {
        $salesDiscounts = SalesDiscount::query()
            ->with(['sale.product', 'discount.product'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('sales-discounts/index', [
            'salesDiscounts' => $salesDiscounts->through(fn (SalesDiscount $salesDiscount) => [
                'id' => $salesDiscount->id,
                'sale_id' => $salesDiscount->sales_id,
                'product' => $salesDiscount->sale?->product,
                'qty' => $salesDiscount->sale?->qty,
                'sale_date' => $salesDiscount->sale?->created_at,
                'discount' => $salesDiscount->discount?->discount,
                'start_date' => $salesDiscount->start_date,
                'end_date' => $salesDiscount->end_date,
            ]),
        ]);
    }

    /**
     * Remove the specified used-discount record.
     */
    public function destroy(SalesDiscount $salesDiscount): RedirectResponse
    {
        $salesDiscount->delete();

        return redirect()->route('sales-discounts.index')->with('success', 'Data diskon terpakai berhasil dihapus.');
    }
}
