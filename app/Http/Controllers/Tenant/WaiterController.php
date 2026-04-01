<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');
        $tables = RestaurantTable::where('active', true)->orderBy('name')->get();

        // Para cada mesa activa, buscar si tiene pedidos activos hoy
        $activeOrders = Order::with('table')
            ->whereDate('created_at', today())
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->whereNotNull('table_id')
            ->get()
            ->keyBy('table_id');

        return view('tenant.waiter.index', compact('tables', 'activeOrders', 'tenant'));
    }

    public function createOrder(RestaurantTable $table)
    {
        $tenant     = tenant('id');
        $categories = Category::with('activeDishes')->where('active', true)->orderBy('order')->get();
        return view('tenant.waiter.create-order', compact('table', 'categories', 'tenant'));
    }

    public function storeOrder(Request $request, RestaurantTable $table)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.dish_id'  => 'required|exists:dishes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name'    => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ]);

        $total = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $dish     = Dish::findOrFail($item['dish_id']);
            $subtotal = $dish->price * $item['quantity'];
            $total   += $subtotal;

            $orderItems[] = [
                'dish_id'    => $dish->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $dish->price,
                'notes'      => $item['notes'] ?? null,
            ];
        }

        $order = Order::create([
            'table_id'      => $table->id,
            'user_id'       => Auth::id(),
            'customer_name' => $request->customer_name,
            'status'        => Order::STATUS_PENDING,
            'total'         => $total,
            'notes'         => $request->notes,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        return redirect()
            ->route('tenant.waiter', ['tenant' => tenant('id')])
            ->with('success', "Pedido #{$order->id} registrado para {$table->name}.");
    }
}
