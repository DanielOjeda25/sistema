<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si a una persona con la sesion abierta le marcan la cuenta como inactiva,
 * la sesion se cierra en su proximo pedido (el login ya bloquea las nuevas).
 */
class CerrarSesionSiInactivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && $usuario->estado === 'inactivo') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta está inactiva. Hablá con el Jefe del sistema.']);
        }

        return $next($request);
    }
}
