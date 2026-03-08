<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class MenuPublicController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');

        $categories = Category::with('activeDishes')
            ->where('active', true)
            ->orderBy('order')
            ->get();

        return view('public.menu', compact('categories', 'tenant'));
    }

    public function order(Request $request)
    {
        $tenant = tenant('id');

        $request->validate([
            'customer_name'       => 'nullable|string|max:100',
            'items'               => 'required|array|min:1',
            'items.*.dish_id'     => 'required|exists:dishes,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'notes'               => 'nullable|string|max:500',
        ]);

        $total = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $dish = Dish::findOrFail($item['dish_id']);
            $subtotal = $dish->price * $item['quantity'];
            $total += $subtotal;

            $orderItems[] = [
                'dish_id'    => $dish->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $dish->price,
                'notes'      => $item['notes'] ?? null,
            ];
        }

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'status'        => Order::STATUS_PENDING,
            'total'         => $total,
            'notes'         => $request->notes,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        $this->deductInventory($order);

        return redirect()->route('tenant.order.status', [
            'tenant' => $tenant,
            'order'  => $order->id,
        ]);
    }

    private function deductInventory(Order $order): void
    {
        $items = $order->items()->with('dish.ingredients')->get();

        $affectedIngredients = collect();

        foreach ($items as $item) {
            foreach ($item->dish->ingredients as $ingredient) {
                $consumed = $item->quantity * $ingredient->pivot->quantity;
                $newStock  = max(0, $ingredient->stock - $consumed);
                $ingredient->update(['stock' => $newStock]);
                $affectedIngredients->push($ingredient->refresh());
            }
        }

        // Recalcular disponibilidad de platos afectados (sin duplicados)
        $affectedIngredients->unique('id')->each->syncDishesAvailability();
    }

    public function status(Order $order)
    {
        $tenant = tenant('id');
        $order->load('items.dish');
        return view('public.order-status', compact('order', 'tenant'));
    }
}
