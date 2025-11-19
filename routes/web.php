<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirigir la página principal al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rutas de perfil
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->middleware('auth')->name('profile');
Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::get('/profile/status', [App\Http\Controllers\ProfileController::class, 'getProfileStatus'])->middleware('auth')->name('profile.status');

// Ruta del Dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Rutas de Clientes (solo Administrador o Entrenador según RF20)
Route::middleware(['auth', 'role:Administrador,Entrenador'])->prefix('clientes')->name('clientes.')->group(function () {
    Route::get('/', [App\Http\Controllers\ClienteController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ClienteController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ClienteController::class, 'store'])->name('store');
    Route::post('/{userId}/assign-plan', [App\Http\Controllers\ClienteController::class, 'assignPlan'])->name('assign-plan');
    Route::get('/{cliente}', [App\Http\Controllers\ClienteController::class, 'show'])->name('show');
    Route::get('/{cliente}/edit', [App\Http\Controllers\ClienteController::class, 'edit'])->name('edit');
    Route::put('/{cliente}', [App\Http\Controllers\ClienteController::class, 'update'])->name('update');
    Route::delete('/{cliente}', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('destroy');
});

// Rutas de Planes (solo Administrador)
Route::middleware(['auth', 'role:Administrador'])->prefix('planes')->name('planes.')->group(function () {
    Route::get('/', [App\Http\Controllers\PlanController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\PlanController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\PlanController::class, 'store'])->name('store');
    Route::get('/{plan}/edit', [App\Http\Controllers\PlanController::class, 'edit'])->name('edit');
    Route::put('/{plan}', [App\Http\Controllers\PlanController::class, 'update'])->name('update');
    Route::delete('/{plan}', [App\Http\Controllers\PlanController::class, 'destroy'])->name('destroy');
});

// Rutas de Ejercicios
Route::middleware('auth')->prefix('ejercicios')->name('ejercicios.')->group(function () {
    Route::get('/', [App\Http\Controllers\EjercicioController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\EjercicioController::class, 'create'])->middleware('role:Administrador')->name('create');
    Route::post('/', [App\Http\Controllers\EjercicioController::class, 'store'])->middleware('role:Administrador')->name('store');
    Route::get('/{ejercicio}/edit', [App\Http\Controllers\EjercicioController::class, 'edit'])->middleware('role:Administrador')->name('edit');
    Route::put('/{ejercicio}', [App\Http\Controllers\EjercicioController::class, 'update'])->middleware('role:Administrador')->name('update');
    Route::delete('/{ejercicio}', [App\Http\Controllers\EjercicioController::class, 'destroy'])->middleware('role:Administrador')->name('destroy');
    Route::get('/{ejercicio}', [App\Http\Controllers\EjercicioController::class, 'show'])->name('show');
    
    // Ruta para completar ejercicio individual
    Route::post('/{ejercicio}/complete', [App\Http\Controllers\EjercicioController::class, 'complete'])->name('complete');
});

// Rutas de Categorías (API) - Solo Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('categorias')->name('categorias.')->group(function () {
    Route::post('/', [App\Http\Controllers\CategoriaController::class, 'store'])->name('store');
    Route::get('/', [App\Http\Controllers\CategoriaController::class, 'index'])->name('index');
    Route::put('/{categoria}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('update');
    Route::delete('/{categoria}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('destroy');
});

// Rutas de Rutinas (RF13: Entrenadores pueden crear y gestionar rutinas)
Route::middleware('auth')->prefix('rutinas')->name('rutinas.')->group(function () {
    Route::get('/', [App\Http\Controllers\RutinaController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\RutinaController::class, 'create'])->middleware('role:Administrador,Entrenador')->name('create');
    Route::post('/', [App\Http\Controllers\RutinaController::class, 'store'])->middleware('role:Administrador,Entrenador')->name('store');
    Route::get('/{rutina}/edit', [App\Http\Controllers\RutinaController::class, 'edit'])->middleware('role:Administrador,Entrenador')->name('edit');
    Route::put('/{rutina}', [App\Http\Controllers\RutinaController::class, 'update'])->middleware('role:Administrador,Entrenador')->name('update');
    Route::delete('/{rutina}', [App\Http\Controllers\RutinaController::class, 'destroy'])->middleware('role:Administrador,Entrenador')->name('destroy');
    Route::get('/{rutina}', [App\Http\Controllers\RutinaController::class, 'show'])->name('show');
    
    // Rutas para ejecutar rutina (solo para usuarios no administradores)
    Route::post('/{rutina}/start', [App\Http\Controllers\RutinaController::class, 'start'])->name('start');
    Route::get('/{rutina}/execute', [App\Http\Controllers\RutinaController::class, 'execute'])->name('execute');
    Route::post('/{rutina}/complete-exercise', [App\Http\Controllers\RutinaController::class, 'completeExercise'])->name('complete-exercise');
    Route::post('/{rutina}/finish', [App\Http\Controllers\RutinaController::class, 'finish'])->name('finish');
});

// Rutas de Tipos de Rutinas (API) - Solo Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('tipo-rutinas')->name('tipo-rutinas.')->group(function () {
    Route::post('/', [App\Http\Controllers\TipoRutinaController::class, 'store'])->name('store');
    Route::get('/', [App\Http\Controllers\TipoRutinaController::class, 'index'])->name('index');
    Route::put('/{tipoRutina}', [App\Http\Controllers\TipoRutinaController::class, 'update'])->name('update');
    Route::delete('/{tipoRutina}', [App\Http\Controllers\TipoRutinaController::class, 'destroy'])->name('destroy');
});

// Rutas de Sesiones
Route::middleware('auth')->prefix('sesiones')->name('sesiones.')->group(function () {
    Route::get('/', [App\Http\Controllers\SesionController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\SesionController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\SesionController::class, 'store'])->name('store');
    Route::get('/{sesion}', [App\Http\Controllers\SesionController::class, 'show'])->name('show');
    Route::get('/{sesion}/edit', [App\Http\Controllers\SesionController::class, 'edit'])->name('edit');
    Route::put('/{sesion}', [App\Http\Controllers\SesionController::class, 'update'])->name('update');
    Route::post('/{sesion}/complete', [App\Http\Controllers\SesionController::class, 'complete'])->name('complete');
    Route::delete('/{sesion}', [App\Http\Controllers\SesionController::class, 'destroy'])->name('destroy');
});

// Rutas de Progreso y Logros
Route::middleware('auth')->prefix('progreso')->name('progreso.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProgresoController::class, 'index'])->name('index');
    Route::get('/logros', [App\Http\Controllers\ProgresoController::class, 'logros'])->name('logros');
});

// Rutas de Contenido
Route::middleware('auth')->prefix('contenido')->name('contenido.')->group(function () {
    Route::get('/', [App\Http\Controllers\ContenidoController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ContenidoController::class, 'create'])->middleware('role:Administrador,Entrenador')->name('create');
    Route::post('/', [App\Http\Controllers\ContenidoController::class, 'store'])->middleware('role:Administrador,Entrenador')->name('store');
    Route::get('/{contenido}', [App\Http\Controllers\ContenidoController::class, 'show'])->name('show');
    Route::get('/{contenido}/edit', [App\Http\Controllers\ContenidoController::class, 'edit'])->middleware('role:Administrador,Entrenador')->name('edit');
    Route::put('/{contenido}', [App\Http\Controllers\ContenidoController::class, 'update'])->middleware('role:Administrador,Entrenador')->name('update');
    Route::delete('/{contenido}', [App\Http\Controllers\ContenidoController::class, 'destroy'])->middleware('role:Administrador,Entrenador')->name('destroy');
    Route::post('/{contenido}/like', [App\Http\Controllers\ContenidoController::class, 'like'])->name('like');
});
