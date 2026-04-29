@extends('layouts.tenant')

@section('title', 'Cocina')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Panel de Cocina</h2>
        <p class="text-gray-400 text-sm mt-1">
            Se actualiza automáticamente · Última actualización: <span id="last-update">—</span>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span id="polling-indicator"
              class="w-2.5 h-2.5 rounded-full bg-green-400 inline-block animate-pulse"></span>
        <span class="text-sm text-gray-500">En vivo</span>
    </div>
</div>

<div id="kds-board" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <div class="col-span-full text-center py-20 text-gray-300">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-lg">Cargando pedidos...</p>
    </div>
</div>

@push('scripts')
<script>
const tenant = @json($tenant);
const ordersUrl = `/{{ $tenant }}/admin/cocina/pedidos`;
const updateUrl = (id) => `/{{ $tenant }}/admin/cocina/${id}/estado`;
const csrf = document.querySelector('meta[name=csrf-token]').content;

const statusLabel = { pending: 'Pendiente', preparing: 'Preparando' };
const statusNext  = { pending: 'preparing', preparing: 'ready' };
const statusBtn   = { pending: 'Iniciar', preparing: 'Listo ✓' };
const statusColor = {
    pending:   'border-yellow-400 bg-yellow-50',
    preparing: 'border-blue-400 bg-blue-50',
};
const statusBadge = {
    pending:   'bg-yellow-100 text-yellow-700',
    preparing: 'bg-blue-100 text-blue-700',
};

async function loadOrders() {
    try {
        const res = await fetch(ordersUrl);
        const orders = await res.json();
        render(orders);
        document.getElementById('last-update').textContent = new Date().toLocaleTimeString('es-CO');
    } catch (e) {
        document.getElementById('polling-indicator').classList.replace('bg-green-400', 'bg-red-400');
    }
}

function render(orders) {
    const board = document.getElementById('kds-board');

    if (orders.length === 0) {
        board.innerHTML = `
            <div class="col-span-full text-center py-20 text-gray-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-lg font-medium text-gray-400">Sin pedidos pendientes</p>
            </div>`;
        return;
    }

    board.innerHTML = orders.map(order => {
        const items = order.items.map(i =>
            `<div class="flex gap-2 text-sm">
                <span class="font-bold text-gray-800 w-6 text-right flex-shrink-0">${i.qty}x</span>
                <div>
                    <span class="text-gray-800">${i.name}</span>
                    ${i.note ? `<span class="text-orange-500 text-xs block">${i.note}</span>` : ''}
                </div>
             </div>`
        ).join('');

        const context = [order.table, order.customer_name].filter(Boolean).join(' · ');
        const nextStatus = statusNext[order.status];

        return `
        <div class="bg-white rounded-xl border-l-4 ${statusColor[order.status]} shadow-sm p-5 flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg leading-tight">Pedido #${order.id}</h3>
                    ${context ? `<p class="text-gray-500 text-sm mt-0.5">${context}</p>` : ''}
                    <p class="text-gray-400 text-xs mt-0.5">${order.created_at}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full ${statusBadge[order.status]}">
                    ${statusLabel[order.status]}
                </span>
            </div>

            <div class="space-y-2 border-t pt-3">${items}</div>

            ${order.notes ? `<p class="text-xs bg-orange-50 text-orange-700 rounded-lg px-3 py-2 border border-orange-100">📝 ${order.notes}</p>` : ''}

            ${nextStatus ? `
            <button onclick="advanceOrder(${order.id}, '${nextStatus}', this)"
                    class="w-full py-2.5 rounded-lg text-sm font-bold transition
                    ${nextStatus === 'ready'
                        ? 'bg-green-500 hover:bg-green-600 text-white'
                        : 'bg-blue-500 hover:bg-blue-600 text-white'}">
                ${statusBtn[order.status]}
            </button>` : ''}
        </div>`;
    }).join('');
}

async function advanceOrder(id, newStatus, btn) {
    btn.disabled = true;
    btn.textContent = '...';
    await fetch(updateUrl(id), {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ status: newStatus }),
    });
    loadOrders();
}

loadOrders();

const pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
    wsHost:            '{{ env("REVERB_HOST", "localhost") }}',
    wsPort:            {{ env("REVERB_PORT", 8080) }},
    wssPort:           {{ env("REVERB_PORT", 8080) }},
    forceTLS:          {{ env("REVERB_SCHEME", "http") === "https" ? "true" : "false" }},
    enabledTransports: ['ws', 'wss'],
    disableStats:      true,
    cluster:           'mt1',
    authEndpoint:      '/{{ $tenant }}/broadcasting/auth',
    auth:              { headers: { 'X-CSRF-TOKEN': csrf } },
});

const kitchenCh = pusher.subscribe('private-kitchen.{{ $tenant }}');
kitchenCh.bind('order.created', loadOrders);
kitchenCh.bind('order.updated', loadOrders);

pusher.connection.bind('connected', () => {
    document.getElementById('polling-indicator').classList.remove('bg-red-400');
    document.getElementById('polling-indicator').classList.add('bg-green-400');
});
pusher.connection.bind('disconnected', () => {
    document.getElementById('polling-indicator').classList.remove('bg-green-400');
    document.getElementById('polling-indicator').classList.add('bg-red-400');
});
</script>
@endpush
@endsection
