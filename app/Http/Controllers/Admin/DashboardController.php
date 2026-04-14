<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Statusi "Dorëzuar" — ndrysho nëse quhet ndryshe në databazë
    const DELIVERED = 'delivered';

    public function index()
    {
        // ── POROSITË ─────────────────────────────────────────────
        $ordersCount   = Order::count();
        $revenue       = Order::sum('total_amount');
        $pendingCount  = Order::whereNotIn('status', [self::DELIVERED])->count();

        // ── FITIMI — vetëm Dorëzuar ──────────────────────────────
        $deliveredIds = Order::where('status', self::DELIVERED)->pluck('id');

        $deliveredCount        = $deliveredIds->count();
        $deliveredRevenue      = Order::where('status', self::DELIVERED)->sum('total_amount');
        $avgOrderValue         = $deliveredCount > 0 ? $deliveredRevenue / $deliveredCount : 0;

        $todayDeliveredRevenue = Order::where('status', self::DELIVERED)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $monthDeliveredRevenue = Order::where('status', self::DELIVERED)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // ── PRODUKTE ─────────────────────────────────────────────
        $productsCount   = Product::count();
        $categoriesCount = Category::count();
        $outOfStock      = Product::where('stock', 0)->count();

        // ── ITEMS TË SHITURA ─────────────────────────────────────
        $itemsSold = OrderItem::whereIn('order_id', $deliveredIds)->sum('quantity');

        // ── LATEST ───────────────────────────────────────────────
        $latestOrders   = Order::latest()->take(6)->get();
        $latestProducts = Product::with(['images', 'category'])->latest()->take(5)->get();

        // ── TOP PRODUKTET (nga dorëzimet) ────────────────────────
        $topProducts = OrderItem::whereIn('order_id', $deliveredIds)
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->limit(6)
            ->with('product')
            ->get();

        // ── GRAFIKU — 12 muajt e vitit ───────────────────────────
        $monthNames = ['','Jan','Shk','Mar','Pri','Maj','Qer','Kor','Gus','Sht','Tet','Nën','Dhj'];

        $rawChart = Order::where('status', self::DELIVERED)
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartData = collect(range(1, 12))->map(fn($m) => (object)[
            'month'        => $m,
            'month_label'  => $monthNames[$m],
            'orders_count' => $rawChart->get($m)?->orders_count ?? 0,
            'revenue'      => $rawChart->get($m)?->revenue ?? 0,
        ]);

        return view('admin.dashboard', compact(
            // Porositë
            'ordersCount', 'revenue', 'pendingCount',
            // Fitimi
            'deliveredCount', 'deliveredRevenue',
            'todayDeliveredRevenue', 'monthDeliveredRevenue', 'avgOrderValue',
            // Produkte
            'productsCount', 'categoriesCount', 'outOfStock',
            // Items
            'itemsSold',
            // Lists
            'latestOrders', 'latestProducts', 'topProducts',
            // Chart
            'chartData'
        ));
    }
}