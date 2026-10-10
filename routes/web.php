<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\MantenimientoController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('equipos', EquipoController::class)
    ->except('show')
    ->parameters(['equipos' => 'equipo']);
Route::resource('mantenimientos', MantenimientoController::class)
    ->except('show')
    ->parameters(['mantenimientos' => 'mantenimiento']);
