<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_puede_ver_login_sin_recuperacion_de_contrasena(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Iniciar sesión')
            ->assertSee('name="email"', false)->assertSee('name="password"', false);

        $this->get('/forgot-password')->assertNotFound();
        $this->assertGuest();
    }

    public function test_inicio_correcto_regenera_sesion_y_muestra_nombre(): void
    {
        $user = User::factory()->create(['name' => 'Usuario evaluación', 'password' => 'Clave123!']);
        $this->withSession(['marca' => 'conservada']);
        $sessionId = session()->getId();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'Clave123!'])
            ->assertRedirect(route('dashboard'))->assertSessionHasNoErrors()->assertSessionHas('marca', 'conservada');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($sessionId, session()->getId());
        $this->get(route('dashboard'))->assertOk()->assertSee('Usuario evaluación')->assertSee('Cerrar sesión');
    }

    public function test_inicio_redirige_a_la_seccion_solicitada(): void
    {
        $user = User::factory()->create(['password' => 'Clave123!']);
        $this->get(route('equipos.index'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'Clave123!'])
            ->assertRedirect(route('equipos.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_credenciales_incorrectas_no_autentican_ni_conservan_contrasena(): void
    {
        $user = User::factory()->create(['password' => 'Clave123!']);
        $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email, 'password' => 'Incorrecta',
        ])->assertRedirect(route('login'))->assertSessionHasErrors([
            'email' => 'El correo electrónico o la contraseña son incorrectos.',
        ])->assertSessionHas('_old_input.email', $user->email)
            ->assertSessionMissing('_old_input.password');
        $this->assertGuest();
        $this->get(route('login'))->assertOk()->assertSee('El correo electrónico o la contraseña son incorrectos.')
            ->assertSee($user->email)->assertDontSee('Incorrecta');
    }

    public function test_correo_inexistente_recibe_el_mismo_error(): void
    {
        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'inexistente@example.com', 'password' => 'Incorrecta',
        ])->assertRedirect(route('login'))->assertSessionHasErrors([
            'email' => 'El correo electrónico o la contraseña son incorrectos.',
        ]);
        $this->assertGuest();
    }

    public function test_validacion_de_campos_del_login(): void
    {
        $this->post(route('login.store'), [])->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
        $this->post(route('login.store'), ['email' => 'no-es-correo', 'password' => ['incorrecto']])
            ->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_todas_las_rutas_del_sistema_requieren_autenticacion(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        foreach (['equipos', 'mantenimientos'] as $module) {
            $this->get(route($module.'.index'))->assertRedirect(route('login'));
            $this->get(route($module.'.create'))->assertRedirect(route('login'));
            $this->get(route($module.'.edit', 99999))->assertRedirect(route('login'));
            $this->post(route($module.'.store'), [])->assertRedirect(route('login'));
            $this->put(route($module.'.update', 99999), [])->assertRedirect(route('login'));
            $this->delete(route($module.'.destroy', 99999))->assertRedirect(route('login'));
        }
        $this->assertDatabaseCount('equipos', 0);
        $this->assertDatabaseCount('mantenimientos', 0);
        $this->assertGuest();
    }

    public function test_usuario_autenticado_no_vuelve_a_login(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('login'))->assertRedirect(route('dashboard'));
        $this->post(route('login.store'), [])->assertRedirect(route('dashboard'));
    }

    public function test_cierre_de_sesion_invalida_sesion_y_protege_nuevo_acceso(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['marca' => 'eliminar']);
        $token = session()->token();
        $sessionId = session()->getId();

        $this->post(route('logout'))->assertRedirect(route('login'))->assertSessionMissing('marca');
        $this->assertGuest();
        $this->assertNotSame($sessionId, session()->getId());
        $this->assertNotSame($token, session()->token());
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->get('/logout')->assertStatus(405);
    }

    public function test_seeder_admin_es_repetible_y_credenciales_funcionan(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $hash = $user->password;
        $this->assertSame('Administrador de demostración', $user->name);
        $this->assertNotSame('Admin123!', $hash);
        $this->assertTrue(Hash::check('Admin123!', $hash));

        $this->seed(AdminUserSeeder::class);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame($hash, $user->fresh()->password);
        $this->post(route('login.store'), ['email' => 'admin@example.com', 'password' => 'Admin123!'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_limita_intentos_repetidos(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => 'inexistente@example.com', 'password' => 'Incorrecta',
            ])->assertRedirect(route('login'));
        }
        $this->post(route('login.store'), [
            'email' => 'inexistente@example.com', 'password' => 'Incorrecta',
        ])->assertStatus(429);
        $this->assertGuest();
    }
}
