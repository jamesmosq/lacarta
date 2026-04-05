<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — LaCarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 w-full max-w-sm">
    <div class="text-center mb-8">
        <span class="text-2xl font-bold text-orange-600">LaCarta</span>
        <p class="text-gray-500 text-sm mt-2">Ingresa tu correo para acceder a tu restaurante</p>
    </div>

    <form method="POST" action="{{ route('central.login.find') }}">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo del restaurante</label>
            <input type="email" name="email" value="{{ old('email') }}" autofocus
                   placeholder="dueno@mirestaurante.com"
                   class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          @error('email') border-red-400 @enderror">
            @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition">
            Continuar
        </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-6">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="text-orange-600 hover:underline">Registra tu restaurante</a>
    </p>
</div>
</body>
</html>
