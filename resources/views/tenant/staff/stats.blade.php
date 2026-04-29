@extends('layouts.tenant')

@section('title', 'Estadisticas por mesero')

@section('content')

<div class="mb-6 flex items-center gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <a href="{{ route('tenant.staff', ['tenant' => tenant('id')]) }}"
           class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Estadisticas por mesero</h2>
            <p class="text-gray-500 text-sm">Rendimiento del equipo en el periodo seleccionado</p>
        </div>
    </div>
</div>

{{-- Filtro de periodo --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('tenant.staff.stats', ['tenant' => tenant('id')]) }}"
          class="flex flex-wrap items-end gap-3">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Periodo</label>
            <select name="periodo" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="hoy"    {{ $periodo === 'hoy'    ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Esta semana</option>
                <option value="mes"    {{ $periodo === 'mes'    ? 'selected' : '' }}>Este mes</option>
                <option value="personalizado" {{ $periodo === 'personalizado' ? 'selected' : '' }}>Personalizado</option>
            </select>
        </div>

        @if($periodo === 'personalizado')
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ $from->toDateString() }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ $to->toDateString() }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <button type="submit"
                class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition">
            Aplicar
        </button>
        @endif

        <p class="text-xs text-gray-400 ml-auto self-center">
            {{ $from->format('d M') }} — {{ $to->format('d M Y') }}
        </p>
    </form>
</div>

@if($stats->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center text-gray-400">
    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <p class="font-medium">No hay meseros ni personal de cocina registrados.</p>
</div>
@else

{{-- KPIs globales del equipo --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total pedidos</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats->sum('orders') }}</p>
        <p class="text-xs text-gray-400 mt-1">en el periodo</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos totales</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($stats->sum('revenue'), 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">cobrados por el equipo</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Horas trabajadas</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats->sum('hours_worked'), 1) }}h</p>
        <p class="text-xs text-gray-400 mt-1">suma del equipo</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Turnos cumplidos</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats->sum('shifts_count') }}</p>
        <p class="text-xs text-gray-400 mt-1">en el periodo</p>
    </div>
</div>

{{-- Tabla de estadisticas --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Detalle por empleado</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Empleado</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-500">Pedidos</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-500">Ingresos</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-500">Ticket prom.</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-500">Turnos</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-500">Horas trab.</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($stats as $row)
                @php $maxOrders = $stats->max('orders') ?: 1; @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-orange-600 font-bold text-sm">
                                    {{ strtoupper(substr($row->waiter->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $row->waiter->name }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $row->waiter->role === 'waiter' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ \App\Models\TenantUser::roleLabel($row->waiter->role) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center gap-1">
                            <span class="font-bold text-gray-900">{{ $row->orders }}</span>
                            <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-400 rounded-full"
                                     style="width: {{ $maxOrders > 0 ? round($row->orders / $maxOrders * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="font-semibold text-gray-900">${{ number_format($row->revenue, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        @if($row->avg_ticket > 0)
                        <span class="text-gray-600">${{ number_format($row->avg_ticket, 0, ',', '.') }}</span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center text-gray-600">
                        {{ $row->shifts_count }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($row->hours_worked > 0)
                        <span class="font-medium text-gray-700">{{ $row->hours_worked }}h</span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-gray-200 bg-gray-50">
                <tr>
                    <td class="px-6 py-3 font-semibold text-gray-600 text-sm">Totales</td>
                    <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $stats->sum('orders') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($stats->sum('revenue'), 0, ',', '.') }}</td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $stats->sum('shifts_count') }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-700">{{ number_format($stats->sum('hours_worked'), 1) }}h</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endif

@endsection
