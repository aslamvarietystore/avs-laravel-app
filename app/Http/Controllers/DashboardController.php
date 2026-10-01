<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRevenue = (float) Order::query()
            ->where('order_status', 'completed')
            ->sum('total');

        $totalOrders = Order::query()->count();

        $activeProducts = Product::query()
            ->where('status', 'active')
            ->count();

        $lowStockItems = Product::query()
            ->where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        $recentOrders = Order::query()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'activeProducts',
            'lowStockItems',
            'recentOrders',
        ));
    }
}
