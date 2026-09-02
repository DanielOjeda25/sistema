<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class EntregableIA extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'entregables_ia';

    protected $fillable = [
        'titulo',
        'contenido',
        'tipo',
        'origen',
        'modelo_ia',
        'version_prompt',
        'contexto_fuente',
        'estado',
        'visible_cliente',
        'generado_en',
        'proyecto_id',
        'generado_por',
        'aprobado_por',
        'aprobado_en',
        'mensaje_error',
    ];

    protected function casts(): array
    {
        return [
            'contexto_fuente' => 'array',
            'visible_cliente' => 'boolean',
            'generado_en' => 'datetime',
            'aprobado_en' => 'datetime',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Entregables visibles para el usuario: todos para los roles internos,
     * solo los de los proyectos de su empresa para un usuario con rol Cliente.
     */
    public function scopeVisiblePara(Builder $query, User $usuario): Builder
    {
        if ($usuario->esCliente()) {
            $query->whereHas('proyecto', fn ($q) => $q->where('cliente_id', $usuario->cliente_id))
                ->where(function ($q) {
                    $q->where('origen', 'manual')
                        ->orWhere(function ($informe) {
                            $informe->where('origen', 'ia')
                                ->where('visible_cliente', true)
                                ->where('estado', 'aprobado');
                        });
                });
        }

        return $query;
    }

    public function generador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generado_por');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
