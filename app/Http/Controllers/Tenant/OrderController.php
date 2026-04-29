<?php

namespace App\Http\Controllers\Tenant;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');

        $orders = Order::with(['table', 'items.dish'])
            ->whereDate('created_at', today())
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'preparing' THEN 2 WHEN 'ready' THEN 3 ELSE 4 END")
            ->latest()
            ->get();

        return view('tenant.orders.index', compact('orders', 'tenant'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function kitchen()
    {
        $tenant = tenant('id');
        return view('tenant.orders.kitchen', compact('tenant'));
    }

    public function kitchenOrders(): JsonResponse
    {
        $orders = Order::with(['table', 'items.dish'])
            ->whereIn('status', ['pending', 'preparing'])
            ->whereDate('created_at', today())
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 ELSE 2 END")
            ->oldest()
            ->get()
            ->map(fn($o) => [
                'id'            => $o->id,
                'status'        => $o->status,
                'table'         => $o->table?->name,
                'customer_name' => $o->customer_name,
                'notes'         => $o->notes,
                'created_at'    => $o->created_at->format('h:i a'),
                'items'         => $o->items->map(fn($i) => [
                    'qty'  => $i->quantity,
                    'name' => $i->dish->name,
                    'note' => $i->notes,
                ]),
            ]);

        return response()->json($orders);
    }

    public function kitchenUpdate(Request $request, Order $order): JsonResponse
    {
        $request->validate(['status' => 'required|in:pending,preparing,ready,delivered']);
        $order->update(['status' => $request->status]);

        $order->load('table');
        OrderStatusUpdated::dispatch(
            $order->id,
            $request->status,
            $order->table?->greeted_at !== null,
            tenant('id'),
        );

        return response()->json(['ok' => true]);
    }
}
