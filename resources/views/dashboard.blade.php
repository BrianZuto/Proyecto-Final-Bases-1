@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Hero Section -->
    <div class="mb-6 rounded-xl overflow-hidden relative" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&h=400&fit=crop'); background-size: cover; background-position: center;">
        <div class="p-8 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold mb-2">
                        ¡Bienvenido de vuelta, <span class="text-blue-300">{{ Auth::user()->name ?? 'Atleta' }}</span>!
                    </h2>
                    <p class="text-blue-100 mb-6 max-w-2xl">
                        Es hora de superar tus límites y alcanzar nuevas metas. Tu próximo entrenamiento te espera.
                    </p>
                    <div class="flex space-x-4">
                        <button class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                            </svg>
                            <span>▷ Comenzar Entrenamiento</span>
                        </button>
                        <button class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Nueva Rutina</span>
                        </button>
                    </div>
                </div>
                @if($planActivo)
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 ml-6 border border-white/20">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="font-semibold text-white">Plan Activo</span>
                        </div>
                        <p class="text-lg font-bold text-white">{{ $planActivo->nombre }}</p>
                        <p class="text-sm text-blue-200 mt-1">
                            Vence: {{ \Carbon\Carbon::parse($planActivo->pivot->fecha_fin)->format('d/m/Y') }}
                        </p>
                    </div>
                @else
                    <div class="bg-yellow-500/20 backdrop-blur-sm rounded-lg p-4 ml-6 border border-yellow-300/30">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-yellow-200 font-semibold">Sin plan asignado</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Card 1: Progreso Semanal -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="mb-4">
                <div class="text-3xl font-bold text-gray-800 mb-2">{{ $stats['completados'] }}/{{ $stats['total'] }}</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($stats['completados'] / $stats['total']) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Calorías Quemadas -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-sm text-gray-600 mb-1">Calorías Quemadas</h3>
                    <div class="text-3xl font-bold text-gray-800">{{ number_format($stats['calorias']) }}</div>
                    <p class="text-sm text-green-600 mt-1">+12% vs semana pasada</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Peso Levantado -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-sm text-gray-600 mb-1">Peso Levantado</h3>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['peso_levantado'] }} T</div>
                    <p class="text-sm text-gray-500 mt-1">Meta: 10T esta semana</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Tiempo Total -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-sm text-gray-600 mb-1">Tiempo Total</h3>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['tiempo_total'] }}h</div>
                    <p class="text-sm text-gray-500 mt-1">Promedio: 3h por sesión</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Secciones Inferiores -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Progreso Reciente -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Progreso Reciente</span>
                </h3>
            </div>
            <div class="space-y-4">
                @forelse($progreso_reciente as $progreso)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $progreso['ejercicio'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $progreso['anterior'] }} → {{ $progreso['actual'] }}</p>
                        </div>
                        <span class="text-green-600 font-semibold">{{ $progreso['mejora'] }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay progreso reciente</p>
                @endforelse
            </div>
        </div>

        <!-- Próximos Entrenamientos -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Próximos Entrenamientos</span>
                </h3>
            </div>
            <div class="space-y-4">
                @forelse($proximos_entrenamientos as $entrenamiento)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $entrenamiento['fecha'] }}</p>
                            <p class="text-gray-600">{{ $entrenamiento['rutina'] }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay entrenamientos programados</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
