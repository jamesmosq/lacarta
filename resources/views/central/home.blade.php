@extends('layouts.app')

@section('title', 'LaCarta — Menú digital para restaurantes')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100">

    {{-- Nav --}}
    <nav class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <span class="text-2xl font-bold text-orange-600">🍽️ LaCarta</span>
            <a href="{{ route('register') }}"
               class="bg-orange-600 text-white px-6 py-2 rounded-full hover:bg-orange-700 transition font-medium">
                Empezar gratis
            </a>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="max-w-6xl mx-auto px-6 py-24 text-center">
        <h1 class="text-5xl font-bold text-gray-900 leading-tight mb-6">
            Tu restaurante con<br>
            <span class="text-orange-600">menú digital en minutos</span>
        </h1>
        <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
            Tus clientes escanean un QR, ven tu menú y hacen su pedido desde la mesa.
            Sin apps, sin complicaciones.
        </p>
        <a href="{{ route('register') }}"
           class="bg-orange-600 text-white px-10 py-4 rounded-full text-lg font-semibold hover:bg-orange-700 transition shadow-lg">
            Registra tu restaurante gratis →
        </a>
        <p class="text-gray-400 text-sm mt-4">14 días de prueba · Sin tarjeta de crédito</p>
    </section>

    {{-- Features --}}
    <section class="max-w-6xl mx-auto px-6 pb-24 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
            <div class="text-5xl mb-4">📱</div>
            <h3 class="text-xl font-bold mb-2">Menú digital con QR</h3>
            <p class="text-gray-500">Tus clientes ven el menú desde su celular escaneando el código de la mesa.</p>
        </div>
        <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
            <div class="text-5xl mb-4">🛒</div>
            <h3 class="text-xl font-bold mb-2">Pedidos en tiempo real</h3>
            <p class="text-gray-500">Recibe y gestiona pedidos al instante desde tu panel de administración.</p>
        </div>
        <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
            <div class="text-5xl mb-4">📊</div>
            <h3 class="text-xl font-bold mb-2">Reportes de ventas</h3>
            <p class="text-gray-500">Sabe qué platos vendes más y toma mejores decisiones para tu negocio.</p>
        </div>
    </section>

    @if(session('success'))
    <div class="max-w-xl mx-auto mb-8">
        <div class="bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-xl text-center">
            {{ session('success') }}
        </div>
    </div>
    @endif

</div>
@endsection
