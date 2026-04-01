@extends('layouts.app')

@section('title', 'Registra tu restaurante — LaCarta')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-lg p-10 w-full max-w-md">

        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-bold text-orange-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                LaCarta
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-4">Registra tu restaurante</h2>
            <p class="text-gray-500 text-sm mt-1">14 días gratis, sin tarjeta</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del restaurante</label>
                <input type="text" name="restaurant_name" value="{{ old('restaurant_name') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                       placeholder="Ej: El Rincón Paisa" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Identificador único
                    <span class="text-gray-400 font-normal">(solo letras, números y guiones)</span>
                </label>
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-orange-400">
                    <span class="bg-gray-50 px-3 py-2.5 text-gray-400 text-sm border-r border-gray-300">lacarta.app/</span>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="flex-1 px-3 py-2.5 outline-none"
                           placeholder="rincon-paisa" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                       placeholder="tu@restaurante.com" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                       placeholder="Mínimo 8 caracteres" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                       placeholder="Repite la contraseña" required>
            </div>

            <button type="submit"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                Crear mi restaurante →
            </button>
        </form>

        <p class="text-center text-gray-400 text-sm mt-6">
            <a href="{{ route('home') }}" class="hover:text-orange-600">← Volver al inicio</a>
        </p>
    </div>
</div>
@endsection
