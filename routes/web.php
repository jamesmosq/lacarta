<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\TenantRegisterController;
use App\Http\Controllers\Central\CentralLoginController;
use App\Http\Controllers\Central\SuperAdminAuthController;
use App\Http\Controllers\Central\SuperAdminController;

// Landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Registro de restaurante
Route::get('/registro', [TenantRegisterController::class, 'create'])->name('register');
Route::post('/registro', [TenantRegisterController::class, 'store'])->name('register.store');

// Login centralizado: "Encuentra tu restaurante por correo"
Route::get('/acceso',  [CentralLoginController::class, 'show'])->name('central.login');
Route::post('/acceso', [CentralLoginController::class, 'find'])->name('central.login.find');

// SuperAdmin
Route::prefix('/superadmin')->name('superadmin.')->group(function () {
    Route::get('/login',  [SuperAdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout',[SuperAdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['superadmin'])->group(function () {
        Route::get('/dashboard',                          [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::patch('/tenant/{tenant}/toggle-active',    [SuperAdminController::class, 'toggleActive'])->name('tenant.toggle');
        Route::get('/tenant/{tenant}',                    [SuperAdminController::class, 'tenantDetail'])->name('tenant.detail');
        Route::patch('/tenant/{tenant}/plan',             [SuperAdminController::class, 'updatePlan'])->name('tenant.plan');
        Route::post('/tenant/{tenant}/impersonate',       [SuperAdminController::class, 'impersonate'])->name('tenant.impersonate');
        Route::post('/impersonate/exit',                  [SuperAdminController::class, 'stopImpersonating'])->name('impersonate.exit');
        Route::post('/tenant/{tenant}/notify',            [SuperAdminController::class, 'sendNotification'])->name('tenant.notify');
        Route::post('/tenant/{tenant}/reset-password',   [SuperAdminController::class, 'resetOwnerPassword'])->name('tenant.reset_password');
        Route::post('/broadcast',                        [SuperAdminController::class, 'broadcastAll'])->name('broadcast');
    });
});
