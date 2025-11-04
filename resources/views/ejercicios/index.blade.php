@extends('layouts.app')

@section('title', 'Catálogo de Ejercicios')

@section('content')
    <!-- Header con Título y Botón -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10"/>
                    <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Catálogo de Ejercicios</h1>
                <p class="text-gray-600">Descubre y domina nuevos ejercicios para tu entrenamiento</p>
            </div>
        </div>
        <div class="flex space-x-3">
            @auth
                @if(Auth::user()->isAdministrador())
                    <button onclick="openCreateCategoryModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        + Crear Categoría
                    </button>
                    <a href="{{ route('ejercicios.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        + Crear Ejercicio
                    </a>
                @endif
            @endauth
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(isset($message))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-6">
            {{ $message }}
        </div>
    @endif

    <!-- Barra de Búsqueda y Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('ejercicios.index') }}" class="flex flex-wrap items-center gap-4">
            <!-- Búsqueda -->
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           placeholder="Buscar ejercicios..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Filtro Categoría -->
            <div class="min-w-[150px]">
                <select name="categoria_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="Todos">Todos</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro Dificultad (Nivel) -->
            <div class="min-w-[150px]">
                <select name="dificultad" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="Todos">Todos</option>
                    <option value="Principiante" {{ request('dificultad') == 'Principiante' ? 'selected' : '' }}>Principiante</option>
                    <option value="Intermedio" {{ request('dificultad') == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                    <option value="Avanzado" {{ request('dificultad') == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                </select>
            </div>

            <!-- Botón Filtrar -->
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Filtrar
            </button>

            @if(request()->filled('buscar') || (request()->filled('categoria_id') && request('categoria_id') !== 'Todos') || (request()->filled('dificultad') && request('dificultad') !== 'Todos'))
                <a href="{{ route('ejercicios.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Limpiar
                </a>
            @endif
        </form>

        <!-- Contador y Más Filtros -->
        <div class="flex items-center justify-between mt-4">
            <p class="text-sm text-gray-600">{{ $ejercicios->count() > 0 ? $ejercicios->total() : 0 }} ejercicios encontrados</p>
            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Más Filtros</span>
            </button>
        </div>
    </div>

    <!-- Grid de Ejercicios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ejercicios as $ejercicio)
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <!-- Icono y Título -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10"/>
                                <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">{{ $ejercicio->nombre }}</h3>
                        </div>
                    </div>
                    @auth
                        @if(Auth::user()->isAdministrador())
                            <div class="flex space-x-1">
                                <a href="{{ route('ejercicios.edit', $ejercicio->id) }}" class="p-1 text-gray-400 hover:text-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($ejercicio->categoria)
                        <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">
                            {{ $ejercicio->categoria->nombre }}
                        </span>
                    @elseif($ejercicio->grupo_muscular)
                        <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">
                            {{ $ejercicio->grupo_muscular }}
                        </span>
                    @endif
                    @if($ejercicio->dificultad)
                        <span class="px-2 py-1 {{ $ejercicio->color_dificultad }} rounded-full text-xs font-medium">
                            {{ $ejercicio->dificultad }}
                        </span>
                    @endif
                </div>

                <!-- Detalles -->
                <div class="flex items-center space-x-4 mb-4 text-sm text-gray-600">
                    @if($ejercicio->duracion_minutos)
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $ejercicio->duracion_minutos }}min</span>
                        </div>
                    @endif
                    @if($ejercicio->calorias_estimadas)
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>{{ $ejercicio->calorias_estimadas }} cal</span>
                        </div>
                    @endif
                    @if($ejercicio->calificacion)
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-yellow-500 fill-current" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span>{{ number_format($ejercicio->calificacion, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Equipo -->
                @if($ejercicio->equipo)
                    <div class="mb-4">
                        <span class="text-xs text-gray-500">Equipo:</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium ml-2">
                            {{ $ejercicio->equipo }}
                        </span>
                    </div>
                @endif

                <!-- Botones -->
                <div class="flex space-x-2">
                    <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                        </svg>
                        <span>Iniciar</span>
                    </button>
                    <a href="{{ route('ejercicios.show', $ejercicio->id) }}" class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-center">
                        Ver Detalle
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500 text-lg">No se encontraron ejercicios</p>
                @auth
                    @if(Auth::user()->isAdministrador())
                        <a href="{{ route('ejercicios.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Crear Primer Ejercicio
                        </a>
                    @endif
                @endauth
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if(method_exists($ejercicios, 'hasPages') && $ejercicios->hasPages())
        <div class="mt-6">
            {{ $ejercicios->links() }}
        </div>
    @endif

    <!-- Modal para Crear Categoría -->
    @auth
        @if(Auth::user()->isAdministrador())
        <div id="createCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Crear Nueva Categoría</h3>
                    <button onclick="closeCreateCategoryModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form id="createCategoryForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Categoría *</label>
                            <input type="text" name="nombre" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea name="descripcion" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                            <input type="color" name="color" value="#3B82F6"
                                   class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeCreateCategoryModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @endauth

    <script>
        @auth
            @if(Auth::user()->isAdministrador())
            function openCreateCategoryModal() {
                const modal = document.getElementById('createCategoryModal');
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }

            function closeCreateCategoryModal() {
                const modal = document.getElementById('createCategoryModal');
                const form = document.getElementById('createCategoryForm');
                if (modal) {
                    modal.classList.add('hidden');
                }
                if (form) {
                    form.reset();
                }
            }

            // Manejar envío del formulario de categoría
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('createCategoryForm');
                const modal = document.getElementById('createCategoryModal');

                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        try {
                            const formData = new FormData(this);
                            const response = await fetch('{{ route("categorias.store") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                    'Accept': 'application/json',
                                }
                            });

                            // Verificar si la respuesta es JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await response.text();
                                throw new Error('La respuesta no es JSON. Puede ser un error de autenticación o permisos.');
                            }

                            const data = await response.json();

                            if (data.success) {
                                alert('Categoría creada exitosamente');
                                closeCreateCategoryModal();
                                location.reload(); // Recargar para mostrar la nueva categoría
                            } else {
                                let errorMsg = data.message || 'No se pudo crear la categoría';
                                if (data.errors) {
                                    const errors = Object.values(data.errors).flat();
                                    errorMsg = errors.join(', ');
                                }
                                alert('Error: ' + errorMsg);
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error al crear la categoría: ' + error.message);
                        }
                    });
                }

                // Cerrar modal al hacer clic fuera
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeCreateCategoryModal();
                        }
                    });
                }
            });
            @endif
        @endauth
    </script>
@endsection

