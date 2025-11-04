@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
    <!-- Título -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Editar Perfil</h1>
        <p class="text-gray-600">Completa tu información personal</p>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulario de Edición -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Primer Nombre -->
                <div>
                    <label for="primer_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Primer Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="primer_nombre" 
                           name="primer_nombre" 
                           value="{{ old('primer_nombre', $user->primer_nombre) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('primer_nombre') border-red-500 @enderror"
                           required>
                    @error('primer_nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Segundo Nombre -->
                <div>
                    <label for="segundo_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Segundo Nombre
                    </label>
                    <input type="text" 
                           id="segundo_nombre" 
                           name="segundo_nombre" 
                           value="{{ old('segundo_nombre', $user->segundo_nombre) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Primer Apellido -->
                <div>
                    <label for="primer_apellido" class="block text-sm font-medium text-gray-700 mb-2">
                        Primer Apellido <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="primer_apellido" 
                           name="primer_apellido" 
                           value="{{ old('primer_apellido', $user->primer_apellido) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('primer_apellido') border-red-500 @enderror"
                           required>
                    @error('primer_apellido')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Segundo Apellido -->
                <div>
                    <label for="segundo_apellido" class="block text-sm font-medium text-gray-700 mb-2">
                        Segundo Apellido
                    </label>
                    <input type="text" 
                           id="segundo_apellido" 
                           name="segundo_apellido" 
                           value="{{ old('segundo_apellido', $user->segundo_apellido) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Nombre de Usuario -->
                <div>
                    <label for="nombre_usuario" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de Usuario <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nombre_usuario" 
                           name="nombre_usuario" 
                           value="{{ old('nombre_usuario', $user->nombre_usuario) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nombre_usuario') border-red-500 @enderror"
                           required>
                    @error('nombre_usuario')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfonos -->
                <div>
                    <label for="telefonos" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfonos <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="telefonos" 
                           name="telefonos" 
                           value="{{ old('telefonos', $user->telefonos) }}"
                           placeholder="Ej: 3001234567, 6012345678"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telefonos') border-red-500 @enderror"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Puedes separar múltiples teléfonos con comas</p>
                    @error('telefonos')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dirección -->
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección <span class="text-red-500">*</span>
                    </label>
                    <textarea id="direccion" 
                              name="direccion" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('direccion') border-red-500 @enderror"
                              required>{{ old('direccion', $user->direccion) }}</textarea>
                    @error('direccion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nueva Contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Deja en blanco si no deseas cambiar la contraseña</p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('profile') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
@endsection

