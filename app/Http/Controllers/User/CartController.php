<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Cart\AddToCartRequest;
use App\Models\Produto;
use App\Services\User\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Controller do carrinho: cria, atualiza e remove linhas personalizadas.
 */
class CartController extends Controller
{
    // Adiciona uma peça personalizada e remove a imagem se o carrinho falhar.
    public function adicionar(AddToCartRequest $request, Produto $produto, CartService $carrinho): JsonResponse|RedirectResponse
    {
        $dados = $request->validated();
        $imagem = $request->file('imagem_inspiracao')?->store('inspiracoes', 'public');

        $personalizacao = [
            'cor_produto' => $dados['cor_produto'],
            'escrita' => $dados['escrita'] ?? null,
            'cor_escrita' => $dados['cor_escrita'] ?? null,
            'fonte_escrita' => $dados['fonte_escrita'] ?? null,
            'desenho' => $dados['desenho'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'imagem_inspiracao' => $imagem,
        ];

        try {
            $carrinho->adicionar($produto, $personalizacao, (int) $dados['quantidade']);
        } catch (Throwable $erro) {
            if ($imagem) {
                Storage::disk('public')->delete($imagem);
            }

            throw $erro;
        }

        return $this->responder($request, $carrinho);
    }

    // Atualiza a quantidade de uma linha existente.
    public function atualizar(Request $request, string $linha, CartService $carrinho): JsonResponse|RedirectResponse
    {
        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $carrinho->atualizar($linha, (int) $dados['quantidade']);

        return $this->responder($request, $carrinho);
    }

    // Remove uma linha do carrinho.
    public function remover(Request $request, string $linha, CartService $carrinho): JsonResponse|RedirectResponse
    {
        $carrinho->remover($linha);

        return $this->responder($request, $carrinho);
    }

    // Mantém JSON para AJAX e redirect para formulários comuns.
    private function responder(Request $request, CartService $carrinho): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'cart_count' => $carrinho->quantidadeTotal(),
                'cart_total' => $carrinho->total(),
                'cart_total_formatado' => $carrinho->totalFormatado(),
                'items_html' => view('user.partials.cart-items', ['itens' => $carrinho->itens()])->render(),
            ]);
        }

        return back();
    }
}
