<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class CentralLoginController extends Controller
{
    public function show()
    {
        return view('central.login');
    }

    public function find(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $tenant = Tenant::where('email', $request->email)->first();

        if (!$tenant) {
            return back()->withErrors(['email' => 'No encontramos un restaurante con ese correo.'])->withInput();
        }

        if (!$tenant->is_active) {
            return back()->withErrors(['email' => 'Este restaurante está desactivado. Contacta al soporte.'])->withInput();
        }

        return redirect()->to("/{$tenant->slug}/admin/login");
    }
}
