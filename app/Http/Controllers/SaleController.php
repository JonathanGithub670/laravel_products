<?php

namespace App\Http\Controllers;

use App\Actions\RegisterSaleAction;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    /**
     * Display a listing of the sales.
     */
    public function index(Request $request): Response
    {
        $sales = Sale::query()
            ->with(['product', 'latestSalesDiscount.discount'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('sales/index', [
            'sales' => $sales->through(fn (Sale $sale) => [
                'id' => $sale->id,
                'product' => $sale->product,
                'qty' => $sale->qty,
                'date' => $sale->created_at,
                'discount_percent' => $sale->latestSalesDiscount?->discount?->discount,
                'total' => $sale->discountedTotal(),
            ]),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created sale. A discount that is valid on the sale date
     * is applied automatically.
     */
    public function store(StoreSaleRequest $request, RegisterSaleAction $registerSale): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'qty', 'sale_date']);

        /** @var Product $product */
        $product = Product::findOrFail($data['product_id']);

        $saleDate = isset($data['sale_date']) && $data['sale_date'] !== ''
            ? Carbon::parse($data['sale_date'])
            : Carbon::now();

        $registerSale->handle($product, $data['qty'], $saleDate);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil ditambahkan.');
    }

    /**
     * Update the specified sale.
     */
    public function update(UpdateSaleRequest $request, Sale $sale, RegisterSaleAction $registerSale): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'qty', 'sale_date']);

        /** @var Product $product */
        $product = Product::findOrFail($data['product_id']);

        $saleDate = isset($data['sale_date']) && $data['sale_date'] !== ''
            ? Carbon::parse($data['sale_date'])
            : $sale->created_at;

        $registerSale->update($sale, $product, $data['qty'], $saleDate);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui.');
    }

    /**
     * Remove the specified sale.
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}
