<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller da área do cliente com dados da conta e histórico de pedidos.
 */
class PerfilController extends Controller
{
    // Carrega o resumo da conta e a lista paginada de pedidos.
    public function index(Request $request): View
    {
        $usuario = $request->user();

        $pedidos = Pedido::query()
            ->with('produtoRelacionado')
            ->where('usuario', $usuario->getKey())
            ->orderByDesc('data_pedido')
            ->orderByDesc('id_pedido')
            ->paginate(6);

        return view('user.perfil.index', [
            'usuario' => $usuario,
            'pedidos' => $pedidos,
            'totalPedidos' => Pedido::query()->where('usuario', $usuario->getKey())->count(),
            'totalPecas' => (int) Pedido::query()->where('usuario', $usuario->getKey())->sum('quantidade'),
            'pedidosEmAndamento' => Pedido::query()
                ->where('usuario', $usuario->getKey())
                ->whereNotIn('status', ['finalizado', 'entregue', 'cancelado'])
                ->count(),
        ]);
    }
}
