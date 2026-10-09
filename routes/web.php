<?php

use App\Http\Controllers\EquipoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('dashboard');
Route::resource('equipos', EquipoController::class)
    ->except('show')
    ->parameters(['equipos' => 'equipo']);
Route::view('/mantenimientos', 'mantenimientos.index')->name('mantenimientos.index');
