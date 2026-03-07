<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IncidenciasController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\WorkerMiddleware;


// Auth (todo se maneja desde AuthController)
Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'showLoginForm')->name('login');
    Route::get('/register', 'showRegisterForm')->name('register.show');
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->controller(AdminController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::patch('/users/{id}/toggle-activo', [AdminController::class, 'toggleActivo'])->name('admin.users.toggle-activo');
    Route::get('/prueba', 'testing')->name('admin.prueba');
    Route::get('/usuariosg', 'usersView');
    Route::get('/gestion-incidencias', 'gestionIncidencias')->name('admin.gestion.incidencias');
    Route::post('/incidencias', 'storeIncidencia')->name('admin.incidencias.store');
    Route::put('/incidencias/{id}', 'updateIncidencia')->name('admin.incidencias.update');
    Route::delete('/incidencias/{id}', 'destroyIncidencia')->name('admin.incidencias.destroy');
    Route::patch('/incidencias/{id}/estatus', 'cambiarEstatus')->name('admin.incidencias.estatus');
    Route::patch('/incidencias/{id}/asignar', 'asignarTrabajador')->name('admin.incidencias.asignar');
    Route::patch('/incidencias/{id}/revisar', 'revisarCierre')->name('admin.incidencias.revisar');
    Route::get('/monitoreo', 'monitoreo')->name('admin.monitoreo');
    Route::get('/mapa', 'mapaCalor')->name('admin.mapa');
    });
    
    Route::apiResource('incidencias', IncidenciasController::class);
    Route::middleware('auth')->prefix('reportes')->group(function () {
        Route::get('/gestion-reportes', [IncidenciasController::class, 'showIncidencias'])->name('showIncidencias');
    });

// Rutas para trabajadores (acceso restringido por rol)
Route::middleware(['auth', WorkerMiddleware::class])->prefix('trabajador')->controller(TrabajadorController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('trabajador.dashboard');
    Route::get('/tareas', 'tareas')->name('trabajador.tareas');
    Route::get('/reportes', 'reportes')->name('trabajador.reportes');
    Route::post('/incidencias/{id}/cerrar', 'cerrarOrden')->name('trabajador.cerrar');
});

