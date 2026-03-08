<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Dish;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index(string $tenant)
    {
        $stats = [
            'orders_today'   => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'total_dishes'   => Dish::count(),
            'total_categories' => Category::count(),
        ];

        $recent_orders = Order::with(['table', 'items.dish'])
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PREPARING])
            ->latest()
            ->take(10)
            ->get();

        return view('tenant.dashboard', compact('stats', 'recent_orders', 'tenant'));
    }
}
