<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido',
        'email',
        'estado',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===== Relaciones inversas =====
    // Cada FK que apunta a "users" en otras tablas tiene aca su hasMany.

    public function proyectosComoPm(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'pm_id');
    }

    public function tareasAsignadas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'asignado_a');
    }

    public function solicitudesRealizadas(): HasMany
    {
        return $this->hasMany(SolicitudCambio::class, 'solicitado_por');
    }

    public function entregablesGenerados(): HasMany
    {
        return $this->hasMany(EntregableIA::class, 'generado_por');
    }

    public function facturasEmitidas(): HasMany
    {
        return $this->hasMany(Factura::class, 'emitida_por');
    }
}
