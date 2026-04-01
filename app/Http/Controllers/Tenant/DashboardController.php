<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Dish;
use App\Models\Category;
use App\Models\Tenant;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd   = Carbon::today()->endOfDay();
        $weekStart  = Carbon::now()->startOfWeek()->startOfDay();

        $stats = [
            'orders_today'     => Order::whereDate('created_at', today())->count(),
            'pending_orders'   => Order::where('status', Order::STATUS_PENDING)->count(),
            'revenue_today'    => Order::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total'),
            'revenue_week'     => Order::whereBetween('created_at', [$weekStart, $todayEnd])->sum('total'),
        ];

        $recent_orders = Order::with(['table', 'items.dish'])
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PREPARING])
            ->latest()
            ->take(8)
            ->get();

        $top_dishes = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('dishes', 'order_items.dish_id', '=', 'dishes.id')
            ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
            ->selectRaw('dishes.name, dishes.image, SUM(order_items.quantity) as total_qty, SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            ->groupBy('dishes.id', 'dishes.name', 'dishes.image')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $revenue_by_hour = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw("EXTRACT(HOUR FROM created_at)::int as hour, SUM(total) as revenue, COUNT(*) as orders_count")
            ->groupByRaw("EXTRACT(HOUR FROM created_at)::int")
            ->orderBy('hour')
            ->get();

        $tenantModel = Tenant::find($tenant);

        return view('tenant.dashboard', compact(
            'stats', 'recent_orders', 'tenant', 'top_dishes', 'revenue_by_hour', 'tenantModel'
        ));
    }
}
