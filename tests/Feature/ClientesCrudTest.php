<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ciclo de vida del módulo Clientes, contado en orden: crear -> editar ->
 * eliminar. Los tests se encadenan como relato (uno por tarea) pero cada
 * uno es autónomo: RefreshDatabase reinicia la base en cada test, así que
 * quien edita o elimina arma primero su propio cliente.
 */
class ClientesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function jefe(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }

    #[Test]
    public function un_jefe_puede_crear_un_cliente(): void
    {
        $this->actingAs($this->jefe())->post(route('clientes.store'), [
            'nombre' => 'Lucia', 'apellido' => 'Paz', 'email' => 'lucia@emp.com',
            'telefono' => '123', 'empresa' => 'Emp Lucia', 'estado' => 'activo',
        ])->assertRedirect();

        $this->assertDatabaseHas('clientes', ['email' => 'lucia@emp.com', 'empresa' => 'Emp Lucia']);
    }

    #[Test]
    public function un_jefe_puede_editar_un_cliente(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Lucia', 'apellido' => 'Paz', 'email' => 'lucia@emp.com',
            'telefono' => '123', 'empresa' => 'Emp Lucia', 'estado' => 'activo',
        ]);

        $this->actingAs($this->jefe())->put(route('clientes.update', $cliente), [
            'nombre' => 'Lucia', 'apellido' => 'Paz', 'email' => 'lucia@emp.com',
            'telefono' => '456', 'empresa' => 'Emp Lucia SA', 'estado' => 'inactivo',
        ])->assertRedirect();

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'estado' => 'inactivo', 'empresa' => 'Emp Lucia SA']);
    }

    #[Test]
    public function un_jefe_puede_eliminar_un_cliente(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Lucia', 'apellido' => 'Paz', 'email' => 'lucia@emp.com',
            'telefono' => '123', 'empresa' => 'Emp Lucia', 'estado' => 'activo',
        ]);

        $this->actingAs($this->jefe())->delete(route('clientes.destroy', $cliente))->assertRedirect();

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }
}
