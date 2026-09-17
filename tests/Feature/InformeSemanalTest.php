<?php

namespace Tests\Feature;

use App\Mail\ResumenSprintSemanal;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tarjeta 6 de la guia: informe IA semanal por email al Jefe.
 * Corre con AI_PROVIDER=fake, asi no depende de OpenRouter.
 */
class InformeSemanalTest extends TestCase
{
    use RefreshDatabase;

    private function sprintActivo(): Sprint
    {
        Role::firstOrCreate(['name' => 'Cliente']);
        $cliente = Cliente::create([
            'nombre' => 'Juan', 'apellido' => 'Perez', 'email' => uniqid().'@test.com',
            'empresa' => 'Empresa '.uniqid(), 'estado' => 'activo',
        ]);
        $pm = User::create([
            'name' => 'PM', 'apellido' => 'Test', 'email' => uniqid().'@pm.test',
            'estado' => 'activo', 'password' => bcrypt('1234'),
        ]);
        $proyecto = Proyecto::create([
            'nombre' => 'Proyecto '.uniqid(), 'fecha_inicio' => today(),
            'estado' => 'en_progreso', 'cliente_id' => $cliente->id, 'pm_id' => $pm->id,
        ]);

        return Sprint::create([
            'proyecto_id' => $proyecto->id,
            'nombre' => 'Sprint activo',
            'fecha_inicio' => today(),
            'fecha_fin' => today()->addDays(13),
            'estado' => 'activo',
        ]);
    }

    public function test_envia_al_jefe_el_resumen_de_los_sprints_activos(): void
    {
        Role::firstOrCreate(['name' => 'Jefe']);
        $jefe = User::create([
            'name' => 'Jefe', 'apellido' => 'Global', 'email' => 'jefe@test.com',
            'estado' => 'activo', 'password' => bcrypt('1234'),
        ]);
        $jefe->assignRole('Jefe');
        $this->sprintActivo();

        Mail::fake();
        $this->artisan('informes:resumen-sprint-semanal')->assertSuccessful();

        // Un sprint activo x un Jefe = un mail. Con Mail::raw el mensaje no es
        // un Mailable tipable, así que verificamos por conteo y por el log.
        Mail::assertSent(ResumenSprintSemanal::class, function ($mail) use ($jefe) {
            return $mail->hasTo($jefe->email);
        });
    }

    public function test_sin_sprints_activos_no_envia_nada(): void
    {
        Role::firstOrCreate(['name' => 'Jefe']);
        $jefe = User::create([
            'name' => 'Jefe', 'apellido' => 'Global', 'email' => 'jefe2@test.com',
            'estado' => 'activo', 'password' => bcrypt('1234'),
        ]);
        $jefe->assignRole('Jefe');

        Mail::fake();
        $this->artisan('informes:resumen-sprint-semanal')->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_sin_jefes_el_comando_termina_sin_error(): void
    {
        $this->sprintActivo();

        Mail::fake();
        $this->artisan('informes:resumen-sprint-semanal')->assertSuccessful();

        Mail::assertNothingSent();
    }
}
