<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function datos(array $changes = []): array
    {
        return array_replace([
            'name' => 'Usuario nuevo', 'email' => 'nuevo@example.com',
            'password' => 'Clave123!', 'password_confirmation' => 'Clave123!',
        ], $changes);
    }

    public function test_pantalla_de_registro_y_enlaces_de_navegacion(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Crear cuenta')
            ->assertSee(route('register'));
        $this->get(route('register'))->assertOk()->assertSee('Confirmar contraseña')
            ->assertSee('Iniciar sesión')->assertSee(route('login'));
        $this->assertGuest();
    }

    public function test_registro_crea_usuario_con_hash_e_inicia_sesion(): void
    {
        $this->withSession(['marca' => 'conservada']);
        $sessionId = session()->getId();
        $this->post(route('register.store'), $this->datos())->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();
        $user = User::where('email', 'nuevo@example.com')->firstOrFail();
        $this->assertSame('Usuario nuevo', $user->name);
        $this->assertNotSame('Clave123!', $user->password);
        $this->assertTrue(Hash::check('Clave123!', $user->password));
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($sessionId, session()->getId());
        $this->get(route('dashboard'))->assertOk()->assertSee('Usuario nuevo');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_correo_duplicado_no_crea_usuario_y_muestra_error(): void
    {
        User::factory()->create(['email' => 'nuevo@example.com']);
        $this->post(route('register.store'), $this->datos())->assertRedirect(route('register'))
            ->assertSessionHasErrors('email')
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
        $this->get(route('register'))->assertOk()->assertSee('Este correo electrónico ya está registrado.')
            ->assertSee('Usuario nuevo')->assertSee('nuevo@example.com')->assertDontSee('Clave123!');
    }

    public function test_campos_obligatorios_y_correo_invalido(): void
    {
        $this->post(route('register.store'), [])->assertSessionHasErrors(['name', 'email', 'password']);
        $this->post(route('register.store'), $this->datos(['email' => 'incorrecto', 'name' => str_repeat('A', 256)]))
            ->assertSessionHasErrors(['name', 'email']);
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_contrasena_corta_o_confirmacion_incorrecta_no_registran(): void
    {
        $this->post(route('register.store'), $this->datos([
            'password' => 'corta', 'password_confirmation' => 'corta',
        ]))->assertSessionHasErrors('password');
        $this->post(route('register.store'), $this->datos([
            'password_confirmation' => 'OtraClave123!',
        ]))->assertSessionHasErrors('password');
        $datos = $this->datos();
        unset($datos['password_confirmation']);
        $this->post(route('register.store'), $datos)->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_usuario_autenticado_no_puede_registrar_otra_cuenta(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('register'))->assertRedirect(route('dashboard'));
        $this->post(route('register.store'), $this->datos())->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('users', 1);
    }
}
