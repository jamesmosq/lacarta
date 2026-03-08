<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tenant = tenant('id');

        [$start, $end, $periodo] = $this->resolvePeriod($request);

        $base = Order::whereBetween('created_at', [$start, $end]);

        $totalRevenue = (clone $base)->sum('total');
        $totalOrders  = (clone $base)->count();
        $avgOrder     = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $delivered    = (clone $base)->where('status', Order::STATUS_DELIVERED)->count();

        // Ingresos por día (para el gráfico)
        $revenueByDay = (clone $base)
            ->selectRaw("DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders_count")
            ->groupByRaw("DATE(created_at)")
            ->orderBy('date')
            ->get();

        // Pedidos por estado
        $byStatus = (clone $base)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // Platos más vendidos
        $topDishes = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('dishes', 'order_items.dish_id', '=', 'dishes.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('dishes.name, SUM(order_items.quantity) as total_qty, SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            ->groupBy('dishes.id', 'dishes.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('tenant.reports.index', compact(
            'tenant', 'periodo', 'start', 'end',
            'totalRevenue', 'totalOrders', 'avgOrder', 'delivered',
            'revenueByDay', 'byStatus', 'topDishes'
        ));
    }

    public function export(Request $request)
    {
        $tenant = tenant('id');

        [$start, $end] = $this->resolvePeriod($request);

        $orders = Order::with(['items.dish', 'table'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $filename = "ventas_{$tenant}_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // BOM para que Excel abra bien UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Pedido #', 'Fecha', 'Cliente', 'Mesa', 'Estado', 'Platos', 'Total']);

            foreach ($orders as $order) {
                $dishes = $order->items
                    ->map(fn($i) => "{$i->dish->name} x{$i->quantity}")
                    ->join(', ');

                fputcsv($handle, [
                    $order->id,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name ?? '-',
                    $order->table->name ?? 'Sin mesa',
                    $order->statusLabel(),
                    $dishes,
                    number_format($order->total, 0, ',', '.'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function resolvePeriod(Request $request): array
    {
        $periodo = $request->get('periodo', 'semana');

        $start = match ($periodo) {
            'hoy'    => Carbon::today()->startOfDay(),
            'mes'    => Carbon::now()->startOfMonth()->startOfDay(),
            'custom' => Carbon::parse($request->get('desde', today()))->startOfDay(),
            default  => Carbon::now()->startOfWeek()->startOfDay(),
        };

        $end = match ($periodo) {
            'hoy'    => Carbon::today()->endOfDay(),
            'mes'    => Carbon::now()->endOfMonth()->endOfDay(),
            'custom' => Carbon::parse($request->get('hasta', today()))->endOfDay(),
            default  => Carbon::now()->endOfWeek()->endOfDay(),
        };

        return [$start, $end, $periodo];
    }
}
