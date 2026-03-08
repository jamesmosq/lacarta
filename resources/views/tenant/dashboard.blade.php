@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
    <p class="text-gray-400 text-sm mt-1">{{ now()->isoFormat('dddd D [de] MMMM, YYYY') }}</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Pedidos hoy</p>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['orders_today'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Pendientes</p>
        <p class="text-3xl font-bold text-yellow-500">{{ $stats['pending_orders'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Platos</p>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_dishes'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Categorias</p>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_categories'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="font-semibold text-gray-900">Pedidos activos</h3>
        <a href="{{ route('tenant.orders', ['tenant' => $tenant]) }}" class="text-orange-600 text-sm hover:underline">Ver todos</a>
    </div>

    @if($recent_orders->isEmpty())
    <div class="px-6 py-16 text-center text-gray-400">
        <p class="text-sm">No hay pedidos pendientes en este momento.</p>
    </div>
    @else
    <div class="divide-y">
        @foreach($recent_orders as $order)
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <p class="font-medium text-sm">Pedido #{{ $order->id }}
                    @if($order->table) <span class="text-gray-400 font-normal">· {{ $order->table->name }}</span> @endif
                </p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $order->items->count() }} item(s) · ${{ number_format($order->total, 0, ',', '.') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium
                @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                @elseif($order->status === 'ready') bg-green-100 text-green-700
                @else bg-gray-100 text-gray-500 @endif">
                {{ $order->statusLabel() }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

<div class="mt-6 bg-orange-50 border border-orange-100 rounded-xl p-4 flex items-center justify-between">
    <div>
        <p class="text-sm font-medium text-orange-800">Menu publico del restaurante</p>
        <p class="text-xs text-orange-500 mt-0.5">Comparte este enlace con tus clientes</p>
    </div>
    <a href="{{ url('/' . $tenant . '/menu') }}" target="_blank"
       class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 transition">
        Ver menu
    </a>
</div>
@endsection
