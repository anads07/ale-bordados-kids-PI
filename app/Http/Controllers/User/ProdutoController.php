<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\User\FavoriteService;
use Illuminate\View\View;

/**
 * Controller público da página de detalhes do produto.
 */
class ProdutoController extends Controller
{
    // Exibe o produto, seus relacionados e o estado de favorito.
    public function show(Produto $produto, FavoriteService $favoritos): View
    {
        $relacionados = Produto::query()
            ->where('categoria', $produto->categoria)
            ->where('id_pr', '!=', $produto->id_pr)
            ->orderBy('id_pr')
            ->limit(3)
            ->get();

        return view('user.produtos.show', [
            'produto' => $produto,
            'favoritado' => $favoritos->has($produto),
            'personalizacao' => config('mock.personalizacao', []),
            'relacionados' => $relacionados,
        ]);
    }
}
