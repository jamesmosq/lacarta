@extends('layouts.tenant')

@section('title', 'Pedidos')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Pedidos de hoy</h2>
    <p class="text-gray-500 text-sm" id="fecha-pedidos"></p>
    <script>
        const ahora = new Date();
        const opciones = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Bogota' };
        const fecha = ahora.toLocaleDateString('es-CO', opciones);
        document.getElementById('fecha-pedidos').textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);
    </script>
</div>

@if($orders->isEmpty())
<div class="bg-white rounded-xl shadow-sm px-6 py-16 text-center text-gray-400">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    </div>
    <p class="text-lg font-medium">No hay pedidos hoy</p>
</div>
@else
<div class="space-y-4">
    @foreach($orders as $order)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="font-bold text-lg">Pedido #{{ $order->id }}</h3>
                <div class="text-gray-500 text-sm">
                    @if($order->table) {{ $order->table->name }} · @endif
                    @if($order->customer_name) {{ $order->customer_name }} · @endif
                    {{ $order->created_at->format('h:i a') }}
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                @elseif($order->status === 'ready') bg-green-100 text-green-700
                @else bg-gray-100 text-gray-600 @endif">
                {{ $order->statusLabel() }}
            </span>
        </div>

        {{-- Ítems del pedido --}}
        <div class="border-t pt-4 mb-4 space-y-2">
            @foreach($order->items as $item)
            <div class="flex justify-between text-sm">
                <span>{{ $item->quantity }}x {{ $item->dish->name }}
                    @if($item->notes) <em class="text-gray-400">({{ $item->notes }})</em> @endif
                </span>
                <span class="text-gray-600">${{ number_format($item->subtotal(), 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between border-t pt-4">
            <span class="font-bold">Total: ${{ number_format($order->total, 0, ',', '.') }}</span>

            {{-- Cambiar estado --}}
            @if($order->status !== 'delivered')
            <form method="POST" action="{{ route('tenant.order.update', ['tenant' => $tenant, 'order' => $order->id]) }}">
                @csrf @method('PATCH')
                <div class="flex items-center gap-3">
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm outline-none">
                        <option value="pending"   {{ $order->status === 'pending'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparando</option>
                        <option value="ready"     {{ $order->status === 'ready'     ? 'selected' : '' }}>Listo</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Entregado</option>
                    </select>
                    <button type="submit"
                            class="bg-orange-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-orange-700 transition">
                        Actualizar
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
