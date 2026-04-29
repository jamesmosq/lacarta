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

    {{-- Cambiar contraseña --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <button type="button" onclick="togglePasswordForm()"
                class="w-full flex items-center justify-between text-left">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-900">Cambiar contraseña</span>
            </div>
            <svg id="pwd-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="password-form" class="hidden mt-5 pt-5 border-t border-gray-100">
            @if($errors->has('current_password'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-2 mb-4 text-sm">
                {{ $errors->first('current_password') }}
            </div>
            @endif

            <form method="POST" action="{{ route('tenant.profile.password', ['tenant' => tenant('id')]) }}"
                  class="space-y-4 max-w-sm">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm
                                  @error('current_password') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                    <input type="password" name="password" required minlength="6" autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" required minlength="6" autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 outline-none text-sm">
                </div>
                <button type="submit"
                        class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition">
                    Actualizar contraseña
                </button>
            </form>
        </div>
    </div>

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

@push('scripts')
<script>
function togglePasswordForm() {
    const form    = document.getElementById('password-form');
    const chevron = document.getElementById('pwd-chevron');
    const hidden  = form.classList.contains('hidden');
    form.classList.toggle('hidden', !hidden);
    chevron.style.transform = hidden ? 'rotate(180deg)' : '';
}

@if($errors->has('current_password'))
document.addEventListener('DOMContentLoaded', () => togglePasswordForm());
@endif
</script>
@endpush
@endsection
