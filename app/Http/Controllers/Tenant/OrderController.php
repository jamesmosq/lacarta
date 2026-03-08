<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(string $tenant)
    {
        $orders = Order::with(['table', 'items.dish'])
            ->whereDate('created_at', today())
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'preparing' THEN 2 WHEN 'ready' THEN 3 ELSE 4 END")
            ->latest()
            ->get();

        return view('tenant.orders.index', compact('orders', 'tenant'));
    }

    public function updateStatus(Request $request, string $tenant, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado.');
    }
}
