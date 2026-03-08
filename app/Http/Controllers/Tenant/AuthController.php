<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(string $tenant)
    {
        return view('tenant.auth.login', ['tenantSlug' => $tenant]);
    }

    public function login(Request $request, string $tenant)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant]);
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function logout(Request $request, string $tenant)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login', ['tenant' => $tenant]);
    }
}
