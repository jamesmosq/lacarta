<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRegisterController extends Controller
{
    public function create()
    {
        return view('central.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'slug'            => 'required|string|max:50|unique:tenants,slug|regex:/^[a-z0-9\-]+$/',
            'email'           => 'required|email|unique:tenants,email',
            'password'        => 'required|string|min:8|confirmed',
        ], [
            'slug.regex'  => 'El identificador solo puede tener letras minúsculas, números y guiones.',
            'slug.unique' => 'Ese nombre de restaurante ya está tomado.',
        ]);

        // Crear el tenant (esto dispara CreateDatabase + MigrateDatabase automáticamente)
        $tenant = Tenant::create([
            'id'            => $request->slug,
            'name'          => $request->restaurant_name,
            'email'         => $request->email,
            'slug'          => $request->slug,
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Crear el usuario dueño dentro de la BD del tenant
        tenancy()->initialize($tenant);

        \App\Models\TenantUser::create([
            'name'     => $request->restaurant_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'owner',
        ]);

        tenancy()->end();

        return redirect()->route('home')->with('login_url',
            url("/{$request->slug}/admin/login")
        )->with('restaurant_name', $request->restaurant_name);
    }
}
