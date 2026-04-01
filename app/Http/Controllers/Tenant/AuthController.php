<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('tenant.auth.login', ['tenantSlug' => tenant('id')]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $t    = tenant('id');

            return match(true) {
                $user->isKitchen() => redirect()->route('tenant.kitchen', ['tenant' => $t]),
                $user->isWaiter()  => redirect()->route('tenant.waiter',  ['tenant' => $t]),
                default            => redirect()->route('tenant.dashboard', ['tenant' => $t]),
            };
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login', ['tenant' => tenant('id')]);
    }
}
