@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <!-- Título y Subtítulo -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Mi Perfil</h1>
        <p class="text-gray-600">Gestiona tu perfil y preferencias</p>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tarjeta de Perfil -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 relative">
        <!-- Botón Editar -->
        <a href="{{ route('profile.edit') }}" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-blue-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>

        <div class="flex items-start space-x-6">
            <!-- Avatar -->
            <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-3xl font-bold text-white">{{ $iniciales }}</span>
            </div>

            <!-- Información del Usuario -->
            <div class="flex-1">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        {{ $user->primer_nombre ?? '' }} 
                        {{ $user->segundo_nombre ?? '' }} 
                        {{ $user->primer_apellido ?? '' }} 
                        {{ $user->segundo_apellido ?? '' }}
                        @if(empty($user->primer_nombre) && empty($user->primer_apellido))
                            {{ $user->name }}
                        @endif
                    </h2>
                    <p class="text-gray-500 text-sm">Miembro desde {{ $user->created_at->format('F Y') }}</p>
                </div>

                <!-- Información Completa en Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- ID -->
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        <span class="font-medium">ID:</span>
                        <span>{{ $user->id }}</span>
                    </div>

                    <!-- Rol -->
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="font-medium">Rol:</span>
                        @if($user->rol === 'Administrador')
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">{{ $user->rol }}</span>
                        @elseif($user->rol === 'Coach')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">{{ $user->rol }}</span>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">{{ $user->rol ?? 'Deportista' }}</span>
                        @endif
                    </div>

                    <!-- Primer Nombre -->
                    @if($user->primer_nombre)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <span class="font-medium">Primer Nombre:</span>
                        <span>{{ $user->primer_nombre }}</span>
                    </div>
                    @endif

                    <!-- Segundo Nombre -->
                    @if($user->segundo_nombre)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <span class="font-medium">Segundo Nombre:</span>
                        <span>{{ $user->segundo_nombre }}</span>
                    </div>
                    @endif

                    <!-- Primer Apellido -->
                    @if($user->primer_apellido)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <span class="font-medium">Primer Apellido:</span>
                        <span>{{ $user->primer_apellido }}</span>
                    </div>
                    @endif

                    <!-- Segundo Apellido -->
                    @if($user->segundo_apellido)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <span class="font-medium">Segundo Apellido:</span>
                        <span>{{ $user->segundo_apellido }}</span>
                    </div>
                    @endif

                    <!-- Nombre de Usuario -->
                    @if($user->nombre_usuario)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium">Usuario:</span>
                        <span>{{ $user->nombre_usuario }}</span>
                    </div>
                    @endif

                    <!-- Email -->
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Email:</span>
                        <span>{{ $user->email }}</span>
                    </div>

                    <!-- Teléfonos -->
                    @if($user->telefonos)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="font-medium">Teléfonos:</span>
                        <span>{{ $user->telefonos }}</span>
                    </div>
                    @endif

                    <!-- Dirección -->
                    @if($user->direccion)
                    <div class="flex items-start space-x-2 text-gray-600 col-span-1 md:col-span-2">
                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <span class="font-medium">Dirección:</span>
                            <p class="text-gray-700">{{ $user->direccion }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2">
                    @if($planActivo)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">{{ $planActivo->nombre }}</span>
                    @else
                        <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">Sin Plan</span>
                    @endif
                    @if($user->rol === 'Coach' || $user->rol === 'Administrador')
                        <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">Coach Verificado</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Sesiones Totales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm text-gray-600 mb-2">Sesiones Totales</h3>
            <div class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($stats['sesiones_totales']) }}</div>
            <p class="text-sm text-green-600">+{{ $stats['sesiones_mes'] }} este mes</p>
        </div>

        <!-- Racha Actual -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm text-gray-600 mb-2">Racha Actual</h3>
            <div class="text-3xl font-bold text-gray-800 mb-1">{{ $stats['racha_actual'] }} días</div>
            <p class="text-sm text-blue-600">¡Sigue así!</p>
        </div>

        <!-- Logros -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm text-gray-600 mb-2">Logros</h3>
            <div class="text-3xl font-bold text-gray-800 mb-1">{{ $stats['logros'] }}</div>
            <p class="text-sm text-gray-500">Top {{ $stats['logros_percentil'] }}%</p>
        </div>
    </div>

    <!-- Objetivos del Mes -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Objetivos del Mes</h3>
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
        </div>

        <div class="space-y-4">
            @foreach($objetivos as $objetivo)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-700">{{ $objetivo['titulo'] }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $objetivo['actual'] }}/{{ $objetivo['meta'] }}{!! $objetivo['tipo'] === 'kg' ? 'kg' : '' !!}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ ($objetivo['actual'] / $objetivo['meta']) * 100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Plan Asignado -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                @if($planActivo)
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $planActivo->nombre }}</h3>
                    <p class="text-sm text-gray-600">
                        Vence: {{ \Carbon\Carbon::parse($planActivo->pivot->fecha_fin)->format('d') }} de {{ \Carbon\Carbon::parse($planActivo->pivot->fecha_fin)->format('F') }}, {{ \Carbon\Carbon::parse($planActivo->pivot->fecha_fin)->format('Y') }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Precio: ${{ number_format($planActivo->precio, 0, ',', '.') }} COP - Duración: {{ $planActivo->duracion_dias }} días
                    </p>
                @else
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Sin Plan Asignado</h3>
                    <p class="text-sm text-gray-600">No tienes un plan activo asignado</p>
                @endif
            </div>
            @if($planActivo)
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Activo</span>
            @else
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">Sin Plan</span>
            @endif
        </div>
        @if($planActivo)
            <div class="mb-4">
                <p class="text-sm text-gray-700 mb-2">{{ $planActivo->descripcion }}</p>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Inicio: {{ \Carbon\Carbon::parse($planActivo->pivot->fecha_inicio)->format('d/m/Y') }}</span>
                </div>
            </div>
        @endif
        <div class="flex space-x-4">
            @if($planActivo)
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Ver Detalles
                </button>
            @endif
            <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                Contactar Soporte
            </button>
        </div>
    </div>
@endsection
