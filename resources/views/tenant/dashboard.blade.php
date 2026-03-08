@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
    <p class="text-gray-500">Resumen del día de hoy</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
        <div class="text-4xl font-bold text-orange-600">{{ $stats['orders_today'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Pedidos hoy</div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
        <div class="text-4xl font-bold text-yellow-500">{{ $stats['pending_orders'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Pendientes</div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
        <div class="text-4xl font-bold text-blue-500">{{ $stats['total_dishes'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Platos en menú</div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
        <div class="text-4xl font-bold text-green-500">{{ $stats['total_categories'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Categorías</div>
    </div>
</div>

{{-- Pedidos activos --}}
<div class="bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="font-bold text-gray-900">Pedidos activos</h3>
        <a href="{{ route('tenant.orders', ['tenant' => $tenant]) }}"
           class="text-orange-600 text-sm hover:underline">Ver todos →</a>
    </div>

    @if($recent_orders->isEmpty())
    <div class="px-6 py-12 text-center text-gray-400">
        <div class="text-4xl mb-2">🎉</div>
        No hay pedidos pendientes
    </div>
    @else
    <div class="divide-y">
        @foreach($recent_orders as $order)
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <span class="font-medium">Pedido #{{ $order->id }}</span>
                @if($order->table)
                    <span class="text-gray-400 text-sm ml-2">{{ $order->table->name }}</span>
                @endif
                <div class="text-sm text-gray-500 mt-0.5">
                    {{ $order->items->count() }} ítem(s) · ${{ number_format($order->total, 0, ',', '.') }}
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium
                @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                @elseif($order->status === 'ready') bg-green-100 text-green-700
                @else bg-gray-100 text-gray-600 @endif">
                {{ $order->statusLabel() }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Link menú público --}}
<div class="mt-6 bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-center justify-between">
    <div>
        <p class="font-medium text-orange-800">Menú público de tu restaurante</p>
        <p class="text-sm text-orange-600 mt-0.5">Comparte este link con tus clientes</p>
    </div>
    <a href="{{ url('/' . $tenant . '/menu') }}" target="_blank"
       class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 transition">
        Ver menú →
    </a>
</div>
@endsection
