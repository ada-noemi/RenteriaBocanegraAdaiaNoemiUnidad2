<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipoRequest;
use App\Models\Equipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EquipoController extends Controller
{
    public function index(): View
    {
        return view('equipos.index', [
            'equipos' => Equipo::query()->latest('id')->paginate(10),
            'equipoModal' => Equipo::find(session('equipo_modal_id')),
        ]);
    }

    public function create(): RedirectResponse
    {
        return to_route('equipos.index')->with('equipo_modal', 'equipo-create');
    }

    public function store(EquipoRequest $request): RedirectResponse
    {
        Equipo::create($request->validated());

        return to_route('equipos.index')->with('success', 'Equipo registrado correctamente.');
    }

    public function edit(Equipo $equipo): RedirectResponse
    {
        return to_route('equipos.index')->with('equipo_modal', 'equipo-edit-'.$equipo->id)->with('equipo_modal_id', $equipo->id);
    }

    public function update(EquipoRequest $request, Equipo $equipo): RedirectResponse
    {
        $equipo->update($request->validated());

        return to_route('equipos.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipo $equipo): RedirectResponse
    {
        if ($equipo->mantenimientos()->exists()) {
            return to_route('equipos.index')->with('error', 'No se puede eliminar un equipo con mantenimientos asociados. Elimina primero sus mantenimientos.');
        }

        $equipo->delete();

        return to_route('equipos.index')->with('success', 'Equipo eliminado correctamente.');
    }
}
