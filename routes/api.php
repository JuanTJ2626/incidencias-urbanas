<?php
use App\Http\Controllers\IncidenciasController;
use Illuminate\Support\Facades\Route;

Route::post('/incidenciasCreate', [IncidenciasController::class, 'store']); 