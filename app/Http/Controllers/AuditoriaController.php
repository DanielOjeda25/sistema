<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Role;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditoria = Audit::with('user.roles')
            ->latest()
            // Busqueda libre: evento o modelo auditado.
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->lower();
                $query->where(function ($sub) use ($q) {
                    $sub->where('event', 'like', "%{$q}%")
                        ->orWhere('auditable_type', 'like', "%{$q}%");
                });
            })
            // Filtro por usuario responsable del cambio.
            ->when($request->filled('usuario'), function ($query) use ($request) {
                $query->where('user_id', $request->string('usuario')->toString());
            })
            // Filtro por rol: queda solo la actividad de los usuarios que
            // cumplen ese rol (Jefe, PM, etc.).
            ->when($request->filled('rol'), function ($query) use ($request) {
                $ids = User::query()
                    ->whereHas('roles', fn ($rol) => $rol->where('name', $request->string('rol')->toString()))
                    ->pluck('id');
                $query->whereIn('user_id', $ids);
            })
            // Filtro por accion: created, updated o deleted.
            ->when($request->filled('accion'), function ($query) use ($request) {
                $query->where('event', $request->string('accion')->toString());
            })
            // Historial de un registro concreto (desde el boton Historial de los listados).
            ->when($request->filled('modelo'), function ($query) use ($request) {
                $query->where('auditable_type', $request->string('modelo')->toString())
                    ->when($request->filled('registro'), fn ($q) => $q->where('auditable_id', $request->string('registro')->toString()));
            })
            ->paginate(20)
            ->withQueryString();

        // Cada fila del visor necesita un titulo legible ("Tarea: Relevamiento
        // de datos") y los cambios traducidos ("Estado: Pendiente → En
        // progreso"), en lugar de "Tarea #9" con crudos de la base.
        $auditoria->getCollection()->transform(function (Audit $registro) {
            $registro->titulo_auditoria = $this->tituloRegistro($registro);
            $registro->cambios_auditoria = $this->cambiosLegibles($registro);

            return $registro;
        });

        // Con roles cargados para que el filtro muestre "Sofia (Programador)".
        $usuarios = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('auditoria.index', compact('auditoria', 'usuarios', 'roles'));
    }

    /**
     * Titulo humano del registro auditado: el nombre/titulo que tenga el
     * propio registro (o "eliminado" si ya no existe en la tabla).
     */
    private function tituloRegistro(Audit $registro): string
    {
        $etiqueta = match ($registro->auditable_type) {
            'App\Models\Tarea' => 'Tarea',
            'App\Models\Proyecto' => 'Proyecto',
            'App\Models\Hito' => 'Hito',
            'App\Models\Sprint' => 'Sprint',
            'App\Models\Cliente' => 'Cliente',
            'App\Models\Factura' => 'Factura',
            'App\Models\EntregableIA' => 'Entregable',
            'App\Models\SolicitudCambio' => 'Solicitud de cambio',
            'App\Models\User' => 'Usuario',
            default => class_basename($registro->auditable_type),
        };

        $camposNombre = ['titulo', 'nombre', 'numero'];
        $candidato = $registro->auditable_type;

        if (class_exists($candidato) && $modelo = $candidato::find($registro->auditable_id)) {
            foreach ($camposNombre as $campo) {
                if (isset($modelo->{$campo}) && $modelo->{$campo} !== null) {
                    return "{$etiqueta}: {$modelo->{$campo}}";
                }
            }

            if ($modelo instanceof User) {
                return "{$etiqueta}: {$modelo->name} {$modelo->apellido}";
            }
        }

        return "{$etiqueta} #{$registro->auditable_id}".($registro->event === 'deleted' ? ' (eliminado)' : '');
    }

    /**
     * Devuelve los cambios del evento "updated" listos para mostrar:
     * nombre del campo en español y valores traducidos (enums, fechas,
     * booleanos) en vez de los crudos de la base.
     */
    private function cambiosLegibles(Audit $registro): array
    {
        if ($registro->event !== 'updated' || ! is_array($registro->getModified())) {
            return [];
        }

        $nombres = [
            'estado' => 'Estado', 'nombre' => 'Nombre', 'titulo' => 'Título',
            'descripcion' => 'Descripción', 'prioridad' => 'Prioridad',
            'orden' => 'Orden', 'completado' => 'Completado', 'monto' => 'Monto',
            'numero' => 'Número', 'detalle' => 'Detalle', 'fecha_limite' => 'Fecha límite',
            'fecha_inicio' => 'Fecha de inicio', 'fecha_fin' => 'Fecha de fin',
            'fecha_objetivo' => 'Fecha objetivo', 'fecha_emision' => 'Fecha de emisión',
            'fecha_vencimiento' => 'Fecha de vencimiento', 'fecha_fin_estimada' => 'Fecha de fin estimada',
            'contenido' => 'Contenido', 'tipo' => 'Tipo', 'empresa' => 'Empresa',
            'telefono' => 'Teléfono', 'email' => 'Correo', 'name' => 'Nombre',
            'apellido' => 'Apellido', 'proyecto_id' => 'Proyecto', 'cliente_id' => 'Cliente',
            'pm_id' => 'Project Manager', 'asignado_a' => 'Asignado a',
            'sprint_id' => 'Sprint', 'emitida_por' => 'Emitida por',
            'generado_por' => 'Generado por', 'solicitado_por' => 'Solicitado por',
            'solicitud_cambio_id' => 'Solicitud de cambio', 'visible_cliente' => 'Visible para el Cliente',
        ];

        $valores = [
            'pendiente' => 'Pendiente', 'en_progreso' => 'En progreso',
            'completada' => 'Completada', 'completado' => 'Completado',
            'cancelada' => 'Cancelada', 'cancelado' => 'Cancelado',
            'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta',
            'borrador' => 'Borrador', 'revisado' => 'Revisado', 'aprobado' => 'Aprobado',
            'pagada' => 'Pagada', 'vencida' => 'Vencida', 'activo' => 'Activo',
            'inactivo' => 'Inactivo', 'planificado' => 'Planificado',
        ];

        $legibles = [];
        foreach ($registro->getModified() as $campo => $cambio) {
            $legibles[] = [
                'campo' => $nombres[$campo] ?? ucfirst(str_replace('_', ' ', $campo)),
                'viejo' => $this->valorLegible($cambio['old'] ?? null, $valores),
                'nuevo' => $this->valorLegible($cambio['new'] ?? null, $valores),
            ];
        }

        return $legibles;
    }

    private function valorLegible($valor, array $valores): string
    {
        if ($valor === null || $valor === '') {
            return '—';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }

        if (is_string($valor) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
            return date('d/m/Y', strtotime($valor));
        }

        return $valores[$valor] ?? (string) $valor;
    }
}
