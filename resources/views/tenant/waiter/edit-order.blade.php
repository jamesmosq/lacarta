@extends('layouts.tenant')

@section('title', 'Editar pedido #' . $order->id)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('tenant.waiter', ['tenant' => $tenant]) }}"
           class="text-orange-600 text-sm hover:underline">← Volver a mesas</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-1">Editar pedido #{{ $order->id }}</h2>
        @if($order->table)
        <p class="text-gray-500 text-sm">{{ $order->table->name }}</p>
        @endif
    </div>
    <div id="cart-summary" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <span id="cart-count">0</span> item(s) —
        <span id="cart-total">$0</span>
    </div>
</div>

<form method="POST" action="{{ route('tenant.waiter.update_order', ['tenant' => $tenant, 'order' => $order->id]) }}"
      id="order-form">
    @csrf
    @method('PATCH')
    <div id="order-inputs"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @forelse($categories as $category)
            @if($category->activeDishes->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-3 border-b">
                    <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                </div>
                <div class="divide-y">
                    @foreach($category->activeDishes as $dish)
                    <div class="px-5 py-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if($dish->image)
                            <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}"
                                 class="w-12 h-12 rounded-lg object-cover flex-shrink-0 border border-gray-100">
                            @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $dish->name }}</p>
                                <p class="text-orange-600 font-bold text-sm mt-0.5">${{ number_format($dish->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" onclick="changeQty({{ $dish->id }}, -1)"
                                    class="w-8 h-8 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span id="qty-{{ $dish->id }}" class="w-6 text-center text-sm font-bold text-gray-900">0</span>
                            <button type="button"
                                    onclick="changeQty({{ $dish->id }}, 1, '{{ addslashes($dish->name) }}', {{ $dish->price }})"
                                    class="w-8 h-8 rounded-full bg-orange-600 text-white hover:bg-orange-700 transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-12 text-center text-gray-400 text-sm">
                No hay platos disponibles.
            </div>
            @endforelse
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-6">
                <h3 class="font-semibold text-gray-900 mb-4">Nuevo resumen</h3>
                <div id="summary-items" class="space-y-2 mb-4 min-h-[60px] text-sm text-gray-400 italic">
                    Ajusta las cantidades
                </div>
                <div id="summary-total" class="hidden border-t pt-3 mb-4">
                    <div class="flex justify-between font-bold text-gray-900">
                        <span>Total</span>
                        <span id="total-display">$0</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Notas</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-orange-400"
                              placeholder="Sin sal, extra salsa...">{{ $order->notes }}</textarea>
                </div>
                <button type="submit" id="submit-btn" disabled
                        class="mt-4 w-full bg-orange-600 text-white py-3 rounded-lg font-bold text-sm hover:bg-orange-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Pre-cargar ítems existentes del pedido
const existing = @json($order->items->mapWithKeys(fn($i) => [$i->dish_id => ['qty' => $i->quantity, 'name' => $i->dish->name, 'price' => $i->unit_price]]));
let cart = {};

document.addEventListener('DOMContentLoaded', () => {
    for (const [id, item] of Object.entries(existing)) {
        cart[id] = { qty: item.qty, name: item.name, price: item.price };
        const el = document.getElementById('qty-' + id);
        if (el) el.textContent = item.qty;
    }
    updateSummary();
});

function changeQty(id, delta, name = '', price = 0) {
    if (!cart[id]) cart[id] = { qty: 0, name, price };
    cart[id].qty = Math.max(0, cart[id].qty + delta);
    if (name)  cart[id].name  = name;
    if (price) cart[id].price = price;
    document.getElementById('qty-' + id).textContent = cart[id].qty;
    updateSummary();
}

function updateSummary() {
    const items  = Object.entries(cart).filter(([, i]) => i.qty > 0);
    const total  = items.reduce((s, [, i]) => s + i.qty * i.price, 0);
    const count  = items.reduce((s, [, i]) => s + i.qty, 0);
    const inputs = document.getElementById('order-inputs');

    document.getElementById('summary-items').innerHTML = items.length === 0
        ? '<span class="text-gray-400 italic text-sm">Ajusta las cantidades</span>'
        : items.map(([, i]) => `<div class="flex justify-between text-sm"><span class="text-gray-700">${i.qty}x ${i.name}</span><span class="font-medium">$${(i.qty * i.price).toLocaleString('es-CO')}</span></div>`).join('');

    const totalEl = document.getElementById('summary-total');
    items.length > 0 ? totalEl.classList.remove('hidden') : totalEl.classList.add('hidden');
    document.getElementById('total-display').textContent = '$' + total.toLocaleString('es-CO');
    document.getElementById('cart-count').textContent = count;
    document.getElementById('cart-total').textContent = '$' + total.toLocaleString('es-CO');
    document.getElementById('submit-btn').disabled = items.length === 0;

    let i = 0;
    inputs.innerHTML = items.map(([id, item]) =>
        `<input type="hidden" name="items[${i}][dish_id]"  value="${id}">
         <input type="hidden" name="items[${i++}][quantity]" value="${item.qty}">`
    ).join('');
}
</script>
@endpush
@endsection
