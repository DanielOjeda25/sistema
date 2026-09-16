<?php

/*
 |---------------------------------------------------------------
 | MODELO CLIENTE (tabla "clientes")
 |---------------------------------------------------------------
 | Representa a la empresa para la que trabajamos. Es la entidad
 | raiz: no depende de nadie (no tiene claves foraneas), pero de
 | ella dependen los proyectos, las cuentas de usuario cliente
 | y las facturas.
 |
 | Relaciones POO que ofrece:
 |   $cliente->proyectos   -> todos sus proyectos   (hasMany)
 |   $cliente->usuarios    -> cuentas de su empresa (hasMany User)
 |   $cliente->facturas    -> sus facturas          (hasMany)
 |
 | Ademas audita automaticamente cada cambio (laravel-auditing).
*/

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Cliente extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'empresa',
        'estado',
    ];

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class);
    }

    /**
     * Cuentas de usuario que representan a este cliente (rol Cliente).
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
