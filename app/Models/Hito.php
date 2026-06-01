<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Hito extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'hitos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_objetivo',
        'completado',
        'proyecto_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_objetivo' => 'date',
            'completado' => 'boolean',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }
}
