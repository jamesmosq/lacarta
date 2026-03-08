@extends('layouts.app')

@section('title', 'LaCarta — Menu digital para restaurantes')

@section('content')
<div class="min-h-screen bg-white">

    <nav class="border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <span class="text-xl font-bold text-orange-600 tracking-tight">LaCarta</span>
            <a href="{{ route('register') }}"
               class="bg-orange-600 text-white px-6 py-2 rounded-full hover:bg-orange-700 transition text-sm font-medium">
                Empezar gratis
            </a>
        </div>
    </nav>

    <section class="max-w-6xl mx-auto px-6 py-28 text-center">
        <h1 class="text-5xl font-bold text-gray-900 leading-tight mb-6">
            Tu restaurante con<br>
            <span class="text-orange-600">menu digital en minutos</span>
        </h1>
        <p class="text-xl text-gray-500 mb-10 max-w-2xl mx-auto">
            Tus clientes escanean un codigo QR, ven tu menu y hacen su pedido desde la mesa.
            Sin apps, sin complicaciones.
        </p>
        <a href="{{ route('register') }}"
           class="bg-orange-600 text-white px-10 py-4 rounded-full text-lg font-semibold hover:bg-orange-700 transition shadow-md">
            Registra tu restaurante gratis
        </a>
        <p class="text-gray-400 text-sm mt-4">14 dias de prueba · Sin tarjeta de credito</p>
    </section>

    <section class="bg-gray-50 py-20">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Menu digital con QR</h3>
                <p class="text-gray-500 text-sm">Tus clientes ven el menu desde su celular escaneando el codigo de la mesa.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Pedidos en tiempo real</h3>
                <p class="text-gray-500 text-sm">Recibe y gestiona pedidos al instante desde tu panel de administracion.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Reportes de ventas</h3>
                <p class="text-gray-500 text-sm">Sabe que platos vendes mas y toma mejores decisiones para tu negocio.</p>
            </div>
        </div>
    </section>

    @if(session('success'))
    <div class="max-w-xl mx-auto my-8 px-6">
        <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl text-center text-sm">
            {{ session('success') }}
        </div>
    </div>
    @endif

</div>
@endsection
