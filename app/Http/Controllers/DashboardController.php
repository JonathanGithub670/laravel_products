<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with sales infographics.
     */
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $today = Carbon::today();

        $startDate = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : $today->copy()->startOfMonth();

        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : $today->copy()->endOfMonth();

        if ($startDate->gt($endDate)) {
            $endDate = $startDate->copy()->endOfDay();
        }

        $salesQuery = Sale::query()
            ->with(['product', 'latestSalesDiscount.discount'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $latestSales = $salesQuery->get(['id', 'product_id', 'qty', 'created_at'])
            ->sortByDesc('created_at')
            ->take(5);

        $totalSales = $salesQuery->get(['id', 'qty', 'product_id'])
            ->sum(fn (Sale $sale) => $sale->discountedTotal());

        return Inertia::render('dashboard/index', [
            'latestSales' => $latestSales->values()->map(fn (Sale $sale) => [
                'id' => $sale->id,
                'product_name' => $sale->product->name,
                'qty' => $sale->qty,
                'date' => $sale->created_at,
                'total' => $sale->discountedTotal(),
                'discount_percent' => $sale->latestSalesDiscount?->discount?->discount,
            ]),
            'totalSales' => round($totalSales, 2),
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
        ]);
    }
}
