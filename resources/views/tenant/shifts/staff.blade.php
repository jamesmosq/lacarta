@extends('layouts.tenant')

@section('title', 'Turnos del equipo')

@section('content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Turnos del equipo</h2>
        <p class="text-gray-500 text-sm">Registro de horas trabajadas y extras</p>
    </div>
    <a href="{{ route('tenant.shifts.export', ['tenant' => tenant('id'), 'desde' => $from, 'hasta' => $to]) }}"
       class="inline-flex items-center gap-2 text-sm bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Exportar CSV
    </a>
</div>

{{-- Filtro de período --}}
<form method="GET" action="{{ route('tenant.shifts.staff', ['tenant' => tenant('id')]) }}"
      class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Desde</label>
        <input type="date" name="desde" value="{{ $from }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Hasta</label>
        <input type="date" name="hasta" value="{{ $to }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
    </div>
    <button type="submit"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition">
        Filtrar
    </button>
</form>

@foreach($staff as $employee)
@if($employee->shifts->isNotEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center">
                <span class="text-sm font-bold text-orange-600">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $employee->name }}</p>
                <p class="text-xs text-gray-400">{{ \App\Models\TenantUser::roleLabel($employee->role) }}</p>
            </div>
        </div>
        @php
            $totalWorked   = $employee->shifts->sum(fn($s) => $s->workedMinutes() ?? 0);
            $totalOvertime = $employee->shifts->sum(fn($s) => max(0, $s->overtimeMinutes() ?? 0));
        @endphp
        <div class="text-right text-sm">
            <p class="font-semibold text-gray-900">{{ gmdate('H:i', $totalWorked * 60) }} hrs totales</p>
            @if($totalOvertime > 0)
            <p class="text-blue-600 font-medium text-xs">+{{ gmdate('H:i', $totalOvertime * 60) }} extra</p>
            @endif
        </div>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($employee->shifts as $shift)
        <div class="px-6 py-3 flex items-center justify-between text-sm">
            <div>
                <span class="text-gray-900 font-medium">{{ $shift->started_at->format('d M') }}</span>
                <span class="text-gray-400 ml-2">{{ $shift->started_at->format('H:i') }} — {{ $shift->ended_at->format('H:i') }}</span>
            </div>
            <div class="text-right">
                <span class="text-gray-700">{{ gmdate('H:i', $shift->workedMinutes() * 60) }}</span>
                @if($shift->overtimeMinutes() > 0)
                <span class="text-blue-600 font-medium ml-2 text-xs">+{{ gmdate('H:i', $shift->overtimeMinutes() * 60) }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endforeach

@if($staff->every(fn($e) => $e->shifts->isEmpty()))
<div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center text-gray-400">
    <p class="text-sm font-medium">Sin turnos registrados en el período seleccionado</p>
</div>
@endif
@endsection
