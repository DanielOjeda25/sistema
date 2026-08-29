<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'cliente_id',
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

    /**
     * El cliente de la empresa que este usuario representa (solo usuarios con
     * rol Cliente; los internos lo tienen en NULL).
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function esCliente(): bool
    {
        return $this->hasRole('Cliente');
    }

    /**
     * Puede este usuario ver el registro dado? Los roles internos ven todo;
     * un Cliente solo ve los registros cuyo proyecto pertenece a su empresa.
     */
    public function puedeVer(Model $modelo): bool
    {
        if (! $this->esCliente()) {
            return true;
        }

        $clienteId = $modelo instanceof Proyecto
            ? $modelo->cliente_id
            : $modelo->proyecto?->cliente_id;

        return $clienteId !== null && $clienteId === $this->cliente_id;
    }

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
