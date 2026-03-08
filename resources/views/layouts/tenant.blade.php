<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', tenant('name')) — Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-64 bg-orange-600 text-white flex flex-col">
        <div class="p-6 border-b border-orange-500">
            <h1 class="text-xl font-bold">🍽️ LaCarta</h1>
            <p class="text-orange-200 text-sm mt-1">{{ tenant('name') }}</p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('tenant.dashboard', ['tenant' => $tenantSlug ?? request()->route('tenant')]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.dashboard') ? 'bg-orange-500' : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('tenant.menu', ['tenant' => $tenantSlug ?? request()->route('tenant')]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.menu*') ? 'bg-orange-500' : '' }}">
                📋 Menú
            </a>
            <a href="{{ route('tenant.orders', ['tenant' => $tenantSlug ?? request()->route('tenant')]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.orders*') ? 'bg-orange-500' : '' }}">
                🛒 Pedidos
            </a>
        </nav>

        <div class="p-4 border-t border-orange-500">
            <form method="POST" action="{{ route('tenant.logout', ['tenant' => $tenantSlug ?? request()->route('tenant')]) }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 rounded-lg hover:bg-orange-500 transition text-sm">
                    🚪 Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Contenido principal --}}
    <main class="flex-1 p-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
