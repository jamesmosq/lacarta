<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\TenantRegisterController;

// Landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Registro de restaurante
Route::get('/registro', [TenantRegisterController::class, 'create'])->name('register');
Route::post('/registro', [TenantRegisterController::class, 'store'])->name('register.store');
