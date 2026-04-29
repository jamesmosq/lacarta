<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SuperAdmin') — LaCarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">

<div class="min-h-screen flex">

    <aside class="w-56 bg-gray-900 text-white flex flex-col flex-shrink-0">
        <div class="p-6 border-b border-gray-700">
            <span class="text-xl font-bold text-orange-500">LaCarta</span>
            <p class="text-gray-400 text-xs mt-1">SuperAdmin</p>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition
                      {{ request()->routeIs('superadmin.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <p class="text-xs text-gray-500 px-4 mb-2">{{ auth('superadmin')->user()?->email }}</p>
            <form method="POST" action="{{ route('superadmin.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8 min-w-0">
        @yield('content')
    </main>

</div>

@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
        @if(session('success'))
        Toast.fire({ icon: 'success', title: @json(session('success')) });
        @elseif(session('error'))
        Toast.fire({ icon: 'error', title: @json(session('error')) });
        @endif
    });
</script>
@endif

@stack('scripts')

</body>
</html>
