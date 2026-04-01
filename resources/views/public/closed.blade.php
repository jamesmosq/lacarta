<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant('name') }} — Cerrado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

<div class="max-w-sm w-full text-center">

    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $tenantModel->name }}</h1>
    <p class="text-gray-500 text-base mb-1">En este momento estamos cerrados.</p>
    <p class="text-gray-400 text-sm">Vuelve más tarde para ver nuestro menú y hacer tu pedido.</p>

    <div class="mt-8 border-t border-gray-100 pt-6 text-xs text-gray-300">
        Powered by LaCarta
    </div>
</div>

</body>
</html>
