<?php

use Illuminate\Support\Facades\Broadcast;

// Canal tenant general: notificaciones del SuperAdmin a cualquier usuario autenticado del tenant
Broadcast::channel('tenant.{tenantSlug}', function ($user, string $tenantSlug) {
    return tenancy()->initialized && tenant('id') === $tenantSlug;
});

// Canal de cocina: dueño y cocina ven todos los pedidos en vivo
Broadcast::channel('kitchen.{tenantSlug}', function ($user, string $tenantSlug) {
    return tenancy()->initialized
        && tenant('id') === $tenantSlug
        && in_array($user->role, ['owner', 'kitchen']);
});

// Canal por mesero: solo el mesero con ese ID recibe sus notificaciones
Broadcast::channel('waiter.{tenantSlug}.{waiterId}', function ($user, string $tenantSlug, string $waiterId) {
    return tenancy()->initialized
        && tenant('id') === $tenantSlug
        && (int) $user->id === (int) $waiterId
        && in_array($user->role, ['owner', 'waiter']);
});
