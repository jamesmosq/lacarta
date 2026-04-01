<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class SettingsController extends Controller
{
    public function toggleOpen()
    {
        $tenant = Tenant::find(tenant('id'));
        $tenant->update(['is_open' => !$tenant->is_open]);

        $label = $tenant->is_open ? 'Restaurante abierto.' : 'Restaurante cerrado. Los clientes no podrán hacer pedidos.';

        return back()->with('success', $label);
    }
}
