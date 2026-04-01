<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, $roles)) {
            // Redirigir según el rol del usuario autenticado
            if ($user?->isKitchen()) {
                return redirect()->route('tenant.kitchen', ['tenant' => tenant('id')]);
            }
            if ($user?->isWaiter()) {
                return redirect()->route('tenant.waiter', ['tenant' => tenant('id')]);
            }
            return redirect()->route('tenant.dashboard', ['tenant' => tenant('id')]);
        }

        return $next($request);
    }
}
