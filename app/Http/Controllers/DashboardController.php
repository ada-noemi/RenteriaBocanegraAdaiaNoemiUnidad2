<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'estadisticas' => [
                'Total de equipos' => Equipo::count(),
                'Equipos activos' => Equipo::where('estado', 'Activo')->count(),
                'Mantenimientos pendientes' => Mantenimiento::where('estado', 'Pendiente')->count(),
                'Mantenimientos finalizados' => Mantenimiento::where('estado', 'Finalizado')->count(),
            ],
            'mantenimientosRecientes' => Mantenimiento::with('equipo')
                ->latest('created_at')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
