<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return Inertia::render('Home');
});
Route::get('/home', function () {
    return Inertia::render('Home');
});

// Auth (todo se maneja desde AuthController)
Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegisterForm')->name('register.show');
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/logout', 'logout')->name('logout');
});

// Admin (todo se maneja desde AdminController)
Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->controller(AdminController::class)
    ->group(function () {
        Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
        Route::post('/users', 'storeUser')->name('admin.users.store');
        Route::put('/users/{id}', 'updateUser')->name('admin.users.update');
        Route::delete('/users/{id}', 'deleteUser')->name('admin.users.delete');
    });