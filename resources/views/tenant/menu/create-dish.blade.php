@extends('layouts.tenant')

@section('title', 'Nuevo plato')

@section('content')
<div class="max-w-lg">
    <div class="mb-6">
        <a href="{{ route('tenant.menu', ['tenant' => $tenant]) }}" class="text-orange-600 text-sm hover:underline">← Volver al menú</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Nuevo plato</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('tenant.dish.store', ['tenant' => $tenant]) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del plato</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                       placeholder="Ej: Bandeja Paisa, Ajiaco..." required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                          placeholder="Ingredientes o descripción breve...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-orange-400">
                    <span class="bg-gray-50 px-3 py-2.5 text-gray-400 text-sm border-r border-gray-300">$</span>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="100"
                           class="flex-1 px-3 py-2.5 outline-none"
                           placeholder="25000" required>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                Agregar al menú
            </button>
        </form>
    </div>
</div>
@endsection
