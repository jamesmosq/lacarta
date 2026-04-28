<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $tenants = Tenant::orderBy('name')->get();
        return view('central.superadmin.dashboard', compact('tenants'));
    }

    public function toggleActive(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Restaurante \"{$tenant->name}\" {$status}.");
    }
}
