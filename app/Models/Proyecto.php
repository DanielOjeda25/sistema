<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon\Carbon|\Illuminate\Support\Carbon\Interface|null $fecha_inicio
 * @property \Illuminate\Support\Carbon\Carbon|\Illuminate\Support\Carbon\Interface|null $fecha_fin_estimada
 * @property string|null $estado
 * @property int|null $cliente_id
 * @property int|null $pm_id
 */
class Proyecto extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin_estimada',
        'estado',
        'cliente_id',
        'pm_id',
    ];

    /**
     * Indica a Laravel qué campos del proyecto deben tratarse como fechas.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin_estimada' => 'date',
        ];
    }

    /**
     * Obtiene el cliente o empresa a la que pertenece el proyecto.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Limita la consulta a los proyectos que puede ver el usuario.
     * Los roles internos ven todos y el Cliente solo los de su empresa.
     */
    public function scopeVisiblePara(Builder $query, User $usuario): Builder
    {
        if ($usuario->esCliente()) {
            $query->where('cliente_id', $usuario->cliente_id);
        }

        return $query;
    }

    /**
     * Obtiene el usuario con rol de Project Manager responsable del proyecto.
     */
    public function pm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Obtiene todas las tareas que forman parte del proyecto.
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    /**
     * Obtiene los sprints utilizados para organizar el trabajo del proyecto.
     */
    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    /**
     * Obtiene los hitos que representan fechas u objetivos importantes.
     */
    public function hitos(): HasMany
    {
        return $this->hasMany(Hito::class);
    }

    /**
     * Obtiene los entregables manuales o generados mediante inteligencia artificial.
     */
    public function entregablesIa(): HasMany
    {
        return $this->hasMany(EntregableIA::class);
    }

    /**
     * Obtiene las facturas emitidas para el proyecto.
     */
    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Obtiene las novedades y actualizaciones registradas durante el proyecto.
     */
    public function actualizaciones(): HasMany
    {
        return $this->hasMany(ActualizacionProyecto::class);
    }

    /**
     * Obtiene las solicitudes de cambio asociadas al proyecto.
     */
    public function solicitudesCambio(): HasMany
    {
        return $this->hasMany(SolicitudCambio::class);
    }
}
