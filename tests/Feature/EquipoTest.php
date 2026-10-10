<?php

namespace Tests\Feature;

use App\Models\Equipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoTest extends TestCase
{
    use RefreshDatabase;

    private function datos(array $changes = []): array
    {
        return array_replace([
            'nombre' => 'Computadora de laboratorio',
            'codigo' => 'EQ-001',
            'tipo' => 'Computadora',
            'ubicacion' => 'Laboratorio 1',
            'estado' => 'Activo',
            'fecha_registro' => '2026-10-08',
        ], $changes);
    }

    public function test_listado_vacio_y_formulario_de_registro(): void
    {
        $this->get(route('equipos.index'))->assertOk()->assertSee('Todavía no existen registros de equipos.');
        $this->get(route('equipos.create'))->assertRedirect(route('equipos.index'))->assertSessionHas('equipo_modal', 'equipo-create');
        $this->get(route('equipos.index'))->assertOk()->assertSee('data-auto-open', false);
    }

    public function test_registrar_y_listar_equipo(): void
    {
        $this->post(route('equipos.store'), $this->datos())
            ->assertRedirect(route('equipos.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('equipos', array_diff_key($this->datos(), ['fecha_registro' => true]));
        $this->assertSame('2026-10-08', Equipo::firstOrFail()->fecha_registro->format('Y-m-d'));
        $this->get(route('equipos.index'))->assertOk()->assertSee('EQ-001')->assertSee('08/10/2026');
    }

    public function test_validacion_rechaza_campos_vacios_estado_y_fecha_invalidos(): void
    {
        $this->post(route('equipos.store'), [])->assertSessionHasErrors([
            'nombre', 'codigo', 'tipo', 'ubicacion', 'estado', 'fecha_registro',
        ]);

        $this->post(route('equipos.store'), $this->datos([
            'estado' => 'Desconocido',
            'fecha_registro' => '2026-02-30',
            'codigo' => str_repeat('A', 51),
        ]))->assertSessionHasErrors(['estado', 'fecha_registro', 'codigo']);

        $this->assertDatabaseCount('equipos', 0);
    }

    public function test_codigo_unico_en_registro_y_edicion(): void
    {
        $primero = Equipo::create($this->datos());
        $segundo = Equipo::create($this->datos(['codigo' => 'EQ-002']));

        $this->post(route('equipos.store'), $this->datos())->assertSessionHasErrors('codigo');
        $this->put(route('equipos.update', $segundo), $this->datos())->assertSessionHasErrors('codigo');
        $this->assertSame('EQ-002', $segundo->fresh()->codigo);
        $this->assertDatabaseCount('equipos', 2);
    }

    public function test_editar_equipo_conservando_su_codigo(): void
    {
        $equipo = Equipo::create($this->datos());

        $this->get(route('equipos.edit', $equipo))->assertRedirect(route('equipos.index'));
        $this->get(route('equipos.index'))->assertOk()->assertSee('EQ-001')->assertSee('data-auto-open', false);
        $this->put(route('equipos.update', $equipo), $this->datos([
            'nombre' => 'Equipo actualizado',
            'estado' => 'Fuera de servicio',
        ]))->assertRedirect(route('equipos.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'codigo' => 'EQ-001',
            'nombre' => 'Equipo actualizado',
            'estado' => 'Fuera de servicio',
        ]);
    }

    public function test_eliminar_y_rechazar_equipo_inexistente(): void
    {
        $equipo = Equipo::create($this->datos());
        $this->delete(route('equipos.destroy', $equipo))->assertRedirect(route('equipos.index'));
        $this->assertDatabaseMissing('equipos', ['id' => $equipo->id]);
        $this->get(route('equipos.edit', $equipo->id))->assertNotFound();
        $this->delete(route('equipos.destroy', $equipo->id))->assertNotFound();
    }

    public function test_error_de_registro_reabre_modal_y_conserva_datos(): void
    {
        Equipo::create($this->datos());
        $this->post(route('equipos.store'), $this->datos(['nombre' => 'Intento duplicado']))
            ->assertRedirect(route('equipos.index'))
            ->assertSessionHasErrors('codigo')
            ->assertSessionHas('equipo_modal', 'equipo-create');

        $this->get(route('equipos.index'))->assertOk()
            ->assertSee('data-auto-open', false)
            ->assertSee('Intento duplicado')
            ->assertSee('Este código ya está registrado.');
    }

    public function test_error_de_edicion_reabre_modal_aunque_equipo_no_este_en_pagina(): void
    {
        $equipo = Equipo::create($this->datos());
        for ($i = 2; $i <= 12; $i++) {
            Equipo::create($this->datos(['codigo' => 'EQ-'.$i]));
        }

        $this->put(route('equipos.update', $equipo), $this->datos([
            'nombre' => 'Edición pendiente',
            'codigo' => 'EQ-2',
        ]))->assertRedirect(route('equipos.index'))
            ->assertSessionHasErrors('codigo')
            ->assertSessionHas('equipo_modal', 'equipo-edit-'.$equipo->id);

        $this->get(route('equipos.index'))->assertOk()
            ->assertSee('id="equipo-edit-'.$equipo->id.'"', false)
            ->assertSee('data-auto-open', false)
            ->assertSee('Edición pendiente');
        $this->assertSame('EQ-001', $equipo->fresh()->codigo);
    }

    public function test_listado_paginado_y_salida_escapada(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Equipo::create($this->datos(['codigo' => 'EQ-'.$i, 'nombre' => '<script>alert(1)</script>']));
        }

        $this->get(route('equipos.index'))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertViewHas('equipos', fn ($equipos) => $equipos->count() === 10 && $equipos->total() === 11);

        $this->get(route('equipos.index', ['page' => 2]))->assertOk()
            ->assertViewHas('equipos', fn ($equipos) => $equipos->count() === 1);
    }
}
