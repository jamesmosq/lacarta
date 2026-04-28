@extends('layouts.tenant')

@section('title', 'Cobrar — ' . $table->name)

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('tenant.waiter', ['tenant' => $tenant]) }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Cobrar — {{ $table->name }}</h2>
            <p class="text-gray-500 text-sm">Resumen de consumo del día</p>
        </div>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
        </svg>
        <p class="text-sm font-medium">Sin pedidos hoy en esta mesa</p>
    </div>
    @else

    {{-- Lista de pedidos --}}
    <div class="space-y-4 mb-6">
        @foreach($orders as $order)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="font-semibold text-gray-800">Pedido #{{ $order->id }}</span>
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
                <div class="flex items-center justify-between py-2 text-sm">
                    <span class="text-gray-700">
                        <span class="font-medium text-gray-900">{{ $item->quantity }}×</span>
                        {{ $item->dish->name }}
                        @if($item->notes)
                            <span class="text-gray-400 italic text-xs"> — {{ $item->notes }}</span>
                        @endif
                    </span>
                    <span class="text-gray-600 font-medium">${{ number_format($item->subtotal(), 2) }}</span>
                </div>
                @endforeach
            </div>

            <div class="flex justify-end pt-3 border-t border-gray-100 mt-2">
                <span class="text-sm font-semibold text-gray-800">
                    Subtotal: ${{ number_format($order->total, 2) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Total y formulario de cobro --}}
    <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-6">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
            <span class="text-lg font-bold text-gray-900">Total a cobrar</span>
            <span class="text-3xl font-bold text-orange-600">${{ number_format($total, 2) }}</span>
        </div>

        <form method="POST" action="{{ route('tenant.cash.pay', ['tenant' => $tenant, 'table' => $table->id]) }}"
              id="cobrar-form">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Método de pago</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $value }}"
                               class="sr-only peer" {{ $value === 'efectivo' ? 'checked' : '' }}>
                        <div class="border-2 rounded-lg p-3 text-center text-sm font-medium transition
                                    peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700
                                    border-gray-200 text-gray-600 hover:border-orange-300">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Nota (opcional)</label>
                <input type="text" name="notes" id="notes" maxlength="300"
                       placeholder="Ej: Propina incluida, factura solicitada..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <button type="button" onclick="confirmarCobro()"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition text-base">
                Confirmar cobro — ${{ number_format($total, 2) }}
            </button>
        </form>
    </div>

    @endif
</div>

@push('scripts')
<script>
function confirmarCobro() {
    Swal.fire({
        title: '¿Confirmar cobro?',
        html: 'Se registrará el pago de <strong>${{ number_format($total, 2) }}</strong> y la mesa quedará disponible.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, cobrar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cobrar-form').submit();
        }
    });
}
</script>
@endpush
@endsection
