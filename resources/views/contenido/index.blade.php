@extends('layouts.app')

@section('title', 'Contenido')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Contenido</h1>
                <p class="text-gray-600">Artículos, videos y recursos educativos</p>
            </div>
        </div>
        @auth
            @if(Auth::user()->isAdministrador() || Auth::user()->isEntrenador())
                <a href="{{ route('contenido.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    + Crear Contenido
                </a>
            @endif
        @endauth
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Estadísticas (Solo Administradores) -->
    @if(isset($stats) && $stats)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Total</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Publicados</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['publicados'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Borradores</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['borradores'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-sm text-gray-600 mb-1">Tipos</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['por_tipo']->count() }}</div>
        </div>
    </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('contenido.index') }}" class="flex flex-wrap items-center gap-4">
            <!-- Búsqueda -->
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="buscar" 
                           value="{{ request('buscar') }}"
                           placeholder="Buscar contenido..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Filtro Tipo -->
            <div class="min-w-[150px]">
                <select name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los tipos</option>
                    <option value="articulo" {{ request('tipo') == 'articulo' ? 'selected' : '' }}>Artículos</option>
                    <option value="video" {{ request('tipo') == 'video' ? 'selected' : '' }}>Videos</option>
                    <option value="infografia" {{ request('tipo') == 'infografia' ? 'selected' : '' }}>Infografías</option>
                    <option value="recurso" {{ request('tipo') == 'recurso' ? 'selected' : '' }}>Recursos</option>
                    <option value="consejo" {{ request('tipo') == 'consejo' ? 'selected' : '' }}>Consejos</option>
                </select>
            </div>

            <!-- Filtro Categoría -->
            @if(count($categorias) > 0)
            <div class="min-w-[150px]">
                <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria }}" {{ request('categoria') == $categoria ? 'selected' : '' }}>
                            {{ $categoria }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Botón Filtrar -->
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Filtrar
            </button>

            @if(request()->filled('buscar') || request()->filled('tipo') || request()->filled('categoria'))
                <a href="{{ route('contenido.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Lista de Contenido -->
    @if($contenido->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($contenido as $item)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    @if($item->imagen_url)
                    <div class="h-48 bg-gray-200 overflow-hidden">
                        <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        @php
                            $iconos = [
                                'articulo' => '📄',
                                'video' => '🎥',
                                'infografia' => '📊',
                                'recurso' => '📎',
                                'consejo' => '💡',
                            ];
                        @endphp
                        <span class="text-6xl">{{ $iconos[$item->tipo] ?? '📄' }}</span>
                    </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold uppercase">
                                {{ ucfirst($item->tipo) }}
                            </span>
                            @if(!$item->publicado)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                Borrador
                            </span>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">{{ $item->titulo }}</h3>
                        
                        @if($item->descripcion)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $item->descripcion }}</p>
                        @endif

                        @if($item->categoria)
                        <div class="mb-4">
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $item->categoria }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ $item->vistas }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    {{ $item->likes }}
                                </span>
                            </div>
                            @if($item->autor_nombre)
                            <span class="text-xs">{{ $item->primer_nombre && $item->primer_apellido ? $item->primer_nombre . ' ' . $item->primer_apellido : $item->autor_nombre }}</span>
                            @endif
                        </div>

                        <a href="{{ route('contenido.show', $item->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Ver Más
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $contenido->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="text-6xl mb-4">📚</div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay contenido disponible</h3>
            <p class="text-gray-600 mb-4">Aún no se ha publicado contenido</p>
            @auth
                @if(Auth::user()->isAdministrador() || Auth::user()->isEntrenador())
                    <a href="{{ route('contenido.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Crear Primer Contenido
                    </a>
                @endif
            @endauth
        </div>
    @endif
@endsection

