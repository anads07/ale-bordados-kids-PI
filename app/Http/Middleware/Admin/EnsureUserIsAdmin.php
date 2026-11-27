<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe a área administrativa ao usuário autorizado.
 */
class EnsureUserIsAdmin
{
    // Confirma a flag de admin e o e-mail configurado antes de seguir.
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdminAutorizado()) {
            abort(403, 'Acesso restrito ao administrador.');
        }

        return $next($request);
    }
}
