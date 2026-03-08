<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú — {{ tenant('name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">

<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="bg-orange-600 text-white px-6 py-8 text-center">
        <h1 class="text-3xl font-bold">{{ tenant('name') }}</h1>
        <p class="text-orange-200 mt-1">Menú digital</p>
    </div>

    {{-- Carrito flotante --}}
    <div id="cart-bar" class="hidden fixed bottom-0 left-0 right-0 bg-orange-600 text-white p-4 z-50">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <span id="cart-count" class="font-medium">0 ítem(s)</span>
            <button onclick="showOrderForm()"
                    class="bg-white text-orange-600 font-bold px-6 py-2 rounded-full hover:bg-orange-50 transition">
                Hacer pedido →
            </button>
        </div>
    </div>

    {{-- Menú --}}
    <div class="px-4 py-6 pb-24">
        @forelse($categories as $category)
        @if($category->activeDishes->count() > 0)
        <h2 class="text-xl font-bold text-gray-800 mb-4 mt-6 first:mt-0">{{ $category->name }}</h2>

        <div class="space-y-3">
            @foreach($category->activeDishes as $dish)
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between gap-4">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">{{ $dish->name }}</h3>
                    @if($dish->description)
                    <p class="text-gray-400 text-sm mt-0.5">{{ $dish->description }}</p>
                    @endif
                    <p class="text-orange-600 font-bold mt-1">${{ number_format($dish->price, 0, ',', '.') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="changeQty({{ $dish->id }}, -1)"
                            class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition font-bold">−</button>
                    <span id="qty-{{ $dish->id }}" class="w-6 text-center font-medium">0</span>
                    <button onclick="changeQty({{ $dish->id }}, 1, '{{ addslashes($dish->name) }}', {{ $dish->price }})"
                            class="w-8 h-8 rounded-full bg-orange-600 text-white hover:bg-orange-700 transition font-bold">+</button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @empty
        <div class="text-center py-16 text-gray-400">
            <div class="text-5xl mb-4">📋</div>
            <p>El menú está vacío por el momento.</p>
        </div>
        @endforelse
    </div>

    {{-- Modal de pedido --}}
    <div id="order-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end">
        <div class="bg-white w-full max-w-2xl mx-auto rounded-t-2xl p-6 max-h-[80vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Confirmar pedido</h2>

            <form method="POST" action="{{ route('tenant.menu.order', ['tenant' => $tenant]) }}">
                @csrf
                <div id="order-summary" class="space-y-2 mb-4 border-b pb-4"></div>

                <div id="order-inputs"></div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tu nombre (opcional)</label>
                    <input type="text" name="customer_name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none"
                           placeholder="Para llamarte cuando esté listo">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas del pedido (opcional)</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none"
                              placeholder="Sin cebolla, extra salsa..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="hideOrderForm()"
                            class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 bg-orange-600 text-white py-3 rounded-xl font-bold hover:bg-orange-700 transition">
                        Enviar pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let cart = {};

function changeQty(id, delta, name = '', price = 0) {
    if (!cart[id]) cart[id] = { qty: 0, name, price };
    cart[id].qty = Math.max(0, cart[id].qty + delta);
    if (name) cart[id].name = name;
    if (price) cart[id].price = price;

    document.getElementById('qty-' + id).textContent = cart[id].qty;

    const total = Object.values(cart).reduce((s, i) => s + i.qty, 0);
    const bar = document.getElementById('cart-bar');
    document.getElementById('cart-count').textContent = total + ' ítem(s)';
    bar.classList.toggle('hidden', total === 0);
}

function showOrderForm() {
    const summary = document.getElementById('order-summary');
    const inputs  = document.getElementById('order-inputs');
    summary.innerHTML = '';
    inputs.innerHTML  = '';

    let i = 0;
    for (const [id, item] of Object.entries(cart)) {
        if (item.qty === 0) continue;
        summary.innerHTML += `<div class="flex justify-between text-sm">
            <span>${item.qty}x ${item.name}</span>
            <span>$${(item.qty * item.price).toLocaleString('es-CO')}</span>
        </div>`;
        inputs.innerHTML += `
            <input type="hidden" name="items[${i}][dish_id]"  value="${id}">
            <input type="hidden" name="items[${i}][quantity]" value="${item.qty}">`;
        i++;
    }

    document.getElementById('order-modal').classList.remove('hidden');
}

function hideOrderForm() {
    document.getElementById('order-modal').classList.add('hidden');
}
</script>
</body>
</html>
