<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashController extends Controller
{
    public function show(RestaurantTable $table)
    {
        $orders = Order::with('items.dish')
            ->where('table_id', $table->id)
            ->whereDate('created_at', today())
            ->orderBy('id')
            ->get();

        $total  = $orders->sum('total');
        $tenant = tenant('id');

        return view('tenant.cash.show', compact('table', 'orders', 'total', 'tenant'));
    }

    public function pay(Request $request, RestaurantTable $table)
    {
        $request->validate([
            'payment_method' => 'required|in:efectivo,tarjeta,transferencia',
            'notes'          => 'nullable|string|max:300',
        ]);

        $orders = Order::where('table_id', $table->id)
            ->whereDate('created_at', today())
            ->get();

        $total = $orders->sum('total');

        Bill::create([
            'table_id'       => $table->id,
            'waiter_id'      => Auth::id(),
            'total'          => $total,
            'payment_method' => $request->payment_method,
            'notes'          => $request->notes,
            'paid_at'        => now(),
        ]);

        // Marcar todos los pedidos del día como entregados
        $orders->each(fn($o) => $o->update(['status' => Order::STATUS_DELIVERED]));

        // Liberar la mesa
        $table->update(['occupied' => false]);

        return redirect()
            ->route('tenant.waiter', ['tenant' => tenant('id')])
            ->with('success', "Mesa {$table->name} cobrada. Total: $" . number_format($total, 2));
    }
}
