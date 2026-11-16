<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloqueia clientes autenticados na área administrativa e libera apenas
 * usuários com a flag is_admin ativa e o e-mail administrativo configurado.
 */
class EnsureUserIsAdmin
{
    /**
     * Verifica as duas camadas de permissão antes de entregar a requisição.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        $emailAdministrativo = (string) config('admin.email');

        if (! $usuario?->is_admin || strcasecmp((string) $usuario->email, $emailAdministrativo) !== 0) {
            abort(403, 'Acesso restrito ao administrador.');
        }

        return $next($request);
    }
}
