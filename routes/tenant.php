<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\MenuController;
use App\Http\Controllers\Tenant\OrderController;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Public\MenuPublicController;

// Rutas públicas del restaurante (cliente escanea QR)
Route::middleware([InitializeTenancyByPath::class])
    ->prefix('/{tenant}')
    ->group(function () {
        Route::get('/menu', [MenuPublicController::class, 'index'])->name('tenant.menu.public');
        Route::post('/menu/pedido', [MenuPublicController::class, 'order'])->name('tenant.menu.order');
        Route::get('/menu/pedido/{order}/estado', [MenuPublicController::class, 'status'])->name('tenant.order.status');
    });

// Rutas del panel de administración del restaurante
Route::middleware(['web', InitializeTenancyByPath::class])
    ->prefix('/{tenant}/admin')
    ->group(function () {
        // Auth
        Route::get('/login', [AuthController::class, 'showLogin'])->name('tenant.login');
        Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
        Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

        // Panel protegido
        Route::middleware(['auth'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

            // Menú
            Route::get('/menu', [MenuController::class, 'index'])->name('tenant.menu');
            Route::get('/menu/categoria/crear', [MenuController::class, 'createCategory'])->name('tenant.category.create');
            Route::post('/menu/categoria', [MenuController::class, 'storeCategory'])->name('tenant.category.store');
            Route::get('/menu/plato/crear', [MenuController::class, 'createDish'])->name('tenant.dish.create');
            Route::post('/menu/plato', [MenuController::class, 'storeDish'])->name('tenant.dish.store');
            Route::patch('/menu/plato/{dish}/disponible', [MenuController::class, 'toggleAvailable'])->name('tenant.dish.toggle');
            Route::delete('/menu/plato/{dish}', [MenuController::class, 'destroyDish'])->name('tenant.dish.destroy');

            // Pedidos
            Route::get('/pedidos', [OrderController::class, 'index'])->name('tenant.orders');
            Route::patch('/pedidos/{order}/estado', [OrderController::class, 'updateStatus'])->name('tenant.order.update');
        });
    });
