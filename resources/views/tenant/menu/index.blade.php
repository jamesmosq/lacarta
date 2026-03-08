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
    <div class="text-5xl mb-4">📋</div>
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
        <h3 class="font-bold text-lg">{{ $category->name }}</h3>
        <span class="text-gray-400 text-sm">{{ $category->dishes->count() }} plato(s)</span>
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
            <div class="flex items-center gap-4">
                <span class="font-bold text-gray-900">${{ number_format($dish->price, 0, ',', '.') }}</span>

                <form method="POST" action="{{ route('tenant.dish.toggle', ['tenant' => $tenant, 'dish' => $dish->id]) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="text-sm px-3 py-1 rounded-lg border transition
                            {{ $dish->available ? 'border-green-300 text-green-600 hover:bg-green-50' : 'border-gray-300 text-gray-500 hover:bg-gray-50' }}">
                        {{ $dish->available ? 'Disponible' : 'Activar' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('tenant.dish.destroy', ['tenant' => $tenant, 'dish' => $dish->id]) }}"
                      onsubmit="return confirm('¿Eliminar este plato?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 text-sm">Eliminar</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endforeach

@endif
@endsection
