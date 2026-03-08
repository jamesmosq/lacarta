@extends('layouts.tenant')

@section('title', 'Inventario')

@section('content')
@php $t = tenant('id'); @endphp

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Inventario</h1>
    </div>

    {{-- Alertas de stock --}}
    @php
        $out = $ingredients->where('stock', '<=', 0);
        $low = $ingredients->filter(fn($i) => $i->stock > 0 && $i->stock <= $i->min_stock);
    @endphp

    @if($out->isNotEmpty())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        <strong>Sin stock:</strong> {{ $out->pluck('name')->join(', ') }}
    </div>
    @endif

    @if($low->isNotEmpty())
    <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
        <strong>Stock bajo:</strong> {{ $low->pluck('name')->join(', ') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Lista de ingredientes --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Ingredientes</h2>
                </div>

                @if($ingredients->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-10">Aun no hay ingredientes registrados.</p>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($ingredients as $ing)
                    @php
                        $status = $ing->stockStatus();
                        $pct = $ing->min_stock > 0
                            ? min(100, round(($ing->stock / ($ing->min_stock * 3)) * 100))
                            : ($ing->stock > 0 ? 100 : 0);
                        $barColor = match($status) {
                            'out' => 'bg-red-400',
                            'low' => 'bg-yellow-400',
                            default => 'bg-green-400',
                        };
                        $badgeColor = match($status) {
                            'out' => 'bg-red-100 text-red-700',
                            'low' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-green-100 text-green-700',
                        };
                        $badgeLabel = match($status) {
                            'out' => 'Sin stock',
                            'low' => 'Stock bajo',
                            default => 'OK',
                        };
                    @endphp
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-gray-800 text-sm">{{ $ing->name }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $badgeColor }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">
                                    Stock: <strong>{{ rtrim(rtrim($ing->stock, '0'), '.') }} {{ $ing->unit }}</strong>
                                    &nbsp;·&nbsp; Minimo: {{ rtrim(rtrim($ing->min_stock, '0'), '.') }} {{ $ing->unit }}
                                    &nbsp;·&nbsp; {{ $ing->dishes_count }} plato(s)
                                </p>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="{{ $barColor }} h-1.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                {{-- Restock --}}
                                <button onclick="openRestock({{ $ing->id }}, '{{ addslashes($ing->name) }}', '{{ $ing->unit }}')"
                                        class="text-xs bg-orange-50 text-orange-700 border border-orange-200 px-3 py-1.5 rounded-lg hover:bg-orange-100 transition font-medium">
                                    Reponer
                                </button>
                                {{-- Eliminar --}}
                                <form method="POST"
                                      action="{{ route('tenant.inventory.destroy', ['tenant' => $t, 'ingredient' => $ing->id]) }}"
                                      onsubmit="return confirm('Eliminar {{ addslashes($ing->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-100 transition">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Recetas por plato --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Recetas por plato</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Vincula ingredientes a platos para activar el control automatico de stock.</p>
                </div>

                @if($dishes->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-8">No hay platos registrados.</p>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($dishes as $dish)
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="font-medium text-sm text-gray-800">{{ $dish->name }}</span>
                            @if(!$dish->available)
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">No disponible</span>
                            @endif
                        </div>

                        {{-- Ingredientes del plato --}}
                        @if($dish->ingredients->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($dish->ingredients as $ing)
                            <span class="inline-flex items-center gap-1 text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full">
                                {{ $ing->name }} · {{ rtrim(rtrim($ing->pivot->quantity, '0'), '.') }} {{ $ing->unit }}
                                <form method="POST"
                                      action="{{ route('tenant.inventory.unlink', ['tenant' => $t, 'dish' => $dish->id, 'ingredient' => $ing->id]) }}"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-1 text-gray-400 hover:text-red-500 transition leading-none">&times;</button>
                                </form>
                            </span>
                            @endforeach
                        </div>
                        @endif

                        {{-- Agregar ingrediente al plato --}}
                        @if($ingredients->isNotEmpty())
                        <form method="POST"
                              action="{{ route('tenant.inventory.link', ['tenant' => $t, 'dish' => $dish->id]) }}"
                              class="flex gap-2 items-end">
                            @csrf
                            <div>
                                <select name="ingredient_id"
                                        class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    <option value="">Seleccionar ingrediente...</option>
                                    @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" name="quantity" step="0.001" min="0.001" placeholder="Cantidad"
                                       class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs w-24 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <button type="submit"
                                    class="bg-orange-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-orange-700 transition">
                                Vincular
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Panel lateral: agregar ingrediente --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Nuevo ingrediente</h2>
                <form method="POST" action="{{ route('tenant.inventory.store', ['tenant' => $t]) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre</label>
                        <input type="text" name="name" required placeholder="Ej: Carne de res"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Unidad</label>
                        <select name="unit"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <option value="unidad">unidad</option>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="L">L</option>
                            <option value="ml">ml</option>
                            <option value="porcion">porcion</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Stock inicial</label>
                            <input type="number" name="stock" step="0.001" min="0" value="0" required
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Stock minimo</label>
                            <input type="number" name="min_stock" step="0.001" min="0" value="0" required
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">El stock minimo es el umbral bajo el cual el plato se deshabilita automaticamente.</p>
                    <button type="submit"
                            class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition text-sm font-medium">
                        Crear ingrediente
                    </button>
                </form>
            </div>

            {{-- Leyenda --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide">Como funciona</h3>
                <ul class="space-y-2 text-xs text-gray-500">
                    <li class="flex gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 mt-1 flex-shrink-0"></span>
                        <span>Stock suficiente — plato disponible</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 mt-1 flex-shrink-0"></span>
                        <span>Stock bajo del minimo — plato se deshabilita</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-400 mt-1 flex-shrink-0"></span>
                        <span>Sin stock — plato deshabilitado</span>
                    </li>
                </ul>
                <p class="text-xs text-gray-400 mt-3">
                    Al recibir un pedido, el sistema descuenta automaticamente los ingredientes segun la receta de cada plato.
                </p>
            </div>
        </div>

    </div>
</div>

{{-- Modal restock --}}
<div id="restock-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Reponer stock</h3>
        <p id="restock-label" class="text-sm text-gray-500 mb-4"></p>
        <form id="restock-form" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad a agregar</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="cantidad" id="restock-cantidad"
                           step="0.001" min="0.001" required
                           class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <span id="restock-unit" class="text-sm text-gray-500 w-12"></span>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition text-sm font-medium">
                    Confirmar
                </button>
                <button type="button" onclick="closeRestock()"
                        class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const baseRestockUrl = "{{ route('tenant.inventory.restock', ['tenant' => $t, 'ingredient' => '__ID__']) }}";

function openRestock(id, name, unit) {
    document.getElementById('restock-label').textContent = name;
    document.getElementById('restock-unit').textContent = unit;
    document.getElementById('restock-cantidad').value = '';
    document.getElementById('restock-form').action = baseRestockUrl.replace('__ID__', id);
    document.getElementById('restock-modal').classList.remove('hidden');
}

function closeRestock() {
    document.getElementById('restock-modal').classList.add('hidden');
}

document.getElementById('restock-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRestock();
});
</script>
@endpush
