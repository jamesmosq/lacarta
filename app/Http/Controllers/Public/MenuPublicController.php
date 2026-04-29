<?php

namespace App\Http\Controllers\Public;

use App\Events\NewOrderCreated;
use App\Events\TableAssignedToWaiter;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuPublicController extends Controller
{
    private function getLang(Request $request): array
    {
        $lang = $request->cookie('menu_lang', 'es');
        $file = in_array($lang, ['es', 'en']) ? $lang : 'es';
        return require resource_path("lang/menu_{$file}.php");
    }

    public function setLang(Request $request, string $lang)
    {
        $lang = in_array($lang, ['es', 'en']) ? $lang : 'es';
        return back()->withCookie(cookie()->forever('menu_lang', $lang));
    }

    public function index(Request $request)
    {
        $tenant      = tenant('id');
        $tenantModel = Tenant::find($tenant);
        $t           = $this->getLang($request);

        if (!$tenantModel->is_open) {
            return view('public.closed', compact('tenant', 'tenantModel'));
        }

        $categories = Cache::remember('menu-categories', 60, fn () =>
            Category::with('activeDishes')
                ->where('active', true)
                ->orderBy('order')
                ->get()
        );

        $table = $request->filled('mesa')
            ? RestaurantTable::where('id', $request->mesa)->where('active', true)->first()
            : null;

        // Si hay mesa y no tiene mesero asignado, mostrar selección de mesero
        if ($table && !$table->assigned_waiter_id && !$request->boolean('skip_waiter')) {
            $waiters = TenantUser::where('role', 'waiter')
                ->where('available', true)
                ->orderBy('name')
                ->get();

            if ($waiters->isNotEmpty()) {
                return view('public.select-waiter', compact('table', 'waiters', 'tenant', 'tenantModel', 't'));
            }
        }

        $lang = $request->cookie('menu_lang', 'es');
        return view('public.menu', compact('categories', 'tenant', 'table', 't', 'lang'));
    }

    /** Asigna el mesero elegido a la mesa y redirige al menú. */
    public function selectWaiter(Request $request, RestaurantTable $table)
    {
        $request->validate([
            'waiter_id' => 'required|exists:users,id',
        ]);

        $table->update([
            'assigned_waiter_id' => $request->waiter_id,
            'assigned_at'        => now(),
            'greeted_at'         => null,
        ]);

        TableAssignedToWaiter::dispatch(
            $table->id,
            $table->name,
            (int) $request->waiter_id,
            tenant('id'),
        );

        return redirect()->route('tenant.menu.public', [
            'tenant' => tenant('id'),
            'mesa'   => $table->id,
        ]);
    }

    public function order(Request $request)
    {
        $tenant = tenant('id');

        $request->validate([
            'customer_name'       => 'nullable|string|max:100',
            'table_id'            => 'nullable|exists:tables,id',
            'items'               => 'required|array|min:1',
            'items.*.dish_id'     => 'required|exists:dishes,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'notes'               => 'nullable|string|max:500',
        ]);

        $total      = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $dish      = Dish::findOrFail($item['dish_id']);
            $subtotal  = $dish->price * $item['quantity'];
            $total    += $subtotal;

            $orderItems[] = [
                'dish_id'    => $dish->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $dish->price,
                'notes'      => $item['notes'] ?? null,
            ];
        }

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_id'      => $request->table_id ?: null,
            'status'        => Order::STATUS_PENDING,
            'total'         => $total,
            'notes'         => $request->notes,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        if ($request->table_id) {
            RestaurantTable::where('id', $request->table_id)->update(['occupied' => true]);
        }

        $this->deductInventory($order);

        NewOrderCreated::dispatch($order->id, tenant('id'));

        return redirect()->route('tenant.order.status', [
            'tenant' => $tenant,
            'order'  => $order->id,
        ]);
    }

    /** JSON: estado del pedido para polling en la vista pública. */
    public function statusJson(Order $order)
    {
        $order->load('table');
        return response()->json([
            'status'  => $order->status,
            'label'   => $order->statusLabel(),
            'greeted' => $order->table?->greeted_at !== null,
        ]);
    }

    private function deductInventory(Order $order): void
    {
        $items               = $order->items()->with('dish.ingredients')->get();
        $affectedIngredients = collect();

        foreach ($items as $item) {
            foreach ($item->dish->ingredients as $ingredient) {
                Cache::store('redis')->lock('inv.' . tenant('id') . '.' . $ingredient->id, 5)
                    ->block(3, function () use ($ingredient, $item, &$affectedIngredients) {
                        $ingredient->refresh();
                        $consumed = $item->quantity * $ingredient->pivot->quantity;
                        $ingredient->update(['stock' => max(0, $ingredient->stock - $consumed)]);
                        $affectedIngredients->push($ingredient->refresh());
                    });
            }
        }

        $affectedIngredients->unique('id')->each->syncDishesAvailability();
    }

    public function status(Request $request, Order $order)
    {
        $tenant = tenant('id');
        $order->load('items.dish', 'table');
        $t = $this->getLang($request);
        return view('public.order-status', compact('order', 'tenant', 't'));
    }
}
