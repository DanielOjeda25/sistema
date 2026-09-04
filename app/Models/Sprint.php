<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Sprint extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'sprints';

    protected $fillable = [
        'proyecto_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Sprints visibles para el usuario: todos para los roles internos, solo
     * los de los proyectos de su empresa para un usuario con rol Cliente.
     */
    public function scopeVisiblePara(Builder $query, User $usuario): Builder
    {
        if ($usuario->esCliente()) {
            $query->whereHas('proyecto', fn ($q) => $q->where('cliente_id', $usuario->cliente_id));
        }

        return $query;
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }
}
