<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionExpiry
{
    // Horas máximas de sesión para meseros y cocina (dueño no expira)
    const MAX_HOURS = 10;

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) return $next($request);

        $user = Auth::user();

        // El dueño nunca expira
        if ($user->isOwner()) return $next($request);

        $loginTime = $request->session()->get('login_time');

        if ($loginTime && now()->diffInHours($loginTime) >= self::MAX_HOURS) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('tenant.login', ['tenant' => tenant('id')])
                ->with('warning', 'Tu sesión expiró después de ' . self::MAX_HOURS . ' horas. Vuelve a ingresar.');
        }

        return $next($request);
    }
}
