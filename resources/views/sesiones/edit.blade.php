@extends('layouts.app')

@section('title', 'Editar Sesión')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Editar Sesión</h1>
                <p class="text-gray-600">Modifica los datos de la sesión</p>
            </div>
            <a href="{{ route('sesiones.show', $sesionData->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Volver
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('sesiones.update', $sesionData->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rutina -->
                <div>
                    <label for="rutina_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Rutina (Opcional)
                    </label>
                    <select name="rutina_id" id="rutina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sesión Libre</option>
                        @foreach($rutinas as $rutina)
                            <option value="{{ $rutina->id }}" {{ old('rutina_id', $sesionData->rutina_id) == $rutina->id ? 'selected' : '' }}>
                                {{ $rutina->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('rutina_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha Sesión -->
                <div>
                    <label for="fecha_sesion" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Sesión <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="fecha_sesion" 
                           id="fecha_sesion" 
                           value="{{ old('fecha_sesion', $sesionData->fecha_sesion) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('fecha_sesion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hora Inicio -->
                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora Inicio (Opcional)
                    </label>
                    <input type="time" 
                           name="hora_inicio" 
                           id="hora_inicio" 
                           value="{{ old('hora_inicio', $sesionData->hora_inicio ? \Carbon\Carbon::parse($sesionData->hora_inicio)->format('H:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('hora_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hora Fin -->
                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora Fin (Opcional)
                    </label>
                    <input type="time" 
                           name="hora_fin" 
                           id="hora_fin" 
                           value="{{ old('hora_fin', $sesionData->hora_fin ? \Carbon\Carbon::parse($sesionData->hora_fin)->format('H:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('hora_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duración (Minutos) -->
                <div>
                    <label for="duracion_minutos" class="block text-sm font-medium text-gray-700 mb-2">
                        Duración (Minutos)
                    </label>
                    <input type="number" 
                           name="duracion_minutos" 
                           id="duracion_minutos" 
                           value="{{ old('duracion_minutos', $sesionData->duracion_minutos) }}"
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('duracion_minutos')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Calorías Quemadas -->
                <div>
                    <label for="calorias_quemadas" class="block text-sm font-medium text-gray-700 mb-2">
                        Calorías Quemadas
                    </label>
                    <input type="number" 
                           name="calorias_quemadas" 
                           id="calorias_quemadas" 
                           value="{{ old('calorias_quemadas', $sesionData->calorias_quemadas) }}"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('calorias_quemadas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ejercicios Completados -->
                <div>
                    <label for="ejercicios_completados" class="block text-sm font-medium text-gray-700 mb-2">
                        Ejercicios Completados
                    </label>
                    <input type="number" 
                           name="ejercicios_completados" 
                           id="ejercicios_completados" 
                           value="{{ old('ejercicios_completados', $sesionData->ejercicios_completados) }}"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('ejercicios_completados')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="estado" id="estado" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="en_progreso" {{ old('estado', $sesionData->estado) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completada" {{ old('estado', $sesionData->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ old('estado', $sesionData->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notas -->
            <div class="mt-6">
                <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas
                </label>
                <textarea name="notas" 
                          id="notas" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notas', $sesionData->notas) }}</textarea>
                @error('notas')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rendimiento -->
            <div class="mt-6">
                <label for="rendimiento" class="block text-sm font-medium text-gray-700 mb-2">
                    Rendimiento
                </label>
                <textarea name="rendimiento" 
                          id="rendimiento" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('rendimiento', $sesionData->rendimiento) }}</textarea>
                @error('rendimiento')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('sesiones.show', $sesionData->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Actualizar Sesión
                </button>
            </div>
        </form>
    </div>
@endsection

