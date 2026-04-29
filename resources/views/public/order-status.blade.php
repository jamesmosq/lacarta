<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del pedido — {{ tenant('name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-md w-full">

    {{-- Notificación mesero saludando --}}
    <div id="greeted-banner" class="hidden mb-6 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-center">
        <p class="text-green-700 font-semibold text-sm">{{ $t['waiter_coming'] }}</p>
    </div>

    <p class="text-xs uppercase tracking-widest text-gray-400 mb-1 text-center">{{ tenant('name') }}</p>
    <h1 class="text-xl font-bold text-gray-900 mb-8 text-center">{{ $t['order_number'] }}{{ $order->id }}</h1>

    {{-- Pasos de estado --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        @php
            $steps = [
                ['key' => 'pending',   'label' => $t['received']],
                ['key' => 'preparing', 'label' => $t['preparing']],
                ['key' => 'ready',     'label' => $t['ready']],
            ];
            $statusOrder = ['pending' => 0, 'preparing' => 1, 'ready' => 2, 'delivered' => 3];
            $current = $statusOrder[$order->status] ?? 0;
        @endphp

        @foreach($steps as $i => $step)
        <div class="flex items-center gap-2">
            <div class="flex flex-col items-center">
                <div id="step-{{ $step['key'] }}"
                     class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                     {{ $current >= $statusOrder[$step['key']] ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                    {{ $i + 1 }}
                </div>
                <span class="text-xs text-gray-400 mt-1 w-16 text-center">{{ $step['label'] }}</span>
            </div>
            @if(!$loop->last)
            <div id="line-{{ $i }}"
                 class="w-8 h-px {{ $current > $statusOrder[$step['key']] ? 'bg-orange-400' : 'bg-gray-200' }} mb-4"></div>
            @endif
        </div>
        @endforeach
    </div>

    <p id="status-label" class="text-2xl font-bold mb-2 text-center
        @if($order->status === 'ready') text-green-600
        @elseif($order->status === 'preparing') text-blue-600
        @else text-orange-600 @endif">
        {{ $order->statusLabel() }}
    </p>

    <p id="status-msg" class="text-gray-400 text-sm mb-8 text-center">
        @if($order->status === 'ready') Tu pedido esta listo. El mesero lo llevara a tu mesa.
        @elseif($order->status === 'preparing') Tu pedido esta siendo preparado. Un momento.
        @else Tu pedido fue recibido y esta en espera.
        @endif
    </p>

    <div class="border-t pt-6 text-left space-y-2">
        @foreach($order->items as $item)
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">{{ $item->quantity }}x {{ $item->dish->name }}</span>
            <span class="text-gray-900 font-medium">${{ number_format($item->subtotal(), 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="flex justify-between font-bold text-sm border-t pt-3 mt-3">
            <span>Total</span>
            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <a href="{{ url('/' . $tenant . '/menu') }}"
       class="block mt-6 text-sm text-orange-600 hover:underline text-center">
        {{ $t['back_to_menu'] }}
    </a>

    {{-- Indicador de actualización --}}
    <p class="text-center text-xs text-gray-300 mt-4" id="update-hint">{{ $t['updating'] }}</p>
</div>

<script>
const jsonUrl  = '{{ route('tenant.order.status.json', ['tenant' => $tenant, 'order' => $order->id]) }}';
const statusOrder = { pending: 0, preparing: 1, ready: 2, delivered: 3 };
const labels   = {
    pending:   '{{ $t['status_pending'] }}',
    preparing: '{{ $t['status_preparing'] }}',
    ready:     '{{ $t['status_ready'] }}',
    delivered: '{{ $t['status_delivered'] }}',
};
const colors   = { pending: 'text-orange-600', preparing: 'text-blue-600', ready: 'text-green-600', delivered: 'text-gray-500' };
const stepKeys = ['pending', 'preparing', 'ready'];
let   isDone   = {{ in_array($order->status, ['delivered']) ? 'true' : 'false' }};
let   wasGreeted = {{ $order->table?->greeted_at ? 'true' : 'false' }};

function updateUI(data) {
    // Actualizar pasos
    const cur = statusOrder[data.status] ?? 0;
    stepKeys.forEach((key, i) => {
        const el   = document.getElementById('step-' + key);
        const line = document.getElementById('line-' + i);
        if (el) {
            el.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ' +
                (cur >= statusOrder[key] ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-400');
        }
        if (line) {
            line.className = 'w-8 h-px mb-4 ' + (cur > statusOrder[key] ? 'bg-orange-400' : 'bg-gray-200');
        }
    });

    // Actualizar label y mensaje
    const labelEl = document.getElementById('status-label');
    labelEl.textContent = data.label;
    labelEl.className   = 'text-2xl font-bold mb-2 text-center ' + (colors[data.status] || 'text-gray-600');
    document.getElementById('status-msg').textContent = labels[data.status] || '';

    // Banner de mesero saludando
    if (data.greeted && !wasGreeted) {
        wasGreeted = true;
        document.getElementById('greeted-banner').classList.remove('hidden');
    }

    isDone = data.status === 'delivered';
}

const pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
    wsHost:            '{{ env("REVERB_HOST", "localhost") }}',
    wsPort:            {{ env("REVERB_PORT", 8080) }},
    wssPort:           {{ env("REVERB_PORT", 8080) }},
    forceTLS:          {{ env("REVERB_SCHEME", "http") === "https" ? "true" : "false" }},
    enabledTransports: ['ws', 'wss'],
    disableStats:      true,
    cluster:           'mt1',
});

const orderCh = pusher.subscribe('order.{{ $order->id }}');
orderCh.bind('order.updated', (data) => {
    if (!isDone) updateUI(data);
});
</script>
</body>
</html>
