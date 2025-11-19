@extends('layouts.app')

@section('title', 'Mis Sesiones')

@section('content')
    <!-- Header con Título y Botón -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Mis Sesiones</h1>
                <p class="text-gray-600">Historial de tus entrenamientos</p>
            </div>
        </div>
        <a href="{{ route('sesiones.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            + Nueva Sesión
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Total Sesiones</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total_sesiones'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Este Mes</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['sesiones_mes'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Calorías Totales</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['calorias_totales']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Tiempo Total</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['tiempo_total']) }} min</div>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('sesiones.index') }}" class="flex flex-wrap items-center gap-4">
            <!-- Filtro Fecha Desde -->
            <div class="min-w-[150px]">
                <label class="block text-sm text-gray-600 mb-1">Desde</label>
                <input type="date" 
                       name="fecha_desde" 
                       value="{{ request('fecha_desde') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filtro Fecha Hasta -->
            <div class="min-w-[150px]">
                <label class="block text-sm text-gray-600 mb-1">Hasta</label>
                <input type="date" 
                       name="fecha_hasta" 
                       value="{{ request('fecha_hasta') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filtro Estado -->
            <div class="min-w-[150px]">
                <label class="block text-sm text-gray-600 mb-1">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="en_progreso" {{ request('estado') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <!-- Filtro Rutina -->
            @if($rutinasUsuario->count() > 0)
            <div class="min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">Rutina</label>
                <select name="rutina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas</option>
                    @foreach($rutinasUsuario as $rutina)
                        <option value="{{ $rutina->id }}" {{ request('rutina_id') == $rutina->id ? 'selected' : '' }}>
                            {{ $rutina->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Botón Filtrar -->
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Filtrar
                </button>
            </div>

            @if(request()->filled('fecha_desde') || request()->filled('fecha_hasta') || request()->filled('estado') || request()->filled('rutina_id'))
                <div class="flex items-end">
                    <a href="{{ route('sesiones.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Limpiar
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Lista de Sesiones -->
    @if($sesiones->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($sesiones as $sesion)
                <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-3">
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $sesion->rutina_nombre ?? 'Sesión Libre' }}
                                </h3>
                                @php
                                    $estadoColors = [
                                        'completada' => 'bg-green-100 text-green-700',
                                        'en_progreso' => 'bg-yellow-100 text-yellow-700',
                                        'cancelada' => 'bg-red-100 text-red-700',
                                    ];
                                    $estadoLabels = [
                                        'completada' => 'Completada',
                                        'en_progreso' => 'En Progreso',
                                        'cancelada' => 'Cancelada',
                                    ];
                                @endphp
                                <span class="px-3 py-1 {{ $estadoColors[$sesion->estado] ?? 'bg-gray-100 text-gray-700' }} rounded-full text-sm font-medium">
                                    {{ $estadoLabels[$sesion->estado] ?? ucfirst($sesion->estado) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <div class="text-sm text-gray-600">Fecha</div>
                                    <div class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($sesion->fecha_sesion)->format('d/m/Y') }}
                                    </div>
                                </div>
                                @if($sesion->hora_inicio)
                                <div>
                                    <div class="text-sm text-gray-600">Hora</div>
                                    <div class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') }}
                                        @if($sesion->hora_fin)
                                            - {{ \Carbon\Carbon::parse($sesion->hora_fin)->format('H:i') }}
                                        @endif
                                    </div>
                                </div>
                                @endif
                                @if($sesion->duracion_minutos)
                                <div>
                                    <div class="text-sm text-gray-600">Duración</div>
                                    <div class="font-semibold text-gray-800">{{ $sesion->duracion_minutos }} min</div>
                                </div>
                                @endif
                                @if($sesion->calorias_quemadas)
                                <div>
                                    <div class="text-sm text-gray-600">Calorías</div>
                                    <div class="font-semibold text-gray-800">{{ number_format($sesion->calorias_quemadas) }}</div>
                                </div>
                                @endif
                            </div>

                            @if($sesion->total_ejercicios > 0)
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-semibold">{{ $sesion->total_ejercicios }}</span> ejercicios completados
                            </div>
                            @endif

                            @if($sesion->notas)
                            <div class="text-sm text-gray-600 italic">
                                "{{ strlen($sesion->notas) > 100 ? substr($sesion->notas, 0, 100) . '...' : $sesion->notas }}"
                            </div>
                            @endif
                        </div>

                        <div class="flex flex-col space-y-2 ml-4">
                            <a href="{{ route('sesiones.show', $sesion->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-center">
                                Ver Detalle
                            </a>
                            <a href="{{ route('sesiones.edit', $sesion->id) }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                                Editar
                            </a>
                            <form action="{{ route('sesiones.destroy', $sesion->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta sesión?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $sesiones->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay sesiones registradas</h3>
            <p class="text-gray-600 mb-4">Comienza a registrar tus entrenamientos</p>
            <a href="{{ route('sesiones.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Crear Primera Sesión
            </a>
        </div>
    @endif
@endsection

