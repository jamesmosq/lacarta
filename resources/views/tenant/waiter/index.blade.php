@extends('layouts.tenant')

@section('title', 'Tomar pedido')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Tomar pedido</h2>
    <p class="text-gray-500">Selecciona una mesa para registrar un pedido</p>
</div>

@if($tables->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center text-gray-400">
    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
    </div>
    <p class="text-sm font-medium">No hay mesas activas</p>
    <p class="text-xs mt-1">El dueño debe crear y activar las mesas primero</p>
</div>
@else
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach($tables as $table)
    @php $activeOrder = $activeOrders->get($table->id) @endphp
    <a href="{{ route('tenant.waiter.create_order', ['tenant' => $tenant, 'table' => $table->id]) }}"
       class="bg-white rounded-xl shadow-sm border-2 p-5 text-center hover:shadow-md transition
              {{ $activeOrder ? 'border-yellow-300' : 'border-gray-100 hover:border-orange-300' }}">

        <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center
                    {{ $activeOrder ? 'bg-yellow-100' : 'bg-orange-50' }}">
            <svg class="w-6 h-6 {{ $activeOrder ? 'text-yellow-500' : 'text-orange-400' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
            </svg>
        </div>

        <p class="font-semibold text-gray-900 text-sm">{{ $table->name }}</p>

        @if($activeOrder)
            <span class="mt-2 inline-block text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">
                Pedido #{{ $activeOrder->id }} activo
            </span>
        @else
            <span class="mt-2 inline-block text-xs text-gray-400">Disponible</span>
        @endif
    </a>
    @endforeach
</div>
@endif
@endsection
