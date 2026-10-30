<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Factura extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'facturas';

    protected $fillable = [
        'numero',
        'monto',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'detalle',
        'proyecto_id',
        'emitida_por',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Facturas visibles para el usuario: todas para los roles internos, solo
     * las de los proyectos de su empresa para un usuario con rol Cliente.
     */
    public function scopeVisiblePara(Builder $query, User $usuario): Builder
    {
        if ($usuario->esCliente()) {
            $query->whereHas('proyecto', fn ($q) => $q->where('cliente_id', $usuario->cliente_id));
        }

        return $query;
    }

    /**
     * Pasa a "vencida" las facturas pendientes cuyo vencimiento ya paso.
     * Se hace una por una (no con un UPDATE masivo) para que quede en la
     * auditoria. Devuelve cuantas cambio.
     */
    public static function marcarVencidas(): int
    {
        $facturas = static::where('estado', 'pendiente')
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<', today())
            ->get();

        $facturas->each->update(['estado' => 'vencida']);

        return $facturas->count();
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitida_por');
    }
}
