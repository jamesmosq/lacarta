<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /** Perfil del usuario autenticado + turno activo + últimos 14 días. */
    public function profile()
    {
        $user        = Auth::user();
        $activeShift = $user->activeShift();
        $recentShifts = $user->shifts()
            ->whereNotNull('ended_at')
            ->orderByDesc('started_at')
            ->limit(10)
            ->get();

        return view('tenant.shifts.profile', compact('user', 'activeShift', 'recentShifts'));
    }

    /** Inicia turno: crea registro y marca al usuario disponible. */
    public function start()
    {
        $user = Auth::user();

        if ($user->activeShift()) {
            return back()->with('warning', 'Ya tienes un turno activo.');
        }

        Shift::create([
            'user_id'        => $user->id,
            'started_at'     => now(),
            'expected_hours' => 8,
        ]);

        $user->update(['available' => true]);

        return back()->with('success', 'Turno iniciado. ¡Buen trabajo!');
    }

    /** Termina turno activo y calcula horas extra. */
    public function end(Shift $shift)
    {
        if ($shift->user_id !== Auth::id()) {
            return back()->with('error', 'No puedes terminar el turno de otro usuario.');
        }

        $shift->update(['ended_at' => now()]);
        Auth::user()->update(['available' => false]);

        $overtime = $shift->refresh()->overtimeMinutes();
        $msg = $overtime > 0
            ? "Turno terminado. Horas extra: " . gmdate('H:i', $overtime * 60)
            : "Turno terminado. Duración: " . gmdate('H:i', abs($shift->workedMinutes()) * 60);

        return back()->with('success', $msg);
    }

    /** Vista owner: todos los turnos del equipo con totales. */
    public function staffShifts(Request $request)
    {
        $from = $request->filled('desde') ? $request->desde : today()->subDays(6)->toDateString();
        $to   = $request->filled('hasta') ? $request->hasta : today()->toDateString();

        $staff = TenantUser::where('role', '!=', 'owner')
            ->with(['shifts' => function ($q) use ($from, $to) {
                $q->whereDate('started_at', '>=', $from)
                  ->whereDate('started_at', '<=', $to)
                  ->whereNotNull('ended_at')
                  ->orderByDesc('started_at');
            }])
            ->orderBy('name')
            ->get();

        return view('tenant.shifts.staff', compact('staff', 'from', 'to'));
    }

    /** Export CSV de turnos del equipo. */
    public function export(Request $request)
    {
        $from = $request->filled('desde') ? $request->desde : today()->subDays(6)->toDateString();
        $to   = $request->filled('hasta') ? $request->hasta : today()->toDateString();

        $shifts = Shift::with('user')
            ->whereHas('user', fn($q) => $q->where('role', '!=', 'owner'))
            ->whereDate('started_at', '>=', $from)
            ->whereDate('started_at', '<=', $to)
            ->whereNotNull('ended_at')
            ->orderByDesc('started_at')
            ->get();

        $rows = collect([['Empleado', 'Rol', 'Inicio', 'Fin', 'Horas trabajadas', 'Horas extra', 'Notas']]);

        foreach ($shifts as $s) {
            $worked   = $s->workedMinutes();
            $overtime = $s->overtimeMinutes();
            $rows->push([
                $s->user->name,
                TenantUser::roleLabel($s->user->role),
                $s->started_at->format('Y-m-d H:i'),
                $s->ended_at->format('Y-m-d H:i'),
                gmdate('H:i', $worked * 60),
                ($overtime >= 0 ? '+' : '') . gmdate('H:i', abs($overtime) * 60),
                $s->notes ?? '',
            ]);
        }

        $csv = $rows->map(fn($r) => implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $r)))->implode("\n");
        $filename = "turnos_{$from}_{$to}.csv";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ])->withHeaders(['Content-Type' => 'text/csv; charset=UTF-8'])
          ->setContent("\xEF\xBB\xBF" . $csv);
    }
}
