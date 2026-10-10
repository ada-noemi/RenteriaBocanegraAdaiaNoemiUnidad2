<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function equipo(string $codigo, string $estado = 'Activo'): Equipo
    {
        return Equipo::create([
            'nombre' => 'Equipo '.$codigo, 'codigo' => $codigo, 'tipo' => 'Computadora',
            'ubicacion' => 'Laboratorio', 'estado' => $estado, 'fecha_registro' => '2026-10-08',
        ]);
    }

    private function mantenimiento(Equipo $equipo, string $estado = 'Pendiente'): Mantenimiento
    {
        return Mantenimiento::create([
            'equipo_id' => $equipo->id, 'tipo' => 'Preventivo',
            'fecha_programada' => '2026-10-15', 'descripcion' => 'Revisión general.', 'estado' => $estado,
        ]);
    }

    public function test_dashboard_vacio_muestra_ceros_y_mensaje(): void
    {
        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('estadisticas', [
                'Total de equipos' => 0, 'Equipos activos' => 0,
                'Mantenimientos pendientes' => 0, 'Mantenimientos finalizados' => 0,
            ])
            ->assertViewHas('mantenimientosRecientes', fn ($items) => $items->isEmpty())
            ->assertSee('Todavía no existen registros de mantenimientos.');
    }

    public function test_tarjetas_muestran_conteos_reales_por_estado(): void
    {
        $activo = $this->equipo('A');
        $this->equipo('B');
        $fuera = $this->equipo('C', 'Fuera de servicio');
        $this->mantenimiento($activo);
        $this->mantenimiento($fuera);
        $this->mantenimiento($activo, 'En proceso');
        $this->mantenimiento($activo, 'Finalizado');

        $estadisticas = [
            'Total de equipos' => 3, 'Equipos activos' => 2,
            'Mantenimientos pendientes' => 2, 'Mantenimientos finalizados' => 1,
        ];
        $response = $this->get(route('dashboard'))->assertOk()->assertViewHas('estadisticas', $estadisticas);
        foreach ($estadisticas as $label => $value) {
            $this->assertMatchesRegularExpression(
                '/<h2[^>]*>'.preg_quote($label, '/').'<\/h2>\s*<p[^>]*>'.$value.'<\/p>/u',
                $response->getContent()
            );
        }
    }

    public function test_recientes_muestra_equipo_tipo_fecha_y_estado(): void
    {
        $equipo = $this->equipo('MOSTRAR');
        $mantenimiento = $this->mantenimiento($equipo, 'En proceso');
        $mantenimiento->update(['tipo' => 'Correctivo']);

        $this->get(route('dashboard'))->assertOk()
            ->assertSee('Equipo MOSTRAR')->assertSee('Correctivo')
            ->assertSee('15/10/2026')->assertSee('En proceso')
            ->assertDontSee('Todavía no existen registros de mantenimientos.')
            ->assertViewHas('mantenimientosRecientes', fn ($items) => $items->count() === 1
                && $items->first()->relationLoaded('equipo')
                && $items->first()->equipo->is($equipo));
    }

    public function test_recientes_son_los_cinco_ultimos_registrados_con_desempate_por_id(): void
    {
        $ids = [];
        for ($i = 1; $i <= 7; $i++) {
            $equipo = $this->equipo('RECIENTE-'.$i);
            $mantenimiento = $this->mantenimiento($equipo);
            // Same creation timestamp checks the stable ID tie-breaker.
            $mantenimiento->created_at = '2026-10-08 10:00:00';
            // Scheduled dates deliberately differ from registration order.
            $mantenimiento->fecha_programada = '2026-10-'.(20 - $i);
            $mantenimiento->save();
            $ids[] = $mantenimiento->id;
        }
        $masAntiguo = $this->mantenimiento($this->equipo('ANTIGUO'));
        $masAntiguo->created_at = '2026-10-01 10:00:00';
        $masAntiguo->save();

        $esperados = array_reverse(array_slice($ids, -5));
        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('mantenimientosRecientes', fn ($items) => $items->pluck('id')->all() === $esperados
                && $items->every(fn ($item) => $item->relationLoaded('equipo')))
            ->assertSeeInOrder(['Equipo RECIENTE-7', 'Equipo RECIENTE-6', 'Equipo RECIENTE-5', 'Equipo RECIENTE-4', 'Equipo RECIENTE-3'])
            ->assertDontSee('Equipo RECIENTE-1')->assertDontSee('Equipo RECIENTE-2')->assertDontSee('Equipo ANTIGUO');
    }

    public function test_dashboard_refleja_cambios_y_eliminaciones_sin_datos_en_cache(): void
    {
        $equipo = $this->equipo('CAMBIOS');
        $mantenimiento = $this->mantenimiento($equipo);
        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('estadisticas', fn ($stats) => $stats['Mantenimientos pendientes'] === 1);

        $equipo->update(['estado' => 'Fuera de servicio']);
        $mantenimiento->update(['estado' => 'Finalizado']);
        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('estadisticas', [
                'Total de equipos' => 1, 'Equipos activos' => 0,
                'Mantenimientos pendientes' => 0, 'Mantenimientos finalizados' => 1,
            ]);

        $mantenimiento->delete();
        $equipo->delete();
        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('estadisticas', fn ($stats) => array_sum($stats) === 0)
            ->assertViewHas('mantenimientosRecientes', fn ($items) => $items->isEmpty());
    }
}
