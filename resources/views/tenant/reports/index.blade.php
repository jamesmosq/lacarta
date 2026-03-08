@extends('layouts.tenant')

@section('title', 'Reportes de ventas')

@section('content')
@php
    $t = tenant('id');
    $fmt = fn($n) => '$' . number_format($n, 0, ',', '.');
@endphp

<div class="max-w-7xl mx-auto">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reportes de ventas</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $start->format('d/m/Y') }} — {{ $end->format('d/m/Y') }}
            </p>
        </div>
        <a href="{{ route('tenant.reports.export', array_merge(['tenant' => $t], request()->only('periodo','desde','hasta'))) }}"
           class="inline-flex items-center gap-2 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar CSV
        </a>
    </div>

    {{-- Selector de periodo --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('tenant.reports', ['tenant' => $t]) }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 block mb-1">Periodo</label>
                <select name="periodo" onchange="toggleCustom(this.value)"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="hoy"    {{ $periodo === 'hoy'    ? 'selected' : '' }}>Hoy</option>
                    <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Esta semana</option>
                    <option value="mes"    {{ $periodo === 'mes'    ? 'selected' : '' }}>Este mes</option>
                    <option value="custom" {{ $periodo === 'custom' ? 'selected' : '' }}>Personalizado</option>
                </select>
            </div>

            <div id="custom-range" class="{{ $periodo === 'custom' ? 'flex' : 'hidden' }} items-end gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-500 block mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde', $start->format('Y-m-d')) }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 block mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta', $end->format('Y-m-d')) }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>

            <button type="submit"
                    class="bg-orange-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                Aplicar
            </button>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ingresos</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $fmt($totalRevenue) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total del periodo</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pedidos</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-400 mt-1">Total del periodo</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ticket promedio</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $fmt($avgOrder) }}</p>
            <p class="text-xs text-gray-400 mt-1">Por pedido</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Entregados</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $delivered }}</p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $totalOrders > 0 ? round($delivered / $totalOrders * 100) : 0 }}% del total
            </p>
        </div>
    </div>

    {{-- Gráfico de ingresos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Ingresos por dia</h2>
        @if($revenueByDay->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Sin datos para el periodo seleccionado.</p>
        @else
            <div class="relative h-48">
                <canvas id="revenueChart"></canvas>
            </div>
        @endif
    </div>

    {{-- Platos mas vendidos + Estado de pedidos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Top platos --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Platos mas vendidos</h2>
            @if($topDishes->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin datos para el periodo seleccionado.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-400 uppercase border-b border-gray-100">
                            <th class="pb-2">#</th>
                            <th class="pb-2">Plato</th>
                            <th class="pb-2 text-right">Unidades</th>
                            <th class="pb-2 text-right">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topDishes as $i => $dish)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 text-gray-400 font-mono text-xs">{{ $i + 1 }}</td>
                            <td class="py-2.5 font-medium text-gray-800">{{ $dish->name }}</td>
                            <td class="py-2.5 text-right text-gray-600">{{ $dish->total_qty }}</td>
                            <td class="py-2.5 text-right font-medium text-gray-800">{{ $fmt($dish->total_revenue) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Pedidos por estado --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Pedidos por estado</h2>
            @if($byStatus->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin datos.</p>
            @else
                <div class="space-y-3">
                    @php
                        $statusMap = [
                            'pending'   => ['label' => 'Pendiente',  'color' => 'bg-yellow-400'],
                            'preparing' => ['label' => 'Preparando', 'color' => 'bg-blue-400'],
                            'ready'     => ['label' => 'Listo',      'color' => 'bg-green-400'],
                            'delivered' => ['label' => 'Entregado',  'color' => 'bg-gray-300'],
                        ];
                    @endphp
                    @foreach($statusMap as $key => $meta)
                    @php $count = $byStatus->get($key, 0); @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full {{ $meta['color'] }} flex-shrink-0"></span>
                        <span class="text-sm text-gray-600 flex-1">{{ $meta['label'] }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Mini barras de progreso --}}
                @if($totalOrders > 0)
                <div class="mt-5 space-y-2">
                    @foreach($statusMap as $key => $meta)
                    @php $count = $byStatus->get($key, 0); $pct = round($count / $totalOrders * 100); @endphp
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="{{ $meta['color'] }} h-1.5 rounded-full transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    @endforeach
                </div>
                @endif
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function toggleCustom(val) {
    document.getElementById('custom-range').classList.toggle('hidden', val !== 'custom');
    document.getElementById('custom-range').classList.toggle('flex', val === 'custom');
}

@if($revenueByDay->isNotEmpty())
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($revenueByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
        datasets: [{
            label: 'Ingresos',
            data: @json($revenueByDay->pluck('revenue')),
            backgroundColor: 'rgba(234, 88, 12, 0.8)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '$' + ctx.parsed.y.toLocaleString('es-CO')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: val => '$' + (val / 1000).toFixed(0) + 'k',
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});
@endif
</script>
@endpush
