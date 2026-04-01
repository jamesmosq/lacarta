@extends('layouts.tenant')

@section('title', 'Menú')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Menú</h2>
        <p class="text-gray-500">Gestiona categorías y platos</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('tenant.category.create', ['tenant' => $tenant]) }}"
           class="border border-orange-600 text-orange-600 px-4 py-2 rounded-lg text-sm hover:bg-orange-50 transition">
            + Categoría
        </a>
        <a href="{{ route('tenant.dish.create', ['tenant' => $tenant]) }}"
           class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 transition">
            + Plato
        </a>
    </div>
</div>

@if($categories->isEmpty())
<div class="bg-white rounded-xl shadow-sm px-6 py-16 text-center text-gray-400">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <p class="text-lg font-medium">Tu menú está vacío</p>
    <p class="text-sm mt-1">Crea una categoría primero (Entradas, Platos, Bebidas...)</p>
    <a href="{{ route('tenant.category.create', ['tenant' => $tenant]) }}"
       class="inline-block mt-4 bg-orange-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-orange-700 transition">
        Crear categoría
    </a>
</div>
@else

@foreach($categories as $category)
<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <div>
            <h3 class="font-bold text-lg">{{ $category->name }}</h3>
            <span class="text-gray-400 text-sm">{{ $category->dishes->count() }} plato(s)</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.category.edit', ['tenant' => $tenant, 'category' => $category->id]) }}"
               class="text-sm text-gray-500 hover:text-orange-600 px-3 py-1 rounded-lg border border-gray-200 hover:border-orange-300 transition">
                Editar
            </a>
            <form method="POST" action="{{ route('tenant.category.destroy', ['tenant' => $tenant, 'category' => $category->id]) }}"
                  class="form-delete-category">
                @csrf @method('DELETE')
                <button type="button"
                        onclick="confirmarEliminarCategoria(this.closest('form'), '{{ $category->name }}')"
                        class="text-sm text-red-400 hover:text-red-600 px-3 py-1 rounded-lg border border-transparent hover:border-red-200 transition">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    @if($category->dishes->isEmpty())
    <div class="px-6 py-8 text-center text-gray-400 text-sm">
        No hay platos en esta categoría.
        <a href="{{ route('tenant.dish.create', ['tenant' => $tenant]) }}" class="text-orange-600 hover:underline">Agregar uno</a>
    </div>
    @else
    <div class="divide-y">
        @foreach($category->dishes as $dish)
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex-1 flex items-center gap-4">
                @if($dish->image)
                <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}"
                     class="w-14 h-14 rounded-lg object-cover flex-shrink-0 border border-gray-100">
                @else
                <div class="w-14 h-14 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                @endif
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <span class="font-medium">{{ $dish->name }}</span>
                    @if(!$dish->available)
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">No disponible</span>
                    @endif
                </div>
                @if($dish->description)
                    <p class="text-gray-400 text-sm mt-0.5">{{ $dish->description }}</p>
                @endif
            </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-bold text-gray-900">${{ number_format($dish->price, 0, ',', '.') }}</span>

                <form method="POST" action="{{ route('tenant.dish.toggle', ['tenant' => $tenant, 'dish' => $dish->id]) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="text-sm px-3 py-1 rounded-lg border transition
                            {{ $dish->available ? 'border-green-300 text-green-600 hover:bg-green-50' : 'border-gray-300 text-gray-500 hover:bg-gray-50' }}">
                        {{ $dish->available ? 'Disponible' : 'Activar' }}
                    </button>
                </form>

                <a href="{{ route('tenant.dish.edit', ['tenant' => $tenant, 'dish' => $dish->id]) }}"
                   class="text-sm text-gray-500 hover:text-orange-600 px-3 py-1 rounded-lg border border-gray-200 hover:border-orange-300 transition">
                    Editar
                </a>

                <form method="POST" action="{{ route('tenant.dish.destroy', ['tenant' => $tenant, 'dish' => $dish->id]) }}">
                    @csrf @method('DELETE')
                    <button type="button"
                            onclick="confirmarEliminarPlato(this.closest('form'), '{{ $dish->name }}')"
                            class="text-sm text-red-400 hover:text-red-600 px-3 py-1 rounded-lg border border-transparent hover:border-red-200 transition">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endforeach

@endif

@push('scripts')
<script>
function confirmarEliminarPlato(form, nombre) {
    Swal.fire({
        title: '¿Eliminar plato?',
        html: `<span class="text-gray-600">Se eliminará <strong>${nombre}</strong> permanentemente.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(result => { if (result.isConfirmed) form.submit(); });
}

function confirmarEliminarCategoria(form, nombre) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        html: `<span class="text-gray-600">Se eliminará <strong>${nombre}</strong> y todos sus platos permanentemente.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar',
    }).then(result => { if (result.isConfirmed) form.submit(); });
}
</script>
@endpush
@endsection
