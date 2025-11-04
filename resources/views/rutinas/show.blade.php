@extends('layouts.app')

@section('title', 'Detalle de la Rutina')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $rutina->nombre }}</h1>
                @if($rutina->tipoRutina)
                    <p class="text-gray-600">Tipo: {{ $rutina->tipoRutina->nombre }}</p>
                @endif
            </div>
            <div class="flex space-x-4">
                @auth
                    @if(Auth::user()->isAdministrador())
                        <a href="{{ route('rutinas.edit', $rutina->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Editar
                        </a>
                    @endif
                @endauth
                <a href="{{ route('rutinas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($rutina->tipoRutina)
                                <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">
                                    {{ $rutina->tipoRutina->nombre }}
                                </span>
                            @endif
                            @if($rutina->nivel)
                                <span class="px-3 py-1 {{ $rutina->color_nivel }} rounded-full text-sm font-medium">
                                    {{ $rutina->nivel }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            @if($rutina->tiempo_estimado_minutos)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $rutina->tiempo_estimado_minutos }}</div>
                                    <div class="text-sm text-gray-500">Minutos</div>
                                </div>
                            @endif
                            @if($rutina->calorias_estimadas)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $rutina->calorias_estimadas }}</div>
                                    <div class="text-sm text-gray-500">Calorías</div>
                                </div>
                            @endif
                            @if($rutina->ejercicios->count() > 0)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $rutina->ejercicios->count() }}</div>
                                    <div class="text-sm text-gray-500">Ejercicios</div>
                                </div>
                            @endif
                        </div>

                        @if($rutina->descripcion)
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Descripción</h3>
                                <p class="text-gray-700">{{ $rutina->descripcion }}</p>
                            </div>
                        @endif

                        <!-- Barra de Progreso (solo para usuarios no administradores) -->
                        @auth
                            @if(!Auth::user()->isAdministrador() && $progreso)
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800">Progreso</h3>
                                        <span class="text-lg font-bold text-gray-800">{{ number_format($progreso->porcentaje_completado, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-blue-600 h-3 rounded-full transition-all" style="width: {{ $progreso->porcentaje_completado }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>Sesiones completadas: {{ $progreso->sesiones_completadas }}</span>
                                        @if($progreso->fecha_ultima_sesion)
                                            <span>Última sesión: {{ \Carbon\Carbon::parse($progreso->fecha_ultima_sesion)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endauth

                        <!-- Lista de Ejercicios -->
                        @if($rutina->ejercicios->count() > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ejercicios de la Rutina</h3>
                                <div class="space-y-3">
                                    @foreach($rutina->ejercicios as $ejercicio)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                            #{{ $ejercicio->pivot->orden }}
                                                        </span>
                                                        <h4 class="font-semibold text-gray-800">{{ $ejercicio->nombre }}</h4>
                                                        @if($ejercicio->categoria)
                                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">
                                                                {{ $ejercicio->categoria->nombre }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-600">
                                                        @if($ejercicio->pivot->series)
                                                            <div><span class="font-medium">Series:</span> {{ $ejercicio->pivot->series }}</div>
                                                        @endif
                                                        @if($ejercicio->pivot->repeticiones)
                                                            <div><span class="font-medium">Reps:</span> {{ $ejercicio->pivot->repeticiones }}</div>
                                                        @endif
                                                        @if($ejercicio->pivot->peso)
                                                            <div><span class="font-medium">Peso:</span> {{ $ejercicio->pivot->peso }}</div>
                                                        @endif
                                                        @if($ejercicio->pivot->descanso_segundos)
                                                            <div><span class="font-medium">Descanso:</span> {{ $ejercicio->pivot->descanso_segundos }}s</div>
                                                        @endif
                                                    </div>
                                                    @if($ejercicio->pivot->notas)
                                                        <p class="text-xs text-gray-500 mt-2">{{ $ejercicio->pivot->notas }}</p>
                                                    @endif
                                                </div>
                                                <a href="{{ route('ejercicios.show', $ejercicio->id) }}" class="ml-4 text-blue-600 hover:text-blue-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex space-x-4 mt-6">
                    <button class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                        </svg>
                        <span>Iniciar Rutina</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información Adicional -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información</h3>
                <div class="space-y-3">
                    @if($rutina->tipoRutina)
                        <div>
                            <span class="text-sm text-gray-600">Tipo de Rutina:</span>
                            <p class="font-medium text-gray-800">{{ $rutina->tipoRutina->nombre }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-600">Nivel:</span>
                        <p class="font-medium text-gray-800">{{ $rutina->nivel }}</p>
                    </div>
                    @if($rutina->tiempo_estimado_minutos)
                        <div>
                            <span class="text-sm text-gray-600">Tiempo Estimado:</span>
                            <p class="font-medium text-gray-800">{{ $rutina->tiempo_estimado_minutos }} minutos</p>
                        </div>
                    @endif
                    @if($rutina->calorias_estimadas)
                        <div>
                            <span class="text-sm text-gray-600">Calorías Estimadas:</span>
                            <p class="font-medium text-gray-800">{{ $rutina->calorias_estimadas }} cal</p>
                        </div>
                    @endif
                    @if($rutina->ejercicios->count() > 0)
                        <div>
                            <span class="text-sm text-gray-600">Total de Ejercicios:</span>
                            <p class="font-medium text-gray-800">{{ $rutina->ejercicios->count() }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

