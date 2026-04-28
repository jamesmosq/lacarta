<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenantModel->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-2xl mx-auto min-h-screen">
    <div class="bg-orange-600 text-white px-6 py-8 text-center">
        <h1 class="text-2xl font-bold tracking-tight">{{ $tenantModel->name }}</h1>
        <p class="text-orange-200 text-sm mt-1">{{ $table->name }}</p>
    </div>

    <div class="px-6 py-8">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-gray-900">¿Quién te va a atender?</h2>
            <p class="text-gray-500 text-sm mt-1">Selecciona tu mesero</p>
        </div>

        <form method="POST" action="{{ route('tenant.menu.select_waiter', ['tenant' => $tenant, 'table' => $table->id]) }}">
            @csrf
            <div class="space-y-3 mb-8">
                @foreach($waiters as $waiter)
                <label class="cursor-pointer block">
                    <input type="radio" name="waiter_id" value="{{ $waiter->id }}" class="sr-only peer" required>
                    <div class="bg-white border-2 rounded-xl p-4 flex items-center gap-4 transition
                                peer-checked:border-orange-500 peer-checked:bg-orange-50
                                border-gray-200 hover:border-orange-300">
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-600 font-bold text-lg">{{ strtoupper(substr($waiter->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $waiter->name }}</p>
                            <p class="text-xs text-green-600 font-medium">Disponible</p>
                        </div>
                        <div class="ml-auto">
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-orange-500 peer-checked:bg-orange-500 transition"></div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <button type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl transition text-base">
                Confirmar
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-4">
            <a href="{{ route('tenant.menu.public', ['tenant' => $tenant, 'mesa' => $table->id, 'skip_waiter' => 1]) }}"
               class="hover:underline">Continuar sin seleccionar mesero</a>
        </p>
    </div>
</div>

</body>
</html>
