@extends('layouts.superadmin')

@section('title', $tenant->name)

@section('content')

{{-- Encabezado --}}
<div class="mb-6 flex items-start justify-between gap-4">
    <div class="flex items-start gap-4">
        <a href="{{ route('superadmin.dashboard') }}"
           class="mt-1 p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
            <p class="text-gray-400 text-sm font-mono mt-0.5">{{ $tenant->slug }} · {{ $tenant->email }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ url('/' . $tenant->slug . '/menu') }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium bg-green-50 text-green-700 hover:bg-green-100 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Menu publico
        </a>
        <a href="{{ url('/' . $tenant->slug . '/admin/login') }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-medium bg-orange-50 text-orange-700 hover:bg-orange-100 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Panel admin
        </a>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium
            {{ $tenant->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $tenant->is_active ? 'Activo' : 'Inactivo' }}
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium
            {{ $tenant->plan === 'pro' ? 'bg-purple-100 text-purple-700' :
               ($tenant->plan === 'basic' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
            {{ ucfirst($tenant->plan) }}
        </span>
    </div>
</div>

@if($error)
<div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 mb-6 text-sm flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    {{ $error }}
</div>
@endif

{{-- KPIs del tenant --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pedidos historicos</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos totales</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pedidos activos</p>
        <p class="text-3xl font-bold {{ $activeOrders->count() > 0 ? 'text-orange-600' : 'text-gray-900' }} mt-2">
            {{ $activeOrders->count() }}
        </p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Equipo</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $team->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">usuarios registrados</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna izquierda --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Pedidos activos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Pedidos activos</h2>
                @if($activeOrders->count() > 0)
                <span class="inline-flex items-center gap-1.5 text-xs text-orange-600 font-medium">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    En curso
                </span>
                @endif
            </div>
            @if($activeOrders->isEmpty())
            <p class="px-6 py-8 text-center text-gray-400 text-sm">Sin pedidos activos ahora mismo.</p>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($activeOrders as $order)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">Pedido #{{ $order->id }}</p>
                            <p class="text-xs text-gray-400">Mesa {{ $order->table_id }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $order->status === 'pending'   ? 'bg-yellow-100 text-yellow-700' :
                               ($order->status === 'preparing' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($order->items as $item)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                            {{ $item->quantity }}x {{ $item->dish?->name ?? 'Plato eliminado' }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Ultimos 10 pedidos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Ultimos pedidos</h2>
            </div>
            @if($recentOrders->isEmpty())
            <p class="px-6 py-8 text-center text-gray-400 text-sm">No hay pedidos registrados.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-500">#</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-500">Mesa</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-500">Mesero</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-500">Estado</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-500">Items</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="px-6 py-3 text-gray-400 text-xs">#{{ $order->id }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $order->table_id }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $order->waiter?->name ?? 'QR' }}</td>
                            <td class="px-6 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700' :
                                       ($order->status === 'ready'    ? 'bg-blue-100 text-blue-700' :
                                       ($order->status === 'preparing' ? 'bg-indigo-100 text-indigo-700' :
                                                                          'bg-yellow-100 text-yellow-700')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-400 text-xs">{{ $order->items->count() }} items</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>

    {{-- Columna derecha --}}
    <div class="space-y-6">

        {{-- Acciones rapidas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
            <h2 class="font-semibold text-gray-800 mb-4">Acciones</h2>

            {{-- Impersonar --}}
            @if($tenant->is_active)
            <form method="POST" action="{{ route('superadmin.tenant.impersonate', $tenant->id) }}"
                  id="impersonate-form">
                @csrf
                <button type="button" onclick="confirmImpersonate()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Entrar como owner
                </button>
            </form>
            @endif

            {{-- Restablecer contraseña del owner --}}
            <form method="POST" action="{{ route('superadmin.tenant.reset_password', $tenant->id) }}"
                  id="reset-pwd-form">
                @csrf
                <input type="hidden" name="password" id="reset-pwd-input">
                <button type="button" onclick="resetOwnerPassword()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Restablecer contraseña owner
                </button>
            </form>

            {{-- Toggle activo --}}
            <form method="POST" action="{{ route('superadmin.tenant.toggle', $tenant->id) }}"
                  id="toggle-form">
                @csrf
                @method('PATCH')
                <button type="button"
                        onclick="confirmToggle()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition
                               {{ $tenant->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-200' }}">
                    {{ $tenant->is_active ? 'Desactivar restaurante' : 'Activar restaurante' }}
                </button>
            </form>
        </div>

        {{-- Gestion de plan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-800 mb-4">Plan y trial</h2>
            <form method="POST" action="{{ route('superadmin.tenant.plan', $tenant->id) }}">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Plan</label>
                    <select name="plan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="trial" {{ $tenant->plan === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="basic" {{ $tenant->plan === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="pro"   {{ $tenant->plan === 'pro'   ? 'selected' : '' }}>Pro</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vencimiento del trial</label>
                    <input type="date" name="trial_ends_at"
                           value="{{ $tenant->trial_ends_at ? \Carbon\Carbon::parse($tenant->trial_ends_at)->format('Y-m-d') : '' }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <button type="submit"
                        class="w-full bg-orange-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-orange-700 transition">
                    Guardar cambios
                </button>
            </form>
        </div>

        {{-- Enviar notificacion --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-800 mb-4">Enviar notificacion</h2>
            <form method="POST" action="{{ route('superadmin.tenant.notify', $tenant->id) }}">
                @csrf
                <div class="mb-3">
                    <textarea name="message" rows="3" maxlength="500"
                              placeholder="Escribe un mensaje para el owner del restaurante..."
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                              required></textarea>
                    <p class="text-xs text-gray-400 mt-1">Aparece como banner en su panel al ingresar.</p>
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-blue-700 transition">
                    Enviar notificacion
                </button>
            </form>
        </div>

        {{-- Equipo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Equipo ({{ $team->count() }})</h2>
            </div>
            @if($team->isEmpty())
            <p class="px-5 py-6 text-center text-gray-400 text-sm">Sin usuarios registrados.</p>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($team as $member)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $member->name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($member->role) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Top platos --}}
        @if($topDishes->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Top platos</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($topDishes as $i => $item)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-400 w-4">{{ $i + 1 }}</span>
                        <p class="text-sm text-gray-800">{{ $item->dish?->name ?? 'Plato eliminado' }}</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-500">{{ $item->total_qty }} uds</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Notificaciones enviadas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Notificaciones enviadas</h2>
            </div>
            @if($notifications->isEmpty())
            <p class="px-5 py-6 text-center text-gray-400 text-sm">No hay notificaciones enviadas.</p>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($notifications as $notif)
                <div class="px-5 py-3">
                    <p class="text-sm text-gray-700">{{ $notif->message }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs text-gray-400">{{ $notif->created_at->format('d M Y H:i') }}</p>
                        @if($notif->read_at)
                        <span class="text-xs text-green-600">Leida</span>
                        @else
                        <span class="text-xs text-yellow-600">Sin leer</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmImpersonate() {
    Swal.fire({
        title: '¿Entrar como {{ addslashes($tenant->name) }}?',
        text: 'Veras el panel de administracion como si fueras el owner de este restaurante.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, entrar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) document.getElementById('impersonate-form').submit();
    });
}

function confirmToggle() {
    const isActive = {{ $tenant->is_active ? 'true' : 'false' }};
    Swal.fire({
        title: (isActive ? 'Desactivar ' : 'Activar ') + '{{ addslashes($tenant->name) }}?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc2626' : '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: isActive ? 'Si, desactivar' : 'Si, activar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) document.getElementById('toggle-form').submit();
    });
}

function resetOwnerPassword() {
    Swal.fire({
        title: 'Nueva contraseña para el owner',
        html: '<p class="text-sm text-gray-500 mb-2">El owner de <strong>{{ addslashes($tenant->name) }}</strong> usará esta contraseña para ingresar.</p>',
        input: 'password',
        inputLabel: 'Contraseña temporal (mínimo 6 caracteres)',
        inputPlaceholder: 'Nueva contraseña',
        showCancelButton: true,
        confirmButtonColor: '#374151',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Restablecer',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value.length < 6) return 'La contraseña debe tener al menos 6 caracteres.';
        }
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('reset-pwd-input').value = result.value;
            document.getElementById('reset-pwd-form').submit();
        }
    });
}
</script>
@endpush
