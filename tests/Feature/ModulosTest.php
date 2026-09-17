<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tarjeta 7 de la guia: cobertura de los listados de cada modulo y de los
 * permisos de lectura por rol (quien ve que seccion y que escopo).
 */
class ModulosTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioConRol(string $rol, string $email): User
    {
        Role::firstOrCreate(['name' => $rol]);
        $user = User::create([
            'name' => $rol, 'apellido' => 'Test', 'email' => $email,
            'estado' => 'activo', 'password' => bcrypt('1234'),
        ]);
        $user->assignRole($rol);

        return $user;
    }

    public function test_el_jefe_ve_todos_los_listados(): void
    {
        $jefe = $this->usuarioConRol('Jefe', 'jefe.modulos@test.com');

        foreach ([
            'clientes', 'proyectos', 'tareas', 'hitos', 'solicitudes-cambio',
            'sprints', 'entregables', 'facturas', 'usuarios', 'auditoria',
        ] as $ruta) {
            $this->actingAs($jefe)->get("/{$ruta}")->assertOk();
        }
    }

    public function test_el_programador_no_ve_gestion_comercial_ni_administracion(): void
    {
        $dev = $this->usuarioConRol('Programador', 'dev.modulos@test.com');

        // La cartera de clientes es lectura interna: el Programador la ve,
        // pero la administración de usuarios y la auditoría no.
        $this->actingAs($dev)->get('/clientes')->assertOk();
        $this->actingAs($dev)->get('/usuarios')->assertForbidden();
        $this->actingAs($dev)->get('/auditoria')->assertForbidden();

        // Lo suyo si lo ve.
        $this->actingAs($dev)->get('/tareas')->assertOk();
        $this->actingAs($dev)->get('/entregables')->assertOk();
    }

    public function test_un_cliente_solo_ve_proyectos_de_su_empresa(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Mia', 'apellido' => 'Garcia', 'email' => uniqid().'@test.com',
            'empresa' => 'Mi Empresa', 'estado' => 'activo',
        ]);
        $otra = Cliente::create([
            'nombre' => 'Otro', 'apellido' => 'Perez', 'email' => uniqid().'@test.com',
            'empresa' => 'Otra Empresa', 'estado' => 'activo',
        ]);
        $pm = $this->usuarioConRol('PM', 'pm.modulos@test.com');

        $mio = Proyecto::create([
            'nombre' => 'Proyecto Propio', 'fecha_inicio' => today(),
            'estado' => 'en_progreso', 'cliente_id' => $cliente->id, 'pm_id' => $pm->id,
        ]);
        $ajeno = Proyecto::create([
            'nombre' => 'Proyecto Ajeno', 'fecha_inicio' => today(),
            'estado' => 'en_progreso', 'cliente_id' => $otra->id, 'pm_id' => $pm->id,
        ]);

        Role::firstOrCreate(['name' => 'Cliente']);
        $usuarioCliente = User::create([
            'name' => 'Cliente', 'apellido' => 'Web', 'email' => 'cliente.modulos@test.com',
            'estado' => 'activo', 'password' => bcrypt('1234'), 'cliente_id' => $cliente->id,
        ]);
        $usuarioCliente->assignRole('Cliente');

        $resp = $this->actingAs($usuarioCliente)->get('/proyectos')->assertOk();
        $resp->assertSee($mio->nombre);
        $resp->assertDontSee($ajeno->nombre);

        // Y por URL directa a un proyecto ajeno: 403 (puedeVer).
        $this->actingAs($usuarioCliente)->get("/proyectos/{$ajeno->id}")->assertForbidden();

        // La cartera completa de clientes no es para el Cliente externo.
        $this->actingAs($usuarioCliente)->get('/clientes')->assertForbidden();
    }

    public function test_invitado_no_entra_a_ningun_listado(): void
    {
        foreach (['proyectos', 'tareas', 'facturas'] as $ruta) {
            $this->get("/{$ruta}")->assertRedirect(route('login'));
        }
    }

    public function test_el_listado_de_tareas_filtra_por_prioridad_y_responsable(): void
    {
        $pm = $this->usuarioConRol('PM', 'pm.filtros@test.com');
        $sofi = $this->usuarioConRol('Programador', 'sofi.filtros@test.com');
        $roberto = $this->usuarioConRol('Programador', 'roberto.filtros@test.com');
        Role::firstOrCreate(['name' => 'Cliente']);
        $cliente = \App\Models\Cliente::create([
            'nombre' => 'F', 'apellido' => 'Test', 'email' => uniqid().'@test.com',
            'empresa' => 'Empresa Filtros', 'estado' => 'activo',
        ]);
        $proyecto = \App\Models\Proyecto::create([
            'nombre' => 'Proyecto Filtros', 'fecha_inicio' => today(),
            'estado' => 'en_progreso', 'cliente_id' => $cliente->id, 'pm_id' => $pm->id,
        ]);

        $alta = \App\Models\Tarea::create(['titulo' => 'Tarea Alta', 'estado' => 'pendiente', 'prioridad' => 'alta', 'proyecto_id' => $proyecto->id, 'asignado_a' => $sofi->id]);
        $media = \App\Models\Tarea::create(['titulo' => 'Tarea Media', 'estado' => 'pendiente', 'prioridad' => 'media', 'proyecto_id' => $proyecto->id, 'asignado_a' => $roberto->id]);

        // Por prioridad: solo aparece la alta.
        $resp = $this->actingAs($pm)->get('/tareas?prioridad=alta')->assertOk();
        $resp->assertSee('Tarea Alta')->assertDontSee('Tarea Media');

        // Por responsable: solo las de Sofi.
        $resp = $this->actingAs($pm)->get("/tareas?asignado_a={$sofi->id}")->assertOk();
        $resp->assertSee('Tarea Alta')->assertDontSee('Tarea Media');

        // Combinado no matchea nada si no existe la combinacion.
        $resp = $this->actingAs($pm)->get("/tareas?asignado_a={$roberto->id}&prioridad=alta")->assertOk();
        $resp->assertSee('No hay tareas registradas');
    }
}
