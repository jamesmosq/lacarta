<?php

namespace App\Http\Controllers\Tenant;

use App\Events\NewOrderCreated;
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

        $total      = 0;
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

        $table->update(['occupied' => true]);

        NewOrderCreated::dispatch($order->id, tenant('id'));

        return redirect()
            ->route('tenant.waiter', ['tenant' => tenant('id')])
            ->with('success', "Pedido #{$order->id} registrado para {$table->name}.");
    }

    /** Editar un pedido pendiente. */
    public function editOrder(Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('tenant.waiter', ['tenant' => tenant('id')])
                ->with('error', 'Solo se pueden editar pedidos pendientes.');
        }

        $tenant     = tenant('id');
        $categories = Category::with('activeDishes')->where('active', true)->orderBy('order')->get();
        $order->load('items.dish');
        return view('tenant.waiter.edit-order', compact('order', 'categories', 'tenant'));
    }

    /** Guardar edición de pedido pendiente. */
    public function updateOrder(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('tenant.waiter', ['tenant' => tenant('id')])
                ->with('error', 'Solo se pueden editar pedidos pendientes.');
        }

        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.dish_id'  => 'required|exists:dishes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes'            => 'nullable|string|max:500',
        ]);

        $total      = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $dish     = Dish::findOrFail($item['dish_id']);
            $total   += $dish->price * $item['quantity'];
            $orderItems[] = [
                'dish_id'    => $dish->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $dish->price,
                'notes'      => $item['notes'] ?? null,
            ];
        }

        $order->items()->delete();
        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }
        $order->update(['total' => $total, 'notes' => $request->notes]);

        return redirect()
            ->route('tenant.waiter', ['tenant' => tenant('id')])
            ->with('success', "Pedido #{$order->id} actualizado.");
    }

    /** Historial de pedidos de una mesa en el día. */
    public function tableHistory(RestaurantTable $table)
    {
        $tenant = tenant('id');
        $orders = Order::with('items.dish', 'waiter')
            ->where('table_id', $table->id)
            ->whereDate('created_at', today())
            ->orderBy('id')
            ->get();

        return view('tenant.waiter.history', compact('table', 'orders', 'tenant'));
    }

    /** JSON: mesas asignadas al mesero sin saludar (para polling de notificaciones). */
    public function notifications()
    {
        $tables = RestaurantTable::where('assigned_waiter_id', Auth::id())
            ->whereNull('greeted_at')
            ->whereNotNull('assigned_at')
            ->get(['id', 'name', 'assigned_at']);

        return response()->json($tables);
    }

    /** Marca la mesa como saludada. */
    public function greet(RestaurantTable $table)
    {
        $table->update(['greeted_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
