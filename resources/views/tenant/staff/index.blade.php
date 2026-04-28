@extends('layouts.tenant')

@section('title', 'Equipo')

@section('content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Equipo</h2>
        <p class="text-gray-500">Gestiona los accesos de meseros y cocina</p>
    </div>
    <a href="{{ route('tenant.shifts.staff', ['tenant' => tenant('id')]) }}"
       class="text-sm text-orange-600 hover:underline font-medium">
        Ver turnos y horas →
    </a>
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
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-600 font-semibold text-sm">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $member->name }}</p>
                        <p class="text-gray-400 text-xs mt-0.5">{{ $member->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        {{ $member->role === 'waiter' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ \App\Models\TenantUser::roleLabel($member->role) }}
                    </span>
                    <form method="POST" action="{{ route('tenant.staff.destroy', ['tenant' => $tenant, 'staff' => $member->id]) }}">
                        @csrf @method('DELETE')
                        <button type="button"
                                onclick="confirmarEliminar(this.closest('form'), '{{ $member->name }}')"
                                class="text-red-400 hover:text-red-600 transition">
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
</script>
@endpush
@endsection
