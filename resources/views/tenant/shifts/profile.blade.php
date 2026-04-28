@extends('layouts.tenant')

@section('title', 'Mi perfil')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Mi perfil</h2>

    {{-- Datos básicos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <span class="text-2xl font-bold text-orange-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full font-medium
                    @if($user->role === 'owner') bg-purple-100 text-purple-700
                    @elseif($user->role === 'waiter') bg-blue-100 text-blue-700
                    @else bg-green-100 text-green-700 @endif">
                    {{ \App\Models\TenantUser::roleLabel($user->role) }}
                </span>
            </div>
            <div class="ml-auto text-right">
                <span class="inline-flex items-center gap-1.5 text-sm font-medium
                    {{ $user->available ? 'text-green-600' : 'text-gray-400' }}">
                    <span class="w-2 h-2 rounded-full {{ $user->available ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                    {{ $user->available ? 'Disponible' : 'No disponible' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Turno activo --}}
    @if($activeShift)
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-green-800">Turno activo</h3>
                <p class="text-green-700 text-sm mt-0.5">
                    Iniciado a las {{ $activeShift->started_at->format('H:i') }}
                    ({{ $activeShift->started_at->diffForHumans() }})
                </p>
            </div>
            <form method="POST" action="{{ route('tenant.shift.end', ['tenant' => tenant('id'), 'shift' => $activeShift->id]) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Terminar turno
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900">Sin turno activo</h3>
                <p class="text-gray-400 text-sm">Inicia tu turno cuando llegues a trabajar</p>
            </div>
            <form method="POST" action="{{ route('tenant.shift.start', ['tenant' => tenant('id')]) }}">
                @csrf
                <button type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Iniciar turno
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Turnos recientes --}}
    @if($recentShifts->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Últimos turnos</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recentShifts as $shift)
            @php
                $worked   = $shift->workedMinutes();
                $overtime = $shift->overtimeMinutes();
            @endphp
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $shift->started_at->format('d M Y') }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $shift->started_at->format('H:i') }} — {{ $shift->ended_at->format('H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">{{ gmdate('H:i', $worked * 60) }} hrs</p>
                    @if($overtime > 0)
                    <p class="text-xs text-blue-600 font-medium">+{{ gmdate('H:i', $overtime * 60) }} extra</p>
                    @elseif($overtime < 0)
                    <p class="text-xs text-gray-400">−{{ gmdate('H:i', abs($overtime) * 60) }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
