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
                            @php
                                $rolNombre = $cliente->rol_nombre ?? ($cliente->rol->nombre ?? ($cliente->rol ?? 'Deportista'));
                            @endphp
                            @if($rolNombre === 'Administrador')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Administrador</span>
                            @elseif($rolNombre === 'Entrenador' || $rolNombre === 'Coach')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Entrenador</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Deportista</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $rolNombre = $cliente->rol_nombre ?? ($cliente->rol->nombre ?? ($cliente->rol ?? 'Deportista'));
                            @endphp
                            @if($rolNombre === 'Deportista')
                                @if($cliente->plan_activo)
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                                {{ $cliente->plan_activo->nombre }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 font-medium">
                                            📅 Vence: {{ \Carbon\Carbon::parse($cliente->plan_activo->pivot->fecha_fin)->format('d/m/Y') }}
                                        </span>
                                        @auth
                                        @if(Auth::user()->isAdministrador())
                                        <form action="{{ route('clientes.assign-plan', $cliente->id) }}" method="POST" class="mt-1">
                                            @csrf
                                            <div class="relative">
                                                <select name="plan_id" onchange="this.form.submit()" class="appearance-none w-full text-xs bg-white border-2 border-blue-300 rounded-lg px-3 py-2 pr-8 font-medium text-gray-700 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm hover:shadow cursor-pointer">
                                                    <option value="">🔄 Cambiar plan...</option>
                                                    @foreach($planes as $plan)
                                                        <option value="{{ $plan->id }}" {{ $cliente->plan_activo && $cliente->plan_activo->id == $plan->id ? 'selected' : '' }}>
                                                            {{ $plan->nombre }}
                                                        </option>
                                                    @endforeach
                                                    <option value="0" class="text-red-600">❌ Quitar plan</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth
                                    </div>
                                @else
                                    <div class="flex flex-col space-y-2">
                                        <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-200 text-gray-700 shadow-sm">
                                            Sin plan
                                        </span>
                                        @auth
                                        @if(Auth::user()->isAdministrador())
                                        <form action="{{ route('clientes.assign-plan', $cliente->id) }}" method="POST" class="mt-1">
                                            @csrf
                                            <div class="relative">
                                                <select name="plan_id" onchange="this.form.submit()" class="appearance-none w-full text-xs bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-300 rounded-lg px-3 py-2 pr-8 font-medium text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 shadow-sm hover:shadow cursor-pointer">
                                                    <option value="">➕ Asignar plan...</option>
                                                    @foreach($planes as $plan)
                                                        <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth
                                    </div>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cliente->telefonos ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
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
