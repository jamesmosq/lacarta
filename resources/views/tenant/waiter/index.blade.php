@extends('layouts.tenant')

@section('title', 'Tomar pedido')

@section('content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Tomar pedido</h2>
        <p class="text-gray-500 text-sm">Selecciona una mesa para registrar un pedido</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Indicador de disponibilidad del mesero autenticado --}}
        @php $user = auth()->user(); @endphp
        <span class="inline-flex items-center gap-1.5 text-xs font-medium
            {{ $user->available ? 'text-green-600' : 'text-gray-400' }}">
            <span class="w-2 h-2 rounded-full {{ $user->available ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></span>
            {{ $user->available ? 'En turno' : 'Sin turno' }}
        </span>
        <a href="{{ route('tenant.profile', ['tenant' => $tenant]) }}"
           class="text-xs text-orange-600 hover:underline">Mi perfil</a>
    </div>
</div>

{{-- Banner de notificación: mesa asignada --}}
<div id="notification-banner" class="hidden mb-6 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="font-semibold text-blue-800 text-sm" id="notification-text">Nueva mesa asignada</p>
            <p class="text-blue-600 text-xs mt-0.5">Un cliente te ha seleccionado como mesero</p>
        </div>
        <button onclick="document.getElementById('notification-banner').classList.add('hidden')"
                class="text-blue-400 hover:text-blue-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
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
    @php
        $activeOrder  = $activeOrders->get($table->id);
        $isOccupied   = $table->occupied;
        $hasPending   = $activeOrder && in_array($activeOrder->status, ['pending', 'preparing', 'ready']);
        $readyToPay   = $isOccupied && !$hasPending;
        $isMyTable    = $table->assigned_waiter_id === auth()->id();
        $needsGreet   = $isMyTable && !$table->greeted_at;
    @endphp

    <div class="bg-white rounded-xl shadow-sm border-2 p-5 flex flex-col gap-3 transition
        @if($needsGreet) border-blue-400 ring-2 ring-blue-200
        @elseif($readyToPay) border-orange-400
        @elseif($hasPending) border-yellow-300
        @else border-gray-100 @endif">

        {{-- Icono + nombre --}}
        <div class="text-center">
            <div class="w-12 h-12 rounded-full mx-auto mb-2 flex items-center justify-center
                @if($needsGreet) bg-blue-100
                @elseif($readyToPay) bg-orange-100
                @elseif($hasPending) bg-yellow-100
                @else bg-orange-50 @endif">
                <svg class="w-6 h-6
                    @if($needsGreet) text-blue-500
                    @elseif($readyToPay) text-orange-500
                    @elseif($hasPending) text-yellow-500
                    @else text-orange-400 @endif"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
                </svg>
            </div>
            <p class="font-semibold text-gray-900 text-sm">{{ $table->name }}</p>
        </div>

        {{-- Badge de estado --}}
        <div class="text-center">
            @if($needsGreet)
                <span class="inline-block text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium animate-pulse">
                    Cliente esperando
                </span>
            @elseif($readyToPay)
                <span class="inline-block text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">
                    Por cobrar
                </span>
            @elseif($hasPending)
                <span class="inline-block text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">
                    Pedido #{{ $activeOrder->id }}
                </span>
            @else
                <span class="inline-block text-xs text-gray-400">Disponible</span>
            @endif
        </div>

        {{-- Mesero asignado --}}
        @if($table->assignedWaiter)
        <div class="text-center">
            <span class="text-xs text-gray-400">
                {{ $table->assignedWaiter->name }}
            </span>
        </div>
        @endif

        {{-- Acciones --}}
        <div class="flex flex-col gap-2 mt-auto">
            @if($needsGreet)
                <button onclick="saludar({{ $table->id }}, '{{ $table->name }}')"
                        class="block text-center text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition">
                    Saludar al cliente
                </button>
                <a href="{{ route('tenant.waiter.create_order', ['tenant' => $tenant, 'table' => $table->id]) }}"
                   class="block text-center text-xs text-gray-500 hover:text-orange-600 transition">
                    + Tomar pedido
                </a>
            @elseif($readyToPay)
                <a href="{{ route('tenant.cash.show', ['tenant' => $tenant, 'table' => $table->id]) }}"
                   class="block text-center text-sm font-bold bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-lg transition">
                    Cobrar
                </a>
                <a href="{{ route('tenant.waiter.create_order', ['tenant' => $tenant, 'table' => $table->id]) }}"
                   class="block text-center text-xs text-gray-500 hover:text-orange-600 transition">
                    + Añadir pedido
                </a>
            @else
                <a href="{{ route('tenant.waiter.create_order', ['tenant' => $tenant, 'table' => $table->id]) }}"
                   class="block text-center text-sm font-medium
                          {{ $hasPending ? 'text-yellow-700 bg-yellow-50 hover:bg-yellow-100' : 'text-orange-600 bg-orange-50 hover:bg-orange-100' }}
                          px-3 py-2 rounded-lg transition">
                    {{ $hasPending ? 'Añadir pedido' : 'Tomar pedido' }}
                </a>
                @if($isOccupied)
                <a href="{{ route('tenant.cash.show', ['tenant' => $tenant, 'table' => $table->id]) }}"
                   class="block text-center text-xs text-gray-500 hover:text-orange-600 transition">
                    Ver cuenta
                </a>
                @endif
            @endif

            @if($isOccupied || $hasPending)
            <a href="{{ route('tenant.waiter.history', ['tenant' => $tenant, 'table' => $table->id]) }}"
               class="block text-center text-xs text-gray-400 hover:text-gray-600 transition">
                Ver historial
            </a>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

@push('scripts')
<script>
const notifUrl  = '{{ route('tenant.waiter.notifications', ['tenant' => $tenant]) }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let knownIds    = new Set();

async function pollNotifications() {
    try {
        const res   = await fetch(notifUrl);
        const tables = await res.json();
        tables.forEach(t => {
            if (!knownIds.has(t.id)) {
                knownIds.add(t.id);
                document.getElementById('notification-text').textContent =
                    `¡${t.name} te ha seleccionado!`;
                document.getElementById('notification-banner').classList.remove('hidden');
            }
        });
        if (tables.length === 0) knownIds.clear();
    } catch {}
}

async function saludar(tableId, tableName) {
    await fetch(`{{ url('') }}/{{ $tenant }}/admin/mesero/mesa/${tableId}/saludar`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
    });
    location.reload();
}

pollNotifications();
setInterval(pollNotifications, 8000);
</script>
@endpush
@endsection
