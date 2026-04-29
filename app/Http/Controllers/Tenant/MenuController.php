<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
        Cache::forget('menu-categories');

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
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only('category_id', 'name', 'description', 'price');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('dishes/' . tenant('id'), 'public');
        }

        Dish::create($data);
        Cache::forget('menu-categories');

        return redirect()->route('tenant.menu', ['tenant' => tenant('id')])
            ->with('success', 'Plato agregado al menú.');
    }

    public function editCategory(Category $category)
    {
        return view('tenant.menu.edit-category', ['tenant' => tenant('id'), 'category' => $category]);
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'order' => 'integer|min:0',
        ]);

        $category->update($request->only('name', 'order'));
        Cache::forget('menu-categories');

        return redirect()->route('tenant.menu', ['tenant' => tenant('id')])
            ->with('success', 'Categoría actualizada.');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        Cache::forget('menu-categories');
        return back()->with('success', 'Categoría eliminada.');
    }

    public function editDish(Dish $dish)
    {
        $tenant = tenant('id');
        $categories = Category::orderBy('name')->get();
        return view('tenant.menu.edit-dish', compact('dish', 'categories', 'tenant'));
    }

    public function updateDish(Request $request, Dish $dish)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:150',
            'description'   => 'nullable|string|max:500',
            'price'         => 'required|numeric|min:0',
            'image'         => 'nullable|image|max:2048',
            'remove_image'  => 'nullable|boolean',
        ]);

        $data = $request->only('category_id', 'name', 'description', 'price');

        if ($request->hasFile('image')) {
            if ($dish->image) Storage::disk('public')->delete($dish->image);
            $data['image'] = $request->file('image')->store('dishes/' . tenant('id'), 'public');
        } elseif ($request->boolean('remove_image') && $dish->image) {
            Storage::disk('public')->delete($dish->image);
            $data['image'] = null;
        }

        $dish->update($data);
        Cache::forget('menu-categories');

        return redirect()->route('tenant.menu', ['tenant' => tenant('id')])
            ->with('success', 'Plato actualizado.');
    }

    public function toggleAvailable(Request $request, Dish $dish)
    {
        $dish->update(['available' => !$dish->available]);
        Cache::forget('menu-categories');
        return back()->with('success', 'Disponibilidad actualizada.');
    }

    public function destroyDish(Dish $dish)
    {
        if ($dish->image) Storage::disk('public')->delete($dish->image);
        $dish->delete();
        Cache::forget('menu-categories');
        return back()->with('success', 'Plato eliminado.');
    }
}
