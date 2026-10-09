<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('dashboard');
Route::view('/equipos', 'equipos.index')->name('equipos.index');
Route::view('/mantenimientos', 'mantenimientos.index')->name('mantenimientos.index');
