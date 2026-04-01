@extends('layouts.tenant')

@section('title', 'Mesas')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mesas</h2>
        <p class="text-gray-500">Genera el QR de cada mesa para que tus clientes pidan desde su celular</p>
    </div>
</div>

{{-- Formulario agregar mesa --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <h3 class="font-semibold text-gray-800 mb-4">Agregar mesa</h3>
    <form method="POST" action="{{ route('tenant.tables.store', ['tenant' => $tenant]) }}"
          class="flex gap-3 items-end">
        @csrf
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la mesa</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                   placeholder="Ej: Mesa 1, Terraza A, Barra..." required>
        </div>
        <button type="submit"
                class="bg-orange-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-orange-700 transition whitespace-nowrap">
            + Agregar
        </button>
    </form>
</div>

@if($tables->isEmpty())
<div class="bg-white rounded-xl shadow-sm px-6 py-16 text-center text-gray-400">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
    </div>
    <p class="text-lg font-medium">No hay mesas registradas</p>
    <p class="text-sm mt-1">Agrega la primera mesa para generar su código QR</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tables as $table)
    @php $menuUrl = url("/{$tenant}/menu?mesa={$table->id}") @endphp
    <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center">

        <div class="flex items-center justify-between w-full mb-4">
            <h3 class="font-bold text-gray-900 text-lg">{{ $table->name }}</h3>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                {{ $table->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $table->active ? 'Activa' : 'Inactiva' }}
            </span>
        </div>

        {{-- QR generado por JS --}}
        <div id="qr-{{ $table->id }}" class="mb-4"></div>

        <p class="text-xs text-gray-400 text-center break-all mb-5">{{ $menuUrl }}</p>

        <div class="flex gap-2 w-full">
            <button onclick="imprimirQR({{ $table->id }}, '{{ $table->name }}')"
                    class="flex-1 border border-orange-300 text-orange-600 py-2 rounded-lg text-sm font-medium hover:bg-orange-50 transition">
                Imprimir QR
            </button>

            <form method="POST" action="{{ route('tenant.tables.toggle', ['tenant' => $tenant, 'table' => $table->id]) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="px-3 py-2 rounded-lg text-sm border transition
                        {{ $table->active ? 'border-gray-200 text-gray-500 hover:bg-gray-50' : 'border-green-300 text-green-600 hover:bg-green-50' }}">
                    {{ $table->active ? 'Desactivar' : 'Activar' }}
                </button>
            </form>

            <form method="POST" action="{{ route('tenant.tables.destroy', ['tenant' => $tenant, 'table' => $table->id]) }}">
                @csrf @method('DELETE')
                <button type="button"
                        onclick="confirmarEliminarMesa(this.closest('form'), '{{ $table->name }}')"
                        class="px-3 py-2 rounded-lg text-sm border border-transparent text-red-400 hover:text-red-600 hover:border-red-200 transition">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Plantilla de impresión oculta --}}
<div id="print-area" class="hidden"></div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const menuUrls = {
    @foreach($tables as $table)
    {{ $table->id }}: "{{ url("/{$tenant}/menu?mesa={$table->id}") }}",
    @endforeach
};

// Generar QRs al cargar
document.addEventListener('DOMContentLoaded', () => {
    @foreach($tables as $table)
    new QRCode(document.getElementById('qr-{{ $table->id }}'), {
        text: menuUrls[{{ $table->id }}],
        width: 160,
        height: 160,
        colorDark: '#1f2937',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
    });
    @endforeach
});

function imprimirQR(tableId, tableName) {
    const canvas = document.querySelector('#qr-' + tableId + ' canvas');
    const img = canvas ? canvas.toDataURL('image/png') : '';
    const url = menuUrls[tableId];

    const win = window.open('', '_blank', 'width=400,height=500');
    win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>QR ${tableName}</title>
            <style>
                body { font-family: sans-serif; display: flex; flex-direction: column;
                       align-items: center; justify-content: center; min-height: 100vh;
                       margin: 0; padding: 20px; text-align: center; }
                h1 { font-size: 22px; margin-bottom: 4px; }
                p  { color: #9ca3af; font-size: 12px; margin-bottom: 16px; }
                img { width: 200px; height: 200px; }
                small { display: block; color: #d1d5db; font-size: 10px; margin-top: 12px; word-break: break-all; }
            </style>
        </head>
        <body>
            <h1>${tableName}</h1>
            <p>Escanea para ver el menú y hacer tu pedido</p>
            <img src="${img}" alt="QR ${tableName}">
            <small>${url}</small>
            <script>window.onload = () => { window.print(); }<\/script>
        </body>
        </html>
    `);
    win.document.close();
}

function confirmarEliminarMesa(form, nombre) {
    Swal.fire({
        title: '¿Eliminar mesa?',
        html: `<span class="text-gray-600">Se eliminará <strong>${nombre}</strong> permanentemente.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(result => { if (result.isConfirmed) form.submit(); });
}
</script>
@endpush
@endsection
