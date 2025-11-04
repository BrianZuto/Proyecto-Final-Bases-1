<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FitTracker') - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-blue-900 text-white flex flex-col transition-all duration-300 ease-in-out" style="width: 256px;">
            <!-- Logo y Título -->
            <div class="p-4 border-b border-blue-800 flex items-center justify-between">
                <div class="flex items-center space-x-3 sidebar-content">
                    <div class="w-8 h-8 bg-blue-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none"/>
                        </svg>
                    </div>
                    <div class="sidebar-text">
                        <h1 class="text-lg font-bold">FitTracker</h1>
                        <p class="text-xs text-blue-300">Pro</p>
                    </div>
                </div>
                <button id="toggleSidebar" class="p-1.5 hover:bg-blue-800 rounded-lg transition sidebar-content flex-shrink-0">
                    <svg id="toggleIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 overflow-hidden p-2">
                <!-- Sección Principal -->
                <div class="mb-3 sidebar-section">
                    <h2 class="text-xs font-semibold text-blue-400 uppercase tracking-wider mb-2 px-2 sidebar-text">PRINCIPAL</h2>
                    <ul class="space-y-0.5 sidebar-list">
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-blue-200 hover:bg-blue-800' }} transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="sidebar-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ejercicios.index') }}" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg {{ request()->routeIs('ejercicios.*') ? 'bg-blue-800 text-white' : 'text-blue-200 hover:bg-blue-800' }} transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 12L6 14M8 12L6 10M16 12L18 14M16 12L18 10"/>
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none"/>
                                </svg>
                                <span class="sidebar-text">Ejercicios</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rutinas.index') }}" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg {{ request()->routeIs('rutinas.*') ? 'bg-blue-800 text-white' : 'text-blue-200 hover:bg-blue-800' }} transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="sidebar-text">Rutinas</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="sidebar-text">Sesiones</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="sidebar-text">Progreso</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                <span class="sidebar-text">Logros</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Sección Gestión -->
                @auth
                @if(Auth::user()->isAdministrador())
                <div class="sidebar-section">
                    <h2 class="text-xs font-semibold text-blue-400 uppercase tracking-wider mb-2 px-2 sidebar-text">GESTIÓN</h2>
                    <ul class="space-y-0.5 sidebar-list">
                        <li>
                            <a href="{{ route('clientes.index') }}" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg {{ request()->routeIs('clientes.*') ? 'bg-blue-800 text-white' : 'text-blue-200 hover:bg-blue-800' }} transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="sidebar-text">Usuarios</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('planes.index') }}" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg {{ request()->routeIs('planes.*') ? 'bg-blue-800 text-white' : 'text-blue-200 hover:bg-blue-800' }} transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="sidebar-text">Planes</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="sidebar-text">Contenido</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="sidebar-text">Configuración</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @else
                <div class="sidebar-section">
                    <h2 class="text-xs font-semibold text-blue-400 uppercase tracking-wider mb-2 px-2 sidebar-text">GESTIÓN</h2>
                    <ul class="space-y-0.5 sidebar-list">
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="sidebar-text">Contenido</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition sidebar-link">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="sidebar-text">Configuración</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
                @endauth
            </nav>

            <!-- Footer del Sidebar -->
            <div class="p-2 border-t border-blue-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 px-2 py-1.5 rounded-lg text-blue-200 hover:bg-blue-800 transition w-full sidebar-link">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="sidebar-text">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Búsqueda -->
                    <div class="flex-1 max-w-xl">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                placeholder="Buscar ejercicios, rutinas..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            >
                        </div>
                    </div>

                    <!-- Iconos del Header -->
                    <div class="flex items-center space-x-4 ml-6">
                        <!-- Notificaciones -->
                        <div class="relative">
                            <button id="notificationButton" class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(!isset($isProfileComplete) || !$isProfileComplete)
                                    <span class="absolute top-1 right-1 block h-3 w-3 rounded-full bg-red-600 ring-2 ring-white"></span>
                                    <span class="absolute top-0 right-0 block h-5 w-5 text-xs text-white bg-red-600 rounded-full flex items-center justify-center">!</span>
                                @endif
                            </button>
                            
                            <!-- Dropdown de Notificaciones -->
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Notificaciones</h3>
                                    @if(!isset($isProfileComplete) || !$isProfileComplete)
                                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-3 rounded">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-yellow-800">Perfil incompleto</p>
                                                    <p class="text-sm text-yellow-700 mt-1">Debes completar tu perfil para continuar. Por favor, completa todos los datos requeridos.</p>
                                                    <a href="{{ route('profile.edit') }}" class="mt-2 inline-block text-sm font-medium text-yellow-800 hover:text-yellow-900">
                                                        Completar perfil →
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-600">No hay notificaciones pendientes</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Perfil -->
                        <a href="{{ route('profile') }}" class="p-2 text-gray-600 hover:text-blue-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </a>

                        <!-- Ayuda -->
                        <button class="p-2 text-gray-600 hover:text-blue-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Contenido -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Estado del sidebar (guardado en localStorage)
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggleSidebar');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const sidebarContent = document.querySelectorAll('.sidebar-content');
        
        function updateSidebar() {
            if (sidebarCollapsed) {
                sidebar.style.width = '80px';
                sidebar.classList.add('collapsed');
                sidebarTexts.forEach(el => el.style.display = 'none');
                sidebarLinks.forEach(link => {
                    link.style.justifyContent = 'center';
                });
                sidebarContent.forEach(content => {
                    content.style.justifyContent = 'center';
                });
                document.getElementById('toggleIcon').style.transform = 'rotate(180deg)';
            } else {
                sidebar.style.width = '256px';
                sidebar.classList.remove('collapsed');
                sidebarTexts.forEach(el => el.style.display = 'block');
                sidebarLinks.forEach(link => {
                    link.style.justifyContent = 'flex-start';
                });
                sidebarContent.forEach(content => {
                    content.style.justifyContent = 'flex-start';
                });
                document.getElementById('toggleIcon').style.transform = 'rotate(0deg)';
            }
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
        }
        
        toggleButton.addEventListener('click', () => {
            sidebarCollapsed = !sidebarCollapsed;
            updateSidebar();
        });
        
        // Inicializar estado
        updateSidebar();
        
        // Manejo del dropdown de notificaciones
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        if (notificationButton && notificationDropdown) {
            notificationButton.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });
            
            // Cerrar dropdown al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }
    </script>
    
    <style>
        .sidebar-text {
            transition: opacity 0.2s;
            white-space: nowrap;
        }
        
        #sidebar.collapsed .sidebar-text {
            display: none !important;
        }
        
        #sidebar.collapsed .sidebar-link {
            justify-content: center !important;
            padding: 0.75rem !important;
        }
        
        #sidebar.collapsed .sidebar-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        
        #sidebar.collapsed .sidebar-list li {
            margin: 0 !important;
        }
        
        #sidebar.collapsed .sidebar-section {
            margin-bottom: 1.5rem !important;
        }
        
        #sidebar.collapsed .sidebar-content {
            justify-content: center !important;
        }
        
        #toggleSidebar svg {
            transition: transform 0.3s;
        }
        
        /* Asegurar que no haya scroll */
        #sidebar {
            overflow: hidden !important;
        }
        
        #sidebar nav {
            overflow: hidden !important;
        }
        
        .sidebar-link {
            transition: all 0.2s;
        }
        
        /* Ajustar espaciado cuando está colapsado */
        #sidebar.collapsed nav {
            padding: 1rem 0.5rem !important;
        }
        
        /* Asegurar que los iconos se vean bien separados cuando está colapsado */
        #sidebar.collapsed .sidebar-list li a {
            min-height: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        
        /* Iconos más grandes cuando está colapsado */
        #sidebar.collapsed .sidebar-link svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }
        
        /* Logo del mismo tamaño cuando está colapsado */
        #sidebar.collapsed .sidebar-content .bg-blue-700 svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }
        
        #sidebar.collapsed .sidebar-content .bg-blue-700 {
            width: 2.5rem !important;
            height: 2.5rem !important;
        }
    </style>
</body>
</html>

