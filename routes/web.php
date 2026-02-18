<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
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

// Admin (todo se maneja desde AdminController)
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
        Route::post('/users', 'storeUser')->name('admin.users.store');
        Route::put('/users/{id}', 'updateUser')->name('admin.users.update');
        Route::delete('/users/{id}', 'deleteUser')->name('admin.users.delete'); 
        Route::get('/prueba', 'testing')->name('admin.prueba');
    });

Route::apiResource('incidencias', IncidenciasController::class);