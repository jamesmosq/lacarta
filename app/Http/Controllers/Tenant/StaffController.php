<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Order;
use App\Models\Shift;
use App\Models\TenantUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');
        $staff  = TenantUser::where('role', '!=', 'owner')->orderBy('name')->get();
        return view('tenant.staff.index', compact('staff', 'tenant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:waiter,kitchen',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Ya existe un usuario con ese correo.',
        ]);

        TenantUser::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Usuario \"{$request->name}\" creado.");
    }

    public function destroy(TenantUser $staff)
    {
        if ($staff->isOwner()) {
            return back()->with('error', 'No se puede eliminar al dueño.');
        }

        $staff->delete();
        return back()->with('success', 'Usuario eliminado.');
    }

    public function resetPassword(Request $request, TenantUser $staff)
    {
        if ($staff->isOwner()) {
            return back()->with('error', 'No se puede modificar la contraseña del dueño desde aquí.');
        }

        $request->validate(['password' => 'required|string|min:6']);

        $staff->update(['password' => Hash::make($request->password)]);

        return back()->with('success', "Contraseña de \"{$staff->name}\" actualizada.");
    }

    public function stats(Request $request)
    {
        $periodo = $request->get('periodo', 'mes');

        [$from, $to] = match($periodo) {
            'hoy'    => [Carbon::today(), Carbon::today()],
            'semana' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'personalizado' => [
                Carbon::parse($request->get('desde', Carbon::now()->startOfMonth()->toDateString())),
                Carbon::parse($request->get('hasta', Carbon::today()->toDateString())),
            ],
            default  => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };

        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        $waiters = TenantUser::where('role', '!=', 'owner')->orderBy('name')->get();

        $stats = $waiters->map(function ($waiter) use ($fromDate, $toDate) {
            $orders = Order::where('user_id', $waiter->id)
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)
                ->count();

            $revenue = (float) Bill::where('waiter_id', $waiter->id)
                ->whereDate('paid_at', '>=', $fromDate)
                ->whereDate('paid_at', '<=', $toDate)
                ->sum('total');

            $completedShifts = Shift::where('user_id', $waiter->id)
                ->whereNotNull('ended_at')
                ->whereDate('started_at', '>=', $fromDate)
                ->whereDate('started_at', '<=', $toDate)
                ->get();

            $totalMinutes = $completedShifts->sum(fn($s) => $s->workedMinutes() ?? 0);

            return (object) [
                'waiter'       => $waiter,
                'orders'       => $orders,
                'revenue'      => $revenue,
                'avg_ticket'   => $orders > 0 ? round($revenue / $orders, 0) : 0,
                'hours_worked' => $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0,
                'shifts_count' => $completedShifts->count(),
            ];
        })->sortByDesc('orders')->values();

        return view('tenant.staff.stats', compact('stats', 'periodo', 'from', 'to'));
    }
}
