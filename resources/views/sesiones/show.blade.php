@extends('layouts.app')

@section('title', 'Detalle de Sesión')

@section('content')
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

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    {{ $sesionData->rutina_nombre ?? 'Sesión Libre' }}
                </h1>
                <p class="text-gray-600">
                    {{ \Carbon\Carbon::parse($sesionData->fecha_sesion)->format('d/m/Y') }}
                    @if($sesionData->hora_inicio)
                        - {{ \Carbon\Carbon::parse($sesionData->hora_inicio)->format('H:i') }}
                    @endif
                </p>
            </div>
            <div class="flex space-x-4">
                @if($sesionData->estado === 'en_progreso')
                <form action="{{ route('sesiones.complete', $sesionData->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de completar esta sesión?');" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Completar Sesión</span>
                    </button>
                </form>
                @endif
                <a href="{{ route('sesiones.edit', $sesionData->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Editar
                </a>
                <a href="{{ route('sesiones.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Volver
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tarjeta Principal -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-start space-x-4 mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-4">
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
                            <span class="px-3 py-1 {{ $estadoColors[$sesionData->estado] ?? 'bg-gray-100 text-gray-700' }} rounded-full text-sm font-medium">
                                {{ $estadoLabels[$sesionData->estado] ?? ucfirst($sesionData->estado) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            @if($sesionData->duracion_minutos)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $sesionData->duracion_minutos }}</div>
                                    <div class="text-sm text-gray-500">Minutos</div>
                                </div>
                            @endif
                            @if($sesionData->calorias_quemadas)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ number_format($sesionData->calorias_quemadas) }}</div>
                                    <div class="text-sm text-gray-500">Calorías</div>
                                </div>
                            @endif
                            @if($sesionData->ejercicios_completados)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $sesionData->ejercicios_completados }}</div>
                                    <div class="text-sm text-gray-500">Ejercicios</div>
                                </div>
                            @endif
                        </div>

                        @if($sesionData->notas)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Notas</h3>
                            <p class="text-gray-600">{{ $sesionData->notas }}</p>
                        </div>
                        @endif

                        @if($sesionData->rendimiento)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Rendimiento</h3>
                            <p class="text-gray-600">{{ $sesionData->rendimiento }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ejercicios Completados -->
            @if($ejercicios->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Ejercicios Completados</h2>
                <div class="space-y-4">
                    @foreach($ejercicios as $ejercicio)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start space-x-4">
                                @if($ejercicio->imagen_url)
                                <img src="{{ $ejercicio->imagen_url }}" alt="{{ $ejercicio->ejercicio_nombre }}" class="w-20 h-20 object-cover rounded-lg">
                                @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">{{ $ejercicio->ejercicio_nombre }}</h3>
                                    @if($ejercicio->categoria_nombre)
                                    <p class="text-sm text-gray-600 mb-2">{{ $ejercicio->categoria_nombre }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                        @if($ejercicio->series_completadas)
                                        <span><strong>Series:</strong> {{ $ejercicio->series_completadas }}</span>
                                        @endif
                                        @if($ejercicio->repeticiones_completadas)
                                        <span><strong>Repeticiones:</strong> {{ $ejercicio->repeticiones_completadas }}</span>
                                        @endif
                                        @if($ejercicio->peso_usado)
                                        <span><strong>Peso:</strong> {{ $ejercicio->peso_usado }}</span>
                                        @endif
                                        @if($ejercicio->tiempo_segundos)
                                        <span><strong>Tiempo:</strong> {{ $ejercicio->tiempo_segundos }}s</span>
                                        @endif
                                    </div>
                                    @if($ejercicio->notas)
                                    <p class="text-sm text-gray-500 italic mt-2">{{ $ejercicio->notas }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información de la Rutina -->
            @if($sesionData->rutina_id)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Rutina</h3>
                <p class="text-gray-800 font-medium mb-2">{{ $sesionData->rutina_nombre }}</p>
                @if($sesionData->rutina_descripcion)
                <p class="text-sm text-gray-600">{{ strlen($sesionData->rutina_descripcion) > 100 ? substr($sesionData->rutina_descripcion, 0, 100) . '...' : $sesionData->rutina_descripcion }}</p>
                @endif
                @if($sesionData->rutina_id)
                <a href="{{ route('rutinas.show', $sesionData->rutina_id) }}" class="mt-4 inline-block text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Ver Rutina →
                </a>
                @endif
            </div>
            @endif

            <!-- Horario -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Horario</h3>
                <div class="space-y-2">
                    <div>
                        <div class="text-sm text-gray-600">Fecha</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($sesionData->fecha_sesion)->format('d/m/Y') }}
                        </div>
                    </div>
                    @if($sesionData->hora_inicio)
                    <div>
                        <div class="text-sm text-gray-600">Hora Inicio</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($sesionData->hora_inicio)->format('H:i') }}
                        </div>
                    </div>
                    @endif
                    @if($sesionData->hora_fin)
                    <div>
                        <div class="text-sm text-gray-600">Hora Fin</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($sesionData->hora_fin)->format('H:i') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones</h3>
                <div class="space-y-2">
                    @if($sesionData->estado === 'en_progreso')
                    <form action="{{ route('sesiones.complete', $sesionData->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de completar esta sesión?');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Completar Sesión</span>
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('sesiones.edit', $sesionData->id) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-center">
                        Editar Sesión
                    </a>
                    <form action="{{ route('sesiones.destroy', $sesionData->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta sesión?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                            Eliminar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

