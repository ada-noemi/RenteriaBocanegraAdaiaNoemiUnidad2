<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['equipo_id', 'tipo', 'fecha_programada', 'descripcion', 'estado'])]
class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    protected function casts(): array
    {
        return ['fecha_programada' => 'date'];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }
}
