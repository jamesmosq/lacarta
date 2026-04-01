@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-lg p-10 w-full max-w-md">

        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 text-2xl font-bold text-orange-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                LaCarta
            </span>
            <h2 class="text-2xl font-bold text-gray-900 mt-4">Panel de administración</h2>
            <p class="text-gray-500 text-sm mt-1">Ingresa a tu restaurante</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('tenant.login.post', ['tenant' => $tenantSlug]) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                       required autofocus>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none"
                       required>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded">
                <label for="remember" class="text-sm text-gray-600">Recordarme</label>
            </div>

            <button type="submit"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                Ingresar
            </button>
        </form>
    </div>
</div>
@endsection
