@extends('layouts.tenant')

@section('title', 'Equipo')

@section('content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Equipo</h2>
        <p class="text-gray-500">Gestiona los accesos de meseros y cocina</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('tenant.staff.stats', ['tenant' => tenant('id')]) }}"
           class="inline-flex items-center gap-1.5 text-sm text-orange-600 hover:text-orange-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Estadisticas →
        </a>
        <a href="{{ route('tenant.shifts.staff', ['tenant' => tenant('id')]) }}"
           class="text-sm text-gray-500 hover:text-gray-700 font-medium">
            Ver turnos →
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Formulario agregar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-5">Agregar usuario</h3>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 text-sm">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('tenant.staff.store', ['tenant' => $tenant]) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm"
                       placeholder="Juan García">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm"
                       placeholder="juan@restaurante.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select name="role" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm">
                    <option value="waiter"  {{ old('role') === 'waiter'  ? 'selected' : '' }}>Mesero</option>
                    <option value="kitchen" {{ old('role') === 'kitchen' ? 'selected' : '' }}>Cocina</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña temporal</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm"
                       placeholder="Mínimo 6 caracteres">
            </div>
            <button type="submit"
                    class="w-full bg-orange-600 text-white py-2.5 rounded-lg font-semibold hover:bg-orange-700 transition text-sm">
                Crear usuario
            </button>
        </form>
    </div>

    {{-- Lista del equipo --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-900">Usuarios activos</h3>
        </div>

        @if($staff->isEmpty())
        <div class="px-6 py-16 text-center text-gray-400">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium">No hay usuarios en el equipo</p>
            <p class="text-xs mt-1">Agrega meseros y personal de cocina</p>
        </div>
        @else
        <div class="divide-y">
            @foreach($staff as $member)
            {{-- Formulario oculto para resetear contraseña --}}
            <form id="reset-pwd-form-{{ $member->id }}" method="POST"
                  action="{{ route('tenant.staff.reset_password', ['tenant' => $tenant, 'staff' => $member->id]) }}"
                  class="hidden">
                @csrf @method('PATCH')
                <input type="hidden" name="password" id="reset-pwd-input-{{ $member->id }}">
            </form>

            <div class="px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-600 font-semibold text-sm">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 text-sm">{{ $member->name }}</p>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $member->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        {{ $member->role === 'waiter' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ \App\Models\TenantUser::roleLabel($member->role) }}
                    </span>

                    {{-- Resetear contraseña --}}
                    <button type="button"
                            onclick="resetearPassword({{ $member->id }}, '{{ addslashes($member->name) }}')"
                            class="p-1.5 text-gray-400 hover:text-indigo-600 transition"
                            title="Resetear contraseña">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </button>

                    {{-- Eliminar --}}
                    <form method="POST" action="{{ route('tenant.staff.destroy', ['tenant' => $tenant, 'staff' => $member->id]) }}">
                        @csrf @method('DELETE')
                        <button type="button"
                                onclick="confirmarEliminar(this.closest('form'), '{{ addslashes($member->name) }}')"
                                class="p-1.5 text-gray-400 hover:text-red-600 transition"
                                title="Eliminar usuario">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t bg-gray-50 rounded-b-xl">
            <p class="text-xs text-gray-400">
                URL de acceso para el equipo:
                <span class="font-mono text-gray-600">{{ url('/' . $tenant . '/admin/login') }}</span>
            </p>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function confirmarEliminar(form, nombre) {
    Swal.fire({
        title: 'Eliminar usuario',
        html: `<span class="text-gray-600">Se eliminará el acceso de <strong>${nombre}</strong>.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}

function resetearPassword(id, nombre) {
    Swal.fire({
        title: 'Nueva contraseña para ' + nombre,
        input: 'password',
        inputLabel: 'Mínimo 6 caracteres',
        inputPlaceholder: 'Nueva contraseña',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value.length < 6) return 'La contraseña debe tener al menos 6 caracteres.';
        }
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('reset-pwd-input-' + id).value = result.value;
            document.getElementById('reset-pwd-form-' + id).submit();
        }
    });
}
</script>
@endpush
@endsection
