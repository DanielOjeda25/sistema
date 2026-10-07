<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ciclo de vida del módulo Proyectos, contado en orden: crear -> editar ->
 * eliminar (Jefe), crear -> editar (PM) y la regla de bajas exclusivas del
 * Jefe. Un test por tarea; cada uno arma lo que necesita porque la base
 * se reinicia entre tests (RefreshDatabase).
 */
class ProyectosCrudTest extends TestCase
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

    private function pm(): User
    {
        return User::where('email', 'pm@example.com')->firstOrFail();
    }

    /** Proyecto ya creado en la base, para probar editar/eliminar sin repetir datos. */
    private function proyectoDePrueba(string $nombre): Proyecto
    {
        return Proyecto::create([
            'nombre' => $nombre, 'descripcion' => 'prueba',
            'fecha_inicio' => '2026-10-01', 'fecha_fin_estimada' => '2026-12-01',
            'estado' => 'pendiente', 'cliente_id' => 1, 'pm_id' => $this->jefe()->id,
        ]);
    }

    #[Test]
    public function un_jefe_puede_crear_un_proyecto(): void
    {
        $this->actingAs($this->jefe())->post(route('proyectos.store'), [
            'nombre' => 'Proyecto nuevo del Jefe', 'descripcion' => 'prueba',
            'fecha_inicio' => '2026-10-01', 'fecha_fin_estimada' => '2026-12-01',
            'estado' => 'pendiente', 'cliente_id' => 1, 'pm_id' => $this->jefe()->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyectos', ['nombre' => 'Proyecto nuevo del Jefe']);
    }

    #[Test]
    public function un_jefe_puede_editar_un_proyecto(): void
    {
        $proyecto = $this->proyectoDePrueba('Proyecto del Jefe a editar');

        $this->actingAs($this->jefe())->put(route('proyectos.update', $proyecto), [
            'nombre' => 'Proyecto editado por el Jefe', 'fecha_inicio' => '2026-10-01',
            'estado' => 'en_progreso', 'cliente_id' => 1, 'pm_id' => $proyecto->pm_id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyectos', [
            'id' => $proyecto->id,
            'nombre' => 'Proyecto editado por el Jefe',
            'estado' => 'en_progreso',
        ]);
    }

    #[Test]
    public function un_jefe_puede_eliminar_un_proyecto(): void
    {
        $proyecto = $this->proyectoDePrueba('Proyecto que borra el Jefe');

        $this->actingAs($this->jefe())->delete(route('proyectos.destroy', $proyecto))->assertRedirect();

        $this->assertDatabaseMissing('proyectos', ['id' => $proyecto->id]);
    }

    #[Test]
    public function un_pm_puede_crear_un_proyecto(): void
    {
        // La escritura de proyectos no es exclusiva del Jefe: el PM también
        // tiene permiso según las rutas (role:Jefe|PM).
        $this->actingAs($this->pm())->post(route('proyectos.store'), [
            'nombre' => 'Proyecto nuevo del PM', 'descripcion' => 'prueba',
            'fecha_inicio' => '2026-10-01', 'fecha_fin_estimada' => '2026-12-01',
            'estado' => 'pendiente', 'cliente_id' => 1, 'pm_id' => $this->pm()->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyectos', ['nombre' => 'Proyecto nuevo del PM']);
    }

    #[Test]
    public function un_pm_puede_editar_un_proyecto(): void
    {
        $proyecto = $this->proyectoDePrueba('Proyecto del PM a editar');

        $this->actingAs($this->pm())->put(route('proyectos.update', $proyecto), [
            'nombre' => 'Proyecto editado por el PM', 'fecha_inicio' => '2026-10-01',
            'estado' => 'en_progreso', 'cliente_id' => 1, 'pm_id' => $proyecto->pm_id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyectos', [
            'id' => $proyecto->id,
            'nombre' => 'Proyecto editado por el PM',
            'estado' => 'en_progreso',
        ]);
    }

    #[Test]
    public function el_pm_no_puede_eliminar_proyectos(): void
    {
        // Las bajas son decisión exclusiva del Jefe: el PM, que sí crea y
        // edita, recibe 403 y el proyecto sigue existiendo.
        $proyecto = $this->proyectoDePrueba('Proyecto protegido del PM');

        $this->actingAs($this->pm())->delete(route('proyectos.destroy', $proyecto))->assertForbidden();

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id]);
    }
}
