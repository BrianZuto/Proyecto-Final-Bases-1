@extends('layouts.app')

@section('title', 'Mi Progreso')

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Mi Progreso</h1>
                <p class="text-gray-600">Sigue tu evolución y mejora continua</p>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-blue-100 text-sm font-medium">Sesiones Totales</div>
                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ $stats['sesiones_totales'] }}</div>
            <div class="text-blue-100 text-sm mt-1">Sesiones completadas</div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-green-100 text-sm font-medium">Racha Actual</div>
                <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ $rachaActual }}</div>
            <div class="text-green-100 text-sm mt-1">Días consecutivos</div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-orange-100 text-sm font-medium">Calorías Totales</div>
                <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ number_format($stats['calorias_totales']) }}</div>
            <div class="text-orange-100 text-sm mt-1">Calorías quemadas</div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-purple-100 text-sm font-medium">Rutinas Completadas</div>
                <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ $stats['rutinas_completadas'] }}</div>
            <div class="text-purple-100 text-sm mt-1">Rutinas finalizadas</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Gráfico de Sesiones por Mes -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Sesiones por Mes (Últimos 6 meses)</h2>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($sesionesPorMes as $mes)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-blue-100 rounded-t-lg mb-2 relative" style="height: {{ $mes['cantidad'] > 0 ? max(20, ($mes['cantidad'] / max(array_column($sesionesPorMes, 'cantidad'))) * 200) : 0 }}px">
                            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs font-semibold text-gray-700">
                                {{ $mes['cantidad'] }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-600 text-center transform -rotate-45 origin-top-left" style="writing-mode: vertical-rl;">
                            {{ $mes['mes'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Estadísticas Adicionales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Estadísticas Adicionales</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Tiempo Total</div>
                            <div class="text-sm text-gray-600">{{ number_format($stats['tiempo_total']) }} minutos</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Ejercicios Completados</div>
                            <div class="text-sm text-gray-600">{{ $stats['ejercicios_completados'] }} ejercicios únicos</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Sesiones Este Mes</div>
                            <div class="text-sm text-gray-600">{{ $stats['sesiones_mes'] }} sesiones</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progreso de Rutinas -->
    @if($rutinasProgreso->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Progreso de Rutinas</h2>
        <div class="space-y-4">
            @foreach($rutinasProgreso as $rutina)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800">{{ $rutina->nombre }}</h3>
                        @php
                            $estadoColors = [
                                'completada' => 'bg-green-100 text-green-700',
                                'en_progreso' => 'bg-yellow-100 text-yellow-700',
                                'pendiente' => 'bg-gray-100 text-gray-700',
                            ];
                            $estadoLabels = [
                                'completada' => 'Completada',
                                'en_progreso' => 'En Progreso',
                                'pendiente' => 'Pendiente',
                            ];
                        @endphp
                        <span class="px-3 py-1 {{ $estadoColors[$rutina->estado] ?? 'bg-gray-100 text-gray-700' }} rounded-full text-sm font-medium">
                            {{ $estadoLabels[$rutina->estado] ?? ucfirst($rutina->estado) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                            <span>Progreso</span>
                            <span class="font-semibold">{{ number_format($rutina->porcentaje_completado, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ min(100, $rutina->porcentaje_completado) }}%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Sesiones completadas: <strong>{{ $rutina->sesiones_completadas }}</strong></span>
                        @if($rutina->fecha_ultima_sesion)
                        <span>Última sesión: {{ \Carbon\Carbon::parse($rutina->fecha_ultima_sesion)->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Progreso Reciente de Ejercicios -->
    @if($progresoEjercicios->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Actividad Reciente (Últimos 30 días)</h2>
        <div class="space-y-3">
            @foreach($progresoEjercicios->take(10) as $ejercicio)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ $ejercicio->ejercicio_nombre }}</div>
                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($ejercicio->fecha_registro)->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-gray-800">{{ $ejercicio->veces_completado }}x</div>
                        <div class="text-sm text-gray-600">completado</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection

