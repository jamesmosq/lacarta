@extends('layouts.tenant')

@section('title', 'Editar plato')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('tenant.menu', ['tenant' => $tenant]) }}" class="text-orange-600 text-sm hover:underline">← Volver al menú</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Editar plato</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('tenant.dish.update', ['tenant' => $tenant, 'dish' => $dish->id]) }}"
              class="space-y-5" enctype="multipart/form-data">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $dish->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del plato</label>
                <input type="text" name="name" value="{{ old('name', $dish->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                       placeholder="Ej: Bandeja Paisa, Ajiaco..." required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                          placeholder="Ingredientes o descripción breve...">{{ old('description', $dish->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-orange-400">
                    <span class="bg-gray-50 px-3 py-2.5 text-gray-400 text-sm border-r border-gray-300">$</span>
                    <input type="number" name="price" value="{{ old('price', $dish->price) }}" min="0" step="100"
                           class="flex-1 px-3 py-2.5 outline-none"
                           placeholder="25000" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto del plato <span class="text-gray-400 font-normal">(opcional, máx 2MB)</span></label>

                @if($dish->image)
                <div class="mb-3 flex items-center gap-4">
                    <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}"
                         class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                    <label class="flex items-center gap-2 text-sm text-red-500 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded">
                        Eliminar foto actual
                    </label>
                </div>
                @endif

                <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center cursor-pointer hover:border-orange-400 transition"
                     onclick="document.getElementById('image-input').click()">
                    <img id="image-preview" src="" alt="" class="hidden mx-auto mb-2 rounded-lg max-h-40 object-cover">
                    <p id="image-placeholder" class="text-gray-400 text-sm">
                        {{ $dish->image ? 'Subir nueva foto (reemplaza la actual)' : 'Haz clic para subir una foto' }}
                    </p>
                    <input type="file" id="image-input" name="image" accept="image/*" class="hidden"
                           onchange="previewImage(this)">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                Guardar cambios
            </button>
        </form>
    </div>
</div>
@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('image-placeholder');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.classList.remove('hidden');
        placeholder.textContent = input.files[0].name;
    }
}
</script>
@endpush
@endsection
