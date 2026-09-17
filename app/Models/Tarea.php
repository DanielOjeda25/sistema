<?php

/*
 |---------------------------------------------------------------
 | MODELO TAREA (tabla "tareas")
 |---------------------------------------------------------------
 | Es el ejemplo de tabla CON relaciones: una tarea no existe sola,
 | siempre apunta a otras tablas mediante claves foraneas:
 |
 |   proyecto_id         -> a que proyecto pertenece  (belongsTo Proyecto)
 |   asignado_a          -> quien la trabaja          (belongsTo User)
 |   sprint_id           -> en que etapa/sprint esta  (belongsTo Sprint)
 |   solicitud_cambio_id -> si nacio de un pedido del cliente (opcional)
 |
 | Relaciones POO:
 |   $tarea->proyecto, $tarea->asignado, $tarea->sprint,
 |   $tarea->solicitudCambio   (belongsTo)
 |   Proyecto::tareas() es el lado inverso (hasMany)
 |
 | Ademas: scope visiblePara (un Cliente solo ve las tareas de su
 | empresa) y auditoria automatica de cada cambio.
*/

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Tarea extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tareas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'fecha_limite',
        'proyecto_id',
        'sprint_id',
        'asignado_a',
        'solicitud_cambio_id',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'fecha_limite' => 'date',
            'orden' => 'integer',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    /**
     * Tareas visibles para el usuario: todas para los roles internos, solo las
     * de los proyectos de su empresa para un usuario con rol Cliente.
     */
    public function scopeVisiblePara(Builder $query, User $usuario): Builder
    {
        if ($usuario->esCliente()) {
            $query->whereHas('proyecto', fn ($q) => $q->where('cliente_id', $usuario->cliente_id));
        }

        return $query;
    }

    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function solicitudCambio(): BelongsTo
    {
        return $this->belongsTo(SolicitudCambio::class, 'solicitud_cambio_id');
    }
}
