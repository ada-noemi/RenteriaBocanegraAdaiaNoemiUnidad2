<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MantenimientoTest extends TestCase
{
    use RefreshDatabase;

    private function equipo(string $codigo = 'EQ-M01'): Equipo
    {
        return Equipo::create([
            'nombre' => 'Computadora del laboratorio',
            'codigo' => $codigo,
            'tipo' => 'Computadora',
            'ubicacion' => 'Laboratorio 1',
            'estado' => 'Activo',
            'fecha_registro' => '2026-10-08',
        ]);
    }

    private function datos(Equipo $equipo, array $changes = []): array
    {
        return array_replace([
            'equipo_id' => $equipo->id,
            'tipo' => 'Preventivo',
            'fecha_programada' => '2026-10-15',
            'descripcion' => 'Limpieza y revisión general.',
            'estado' => 'Pendiente',
        ], $changes);
    }

    public function test_listado_vacio_y_aviso_sin_equipos(): void
    {
        $this->get(route('mantenimientos.index'))->assertOk()
            ->assertSee('Todavía no existen registros de mantenimientos.')
            ->assertSee('Primero registra un equipo')
            ->assertSee('id="mantenimiento-create"', false);

        $this->get(route('mantenimientos.create'))->assertRedirect(route('mantenimientos.index'))
            ->assertSessionHas('mantenimiento_modal', 'mantenimiento-create');
    }

    public function test_registro_listado_y_relaciones(): void
    {
        $equipo = $this->equipo();
        $this->post(route('mantenimientos.store'), $this->datos($equipo))
            ->assertRedirect(route('mantenimientos.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('mantenimientos', [
            'equipo_id' => $equipo->id, 'tipo' => 'Preventivo', 'estado' => 'Pendiente',
            'descripcion' => 'Limpieza y revisión general.',
        ]);
        $mantenimiento = Mantenimiento::firstOrFail();
        $this->assertSame('2026-10-15', $mantenimiento->fecha_programada->format('Y-m-d'));
        $this->assertTrue($mantenimiento->equipo->is($equipo));
        $this->assertTrue($equipo->mantenimientos->first()->is($mantenimiento));

        $this->get(route('mantenimientos.index'))->assertOk()
            ->assertSee('EQ-M01')->assertSee('15/10/2026')->assertSee('Preventivo')->assertSee('Pendiente');
    }

    public function test_tipos_y_estados_permitidos(): void
    {
        $equipo = $this->equipo();
        foreach (['Preventivo', 'Correctivo'] as $tipo) {
            foreach (['Pendiente', 'En proceso', 'Finalizado'] as $estado) {
                $this->post(route('mantenimientos.store'), $this->datos($equipo, compact('tipo', 'estado')))
                    ->assertRedirect(route('mantenimientos.index'))->assertSessionHasNoErrors();
                $this->assertDatabaseHas('mantenimientos', compact('tipo', 'estado'));
            }
        }
        $this->assertDatabaseCount('mantenimientos', 6);
    }

    public function test_campos_obligatorios_y_valores_invalidos(): void
    {
        $this->post(route('mantenimientos.store'), [])->assertSessionHasErrors([
            'equipo_id', 'tipo', 'fecha_programada', 'descripcion', 'estado',
        ]);
        $equipo = $this->equipo();
        $this->post(route('mantenimientos.store'), $this->datos($equipo, [
            'equipo_id' => 99999,
            'tipo' => 'Otro',
            'fecha_programada' => '2026-02-30',
            'descripcion' => str_repeat('A', 5001),
            'estado' => 'Otro',
        ]))->assertSessionHasErrors(['equipo_id', 'tipo', 'fecha_programada', 'descripcion', 'estado']);

        $this->post(route('mantenimientos.store'), $this->datos($equipo, [
            'equipo_id' => 'texto',
            'fecha_programada' => '0999-01-01',
            'descripcion' => ['no es texto'],
        ]))->assertSessionHasErrors(['equipo_id', 'fecha_programada', 'descripcion']);

        $this->assertDatabaseCount('mantenimientos', 0);
    }

    public function test_editar_equipo_tipo_fecha_descripcion_y_estado(): void
    {
        $equipo = $this->equipo();
        $otro = $this->equipo('EQ-M02');
        $mantenimiento = Mantenimiento::create($this->datos($equipo));

        $this->get(route('mantenimientos.edit', $mantenimiento))
            ->assertRedirect(route('mantenimientos.index'))
            ->assertSessionHas('mantenimiento_modal', 'mantenimiento-edit-'.$mantenimiento->id);
        $this->get(route('mantenimientos.index'))->assertOk()->assertSee('data-auto-open', false);

        $this->put(route('mantenimientos.update', $mantenimiento), $this->datos($otro, [
            'tipo' => 'Correctivo',
            'fecha_programada' => '2026-10-20',
            'descripcion' => 'Cambio de componente.',
            'estado' => 'Finalizado',
        ]))->assertRedirect(route('mantenimientos.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('mantenimientos', [
            'id' => $mantenimiento->id, 'equipo_id' => $otro->id,
            'tipo' => 'Correctivo', 'descripcion' => 'Cambio de componente.', 'estado' => 'Finalizado',
        ]);
        $this->assertSame('2026-10-20', $mantenimiento->fresh()->fecha_programada->format('Y-m-d'));
    }

    public function test_edicion_invalida_no_modifica_registro(): void
    {
        $equipo = $this->equipo();
        $mantenimiento = Mantenimiento::create($this->datos($equipo));
        $this->put(route('mantenimientos.update', $mantenimiento), $this->datos($equipo, [
            'equipo_id' => 99999, 'tipo' => 'Otro', 'estado' => 'Otro',
            'fecha_programada' => 'incorrecta', 'descripcion' => '',
        ]))->assertSessionHasErrors(['equipo_id', 'tipo', 'estado', 'fecha_programada', 'descripcion']);
        $this->assertSame('Pendiente', $mantenimiento->fresh()->estado);
        $this->assertSame($equipo->id, $mantenimiento->fresh()->equipo_id);
    }

    public function test_eliminar_sin_eliminar_equipo_y_rutas_inexistentes(): void
    {
        $equipo = $this->equipo();
        $mantenimiento = Mantenimiento::create($this->datos($equipo));
        $this->get(route('mantenimientos.index'))->assertOk()
            ->assertSee('id="mantenimiento-delete-'.$mantenimiento->id.'"', false)
            ->assertSee('Esta acción no se puede deshacer.');

        $this->delete(route('mantenimientos.destroy', $mantenimiento))
            ->assertRedirect(route('mantenimientos.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('mantenimientos', ['id' => $mantenimiento->id]);
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id]);
        $this->get(route('mantenimientos.edit', $mantenimiento->id))->assertNotFound();
        $this->put(route('mantenimientos.update', $mantenimiento->id), [])->assertNotFound();
        $this->delete(route('mantenimientos.destroy', $mantenimiento->id))->assertNotFound();
    }

    public function test_error_de_registro_conserva_datos_y_reabre_modal(): void
    {
        $equipo = $this->equipo();
        $this->post(route('mantenimientos.store'), $this->datos($equipo, [
            'descripcion' => 'Descripción conservada', 'tipo' => 'Otro',
        ]))->assertRedirect(route('mantenimientos.index'))
            ->assertSessionHasErrors('tipo')
            ->assertSessionHas('mantenimiento_modal', 'mantenimiento-create');

        $this->get(route('mantenimientos.index'))->assertOk()
            ->assertSee('data-auto-open', false)->assertSee('Descripción conservada')
            ->assertSee('Selecciona un valor válido para tipo.');
    }

    public function test_error_de_edicion_reabre_modal_fuera_de_pagina(): void
    {
        $equipo = $this->equipo();
        $mantenimiento = Mantenimiento::create($this->datos($equipo));
        for ($i = 0; $i < 11; $i++) {
            Mantenimiento::create($this->datos($equipo));
        }
        $this->put(route('mantenimientos.update', $mantenimiento), $this->datos($equipo, [
            'descripcion' => 'Edición conservada', 'estado' => 'Otro',
        ]))->assertSessionHasErrors('estado')
            ->assertSessionHas('mantenimiento_modal', 'mantenimiento-edit-'.$mantenimiento->id);

        $this->get(route('mantenimientos.index'))->assertOk()
            ->assertSee('id="mantenimiento-edit-'.$mantenimiento->id.'"', false)
            ->assertSee('data-auto-open', false)->assertSee('Edición conservada');
    }

    public function test_paginacion_relacion_cargada_y_salida_escapada(): void
    {
        $equipo = $this->equipo();
        for ($i = 0; $i < 11; $i++) {
            Mantenimiento::create($this->datos($equipo, ['descripcion' => '<script>alert(1)</script>']));
        }
        $this->get(route('mantenimientos.index'))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertViewHas('mantenimientos', fn ($items) => $items->count() === 10
                && $items->total() === 11 && $items->every(fn ($item) => $item->relationLoaded('equipo')));
        $this->get(route('mantenimientos.index', ['page' => 2]))->assertOk()
            ->assertViewHas('mantenimientos', fn ($items) => $items->count() === 1);
    }

    public function test_equipo_con_mantenimientos_no_se_elimina(): void
    {
        $equipo = $this->equipo();
        $mantenimiento = Mantenimiento::create($this->datos($equipo));
        $this->delete(route('equipos.destroy', $equipo))->assertRedirect(route('equipos.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id]);
        $this->assertDatabaseHas('mantenimientos', ['id' => $mantenimiento->id]);
        $this->get(route('equipos.index'))->assertOk()->assertSee('No se puede eliminar un equipo con mantenimientos asociados.');

        $this->delete(route('mantenimientos.destroy', $mantenimiento));
        $this->delete(route('equipos.destroy', $equipo))->assertRedirect(route('equipos.index'));
        $this->assertDatabaseMissing('equipos', ['id' => $equipo->id]);
    }
}
