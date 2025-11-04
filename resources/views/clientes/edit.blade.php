@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Editar Cliente</h1>
        <p class="text-gray-600">Actualiza los datos del cliente</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('clientes.update', $cliente) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Primer Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primer Nombre *</label>
                    <input type="text" name="primer_nombre" value="{{ old('primer_nombre', $cliente->primer_nombre) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('primer_nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Segundo Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" value="{{ old('segundo_nombre', $cliente->segundo_nombre) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Primer Apellido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primer Apellido *</label>
                    <input type="text" name="primer_apellido" value="{{ old('primer_apellido', $cliente->primer_apellido) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('primer_apellido')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Segundo Apellido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" value="{{ old('segundo_apellido', $cliente->segundo_apellido) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Nombre de Usuario -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de Usuario *</label>
                    <input type="text" name="nombre_usuario" value="{{ old('nombre_usuario', $cliente->nombre_usuario) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('nombre_usuario')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfonos -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfonos *</label>
                    <input type="text" name="telefonos" value="{{ old('telefonos', $cliente->telefonos) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('telefonos')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dirección -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección *</label>
                    <textarea name="direccion" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('direccion', $cliente->direccion) }}</textarea>
                    @error('direccion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                    <select name="rol" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Deportista" {{ old('rol', $cliente->rol) === 'Deportista' ? 'selected' : '' }}>Deportista</option>
                        <option value="Coach" {{ old('rol', $cliente->rol) === 'Coach' ? 'selected' : '' }}>Coach</option>
                        <option value="Administrador" {{ old('rol', $cliente->rol) === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('rol')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plan (solo para Deportistas) -->
                @if($cliente->rol === 'Deportista')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asignar Plan</label>
                    <select name="plan_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sin plan</option>
                        @foreach($planes as $plan)
                            <option value="{{ $plan->id }}" {{ ($planActivo && $planActivo->id == $plan->id) ? 'selected' : '' }}>
                                {{ $plan->nombre }} - ${{ number_format($plan->precio, 0, ',', '.') }} COP ({{ $plan->duracion_dias }} días)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Solo los Deportistas pueden tener planes asignados</p>
                    @if($planActivo)
                        <p class="text-xs text-blue-600 mt-1">
                            Plan actual: {{ $planActivo->nombre }} - Vence: {{ \Carbon\Carbon::parse($planActivo->pivot->fecha_fin)->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
                @endif

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Deja en blanco si no deseas cambiar la contraseña</p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('clientes.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>

    <script>
        // Mostrar/ocultar campo de plan según el rol
        document.addEventListener('DOMContentLoaded', function() {
            const rolSelect = document.querySelector('select[name="rol"]');
            const planField = document.querySelector('select[name="plan_id"]')?.closest('div');

            function togglePlanField() {
                if (rolSelect && planField) {
                    if (rolSelect.value === 'Deportista') {
                        planField.style.display = 'block';
                    } else {
                        planField.style.display = 'none';
                        document.querySelector('select[name="plan_id"]').value = '';
                    }
                }
            }

            // Ejecutar al cargar
            togglePlanField();

            // Ejecutar al cambiar el rol
            if (rolSelect) {
                rolSelect.addEventListener('change', togglePlanField);
            }
        });
    </script>
@endsection

