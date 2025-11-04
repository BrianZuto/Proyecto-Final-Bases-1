@extends('layouts.app')

@section('title', 'Crear Ejercicio')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear Nuevo Ejercicio</h1>
        <p class="text-gray-600">Completa los datos del nuevo ejercicio</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('ejercicios.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Ejercicio *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categoría (Grupo Muscular) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría (Grupo Muscular) *</label>
                    <select name="categoria_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Seleccione el grupo muscular principal del ejercicio (Espalda, Brazo, Glúteos, etc.)</p>
                    @error('categoria_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grupo Muscular (campo adicional opcional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grupo Muscular Secundario</label>
                    <input type="text" name="grupo_muscular" value="{{ old('grupo_muscular') }}"
                           placeholder="Ej: Bíceps, Tríceps, Cuádriceps (opcional)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Opcional: especifique músculos secundarios trabajados</p>
                    @error('grupo_muscular')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dificultad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dificultad *</label>
                    <select name="dificultad" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Principiante" {{ old('dificultad') == 'Principiante' ? 'selected' : '' }}>Principiante</option>
                        <option value="Intermedio" {{ old('dificultad') == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="Avanzado" {{ old('dificultad') == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                    @error('dificultad')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duración -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                    <input type="number" name="duracion_minutos" value="{{ old('duracion_minutos') }}" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('duracion_minutos')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Calorías -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Calorías Estimadas</label>
                    <input type="number" name="calorias_estimadas" value="{{ old('calorias_estimadas') }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('calorias_estimadas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Calificación -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Calificación (0-5)</label>
                    <input type="number" name="calificacion" value="{{ old('calificacion', 0) }}" step="0.1" min="0" max="5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('calificacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Equipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipo *</label>
                    <input type="text" name="equipo" value="{{ old('equipo') }}" required
                           placeholder="Ej: Barra, Peso Corporal, Mancuernas..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('equipo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instrucciones -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instrucciones</label>
                    <textarea name="instrucciones" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('instrucciones') }}</textarea>
                    @error('instrucciones')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL Imagen -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL de Imagen</label>
                    <input type="url" name="imagen_url" value="{{ old('imagen_url') }}"
                           placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('imagen_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL Video -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL de Video</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}"
                           placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('video_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Asignar Planes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asignar a Planes</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-4">
                        @foreach($planes as $plan)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="planes[]" value="{{ $plan->id }}" {{ in_array($plan->id, old('planes', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700">{{ $plan->nombre }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selecciona los planes a los que tendrán acceso los usuarios para realizar este ejercicio</p>
                    @error('planes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Activo -->
                <div class="md:col-span-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Ejercicio activo</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('ejercicios.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Crear Ejercicio
                </button>
            </div>
        </form>
    </div>
@endsection

