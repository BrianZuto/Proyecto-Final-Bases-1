@extends('layouts.app')

@section('title', 'Editar Rutina')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Editar Rutina</h1>
        <p class="text-gray-600">Actualiza los datos de la rutina</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('rutinas.update', $rutina->id) }}" method="POST" id="rutinaForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Rutina *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $rutina->nombre) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $rutina->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Rutina -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Rutina *</label>
                    <select name="tipo_rutina_id" id="tipo_rutina_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un tipo</option>
                        @foreach($tipoRutinas as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_rutina_id', $rutina->tipo_rutina_id) == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_rutina_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nivel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nivel *</label>
                    <select name="nivel" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Principiante" {{ old('nivel', $rutina->nivel) == 'Principiante' ? 'selected' : '' }}>Principiante</option>
                        <option value="Intermedio" {{ old('nivel', $rutina->nivel) == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="Avanzado" {{ old('nivel', $rutina->nivel) == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                    @error('nivel')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL Imagen -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL de Imagen</label>
                    <input type="url" name="imagen_url" value="{{ old('imagen_url', $rutina->imagen_url) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('imagen_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ejercicios -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700">Ejercicios *</label>
                    <button type="button" onclick="agregarEjercicio()" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        + Agregar Ejercicio
                    </button>
                </div>
                <div id="ejerciciosContainer" class="space-y-4">
                    <!-- Los ejercicios se agregarán aquí dinámicamente -->
                </div>
                @error('ejercicios')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Asignar Planes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Asignar a Planes</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-4">
                    @foreach($planes as $plan)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="planes[]" value="{{ $plan->id }}" {{ in_array($plan->id, old('planes', $planesAsignados ?? [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $plan->nombre }}</span>
                        </label>
                    @endforeach
                </div>
                @error('planes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Activo -->
            <div class="mb-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', $rutina->activo) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Rutina activa</span>
                </label>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('rutinas.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Actualizar Rutina
                </button>
            </div>
        </form>
    </div>

    <script>
        let ejercicioIndex = 0;
        const ejercicios = @json($ejercicios);
        const ejerciciosAsignados = @json($ejerciciosAsignados);

        function agregarEjercicio(ejercicioId = null, datos = {}) {
            const container = document.getElementById('ejerciciosContainer');
            const ejercicioDiv = document.createElement('div');
            ejercicioDiv.className = 'border border-gray-300 rounded-lg p-4 ejercicio-item';
            ejercicioDiv.innerHTML = `
                <div class="flex items-start justify-between mb-4">
                    <h4 class="font-semibold text-gray-800">Ejercicio ${ejercicioIndex + 1}</h4>
                    <button type="button" onclick="eliminarEjercicio(this)" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ejercicio *</label>
                        <select name="ejercicios[${ejercicioIndex}][ejercicio_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccione un ejercicio</option>
                            ${ejercicios.map(e => `<option value="${e.id}" ${ejercicioId == e.id ? 'selected' : ''}>${e.nombre}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Orden *</label>
                        <input type="number" name="ejercicios[${ejercicioIndex}][orden]" value="${datos.orden || ejercicioIndex + 1}" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Series</label>
                        <input type="number" name="ejercicios[${ejercicioIndex}][series]" value="${datos.series || ''}" min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Repeticiones</label>
                        <input type="text" name="ejercicios[${ejercicioIndex}][repeticiones]" value="${datos.repeticiones || ''}" placeholder="Ej: 10-12"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Peso</label>
                        <input type="text" name="ejercicios[${ejercicioIndex}][peso]" value="${datos.peso || ''}" placeholder="Ej: 50kg"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descanso (segundos)</label>
                        <input type="number" name="ejercicios[${ejercicioIndex}][descanso_segundos]" value="${datos.descanso_segundos || ''}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                        <textarea name="ejercicios[${ejercicioIndex}][notas]" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">${datos.notas || ''}</textarea>
                    </div>
                </div>
            `;
            container.appendChild(ejercicioDiv);
            ejercicioIndex++;
        }

        function eliminarEjercicio(button) {
            button.closest('.ejercicio-item').remove();
            actualizarOrdenes();
        }

        function actualizarOrdenes() {
            const items = document.querySelectorAll('.ejercicio-item');
            items.forEach((item, index) => {
                const ordenInput = item.querySelector('input[name*="[orden]"]');
                if (ordenInput) {
                    ordenInput.value = index + 1;
                }
                const titulo = item.querySelector('h4');
                if (titulo) {
                    titulo.textContent = `Ejercicio ${index + 1}`;
                }
            });
        }

        // Cargar ejercicios existentes al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($ejerciciosAsignados) && count($ejerciciosAsignados) > 0)
                @foreach($ejerciciosAsignados as $ejercicioId => $datos)
                    agregarEjercicio({{ $ejercicioId }}, @json($datos));
                @endforeach
            @else
                agregarEjercicio();
            @endif
        });
    </script>
@endsection

