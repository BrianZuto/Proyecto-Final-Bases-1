@extends('layouts.app')

@section('title', 'Crear Contenido')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear Contenido</h1>
                <p class="text-gray-600">Comparte conocimiento y recursos con la comunidad</p>
            </div>
            <a href="{{ route('contenido.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Volver
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('contenido.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Título -->
                <div class="md:col-span-2">
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="titulo" 
                           id="titulo" 
                           value="{{ old('titulo') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo" id="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="articulo" {{ old('tipo') == 'articulo' ? 'selected' : '' }}>Artículo</option>
                        <option value="video" {{ old('tipo') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="infografia" {{ old('tipo') == 'infografia' ? 'selected' : '' }}>Infografía</option>
                        <option value="recurso" {{ old('tipo') == 'recurso' ? 'selected' : '' }}>Recurso</option>
                        <option value="consejo" {{ old('tipo') == 'consejo' ? 'selected' : '' }}>Consejo</option>
                    </select>
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categoría -->
                <div>
                    <label for="categoria" class="block text-sm font-medium text-gray-700 mb-2">
                        Categoría
                    </label>
                    <input type="text" 
                           name="categoria" 
                           id="categoria" 
                           value="{{ old('categoria') }}"
                           placeholder="Ej: Nutrición, Entrenamiento, Salud"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('categoria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea name="descripcion" 
                              id="descripcion" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contenido -->
                <div class="md:col-span-2">
                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenido <span class="text-red-500">*</span>
                    </label>
                    <textarea name="contenido" 
                              id="contenido" 
                              rows="10"
                              required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('contenido') }}</textarea>
                    @error('contenido')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URLs -->
                <div>
                    <label for="imagen_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de Imagen
                    </label>
                    <input type="url" 
                           name="imagen_url" 
                           id="imagen_url" 
                           value="{{ old('imagen_url') }}"
                           placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('imagen_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de Video
                    </label>
                    <input type="url" 
                           name="video_url" 
                           id="video_url" 
                           value="{{ old('video_url') }}"
                           placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('video_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="archivo_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de Archivo
                    </label>
                    <input type="url" 
                           name="archivo_url" 
                           id="archivo_url" 
                           value="{{ old('archivo_url') }}"
                           placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('archivo_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                        Tags (separados por comas)
                    </label>
                    <input type="text" 
                           name="tags" 
                           id="tags" 
                           value="{{ old('tags') }}"
                           placeholder="nutrición, fitness, salud"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publicación -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="publicado" 
                                   value="1"
                                   {{ old('publicado') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Publicar inmediatamente</span>
                        </label>
                    </div>
                    <div class="mt-4">
                        <label for="fecha_publicacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Publicación (opcional)
                        </label>
                        <input type="date" 
                               name="fecha_publicacion" 
                               id="fecha_publicacion" 
                               value="{{ old('fecha_publicacion') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('fecha_publicacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('contenido.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Crear Contenido
                </button>
            </div>
        </form>
    </div>
@endsection

