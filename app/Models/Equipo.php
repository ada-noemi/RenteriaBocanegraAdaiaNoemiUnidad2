<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'codigo', 'tipo', 'ubicacion', 'estado', 'fecha_registro'])]
class Equipo extends Model
{
    protected $table = 'equipos';

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    protected function casts(): array
    {
        return ['fecha_registro' => 'date'];
    }
}
