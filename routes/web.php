<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IncidenciasController;
use App\Http\Middleware\AdminMiddleware;


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
    Route::get('/prueba', 'testing')->name('admin.prueba');
    Route::get('/usuariosg', 'usersView');
    });
    
    Route::apiResource('incidencias', IncidenciasController::class);
    Route::middleware('auth')->prefix('reportes')->group(function () {
        Route::get('/gestion-reportes', [IncidenciasController::class, 'showIncidencias'])->name('showIncidencias');
    });

