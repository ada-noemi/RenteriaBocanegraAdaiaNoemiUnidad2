<?php

namespace App\Http\Controllers;

use App\Http\Requests\MantenimientoRequest;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MantenimientoController extends Controller
{
    public function index(): View
    {
        return view('mantenimientos.index', [
            'mantenimientos' => Mantenimiento::query()->with('equipo')->latest('id')->paginate(10),
            'equiposDisponibles' => Equipo::query()->orderBy('nombre')->get(['id', 'nombre', 'codigo']),
            'mantenimientoModal' => Mantenimiento::find(session('mantenimiento_modal_id')),
        ]);
    }

    public function create(): RedirectResponse
    {
        return to_route('mantenimientos.index')->with('mantenimiento_modal', 'mantenimiento-create');
    }

    public function store(MantenimientoRequest $request): RedirectResponse
    {
        Mantenimiento::create($request->validated());

        return to_route('mantenimientos.index')->with('success', 'Mantenimiento registrado correctamente.');
    }

    public function edit(Mantenimiento $mantenimiento): RedirectResponse
    {
        return to_route('mantenimientos.index')
            ->with('mantenimiento_modal', 'mantenimiento-edit-'.$mantenimiento->id)
            ->with('mantenimiento_modal_id', $mantenimiento->id);
    }

    public function update(MantenimientoRequest $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        $mantenimiento->update($request->validated());

        return to_route('mantenimientos.index')->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy(Mantenimiento $mantenimiento): RedirectResponse
    {
        $mantenimiento->delete();

        return to_route('mantenimientos.index')->with('success', 'Mantenimiento eliminado correctamente.');
    }
}
