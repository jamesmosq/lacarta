@extends('layouts.tenant')

@section('title', 'Nueva categoría')

@section('content')
<div class="max-w-lg">
    <div class="mb-6">
        <a href="{{ route('tenant.menu', ['tenant' => $tenant]) }}" class="text-orange-600 text-sm hover:underline">← Volver al menú</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Nueva categoría</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('tenant.category.store', ['tenant' => $tenant]) }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la categoría</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                       placeholder="Ej: Entradas, Platos fuertes, Bebidas..." required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden de aparición</label>
                <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none">
            </div>
            <button type="submit"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                Crear categoría
            </button>
        </form>
    </div>
</div>
@endsection
