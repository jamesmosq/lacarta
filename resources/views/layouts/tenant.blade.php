<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', tenant('name')) — Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">
    <aside class="w-64 bg-orange-600 text-white flex flex-col">
        <div class="p-6 border-b border-orange-500">
            <span class="text-xl font-bold tracking-tight">LaCarta</span>
            <p class="text-orange-200 text-sm mt-1">{{ tenant('name') }}</p>
        </div>

        @php $t = tenant('id'); $user = auth()->user(); @endphp
        <nav class="flex-1 p-4 space-y-1">

            @if($user->isOwner())
            <a href="{{ route('tenant.dashboard', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.dashboard') ? 'bg-orange-500' : '' }}">
                Dashboard
            </a>
            @endif

            @if($user->isOwner() || $user->isWaiter())
            <a href="{{ route('tenant.waiter', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.waiter*') ? 'bg-orange-500' : '' }}">
                Tomar pedido
            </a>
            @endif

            @if($user->isOwner() || $user->isKitchen())
            <a href="{{ route('tenant.kitchen', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.kitchen*') ? 'bg-orange-500' : '' }}">
                Cocina
            </a>
            @endif

            @if($user->isOwner())
            <a href="{{ route('tenant.menu', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.menu') || request()->routeIs('tenant.category*') || request()->routeIs('tenant.dish*') ? 'bg-orange-500' : '' }}">
                Menu
            </a>
            <a href="{{ route('tenant.tables', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.tables*') ? 'bg-orange-500' : '' }}">
                Mesas
            </a>
            <a href="{{ route('tenant.orders', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.orders') ? 'bg-orange-500' : '' }}">
                Pedidos
            </a>
            <a href="{{ route('tenant.reports', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.reports*') ? 'bg-orange-500' : '' }}">
                Reportes
            </a>
            <a href="{{ route('tenant.inventory', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.inventory*') ? 'bg-orange-500' : '' }}">
                Inventario
            </a>
            <a href="{{ route('tenant.staff', ['tenant' => $t]) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-orange-500 transition {{ request()->routeIs('tenant.staff*') ? 'bg-orange-500' : '' }}">
                Equipo
            </a>
            @endif

        </nav>

        <div class="p-4 border-t border-orange-500">
            <form method="POST" action="{{ route('tenant.logout', ['tenant' => $t]) }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 rounded-lg hover:bg-orange-500 transition text-sm">
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8">
        @yield('content')
    </main>
</div>

@stack('scripts')

@if(session('success') || session('error') || session('warning'))
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    @if(session('success'))
    Toast.fire({ icon: 'success', title: @json(session('success')) });
    @elseif(session('error'))
    Toast.fire({ icon: 'error', title: @json(session('error')) });
    @elseif(session('warning'))
    Toast.fire({ icon: 'warning', title: @json(session('warning')) });
    @endif
</script>
@endif
</body>
</html>
