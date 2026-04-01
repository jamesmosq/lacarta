<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');
        $tables = RestaurantTable::orderBy('name')->get();
        return view('tenant.tables.index', compact('tables', 'tenant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        RestaurantTable::create(['name' => $request->name]);

        return back()->with('success', "Mesa \"{$request->name}\" creada.");
    }

    public function toggleActive(RestaurantTable $table)
    {
        $table->update(['active' => !$table->active]);
        return back()->with('success', 'Estado de mesa actualizado.');
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return back()->with('success', 'Mesa eliminada.');
    }
}
