<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActualizacionProyecto extends Model
{
    use HasFactory;

    protected $table = 'actualizaciones_proyecto';

    protected $fillable = [
        'proyecto_id',
        'creado_por',
        'titulo',
        'descripcion',
        'tipo',
        'fecha',
        'visible_cliente',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'visible_cliente' => 'boolean',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
