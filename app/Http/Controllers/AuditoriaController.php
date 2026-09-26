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
        $rolElegido = $request->string('rol')->toString();
        $usuarioElegido = $request->string('usuario')->toString();

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
            // Filtro por usuario responsable del cambio. Si el usuario elegido
            // no cumple el rol elegido, ya quedo reseteado mas abajo.
            ->when($usuarioElegido !== '', fn ($query) => $query->where('user_id', $usuarioElegido))
            // Filtro por rol: queda solo la actividad de los usuarios que
            // cumplen ese rol (Jefe, PM, etc.).
            ->when($rolElegido !== '', function ($query) use ($rolElegido) {
                $ids = User::query()
                    ->whereHas('roles', fn ($rol) => $rol->where('name', $rolElegido))
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

        // Opciones del buscador de usuarios: "Nombre (Rol)". Si hay rol elegido,
        // quedan solo los que cumplen ese rol (antes se filtraba con JS).
        $opcionesUsuario = User::with('roles')->orderBy('name')
            ->when($rolElegido !== '', fn ($query) => $query->whereHas('roles', fn ($rol) => $rol->where('name', $rolElegido)))
            ->get()
            ->mapWithKeys(fn ($u) => [$u->id => $u->name.' ('.($u->roles->pluck('name')->implode(', ') ?: 'sin rol').')'])
            ->all();

        // Si el usuario elegido no cumple el rol elegido, el filtro vuelve a "Todos".
        if ($usuarioElegido !== '' && ! isset($opcionesUsuario[$usuarioElegido])) {
            $usuarioElegido = '';
        }

        $nombresRoles = Role::orderBy('name')->pluck('name');
        $opcionesRol = $nombresRoles->combine($nombresRoles)->all();

        // Modo historial: el usuario vino desde el boton "Historial" de un
        // listado. El aviso muestra el nombre del registro ("Cliente: Mariana").
        $contexto = null;
        if ($request->filled('modelo')) {
            $contexto = $this->nombreRegistro($request->string('modelo')->toString(), $request->string('registro')->toString())
                ?? $this->etiquetaRegistro($request->string('modelo')->toString()).' #'.$request->string('registro')->toString();
        }

        return view('auditoria.index', compact('auditoria', 'opcionesRol', 'opcionesUsuario', 'usuarioElegido', 'contexto'));
    }

    /**
     * Titulo humano del registro auditado: el nombre/titulo que tenga el
     * propio registro (o "eliminado" si ya no existe en la tabla).
     */
    private function tituloRegistro(Audit $registro): string
    {
        return $this->nombreRegistro($registro->auditable_type, $registro->auditable_id)
            ?? $this->etiquetaRegistro($registro->auditable_type).' #'.$registro->auditable_id
            .($registro->event === 'deleted' ? ' (eliminado)' : '');
    }

    /**
     * Etiqueta legible del tipo auditado ("Cliente", "Solicitud de cambio").
     */
    private function etiquetaRegistro(string $tipo): string
    {
        return match ($tipo) {
            'App\Models\Tarea' => 'Tarea',
            'App\Models\Proyecto' => 'Proyecto',
            'App\Models\Hito' => 'Hito',
            'App\Models\Sprint' => 'Sprint',
            'App\Models\Cliente' => 'Cliente',
            'App\Models\Factura' => 'Factura',
            'App\Models\EntregableIA' => 'Entregable',
            'App\Models\SolicitudCambio' => 'Solicitud de cambio',
            'App\Models\User' => 'Usuario',
            default => class_basename($tipo),
        };
    }

    /**
     * Nombre humano del registro auditado ("Cliente: Mariana"), buscando el
     * registro vivito. Null si el tipo no existe o el registro ya fue borrado.
     */
    private function nombreRegistro(string $tipo, string $id): ?string
    {
        if (! class_exists($tipo) || ! $modelo = $tipo::find($id)) {
            return null;
        }

        $etiqueta = $this->etiquetaRegistro($tipo);

        foreach (['titulo', 'nombre', 'numero'] as $campo) {
            if (isset($modelo->{$campo}) && $modelo->{$campo} !== null) {
                return "{$etiqueta}: {$modelo->{$campo}}";
            }
        }

        if ($modelo instanceof User) {
            return "{$etiqueta}: {$modelo->name} {$modelo->apellido}";
        }

        return null;
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
