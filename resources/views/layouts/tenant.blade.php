<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', tenant('name')) — Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ea580c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="LaCarta">
    <script>if('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');</script>
</head>
<body class="bg-gray-100">

@php
    $t = tenant('id');
    $user = auth()->user();
    $impersonating = session('impersonating_as_superadmin', false);
    $tenantNotification = \App\Models\TenantNotification::where('tenant_id', $t)
        ->whereNull('read_at')
        ->latest()
        ->first();
@endphp

{{-- ── Banner impersonacion superadmin ───────────────────────────── --}}
@if($impersonating)
<div class="bg-indigo-700 text-white text-sm py-2.5 px-4 flex items-center justify-center gap-4">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
    </svg>
    <span>Modo soporte — Ves el panel de <strong>{{ session('impersonating_tenant_name') }}</strong> como owner</span>
    <form method="POST" action="{{ route('superadmin.impersonate.exit') }}">
        @csrf
        <button type="submit"
                class="bg-white text-indigo-700 font-bold text-xs px-3 py-1 rounded-full hover:bg-indigo-50 transition">
            Salir
        </button>
    </form>
</div>
@endif

{{-- ── Notificacion de SuperAdmin ─────────────────────────────────── --}}
@if($tenantNotification)
<div class="bg-blue-600 text-white px-5 py-3 flex items-start justify-between gap-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-xs font-semibold text-blue-200 mb-0.5">Mensaje de LaCarta</p>
            <p class="text-sm">{{ $tenantNotification->message }}</p>
        </div>
    </div>
    <form method="POST"
          action="{{ route('tenant.notification.dismiss', ['tenant' => $t, 'id' => $tenantNotification->id]) }}"
          class="flex-shrink-0">
        @csrf
        <button type="submit" class="text-blue-200 hover:text-white transition p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </form>
</div>
@endif

{{-- ── Barra superior móvil (solo < md) ──────────────────────────── --}}
<header class="md:hidden fixed top-0 left-0 right-0 z-40 bg-orange-600 text-white h-14 flex items-center px-4 gap-3 shadow-md">
    <button id="sidebar-open" aria-label="Abrir menú"
            class="p-1.5 rounded-lg hover:bg-orange-500 transition flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <div class="flex-1 min-w-0">
        <p class="font-bold text-sm truncate">{{ tenant('name') }}</p>
        <p class="text-orange-200 text-xs truncate">@yield('title', 'Panel')</p>
    </div>
    {{-- Acceso rápido al perfil en móvil --}}
    <a href="{{ route('tenant.profile', ['tenant' => $t]) }}"
       class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0 hover:bg-orange-400 transition">
        <span class="text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
    </a>
</header>

{{-- ── Overlay móvil ──────────────────────────────────────────────── --}}
<div id="sidebar-overlay"
     class="hidden fixed inset-0 bg-black/50 z-30 md:hidden"
     onclick="closeSidebar()">
</div>

<div class="min-h-screen flex">

    {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
    <aside id="sidebar"
           class="fixed md:relative inset-y-0 left-0 z-40 w-64 bg-orange-600 text-white flex flex-col
                  transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out
                  md:min-h-screen">

        {{-- Logo (visible en desktop; en móvil está en el topbar) --}}
        <div class="hidden md:block p-6 border-b border-orange-500">
            <span class="text-xl font-bold tracking-tight">LaCarta</span>
            <p class="text-orange-200 text-sm mt-1">{{ tenant('name') }}</p>
        </div>

        {{-- Cierre en móvil --}}
        <div class="md:hidden flex items-center justify-between p-4 border-b border-orange-500">
            <span class="font-bold">Menú</span>
            <button id="sidebar-close" onclick="closeSidebar()"
                    class="p-1.5 rounded-lg hover:bg-orange-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

            @if($user->isOwner())
            <a href="{{ route('tenant.dashboard', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.dashboard') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            @endif

            @if($user->isOwner() || $user->isWaiter())
            <a href="{{ route('tenant.waiter', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.waiter*') || request()->routeIs('tenant.cash*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Tomar pedido
            </a>
            @endif

            @if($user->isOwner() || $user->isKitchen())
            <a href="{{ route('tenant.kitchen', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.kitchen*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
                Cocina
            </a>
            @endif

            @if($user->isOwner())
            <div class="pt-2 pb-1">
                <p class="px-4 text-xs font-semibold text-orange-300 uppercase tracking-wider">Gestión</p>
            </div>
            <a href="{{ route('tenant.menu', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.menu') || request()->routeIs('tenant.category*') || request()->routeIs('tenant.dish*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Menú
            </a>
            <a href="{{ route('tenant.tables', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.tables*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
                </svg>
                Mesas
            </a>
            <a href="{{ route('tenant.orders', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.orders') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Pedidos
            </a>
            <a href="{{ route('tenant.reports', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.reports*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Reportes
            </a>
            <a href="{{ route('tenant.inventory', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.inventory*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Inventario
            </a>
            <a href="{{ route('tenant.staff', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.staff*') || request()->routeIs('tenant.shifts*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Equipo
            </a>
            <a href="{{ route('tenant.menu_bank', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.menu_bank*') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h8m-8 4h4"/>
                </svg>
                Banco de menu
            </a>
            @endif

            <div class="pt-2 pb-1">
                <p class="px-4 text-xs font-semibold text-orange-300 uppercase tracking-wider">Cuenta</p>
            </div>
            <a href="{{ route('tenant.profile', ['tenant' => $t]) }}"
               onclick="closeSidebar()"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-orange-500 transition text-sm {{ request()->routeIs('tenant.profile') ? 'bg-orange-500' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mi perfil
            </a>

        </nav>

        <div class="p-4 border-t border-orange-500">
            <div class="px-4 py-1 mb-2">
                <p class="text-xs text-orange-200 truncate">{{ $user->name }}</p>
                <p class="text-xs text-orange-300">{{ \App\Models\TenantUser::roleLabel($user->role) }}</p>
            </div>
            <form method="POST" action="{{ route('tenant.logout', ['tenant' => $t]) }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 rounded-lg hover:bg-orange-500 transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Contenido principal ──────────────────────────────────────── --}}
    <main class="flex-1 p-4 md:p-8 pt-18 md:pt-8 min-w-0">
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

<script>
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebar-overlay');
const btnOpen  = document.getElementById('sidebar-open');

function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebar.classList.remove('translate-x-0');
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
}

if (btnOpen) btnOpen.addEventListener('click', openSidebar);

// Cerrar con swipe izquierdo en móvil
let touchStartX = 0;
sidebar.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
sidebar.addEventListener('touchend', e => {
    if (touchStartX - e.changedTouches[0].clientX > 60) closeSidebar();
}, { passive: true });
</script>

</body>
</html>
