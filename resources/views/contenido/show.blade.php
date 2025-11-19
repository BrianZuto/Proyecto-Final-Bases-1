@extends('layouts.app')

@section('title', $contenidoData->titulo)

@section('content')
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $contenidoData->titulo }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold uppercase">
                        {{ ucfirst($contenidoData->tipo) }}
                    </span>
                    @if($contenidoData->categoria)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                        {{ $contenidoData->categoria }}
                    </span>
                    @endif
                    @if($contenidoData->fecha_publicacion)
                    <span>{{ \Carbon\Carbon::parse($contenidoData->fecha_publicacion)->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-4">
                @auth
                    @if(Auth::user()->isAdministrador() || (Auth::user()->id == $contenidoData->autor_id))
                        <a href="{{ route('contenido.edit', $contenidoData->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Editar
                        </a>
                    @endif
                @endauth
                <a href="{{ route('contenido.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Volver
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenido Principal -->
        <div class="lg:col-span-2 space-y-6">
            @if($contenidoData->imagen_url)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <img src="{{ $contenidoData->imagen_url }}" alt="{{ $contenidoData->titulo }}" class="w-full h-auto">
            </div>
            @endif

            @if($contenidoData->video_url)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Video</h3>
                <div class="aspect-video bg-gray-100 rounded-lg">
                    <iframe src="{{ $contenidoData->video_url }}" class="w-full h-full rounded-lg" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            @endif

            @if($contenidoData->descripcion)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Descripción</h3>
                <p class="text-gray-700">{{ $contenidoData->descripcion }}</p>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($contenidoData->contenido)) !!}
                </div>
            </div>

            @if($contenidoData->archivo_url)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recurso Descargable</h3>
                <a href="{{ $contenidoData->archivo_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar Recurso
                </a>
            </div>
            @endif

            @if($contenidoData->tags)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $contenidoData->tags) as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ trim($tag) }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información</h3>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-gray-600">Autor</div>
                        <div class="font-semibold text-gray-800">
                            {{ $contenidoData->primer_nombre && $contenidoData->primer_apellido ? $contenidoData->primer_nombre . ' ' . $contenidoData->primer_apellido : ($contenidoData->autor_nombre ?? 'N/A') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Vistas</div>
                        <div class="font-semibold text-gray-800">{{ $contenidoData->vistas }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Likes</div>
                        <div class="font-semibold text-gray-800">{{ $contenidoData->likes }}</div>
                    </div>
                    @if($contenidoData->fecha_publicacion)
                    <div>
                        <div class="text-sm text-gray-600">Fecha de Publicación</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($contenidoData->fecha_publicacion)->format('d/m/Y') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones</h3>
                <div class="space-y-2">
                    <button onclick="likeContenido({{ $contenidoData->id }})" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>Me Gusta (<span id="likes-count">{{ $contenidoData->likes }}</span>)</span>
                    </button>
                </div>
            </div>

            <!-- Contenido Relacionado -->
            @if($contenidoRelacionado->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Contenido Relacionado</h3>
                <div class="space-y-3">
                    @foreach($contenidoRelacionado as $relacionado)
                        <a href="{{ route('contenido.show', $relacionado->id) }}" class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $relacionado->titulo }}</h4>
                            <p class="text-sm text-gray-600">{{ ucfirst($relacionado->tipo) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function likeContenido(id) {
            fetch(`/contenido/${id}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('likes-count').textContent = data.likes;
            });
        }
    </script>
    @endpush
@endsection

