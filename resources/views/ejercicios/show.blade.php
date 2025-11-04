@extends('layouts.app')

@section('title', 'Detalle del Ejercicio')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $ejercicio->nombre }}</h1>
                @if($ejercicio->categoria)
                    <p class="text-gray-600">Categoría: {{ $ejercicio->categoria->nombre }}</p>
                @endif
            </div>
            <div class="flex space-x-4">
                @auth
                    @if(Auth::user()->isAdministrador())
                        <a href="{{ route('ejercicios.edit', $ejercicio->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Editar
                        </a>
                    @endif
                @endauth
                <a href="{{ route('ejercicios.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($ejercicio->grupo_muscular)
                                <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">
                                    {{ $ejercicio->grupo_muscular }}
                                </span>
                            @endif
                            @if($ejercicio->dificultad)
                                <span class="px-3 py-1 {{ $ejercicio->color_dificultad }} rounded-full text-sm font-medium">
                                    {{ $ejercicio->dificultad }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            @if($ejercicio->duracion_minutos)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $ejercicio->duracion_minutos }}</div>
                                    <div class="text-sm text-gray-500">Minutos</div>
                                </div>
                            @endif
                            @if($ejercicio->calorias_estimadas)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $ejercicio->calorias_estimadas }}</div>
                                    <div class="text-sm text-gray-500">Calorías</div>
                                </div>
                            @endif
                            @if($ejercicio->calificacion)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ number_format($ejercicio->calificacion, 1) }}</div>
                                    <div class="text-sm text-gray-500">Calificación</div>
                                </div>
                            @endif
                        </div>

                        @if($ejercicio->equipo)
                            <div class="mb-4">
                                <span class="text-sm text-gray-600">Equipo:</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium ml-2">
                                    {{ $ejercicio->equipo }}
                                </span>
                            </div>
                        @endif

                        @if($ejercicio->descripcion)
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Descripción</h3>
                                <p class="text-gray-700">{{ $ejercicio->descripcion }}</p>
                            </div>
                        @endif

                        @if($ejercicio->instrucciones)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Instrucciones</h3>
                                <p class="text-gray-700 whitespace-pre-line">{{ $ejercicio->instrucciones }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex space-x-4 mt-6">
                    <button class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                        </svg>
                        <span>Iniciar Ejercicio</span>
                    </button>
                </div>
            </div>

            <!-- Video o Imagen -->
            @if($ejercicio->video_url || $ejercicio->imagen_url)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Medios</h3>
                    @if($ejercicio->video_url)
                        <div class="aspect-video bg-gray-100 rounded-lg mb-4">
                            <iframe src="{{ $ejercicio->video_url }}" class="w-full h-full rounded-lg" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @endif
                    @if($ejercicio->imagen_url)
                        <img src="{{ $ejercicio->imagen_url }}" alt="{{ $ejercicio->nombre }}" class="w-full rounded-lg">
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información Adicional -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información</h3>
                <div class="space-y-3">
                    @if($ejercicio->categoria)
                        <div>
                            <span class="text-sm text-gray-600">Categoría:</span>
                            <p class="font-medium text-gray-800">{{ $ejercicio->categoria->nombre }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-600">Dificultad:</span>
                        <p class="font-medium text-gray-800">{{ $ejercicio->dificultad }}</p>
                    </div>
                    @if($ejercicio->grupo_muscular)
                        <div>
                            <span class="text-sm text-gray-600">Grupo Muscular:</span>
                            <p class="font-medium text-gray-800">{{ $ejercicio->grupo_muscular }}</p>
                        </div>
                    @endif
                    @if($ejercicio->equipo)
                        <div>
                            <span class="text-sm text-gray-600">Equipo:</span>
                            <p class="font-medium text-gray-800">{{ $ejercicio->equipo }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

