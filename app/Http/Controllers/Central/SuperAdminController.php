<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $tenants = Tenant::orderBy('name')->get();

        $totalRevenue = 0;
        $totalOrders  = 0;

        foreach ($tenants->where('is_active', true) as $tenant) {
            try {
                tenancy()->initialize($tenant);
                $totalRevenue += \App\Models\Bill::whereDate('paid_at', today())->sum('total');
                $totalOrders  += \App\Models\Order::whereDate('created_at', today())->count();
            } catch (\Throwable $e) {
                // skip tenant if schema unreachable
            } finally {
                try { tenancy()->end(); } catch (\Throwable) {}
            }
        }

        $avgTicket = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return view('central.superadmin.dashboard', compact(
            'tenants',
            'totalRevenue',
            'totalOrders',
            'avgTicket'
        ));
    }

    public function toggleActive(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Restaurante \"{$tenant->name}\" {$status}.");
    }

    public function tenantDetail(Tenant $tenant)
    {
        $activeOrders = collect();
        $recentOrders = collect();
        $team         = collect();
        $topDishes    = collect();
        $totalRevenue = 0;
        $totalOrders  = 0;
        $error        = null;

        try {
            tenancy()->initialize($tenant);

            $activeOrders = \App\Models\Order::whereIn('status', ['pending', 'preparing'])
                ->with('items.dish')
                ->latest()
                ->get();

            $recentOrders = \App\Models\Order::with('items.dish', 'waiter')
                ->latest()
                ->limit(10)
                ->get();

            $team = \App\Models\TenantUser::all();

            $totalRevenue = \App\Models\Bill::sum('total');
            $totalOrders  = \App\Models\Order::count();

            $topDishes = \App\Models\OrderItem::select('dish_id', DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('dish_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->with('dish')
                ->get();

            tenancy()->end();
        } catch (\Throwable $e) {
            tenancy()->end();
            $error = 'No se pudo conectar al esquema del restaurante.';
        }

        $notifications = TenantNotification::where('tenant_id', $tenant->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('central.superadmin.tenant-detail', compact(
            'tenant',
            'activeOrders',
            'recentOrders',
            'team',
            'totalRevenue',
            'totalOrders',
            'topDishes',
            'notifications',
            'error'
        ));
    }

    public function updatePlan(Request $request, Tenant $tenant)
    {
        $request->validate([
            'plan'          => 'required|in:trial,basic,pro',
            'trial_ends_at' => 'nullable|date',
        ]);

        $data = ['plan' => $request->plan];
        if ($request->filled('trial_ends_at')) {
            $data['trial_ends_at'] = $request->trial_ends_at;
        }

        $tenant->update($data);

        return back()->with('success', "Plan de \"{$tenant->name}\" actualizado a {$request->plan}.");
    }

    public function impersonate(Tenant $tenant)
    {
        try {
            tenancy()->initialize($tenant);
            $owner = \App\Models\TenantUser::where('role', 'owner')->first();

            if (!$owner) {
                tenancy()->end();
                return back()->with('error', 'Este restaurante no tiene un owner registrado.');
            }

            Auth::guard('web')->login($owner);
            tenancy()->end();
        } catch (\Throwable $e) {
            tenancy()->end();
            return back()->with('error', 'No se pudo acceder al esquema del restaurante.');
        }

        session([
            'impersonating_as_superadmin' => true,
            'impersonating_tenant_id'     => $tenant->id,
            'impersonating_tenant_name'   => $tenant->name,
        ]);

        return redirect("/{$tenant->slug}/admin/dashboard");
    }

    public function stopImpersonating(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->forget([
            'impersonating_as_superadmin',
            'impersonating_tenant_id',
            'impersonating_tenant_name',
        ]);
        return redirect()->route('superadmin.dashboard');
    }

    public function resetOwnerPassword(Request $request, Tenant $tenant)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        try {
            tenancy()->initialize($tenant);
            $owner = \App\Models\TenantUser::where('role', 'owner')->first();

            if (!$owner) {
                tenancy()->end();
                return back()->with('error', 'Este restaurante no tiene un owner registrado.');
            }

            $owner->update(['password' => Hash::make($request->password)]);
            tenancy()->end();
        } catch (\Throwable $e) {
            try { tenancy()->end(); } catch (\Throwable) {}
            return back()->with('error', 'No se pudo restablecer la contraseña.');
        }

        return back()->with('success', "Contraseña del owner de \"{$tenant->name}\" restablecida correctamente.");
    }

    public function sendNotification(Request $request, Tenant $tenant)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        TenantNotification::create([
            'tenant_id' => $tenant->id,
            'message'   => $request->message,
            'sent_by'   => 'SuperAdmin',
        ]);

        return back()->with('success', "Notificacion enviada a \"{$tenant->name}\".");
    }
}
