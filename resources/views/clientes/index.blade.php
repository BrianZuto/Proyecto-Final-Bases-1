@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestión de Clientes</h1>
            <p class="text-gray-600">Administra los usuarios del sistema</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            + Nuevo Cliente
        </a>
    </div>

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

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('clientes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Filtro por Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar por Nombre</label>
                <input type="text" 
                       name="nombre" 
                       value="{{ request('nombre') }}"
                       placeholder="Nombre, apellido o usuario..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filtro por Rol -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Rol</label>
                <select name="rol" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los roles</option>
                    <option value="Administrador" {{ request('rol') === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Coach" {{ request('rol') === 'Coach' ? 'selected' : '' }}>Coach</option>
                    <option value="Deportista" {{ request('rol') === 'Deportista' ? 'selected' : '' }}>Deportista</option>
                </select>
            </div>

            <!-- Filtro por Plan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Plan</label>
                <select name="plan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los planes</option>
                    @foreach($planes as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Filtrar
                </button>
                @if(request()->anyFilled(['nombre', 'rol', 'plan_id']))
                    <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Clientes -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfonos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clientes as $cliente)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cliente->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $cliente->primer_nombre ?? '' }} {{ $cliente->primer_apellido ?? '' }}
                            @if(empty($cliente->primer_nombre))
                                {{ $cliente->name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cliente->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cliente->nombre_usuario ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cliente->rol === 'Administrador')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">{{ $cliente->rol }}</span>
                            @elseif($cliente->rol === 'Coach')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ $cliente->rol }}</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">{{ $cliente->rol ?? 'Deportista' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cliente->rol === 'Deportista' && $cliente->plan_activo)
                                <div class="flex flex-col">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 mb-1">
                                        {{ $cliente->plan_activo->nombre }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Vence: {{ \Carbon\Carbon::parse($cliente->plan_activo->pivot->fecha_fin)->format('d/m/Y') }}
                                    </span>
                                </div>
                            @elseif($cliente->rol === 'Deportista')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Sin plan</span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cliente->telefonos ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay clientes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $clientes->links() }}
        </div>
    </div>
@endsection
