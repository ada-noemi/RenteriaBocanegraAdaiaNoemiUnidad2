<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'codigo', 'tipo', 'ubicacion', 'estado', 'fecha_registro'])]
class Equipo extends Model
{
    protected $table = 'equipos';

    protected function casts(): array
    {
        return ['fecha_registro' => 'date'];
    }
}
