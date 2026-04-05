<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin — LaCarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">

<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-56 bg-gray-900 text-white flex flex-col">
        <div class="p-6 border-b border-gray-700">
            <span class="text-xl font-bold text-orange-500">LaCarta</span>
            <p class="text-gray-400 text-xs mt-1">SuperAdmin</p>
        </div>
        <nav class="flex-1 p-4">
            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-700 text-white text-sm">
                Restaurantes
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="{{ route('superadmin.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Restaurantes</h1>
            <p class="text-gray-500 text-sm">{{ $tenants->count() }} restaurantes registrados</p>
        </div>

        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 })
                    .fire({ icon: 'success', title: @json(session('success')) });
            });
        </script>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Restaurante</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Correo</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Plan</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Estado</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Registro</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenants as $tenant)
                    <tr class="{{ !$tenant->is_active ? 'opacity-50' : '' }}">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $tenant->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $tenant->slug }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $tenant->email }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $tenant->plan === 'trial' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $tenant->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $tenant->is_active ? 'Activo' : 'Desactivado' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">
                            {{ $tenant->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST"
                                  action="{{ route('superadmin.tenant.toggle', ['tenant' => $tenant->id]) }}"
                                  onsubmit="return confirm('¿{{ $tenant->is_active ? 'Desactivar' : 'Activar' }} este restaurante?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg font-medium transition
                                               {{ $tenant->is_active
                                                  ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                                  : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                    {{ $tenant->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
