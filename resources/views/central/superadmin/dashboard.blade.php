@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Vision global de la plataforma</p>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Restaurantes</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tenants->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $tenants->where('is_active', true)->count() }} activos</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pedidos hoy</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
        <p class="text-xs text-gray-400 mt-1">en todos los restaurantes</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos hoy</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">suma de todos los tenants</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ticket promedio</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($avgTicket, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">hoy en la plataforma</p>
    </div>
</div>

{{-- Tabla de tenants --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Restaurantes registrados</h2>
        <span class="text-xs text-gray-400">{{ $tenants->count() }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Restaurante</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Correo</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Plan</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Trial vence</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Estado</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-500">Registro</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($tenants as $tenant)
                <tr class="hover:bg-gray-50 transition {{ !$tenant->is_active ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $tenant->name }}</p>
                        <p class="text-gray-400 text-xs font-mono">{{ $tenant->slug }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->email }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $tenant->plan === 'pro'   ? 'bg-purple-100 text-purple-700' :
                               ($tenant->plan === 'basic' ? 'bg-blue-100 text-blue-700' :
                                                            'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($tenant->plan) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">
                        {{ $tenant->trial_ends_at ? \Carbon\Carbon::parse($tenant->trial_ends_at)->format('d M Y') : '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $tenant->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $tenant->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">
                        {{ $tenant->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Panel admin del restaurante --}}
                            <a href="{{ url('/' . $tenant->slug . '/admin/dashboard') }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium bg-orange-50 text-orange-700 hover:bg-orange-100 transition"
                               title="Abrir panel admin">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Admin
                            </a>
                            {{-- Menu publico --}}
                            <a href="{{ url('/' . $tenant->slug . '/menu') }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium bg-green-50 text-green-700 hover:bg-green-100 transition"
                               title="Abrir menu publico">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Menu
                            </a>
                            {{-- Detalle interno --}}
                            <a href="{{ route('superadmin.tenant.detail', $tenant->id) }}"
                               class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detalle
                            </a>

                            @if($tenant->is_active)
                            <form method="POST" action="{{ route('superadmin.tenant.impersonate', $tenant->id) }}"
                                  id="impersonate-form-{{ $tenant->id }}">
                                @csrf
                                <button type="button"
                                        onclick="confirmImpersonate('{{ $tenant->id }}', '{{ addslashes($tenant->name) }}')"
                                        class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Entrar
                                </button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('superadmin.tenant.toggle', $tenant->id) }}"
                                  id="toggle-form-{{ $tenant->id }}">
                                @csrf
                                @method('PATCH')
                                <button type="button"
                                        onclick="confirmToggle('{{ $tenant->id }}', '{{ addslashes($tenant->name) }}', {{ $tenant->is_active ? 'true' : 'false' }})"
                                        class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium transition
                                               {{ $tenant->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                    {{ $tenant->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if($tenants->isEmpty())
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay restaurantes registrados.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmImpersonate(id, name) {
    Swal.fire({
        title: '¿Entrar como ' + name + '?',
        text: 'Veras el panel de administracion como si fueras el dueno de este restaurante.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, entrar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) document.getElementById('impersonate-form-' + id).submit();
    });
}

function confirmToggle(id, name, isActive) {
    const action = isActive ? 'desactivar' : 'activar';
    Swal.fire({
        title: '¿' + (isActive ? 'Desactivar' : 'Activar') + ' ' + name + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc2626' : '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, ' + action,
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) document.getElementById('toggle-form-' + id).submit();
    });
}
</script>
@endpush
