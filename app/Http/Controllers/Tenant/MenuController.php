<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dish;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');
        $categories = Category::with('dishes')->orderBy('order')->get();
        return view('tenant.menu.index', compact('categories', 'tenant'));
    }

    public function createCategory()
    {
        return view('tenant.menu.create-category', ['tenant' => tenant('id')]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'order' => 'integer|min:0',
        ]);

        Category::create($request->only('name', 'order'));

        return redirect()->route('tenant.menu', ['tenant' => tenant('id')])
            ->with('success', 'Categoría creada.');
    }

    public function createDish()
    {
        $tenant = tenant('id');
        $categories = Category::orderBy('name')->get();
        return view('tenant.menu.create-dish', compact('categories', 'tenant'));
    }

    public function storeDish(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|numeric|min:0',
        ]);

        Dish::create($request->only('category_id', 'name', 'description', 'price'));

        return redirect()->route('tenant.menu', ['tenant' => tenant('id')])
            ->with('success', 'Plato agregado al menú.');
    }

    public function toggleAvailable(Request $request, Dish $dish)
    {
        $dish->update(['available' => !$dish->available]);
        return back()->with('success', 'Disponibilidad actualizada.');
    }

    public function destroyDish(Dish $dish)
    {
        $dish->delete();
        return back()->with('success', 'Plato eliminado.');
    }
}
