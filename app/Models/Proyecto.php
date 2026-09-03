<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

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

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin_estimada' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Proyectos visibles para el usuario: todos para los roles internos,
     * solo los de su empresa para un usuario con rol Cliente.
     */
    public function scopeVisiblePara(Builder $query, User $usuario): Builder
    {
        if ($usuario->esCliente()) {
            $query->where('cliente_id', $usuario->cliente_id);
        }

        return $query;
    }

    public function pm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    public function hitos(): HasMany
    {
        return $this->hasMany(Hito::class);
    }

    public function entregablesIa(): HasMany
    {
        return $this->hasMany(EntregableIA::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function actualizaciones(): HasMany
    {
        return $this->hasMany(ActualizacionProyecto::class);
    }

    public function solicitudesCambio(): HasMany
    {
        return $this->hasMany(SolicitudCambio::class);
    }
}
