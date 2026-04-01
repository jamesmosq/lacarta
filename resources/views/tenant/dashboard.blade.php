@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
@php $fmt = fn($n) => '$' . number_format($n, 0, ',', '.'); @endphp

{{-- Encabezado --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
        <p class="text-gray-400 text-sm mt-1" id="fecha-colombia"></p>
        <script>
            const ahora = new Date();
            const opciones = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Bogota' };
            const fecha = ahora.toLocaleDateString('es-CO', opciones);
            document.getElementById('fecha-colombia').textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);
        </script>
    </div>
    <div class="flex items-center gap-3">
        {{-- Toggle abierto/cerrado --}}
        <form method="POST" action="{{ route('tenant.settings.toggle_open', ['tenant' => $tenant]) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border transition
                    {{ $tenantModel->is_open
                        ? 'bg-green-50 border-green-300 text-green-700 hover:bg-green-100'
                        : 'bg-red-50 border-red-300 text-red-700 hover:bg-red-100' }}">
                @if($tenantModel->is_open)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Abierto
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Cerrado
                @endif
            </button>
        </form>

        <a href="{{ route('tenant.reports', ['tenant' => $tenant]) }}"
           class="text-sm text-orange-600 border border-orange-200 px-4 py-2 rounded-lg hover:bg-orange-50 transition">
            Ver reportes →
        </a>
    </div>
</div>

{{-- KPIs principales --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Ingresos hoy</p>
        <p class="text-2xl font-bold text-green-600">{{ $fmt($stats['revenue_today']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Esta semana: {{ $fmt($stats['revenue_week']) }}</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Pedidos hoy</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['orders_today'] }}</p>
        <p class="text-xs text-gray-400 mt-1">
            Ticket prom: {{ $stats['orders_today'] > 0 ? $fmt($stats['revenue_today'] / $stats['orders_today']) : '$0' }}
        </p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">En cola</p>
        <p class="text-2xl font-bold text-yellow-500">{{ $stats['pending_orders'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Pedidos pendientes</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Accesos rápidos</p>
        <div class="flex flex-col gap-1.5 mt-1">
            <a href="{{ route('tenant.kitchen', ['tenant' => $tenant]) }}"
               class="text-xs text-center bg-orange-600 text-white px-3 py-1.5 rounded-lg hover:bg-orange-700 transition">
                Panel cocina
            </a>
            <a href="{{ route('tenant.orders', ['tenant' => $tenant]) }}"
               class="text-xs text-center border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                Ver pedidos
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Gráfico ventas por hora --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Ventas por hora — hoy</h3>
        @if($revenue_by_hour->isEmpty())
            <div class="flex items-center justify-center h-40 text-gray-300 text-sm">
                Sin pedidos hoy todavía
            </div>
        @else
            <div class="relative h-40">
                <canvas id="hourChart"></canvas>
            </div>
        @endif
    </div>

    {{-- Top 5 platos del día --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Top platos hoy</h3>
        @if($top_dishes->isEmpty())
            <div class="flex items-center justify-center h-40 text-gray-300 text-sm text-center">
                Aún no hay pedidos hoy
            </div>
        @else
            <div class="space-y-3">
                @foreach($top_dishes as $i => $dish)
                <div class="flex items-center gap-3">
                    <span class="w-5 text-xs text-gray-400 font-mono text-right flex-shrink-0">{{ $i + 1 }}</span>
                    @if($dish->image)
                        <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}"
                             class="w-9 h-9 rounded-lg object-cover flex-shrink-0 border border-gray-100">
                    @else
                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $dish->name }}</p>
                        <p class="text-xs text-gray-400">{{ $fmt($dish->total_revenue) }}</p>
                    </div>
                    <span class="text-sm font-bold text-orange-600 flex-shrink-0">×{{ $dish->total_qty }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Pedidos activos + Link menú --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Pedidos activos</h3>
            <a href="{{ route('tenant.orders', ['tenant' => $tenant]) }}"
               class="text-orange-600 text-sm hover:underline">Ver todos</a>
        </div>
        @if($recent_orders->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                No hay pedidos pendientes en este momento.
            </div>
        @else
            <div class="divide-y">
                @foreach($recent_orders as $order)
                <div class="px-6 py-3.5 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm">Pedido #{{ $order->id }}
                            @if($order->table)
                                <span class="text-gray-400 font-normal">· {{ $order->table->name }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $order->items->count() }} item(s) · {{ $fmt($order->total) }}
                            · {{ $order->created_at->format('h:i a') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        @if($order->status === 'pending')   bg-yellow-100 text-yellow-700
                        @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                        @else bg-green-100 text-green-700 @endif">
                        {{ $order->statusLabel() }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-4">
        {{-- Cierre del día --}}
        <div class="bg-green-50 border border-green-100 rounded-xl p-5">
            <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">Cierre del día</p>
            <p class="text-3xl font-bold text-green-700">{{ $fmt($stats['revenue_today']) }}</p>
            <p class="text-sm text-green-600 mt-1">{{ $stats['orders_today'] }} pedido(s)</p>
            <a href="{{ route('tenant.reports.export', ['tenant' => $tenant, 'periodo' => 'hoy']) }}"
               class="mt-3 block text-center text-xs bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition">
                Exportar resumen del día
            </a>
        </div>

        {{-- Menú público --}}
        <div class="bg-orange-50 border border-orange-100 rounded-xl p-5">
            <p class="text-sm font-medium text-orange-800">Menú público</p>
            <p class="text-xs text-orange-500 mt-0.5 mb-3">Comparte este enlace con tus clientes</p>
            <a href="{{ url('/' . $tenant . '/menu') }}" target="_blank"
               class="block text-center bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 transition">
                Ver menú
            </a>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($revenue_by_hour->isNotEmpty())
<script>
const hoursData = @json($revenue_by_hour);
const allHours  = Array.from({length: 24}, (_, i) => i);
const revenues  = allHours.map(h => {
    const found = hoursData.find(r => r.hour === h);
    return found ? parseFloat(found.revenue) : 0;
});
const labels = allHours.map(h => h.toString().padStart(2,'0') + ':00');

new Chart(document.getElementById('hourChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            data: revenues,
            backgroundColor: revenues.map(v => v > 0 ? 'rgba(234,88,12,0.85)' : 'rgba(0,0,0,0.04)'),
            borderRadius: 4,
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
                    label: ctx => ctx.parsed.y > 0
                        ? '$' + ctx.parsed.y.toLocaleString('es-CO')
                        : 'Sin ventas'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: v => v > 0 ? '$' + (v/1000).toFixed(0) + 'k' : '',
                    font: { size: 10 },
                    maxTicksLimit: 4,
                }
            },
            x: {
                grid: { display: false },
                ticks: {
                    callback: (_, i) => i % 3 === 0 ? labels[i] : '',
                    font: { size: 10 },
                    maxRotation: 0,
                }
            }
        }
    }
});
</script>
@endif
@endpush
@endsection
