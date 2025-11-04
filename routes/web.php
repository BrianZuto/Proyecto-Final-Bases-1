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

// Ruta protegida de ejemplo (dashboard)
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $planActivo = $user->planActivo();
    
    // Aquí luego vendrán los datos de la BD
    $stats = [
        'completados' => 4,
        'total' => 5,
        'calorias' => 2840,
        'peso_levantado' => 8.5,
        'tiempo_total' => 12.5,
    ];
    
    $progreso_reciente = [
        ['ejercicio' => 'Press de Banca', 'anterior' => '80kg x 8', 'actual' => '82.5kg x 8', 'mejora' => '+2.5kg'],
    ];
    
    $proximos_entrenamientos = [
        ['fecha' => 'Hoy - 18:00', 'rutina' => 'Push - Pecho y Tríceps'],
    ];
    
    return view('dashboard', compact('stats', 'progreso_reciente', 'proximos_entrenamientos', 'planActivo'));
})->middleware('auth')->name('dashboard');

// Rutas de Clientes (solo Administrador)
Route::middleware(['auth', 'role:Administrador'])->prefix('clientes')->name('clientes.')->group(function () {
    Route::get('/', [App\Http\Controllers\ClienteController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ClienteController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ClienteController::class, 'store'])->name('store');
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
});

// Rutas de Categorías (API) - Solo Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('categorias')->name('categorias.')->group(function () {
    Route::post('/', [App\Http\Controllers\CategoriaController::class, 'store'])->name('store');
    Route::get('/', [App\Http\Controllers\CategoriaController::class, 'index'])->name('index');
    Route::put('/{categoria}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('update');
    Route::delete('/{categoria}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('destroy');
});

// Rutas de Rutinas
Route::middleware('auth')->prefix('rutinas')->name('rutinas.')->group(function () {
    Route::get('/', [App\Http\Controllers\RutinaController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\RutinaController::class, 'create'])->middleware('role:Administrador')->name('create');
    Route::post('/', [App\Http\Controllers\RutinaController::class, 'store'])->middleware('role:Administrador')->name('store');
    Route::get('/{rutina}/edit', [App\Http\Controllers\RutinaController::class, 'edit'])->middleware('role:Administrador')->name('edit');
    Route::put('/{rutina}', [App\Http\Controllers\RutinaController::class, 'update'])->middleware('role:Administrador')->name('update');
    Route::delete('/{rutina}', [App\Http\Controllers\RutinaController::class, 'destroy'])->middleware('role:Administrador')->name('destroy');
    Route::get('/{rutina}', [App\Http\Controllers\RutinaController::class, 'show'])->name('show');
});

// Rutas de Tipos de Rutinas (API) - Solo Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('tipo-rutinas')->name('tipo-rutinas.')->group(function () {
    Route::post('/', [App\Http\Controllers\TipoRutinaController::class, 'store'])->name('store');
    Route::get('/', [App\Http\Controllers\TipoRutinaController::class, 'index'])->name('index');
    Route::put('/{tipoRutina}', [App\Http\Controllers\TipoRutinaController::class, 'update'])->name('update');
    Route::delete('/{tipoRutina}', [App\Http\Controllers\TipoRutinaController::class, 'destroy'])->name('destroy');
});
