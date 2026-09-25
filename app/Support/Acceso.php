<?php

namespace App\Support;

use App\Models\User;

/**
 * Puerta unica hacia config/accesos.php: la UI pregunta aca en vez de
 * repetir @hasanyrole por cada vista.
 *
 *   Acceso::menu($usuario)            -> enlaces del sidebar filtrados por rol
 *   Acceso::puede('facturas', 'editar') -> accion puntual de un modulo
 */
class Acceso
{
    /**
     * Enlaces del sidebar que le corresponden al usuario, en el orden del
     * archivo de configuracion.
     */
    public static function menu(User $usuario): array
    {
        $roles = $usuario->getRoleNames()->all();

        return collect(config('accesos.menu'))
            ->filter(fn ($enlace) => count(array_intersect($enlace['roles'], $roles)) > 0)
            ->values()
            ->all();
    }

    /**
     * Puede el usuario ejecutar esa accion en ese modulo?
     */
    public static function puede(User $usuario, string $modulo, string $accion): bool
    {
        $rolesAccion = config("accesos.acciones.{$modulo}.{$accion}", []);

        return count(array_intersect($rolesAccion, $usuario->getRoleNames()->all())) > 0;
    }
}
