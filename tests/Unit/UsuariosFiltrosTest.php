<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosFiltrosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_filtra_por_rol(): void
    {
        $html = $this->actingAs($this->jefe())
            ->get('/usuarios?rol=Cliente')
            ->getContent();

        $this->assertStringContainsString('cliente@example.com', $html);
        $this->assertStringNotContainsString('jefe@example.com', $html);
    }

    public function test_filtra_por_rol_programador(): void
    {
        $html = $this->actingAs($this->jefe())
            ->get('/usuarios?rol=Programador')
            ->getContent();

        $this->assertStringContainsString('dev@example.com', $html);
        $this->assertStringNotContainsString('cliente@example.com', $html);
    }

    private function jefe(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }
}
