@extends('layouts.app')

@section('title', 'Ejecutar Rutina')

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
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $rutina->nombre }}</h1>
                @if($rutina->tipoRutina)
                    <p class="text-gray-600">Tipo: {{ $rutina->tipoRutina->nombre }}</p>
                @endif
            </div>
            <a href="{{ route('rutinas.show', $rutina->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Volver
            </a>
        </div>
    </div>

    <!-- Barra de Progreso General -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Progreso de la Sesión</h2>
            <span class="text-2xl font-bold text-blue-600">{{ number_format($progresoSesion, 0) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progresoSesion }}%"></div>
        </div>
        <div class="flex justify-between text-sm text-gray-600">
            <span>{{ $ejerciciosCompletados }} de {{ $totalEjercicios }} ejercicios completados</span>
            @if($progreso)
                <span>Progreso total: {{ number_format($progreso->porcentaje_completado, 0) }}%</span>
            @endif
        </div>
    </div>

    <!-- Botón para abrir modal de completar ejercicios -->
    @php
        $ejerciciosPendientes = $rutina->ejercicios->where('estado', 'pendiente');
    @endphp
    
    @if($ejerciciosPendientes->count() > 0)
        <div class="mb-6 flex justify-center">
            <button 
                onclick="abrirModalCompletar()" 
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Completar Ejercicios</span>
            </button>
        </div>
    @endif

    <!-- Lista de Ejercicios -->
    <div class="space-y-4">
        @foreach($rutina->ejercicios as $index => $ejercicio)
            <div class="bg-white rounded-xl shadow-sm p-6 {{ $ejercicio->estado === 'completado' ? 'border-2 border-green-500' : ($ejercicio->estado === 'en_progreso' ? 'border-2 border-yellow-500' : '') }}" id="ejercicio-{{ $ejercicio->detalle_id }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <!-- Estado del ejercicio -->
                            @if($ejercicio->estado === 'completado')
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-500 line-through">
                                        {{ $ejercicio->nombre }}
                                    </h3>
                                    <span class="text-xs text-green-600 font-medium">✓ Completado</span>
                                </div>
                            @elseif($ejercicio->estado === 'en_progreso')
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $ejercicio->nombre }}
                                    </h3>
                                    <span class="text-xs text-yellow-600 font-medium">⏳ En Progreso</span>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-gray-600 font-semibold">{{ $ejercicio->pivot->orden }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $ejercicio->nombre }}
                                    </h3>
                                    <span class="text-xs text-gray-500">Pendiente</span>
                                </div>
                            @endif

                            @if($ejercicio->categoria)
                                <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">
                                    {{ $ejercicio->categoria->nombre }}
                                </span>
                            @endif
                        </div>

                        <!-- Detalles del ejercicio -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                            @if($ejercicio->pivot->series)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Series</div>
                                    <div class="text-lg font-semibold text-gray-800">{{ $ejercicio->pivot->series }}</div>
                                </div>
                            @endif
                            @if($ejercicio->pivot->repeticiones)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Repeticiones</div>
                                    <div class="text-lg font-semibold text-gray-800">{{ $ejercicio->pivot->repeticiones }}</div>
                                </div>
                            @endif
                            @if($ejercicio->pivot->peso)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Peso</div>
                                    <div class="text-lg font-semibold text-gray-800">{{ $ejercicio->pivot->peso }}</div>
                                </div>
                            @endif
                            @if($ejercicio->pivot->descanso_segundos)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Descanso</div>
                                    <div class="text-lg font-semibold text-gray-800">{{ $ejercicio->pivot->descanso_segundos }}s</div>
                                </div>
                            @endif
                        </div>

                        @if($ejercicio->pivot->notas)
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                <p class="text-sm text-gray-700">{{ $ejercicio->pivot->notas }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Botón Finalizar -->
    @if($ejerciciosCompletados == $totalEjercicios && $progreso && $progreso->estado !== 'completada')
        <div class="mt-6 bg-green-50 border-2 border-green-500 rounded-xl p-6 text-center">
            <h3 class="text-xl font-semibold text-green-800 mb-4">¡Felicidades! Has completado todos los ejercicios</h3>
            <form action="{{ route('rutinas.finish', $rutina->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-lg">
                    Finalizar Rutina
                </button>
            </form>
        </div>
    @endif

    <!-- Modal para completar ejercicios -->
    <div id="modal-completar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-gray-800">Completar Ejercicios</h3>
                    <button onclick="cerrarModalCompletar()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <form id="form-completar-ejercicios" onsubmit="completarEjercicios(event)">
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Selecciona los ejercicios que has completado:</p>
                    <div class="space-y-3">
                        @foreach($ejerciciosPendientes as $ejercicio)
                            <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="ejercicios[]" 
                                    value="{{ $ejercicio->detalle_id }}"
                                    class="mt-1 w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                            #{{ $ejercicio->pivot->orden }}
                                        </span>
                                        <h4 class="font-semibold text-gray-800">{{ $ejercicio->nombre }}</h4>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        @if($ejercicio->pivot->series)
                                            <span>Series: {{ $ejercicio->pivot->series }}</span>
                                        @endif
                                        @if($ejercicio->pivot->repeticiones)
                                            <span class="ml-3">Reps: {{ $ejercicio->pivot->repeticiones }}</span>
                                        @endif
                                        @if($ejercicio->pivot->peso)
                                            <span class="ml-3">Peso: {{ $ejercicio->pivot->peso }}</span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalCompletar()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Completar Seleccionados
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function abrirModalCompletar() {
            const modal = document.getElementById('modal-completar');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function cerrarModalCompletar() {
            const modal = document.getElementById('modal-completar');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function completarEjercicios(event) {
            event.preventDefault();
            
            const form = event.target;
            const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
            const ejerciciosIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

            if (ejerciciosIds.length === 0) {
                alert('Por favor selecciona al menos un ejercicio');
                return;
            }

            const btnSubmit = form.querySelector('button[type="submit"]');
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Completando...';

            fetch(`{{ route('rutinas.complete-exercise', $rutina->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ejercicios: ejerciciosIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cerrarModalCompletar();
                    location.reload();
                } else {
                    alert(data.error || 'Error al completar los ejercicios');
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Completar Seleccionados';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al completar los ejercicios');
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Completar Seleccionados';
            });
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Cerrar modal al hacer clic fuera
            const modal = document.getElementById('modal-completar');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        cerrarModalCompletar();
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
