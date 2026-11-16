<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\User\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller de favoritos: lista as peças salvas e alterna o estado via sessão.
 */
class FavoriteController extends Controller
{
    /**
     * Exibe os produtos favoritados pelo visitante.
     */
    public function index(FavoriteService $favoritos): View
    {
        return view('user.favoritos', [
            'produtos' => $favoritos->produtos(),
        ]);
    }

    /**
     * Alterna o favorito e responde em JSON ou por redirecionamento.
     */
    public function toggle(Request $request, Produto $produto, FavoriteService $favoritos): JsonResponse|RedirectResponse
    {
        $favoritado = $favoritos->toggle($produto);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'product_id' => (int) $produto->id_pr,
                'favorited' => $favoritado,
                'favorites_count' => $favoritos->count(),
            ]);
        }

        return back();
    }
}
