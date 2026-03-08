<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');

        $ingredients = Ingredient::withCount('dishes')->orderBy('name')->get();
        $dishes      = Dish::with('ingredients')->orderBy('name')->get();

        return view('tenant.inventory.index', compact('tenant', 'ingredients', 'dishes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'unit'      => 'required|string|max:20',
            'stock'     => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
        ]);

        Ingredient::create($request->only('name', 'unit', 'stock', 'min_stock'));

        return back()->with('success', 'Ingrediente creado.');
    }

    public function restock(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.001',
        ]);

        $ingredient->increment('stock', $request->cantidad);
        $ingredient->refresh();
        $ingredient->syncDishesAvailability();

        return back()->with('success', "Stock de \"{$ingredient->name}\" actualizado.");
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return back()->with('success', 'Ingrediente eliminado.');
    }

    public function linkDish(Request $request, Dish $dish)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity'      => 'required|numeric|min:0.001',
        ]);

        $dish->ingredients()->syncWithoutDetaching([
            $request->ingredient_id => ['quantity' => $request->quantity],
        ]);

        $dish->load('ingredients');
        $dish->syncAvailabilityFromIngredients();

        return back()->with('success', 'Ingrediente vinculado al plato.');
    }

    public function unlinkDish(Dish $dish, Ingredient $ingredient)
    {
        $dish->ingredients()->detach($ingredient->id);

        $dish->load('ingredients');
        $dish->syncAvailabilityFromIngredients();

        return back()->with('success', 'Ingrediente desvinculado.');
    }
}
