@extends('layouts.tenant')

@section('title', 'Historial — ' . $table->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('tenant.waiter', ['tenant' => $tenant]) }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $table->name }}</h2>
            <p class="text-gray-500 text-sm">Historial de pedidos de hoy</p>
        </div>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center text-gray-400">
        <p class="text-sm font-medium">Sin pedidos hoy en esta mesa</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="font-semibold text-gray-800">Pedido #{{ $order->id }}</span>
                    @if($order->waiter)
                    <span class="text-xs text-gray-400 ml-2">— {{ $order->waiter->name }}</span>
                    @endif
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                    @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                    @elseif($order->status === 'ready') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ $order->statusLabel() }}
                </span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($order->items as $item)
                <div class="flex justify-between py-1.5 text-sm">
                    <span class="text-gray-700">{{ $item->quantity }}× {{ $item->dish->name }}</span>
                    <span class="text-gray-600">${{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</span>
                <span class="font-bold text-sm text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            @if($order->status === 'pending')
            <a href="{{ route('tenant.waiter.edit_order', ['tenant' => $tenant, 'order' => $order->id]) }}"
               class="mt-3 block text-center text-xs text-orange-600 hover:underline">
                Editar pedido
            </a>
            @endif
        </div>
        @endforeach

        <div class="bg-orange-50 border border-orange-200 rounded-xl px-5 py-4 flex justify-between font-bold">
            <span class="text-gray-800">Total del día</span>
            <span class="text-orange-600">${{ number_format($orders->sum('total'), 0, ',', '.') }}</span>
        </div>
    </div>
    @endif
</div>
@endsection
