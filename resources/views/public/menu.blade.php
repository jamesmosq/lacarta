<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — {{ tenant('name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">

<div class="max-w-2xl mx-auto min-h-screen">

    <div class="bg-orange-600 text-white px-6 py-8 text-center relative">
        <h1 class="text-2xl font-bold tracking-tight">{{ tenant('name') }}</h1>
        @if($table)
            <p class="text-orange-200 text-sm mt-1">{{ $t['table'] }}: {{ $table->name }}</p>
        @else
            <p class="text-orange-200 text-sm mt-1">{{ $t['digital_menu'] }}</p>
        @endif
        {{-- Toggle idioma --}}
        <div class="absolute top-4 right-4 flex gap-1">
            <a href="{{ route('tenant.menu.lang', ['tenant' => $tenant, 'lang' => 'es']) }}"
               class="text-xs px-2 py-1 rounded {{ ($lang ?? 'es') === 'es' ? 'bg-white text-orange-600 font-bold' : 'text-orange-200 hover:text-white' }} transition">
                ES
            </a>
            <a href="{{ route('tenant.menu.lang', ['tenant' => $tenant, 'lang' => 'en']) }}"
               class="text-xs px-2 py-1 rounded {{ ($lang ?? 'es') === 'en' ? 'bg-white text-orange-600 font-bold' : 'text-orange-200 hover:text-white' }} transition">
                EN
            </a>
        </div>
    </div>

    {{-- Barra del carrito --}}
    <div id="cart-bar" class="hidden fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 z-50 shadow-2xl">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <span id="cart-count" class="text-sm font-medium">0 {{ $t['items_selected'] }}</span>
            <button onclick="showOrderForm()"
                    class="bg-orange-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-orange-700 transition text-sm">
                {{ $t['add_to_order'] }}
            </button>
        </div>
    </div>

    <div class="px-4 py-6 pb-28">
        @forelse($categories as $category)
        @if($category->activeDishes->count() > 0)

        <h2 class="text-base font-bold text-gray-700 uppercase tracking-wider mb-4 mt-8 first:mt-0">
            {{ $category->name }}
        </h2>

        <div class="space-y-3">
            @foreach($category->activeDishes as $dish)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden flex">
                @if($dish->image)
                <img src="{{ $dish->image }}" alt="{{ $dish->name }}"
                     class="w-24 h-24 object-cover flex-shrink-0">
                @endif
                <div class="flex-1 p-4 flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm">{{ $dish->name }}</p>
                        @if($dish->description)
                        <p class="text-gray-400 text-xs mt-0.5 line-clamp-2">{{ $dish->description }}</p>
                        @endif
                        <p class="text-orange-600 font-bold text-sm mt-1">${{ number_format($dish->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button onclick="changeQty({{ $dish->id }}, -1)"
                                class="w-8 h-8 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 transition text-lg font-bold flex items-center justify-center">
                            −
                        </button>
                        <span id="qty-{{ $dish->id }}" class="w-5 text-center text-sm font-semibold">0</span>
                        <button onclick="changeQty({{ $dish->id }}, 1, '{{ addslashes($dish->name) }}', {{ $dish->price }})"
                                class="w-8 h-8 rounded-full bg-orange-600 text-white hover:bg-orange-700 transition text-lg font-bold flex items-center justify-center">
                            +
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @empty
        <div class="text-center py-20 text-gray-400">
            <p class="text-sm">El menu no tiene platos disponibles por el momento.</p>
        </div>
        @endforelse
    </div>

    {{-- Modal de confirmacion del pedido --}}
    <div id="order-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end">
        <div class="bg-white w-full max-w-2xl mx-auto rounded-t-2xl p-6 max-h-[80vh] overflow-y-auto">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $t['confirm_order'] }}</h2>

            <form method="POST" action="{{ route('tenant.menu.order', ['tenant' => $tenant]) }}">
                @csrf
                @if($table)
                    <input type="hidden" name="table_id" value="{{ $table->id }}">
                    <p class="text-sm text-gray-500 mb-4">Mesa: <strong>{{ $table->name }}</strong></p>
                @endif
                <div id="order-summary" class="space-y-2 mb-4 pb-4 border-b border-gray-100"></div>
                <div id="order-inputs"></div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t['your_name'] }} <span class="text-gray-400 font-normal">{{ $t['name_optional'] }}</span></label>
                    <input type="text" name="customer_name"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-orange-400"
                           placeholder="{{ $t['name_hint'] }}">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t['notes'] }} <span class="text-gray-400 font-normal">{{ $t['name_optional'] }}</span></label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-orange-400"
                              placeholder="{{ $t['notes_hint'] }}"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="hideOrderForm()"
                            class="flex-1 border border-gray-200 text-gray-700 py-3 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        {{ $t['cancel'] }}
                    </button>
                    <button type="submit"
                            class="flex-1 bg-orange-600 text-white py-3 rounded-xl text-sm font-bold hover:bg-orange-700 transition">
                        {{ $t['send_order'] }}
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
    if (name)  cart[id].name  = name;
    if (price) cart[id].price = price;

    document.getElementById('qty-' + id).textContent = cart[id].qty;

    const total = Object.values(cart).reduce((s, i) => s + i.qty, 0);
    document.getElementById('cart-count').textContent = total + ' item(s) seleccionados';
    document.getElementById('cart-bar').classList.toggle('hidden', total === 0);
}

function showOrderForm() {
    const summary = document.getElementById('order-summary');
    const inputs  = document.getElementById('order-inputs');
    summary.innerHTML = '';
    inputs.innerHTML  = '';
    let i = 0, total = 0;

    for (const [id, item] of Object.entries(cart)) {
        if (item.qty === 0) continue;
        const sub = item.qty * item.price;
        total += sub;
        summary.innerHTML += `<div class="flex justify-between text-sm">
            <span class="text-gray-700">${item.qty}x ${item.name}</span>
            <span class="font-medium">$${sub.toLocaleString('es-CO')}</span>
        </div>`;
        inputs.innerHTML += `
            <input type="hidden" name="items[${i}][dish_id]"  value="${id}">
            <input type="hidden" name="items[${i}][quantity]" value="${item.qty}">`;
        i++;
    }
    summary.innerHTML += `<div class="flex justify-between text-sm font-bold pt-2 border-t border-gray-100 mt-2">
        <span>Total</span><span>$${total.toLocaleString('es-CO')}</span>
    </div>`;

    document.getElementById('order-modal').classList.remove('hidden');
}

function hideOrderForm() {
    document.getElementById('order-modal').classList.add('hidden');
}
</script>
</body>
</html>
