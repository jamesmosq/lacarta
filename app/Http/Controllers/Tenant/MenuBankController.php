<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dish;
use App\Models\MenuBank;
use Illuminate\Http\Request;

class MenuBankController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $items = MenuBank::when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")
                                                      ->orWhere('category_hint', 'ilike', "%{$search}%"))
            ->orderBy('category_hint')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();
        $tenant     = tenant('id');

        return view('tenant.menu-bank.index', compact('items', 'categories', 'search', 'tenant'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.bank_id'     => 'required|exists:menu_bank,id',
            'items.*.category_id' => 'required',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        $imported = 0;

        foreach ($request->items as $row) {
            $bankItem = MenuBank::findOrFail($row['bank_id']);

            // Si category_id es "nueva", crear la categoría sugerida
            if ($row['category_id'] === 'nueva') {
                $category = Category::firstOrCreate(
                    ['name' => $bankItem->category_hint ?? 'General'],
                    ['order' => 0, 'active' => true]
                );
            } else {
                $category = Category::findOrFail($row['category_id']);
            }

            Dish::create([
                'category_id' => $category->id,
                'name'        => $bankItem->name,
                'description' => $bankItem->description,
                'price'       => $row['price'],
                'available'   => true,
                'order'       => 0,
            ]);

            $imported++;
        }

        return redirect()
            ->route('tenant.menu', ['tenant' => tenant('id')])
            ->with('success', "{$imported} plato(s) importado(s) al menú.");
    }
}
