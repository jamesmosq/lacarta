<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\MenuController;
use App\Http\Controllers\Tenant\OrderController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\InventoryController;
use App\Http\Controllers\Tenant\TableController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\StaffController;
use App\Http\Controllers\Tenant\WaiterController;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Public\MenuPublicController;

// ─── Rutas públicas (cliente escanea QR) ──────────────────────────────────────
Route::middleware(['web', InitializeTenancyByPath::class])
    ->prefix('/{tenant}')
    ->group(function () {
        Route::get('/menu',                          [MenuPublicController::class, 'index'])->name('tenant.menu.public');
        Route::post('/menu/pedido',                  [MenuPublicController::class, 'order'])->name('tenant.menu.order');
        Route::get('/menu/pedido/{order}/estado',    [MenuPublicController::class, 'status'])->name('tenant.order.status');
    });

// ─── Panel de administración ───────────────────────────────────────────────────
Route::middleware(['web', InitializeTenancyByPath::class])
    ->prefix('/{tenant}/admin')
    ->group(function () {

        // Auth (sin protección)
        Route::get('/login',  [AuthController::class, 'showLogin'])->name('tenant.login');
        Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
        Route::post('/logout',[AuthController::class, 'logout'])->name('tenant.logout');

        // ── Rutas protegidas por auth ──────────────────────────────────────────
        Route::middleware(['auth'])->group(function () {

            // Mesero y dueño: panel de tomar pedidos
            Route::middleware(['role:owner,waiter'])->group(function () {
                Route::get('/mesero',                        [WaiterController::class, 'index'])->name('tenant.waiter');
                Route::get('/mesero/mesa/{table}/pedido',    [WaiterController::class, 'createOrder'])->name('tenant.waiter.create_order');
                Route::post('/mesero/mesa/{table}/pedido',   [WaiterController::class, 'storeOrder'])->name('tenant.waiter.store_order');
            });

            // Cocina y dueño: KDS
            Route::middleware(['role:owner,kitchen'])->group(function () {
                Route::get('/cocina',           [OrderController::class, 'kitchen'])->name('tenant.kitchen');
                Route::get('/cocina/pedidos',   [OrderController::class, 'kitchenOrders'])->name('tenant.kitchen.orders');
                Route::patch('/cocina/{order}/estado', [OrderController::class, 'kitchenUpdate'])->name('tenant.kitchen.update');
            });

            // Solo dueño: gestión completa
            Route::middleware(['role:owner'])->group(function () {

                // Dashboard y settings
                Route::get('/dashboard',                    [DashboardController::class, 'index'])->name('tenant.dashboard');
                Route::patch('/settings/toggle-open',       [SettingsController::class, 'toggleOpen'])->name('tenant.settings.toggle_open');

                // Equipo
                Route::get('/equipo',                       [StaffController::class, 'index'])->name('tenant.staff');
                Route::post('/equipo',                      [StaffController::class, 'store'])->name('tenant.staff.store');
                Route::delete('/equipo/{staff}',            [StaffController::class, 'destroy'])->name('tenant.staff.destroy');

                // Menú
                Route::get('/menu',                         [MenuController::class, 'index'])->name('tenant.menu');
                Route::get('/menu/categoria/crear',         [MenuController::class, 'createCategory'])->name('tenant.category.create');
                Route::post('/menu/categoria',              [MenuController::class, 'storeCategory'])->name('tenant.category.store');
                Route::get('/menu/categoria/{category}/editar', [MenuController::class, 'editCategory'])->name('tenant.category.edit');
                Route::patch('/menu/categoria/{category}',  [MenuController::class, 'updateCategory'])->name('tenant.category.update');
                Route::delete('/menu/categoria/{category}', [MenuController::class, 'destroyCategory'])->name('tenant.category.destroy');
                Route::get('/menu/plato/crear',             [MenuController::class, 'createDish'])->name('tenant.dish.create');
                Route::post('/menu/plato',                  [MenuController::class, 'storeDish'])->name('tenant.dish.store');
                Route::get('/menu/plato/{dish}/editar',     [MenuController::class, 'editDish'])->name('tenant.dish.edit');
                Route::patch('/menu/plato/{dish}',          [MenuController::class, 'updateDish'])->name('tenant.dish.update');
                Route::patch('/menu/plato/{dish}/disponible',[MenuController::class, 'toggleAvailable'])->name('tenant.dish.toggle');
                Route::delete('/menu/plato/{dish}',         [MenuController::class, 'destroyDish'])->name('tenant.dish.destroy');

                // Mesas
                Route::get('/mesas',                        [TableController::class, 'index'])->name('tenant.tables');
                Route::post('/mesas',                       [TableController::class, 'store'])->name('tenant.tables.store');
                Route::patch('/mesas/{table}/activa',       [TableController::class, 'toggleActive'])->name('tenant.tables.toggle');
                Route::delete('/mesas/{table}',             [TableController::class, 'destroy'])->name('tenant.tables.destroy');

                // Pedidos
                Route::get('/pedidos',                      [OrderController::class, 'index'])->name('tenant.orders');
                Route::patch('/pedidos/{order}/estado',     [OrderController::class, 'updateStatus'])->name('tenant.order.update');

                // Reportes
                Route::get('/reportes',                     [ReportController::class, 'index'])->name('tenant.reports');
                Route::get('/reportes/exportar',            [ReportController::class, 'export'])->name('tenant.reports.export');

                // Inventario
                Route::get('/inventario',                   [InventoryController::class, 'index'])->name('tenant.inventory');
                Route::post('/inventario/ingrediente',      [InventoryController::class, 'store'])->name('tenant.inventory.store');
                Route::patch('/inventario/ingrediente/{ingredient}/restock', [InventoryController::class, 'restock'])->name('tenant.inventory.restock');
                Route::delete('/inventario/ingrediente/{ingredient}',        [InventoryController::class, 'destroy'])->name('tenant.inventory.destroy');
                Route::post('/inventario/plato/{dish}/ingrediente',          [InventoryController::class, 'linkDish'])->name('tenant.inventory.link');
                Route::delete('/inventario/plato/{dish}/ingrediente/{ingredient}', [InventoryController::class, 'unlinkDish'])->name('tenant.inventory.unlink');

            }); // fin role:owner
        }); // fin auth
    });
