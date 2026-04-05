@extends('layouts.tenant')

@section('title', 'Banco de menú')

@section('content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Banco de menú</h2>
        <p class="text-gray-500 text-sm">Importa platos prearmados a tu menú</p>
    </div>
    <a href="{{ route('tenant.menu', ['tenant' => $tenant]) }}"
       class="text-sm text-gray-500 hover:text-orange-600 transition">← Volver al menú</a>
</div>

{{-- Buscador --}}
<form method="GET" action="{{ route('tenant.menu_bank', ['tenant' => $tenant]) }}"
      class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Buscar plato o categoría..."
           class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
    <button type="submit"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition">
        Buscar
    </button>
</form>

@if($items->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center text-gray-400">
    <p class="text-sm font-medium">No hay platos en el banco de menú</p>
    <p class="text-xs mt-1">El superadmin puede agregar platos al banco central</p>
</div>
@else

<form method="POST" action="{{ route('tenant.menu_bank.import', ['tenant' => $tenant]) }}" id="import-form">
    @csrf

    {{-- Agrupar por categoría sugerida --}}
    @foreach($items->groupBy('category_hint') as $categoryHint => $groupItems)
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">
            {{ $categoryHint ?: 'Sin categoría' }}
        </h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y">
            @foreach($groupItems as $item)
            <div class="px-5 py-4 flex items-center gap-4" id="row-{{ $item->id }}">
                <input type="checkbox" name="select_{{ $item->id }}" id="chk-{{ $item->id }}"
                       onchange="toggleRow({{ $item->id }}, this.checked)"
                       class="w-4 h-4 accent-orange-600 cursor-pointer flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm">{{ $item->name }}</p>
                    @if($item->description)
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $item->description }}</p>
                    @endif
                </div>
                {{-- Configuración de importación (oculta hasta seleccionar) --}}
                <div id="config-{{ $item->id }}" class="hidden flex items-center gap-3">
                    <input type="hidden" name="items[{{ $loop->index }}][bank_id]" value="{{ $item->id }}">
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5">Precio</label>
                        <input type="number" name="items[{{ $loop->index }}][price]"
                               value="{{ $item->default_price }}" min="0" step="0.01"
                               class="w-28 border border-gray-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5">Categoría</label>
                        <select name="items[{{ $loop->index }}][category_id]"
                                class="border border-gray-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <option value="nueva">+ Crear "{{ $categoryHint ?: 'General' }}"</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <span class="text-sm font-bold text-orange-600 flex-shrink-0">
                    ${{ number_format($item->default_price, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div id="import-bar" class="hidden fixed bottom-0 left-64 right-0 bg-gray-900 text-white p-4 z-50 shadow-2xl">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <span id="selected-count" class="text-sm font-medium">0 platos seleccionados</span>
            <button type="button" onclick="submitImport()"
                    class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded-lg transition text-sm">
                Importar al menú
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
let selected = new Set();

function toggleRow(id, checked) {
    const config = document.getElementById('config-' + id);
    if (checked) {
        selected.add(id);
        config.classList.remove('hidden');
        config.classList.add('flex');
    } else {
        selected.delete(id);
        config.classList.add('hidden');
        config.classList.remove('flex');
    }
    document.getElementById('selected-count').textContent = selected.size + ' plato(s) seleccionado(s)';
    document.getElementById('import-bar').classList.toggle('hidden', selected.size === 0);
}

function submitImport() {
    // Reindexar inputs antes de enviar
    let i = 0;
    document.querySelectorAll('[name^="items["]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, `items[${Math.floor(i++ / 3)}]`);
    });
    document.getElementById('import-form').submit();
}
</script>
@endpush

@endif
@endsection
