<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stock\StoreStockRequest;
use App\Http\Requests\Stock\UpdateStockRequest;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    /**
     * Display a listing of the stock entries.
     */
    public function index(Request $request): Response
    {
        $stocks = Stock::query()
            ->with('product')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('stocks/index', [
            'stocks' => $stocks,
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created stock entry.
     */
    public function store(StoreStockRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'qty']);

        Stock::create($data);

        return redirect()->route('stocks.index')->with('success', 'Pengadaan stok berhasil ditambahkan.');
    }

    /**
     * Update the specified stock entry.
     */
    public function update(UpdateStockRequest $request, Stock $stock): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'qty']);

        $stock->update($data);

        return redirect()->route('stocks.index')->with('success', 'Pengadaan stok berhasil diperbarui.');
    }

    /**
     * Remove the specified stock entry.
     */
    public function destroy(Stock $stock): RedirectResponse
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Pengadaan stok berhasil dihapus.');
    }
}
