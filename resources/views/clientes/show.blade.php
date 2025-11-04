@extends('layouts.app')

@section('title', 'Ver Cliente')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Detalles del Cliente</h1>
                <p class="text-gray-600">Información completa del usuario</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('clientes.edit', $cliente) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Editar
                </a>
                <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Información -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-500">ID</label>
                <p class="text-gray-900 font-semibold">{{ $cliente->id }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Rol</label>
                <p class="mt-1">
                    @if($cliente->rol === 'Administrador')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">{{ $cliente->rol }}</span>
                    @elseif($cliente->rol === 'Coach')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ $cliente->rol }}</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">{{ $cliente->rol ?? 'Deportista' }}</span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Primer Nombre</label>
                <p class="text-gray-900">{{ $cliente->primer_nombre ?? '-' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Segundo Nombre</label>
                <p class="text-gray-900">{{ $cliente->segundo_nombre ?? '-' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Primer Apellido</label>
                <p class="text-gray-900">{{ $cliente->primer_apellido ?? '-' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Segundo Apellido</label>
                <p class="text-gray-900">{{ $cliente->segundo_apellido ?? '-' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Nombre de Usuario</label>
                <p class="text-gray-900">{{ $cliente->nombre_usuario ?? '-' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Email</label>
                <p class="text-gray-900">{{ $cliente->email }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Teléfonos</label>
                <p class="text-gray-900">{{ $cliente->telefonos ?? '-' }}</p>
            </div>
            
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-500">Dirección</label>
                <p class="text-gray-900">{{ $cliente->direccion ?? '-' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Fecha de Registro</label>
                <p class="text-gray-900">{{ $cliente->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Última Actualización</label>
                <p class="text-gray-900">{{ $cliente->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
@endsection

