@extends('layouts.app')

@section('title', 'Mis Logros')

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Mis Logros</h1>
                <p class="text-gray-600">Recompensas por tu dedicación y esfuerzo</p>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Logros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-yellow-100 text-sm font-medium">Total Logros</div>
                <svg class="w-8 h-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ $statsLogros['total_logros'] }}</div>
            <div class="text-yellow-100 text-sm mt-1">Logros desbloqueados</div>
        </div>

        <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-orange-100 text-sm font-medium">Puntos Totales</div>
                <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ number_format($statsLogros['puntos_totales']) }}</div>
            <div class="text-orange-100 text-sm mt-1">Puntos acumulados</div>
        </div>

        <div class="bg-gradient-to-br from-red-400 to-red-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <div class="text-red-100 text-sm font-medium">Logros Este Mes</div>
                <svg class="w-8 h-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold">{{ $statsLogros['logros_mes'] }}</div>
            <div class="text-red-100 text-sm mt-1">Nuevos este mes</div>
        </div>
    </div>

    <!-- Logros Obtenidos -->
    @if($logrosObtenidos->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Logros Obtenidos</h2>
        
        @foreach($logrosPorCategoria as $categoria => $logros)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">{{ $categoria }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($logros as $logro)
                        @php
                            $logroInfo = collect($logrosDisponibles)->firstWhere('nombre', $logro->logro);
                        @endphp
                        <div class="border-2 border-green-500 rounded-xl p-4 bg-gradient-to-br from-green-50 to-green-100 hover:shadow-lg transition">
                            <div class="flex items-start justify-between mb-2">
                                <div class="text-4xl">{{ $logroInfo['icono'] ?? '🏆' }}</div>
                                <div class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    +{{ $logro->puntos }} pts
                                </div>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-1">{{ $logro->logro }}</h4>
                            <p class="text-sm text-gray-600 mb-2">
                                {{ $logroInfo['descripcion'] ?? '' }}
                            </p>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($logro->fecha_asignacion)->format('d/m/Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center mb-6">
        <div class="text-6xl mb-4">🎯</div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Aún no has obtenido logros</h3>
        <p class="text-gray-600">Completa sesiones y rutinas para desbloquear tus primeros logros</p>
    </div>
    @endif

    <!-- Todos los Logros Disponibles -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Todos los Logros Disponibles</h2>
        
        @php
            $logrosDisponiblesCollection = collect($logrosDisponibles);
            $logrosPorCategoriaDisponibles = $logrosDisponiblesCollection->groupBy('categoria');
        @endphp

        @foreach($logrosPorCategoriaDisponibles as $categoria => $logros)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">{{ $categoria }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($logros as $logro)
                        <div class="border-2 {{ $logro['obtenido'] ? 'border-green-500 bg-gradient-to-br from-green-50 to-green-100' : 'border-gray-300 bg-gray-50 opacity-75' }} rounded-xl p-4 hover:shadow-lg transition">
                            <div class="flex items-start justify-between mb-2">
                                <div class="text-4xl {{ $logro['obtenido'] ? '' : 'grayscale' }}">{{ $logro['icono'] }}</div>
                                <div class="bg-{{ $logro['obtenido'] ? 'green' : 'gray' }}-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    +{{ $logro['puntos'] }} pts
                                </div>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-1">{{ $logro['nombre'] }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $logro['descripcion'] }}</p>
                            @if($logro['obtenido'] && isset($logro['fecha_obtencion']))
                            <div class="flex items-center text-xs text-green-600 font-semibold">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Obtenido el {{ \Carbon\Carbon::parse($logro['fecha_obtencion'])->format('d/m/Y') }}
                            </div>
                            @else
                            <div class="flex items-center text-xs text-gray-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Aún no obtenido
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection

