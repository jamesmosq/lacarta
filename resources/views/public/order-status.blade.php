<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del pedido — {{ tenant('name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-md w-full text-center">
    <p class="text-xs uppercase tracking-widest text-gray-400 mb-1">{{ tenant('name') }}</p>
    <h1 class="text-xl font-bold text-gray-900 mb-8">Pedido #{{ $order->id }}</h1>

    {{-- Pasos de estado --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        @php
            $steps = [
                ['key' => 'pending',   'label' => 'Recibido'],
                ['key' => 'preparing', 'label' => 'Preparando'],
                ['key' => 'ready',     'label' => 'Listo'],
            ];
            $statusOrder = ['pending' => 0, 'preparing' => 1, 'ready' => 2, 'delivered' => 3];
            $current = $statusOrder[$order->status] ?? 0;
        @endphp

        @foreach($steps as $i => $step)
        <div class="flex items-center gap-2">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $current >= $statusOrder[$step['key']] ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                    {{ $i + 1 }}
                </div>
                <span class="text-xs text-gray-400 mt-1 w-16 text-center">{{ $step['label'] }}</span>
            </div>
            @if(!$loop->last)
            <div class="w-8 h-px {{ $current > $statusOrder[$step['key']] ? 'bg-orange-400' : 'bg-gray-200' }} mb-4"></div>
            @endif
        </div>
        @endforeach
    </div>

    <p class="text-2xl font-bold mb-2
        @if($order->status === 'ready') text-green-600
        @elseif($order->status === 'preparing') text-blue-600
        @else text-orange-600 @endif">
        {{ $order->statusLabel() }}
    </p>

    <p class="text-gray-400 text-sm mb-8">
        @if($order->status === 'ready') Tu pedido esta listo. El mesero lo llevara a tu mesa en un momento.
        @elseif($order->status === 'preparing') Tu pedido esta siendo preparado. Un momento por favor.
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
       class="block mt-6 text-sm text-orange-600 hover:underline">
        Volver al menu
    </a>
</div>

<script>
    @if(!in_array($order->status, ['delivered']))
    setTimeout(() => location.reload(), 15000);
    @endif
</script>
</body>
</html>
