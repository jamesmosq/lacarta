<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de tu pedido</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

<div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Pedido #{{ $order->id }}</h1>
    <p class="text-gray-500 text-sm mb-8">{{ tenant('name') }}</p>

    {{-- Estado visual --}}
    <div class="flex justify-between items-center mb-8">
        @foreach([
            ['status' => 'pending',   'icon' => '📝', 'label' => 'Recibido'],
            ['status' => 'preparing', 'icon' => '👨‍🍳', 'label' => 'Preparando'],
            ['status' => 'ready',     'icon' => '✅', 'label' => 'Listo'],
        ] as $step)
        <div class="flex-1 flex flex-col items-center">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl mb-2
                {{ in_array($order->status, [$step['status'], 'delivered']) ||
                   ($step['status'] === 'pending' && in_array($order->status, ['preparing', 'ready', 'delivered'])) ||
                   ($step['status'] === 'preparing' && in_array($order->status, ['ready', 'delivered']))
                   ? 'bg-orange-100' : 'bg-gray-100' }}">
                {{ $step['icon'] }}
            </div>
            <span class="text-xs text-gray-500">{{ $step['label'] }}</span>
        </div>
        @if(!$loop->last)
        <div class="w-8 h-px bg-gray-200 mb-6"></div>
        @endif
        @endforeach
    </div>

    <div class="text-3xl font-bold mb-1
        @if($order->status === 'ready') text-green-600
        @elseif($order->status === 'preparing') text-blue-600
        @else text-orange-600 @endif">
        {{ $order->statusLabel() }}
    </div>

    @if($order->status === 'ready')
    <p class="text-gray-500 mt-2">¡Tu pedido está listo! El mesero lo llevará a tu mesa.</p>
    @elseif($order->status === 'preparing')
    <p class="text-gray-500 mt-2">Tu pedido está siendo preparado. ¡Ya casi!</p>
    @else
    <p class="text-gray-500 mt-2">Tu pedido fue recibido y está en cola.</p>
    @endif

    {{-- Resumen --}}
    <div class="border-t mt-6 pt-6 text-left space-y-2">
        @foreach($order->items as $item)
        <div class="flex justify-between text-sm">
            <span>{{ $item->quantity }}x {{ $item->dish->name }}</span>
            <span>${{ number_format($item->subtotal(), 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="flex justify-between font-bold border-t pt-2 mt-2">
            <span>Total</span>
            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <a href="{{ url('/' . $tenant . '/menu') }}"
       class="block mt-6 text-orange-600 text-sm hover:underline">
        ← Volver al menú
    </a>
</div>

{{-- Auto-refresh cada 15 segundos para ver cambios de estado --}}
<script>
    setTimeout(() => location.reload(), 15000);
</script>
</body>
</html>
