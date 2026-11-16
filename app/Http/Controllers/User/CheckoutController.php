<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\User\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller do checkout simulado: revisa o carrinho, grava cada linha
 * personalizada em pedidos, baixa o estoque e limpa a sacola.
 */
class CheckoutController extends Controller
{
    /**
     * Exibe a revisão do pedido ou redireciona quando o carrinho está vazio.
     */
    public function index(CartService $carrinho): View|RedirectResponse
    {
        $itens = $carrinho->itens();

        if ($itens->isEmpty()) {
            return redirect()->route('home')->withErrors(['carrinho' => 'Seu carrinho está vazio.']);
        }

        return view('user.checkout.index', [
            'itens' => $itens,
            'total' => $carrinho->total(),
            'totalFormatado' => $carrinho->totalFormatado(),
            'pagamentos' => config('mock.payments', []),
        ]);
    }

    /**
     * Finaliza a compra dentro de uma transação e baixa o estoque.
     */
    public function store(Request $request, CartService $carrinho): RedirectResponse
    {
        $itens = $carrinho->itens();

        if ($itens->isEmpty()) {
            return redirect()->route('home')->withErrors(['carrinho' => 'Seu carrinho está vazio.']);
        }

        $dados = $request->validate([
            'cep' => ['required', 'string', 'max:9'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],
            'forma_pagamento' => ['required', 'string', Rule::in(config('mock.payments', []))],
        ], [
            'cep.required' => 'Informe o CEP de entrega.',
            'cep.max' => 'O CEP deve ter no máximo 9 caracteres.',
            'endereco.required' => 'Informe o endereço de entrega.',
            'numero.required' => 'Informe o número do endereço.',
            'bairro.required' => 'Informe o bairro.',
            'cidade.required' => 'Informe a cidade.',
            'estado.required' => 'Informe o estado.',
            'estado.size' => 'Use a sigla do estado com 2 letras.',
            'forma_pagamento.required' => 'Selecione a forma de pagamento.',
            'forma_pagamento.in' => 'Selecione uma forma de pagamento válida.',
        ]);

        $identificadores = DB::transaction(function () use ($itens, $dados) {
            $necessidades = $itens
                ->groupBy(fn (array $item) => (int) $item['produto']->id_pr)
                ->map(fn ($linhas) => (int) $linhas->sum('quantidade'));

            $produtos = Produto::query()
                ->whereIn('id_pr', $necessidades->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id_pr');

            foreach ($necessidades as $idProduto => $quantidade) {
                $produto = $produtos->get($idProduto);

                if (! $produto || (int) $produto->estoque < $quantidade) {
                    throw ValidationException::withMessages([
                        'carrinho' => 'O estoque de uma das peças foi atualizado. Revise o carrinho.',
                    ]);
                }
            }

            $identificadores = [];

            foreach ($itens as $item) {
                $personalizacao = $item['personalizacao'];

                $pedido = Pedido::create([
                    'usuario' => Auth::id(),
                    'produto' => $item['produto']->id_pr,
                    'quantidade' => $item['quantidade'],
                    'cor_produto' => $personalizacao['cor_produto'],
                    'escrita' => $personalizacao['escrita'],
                    'cor_escrita' => $personalizacao['cor_escrita'],
                    'fonte_escrita' => $personalizacao['fonte_escrita'],
                    'desenho' => $personalizacao['desenho'],
                    'observacoes' => $personalizacao['observacoes'],
                    'imagem_inspiracao' => $item['imagem_inspiracao'],
                    'cep' => $dados['cep'],
                    'endereco' => $dados['endereco'],
                    'numero' => $dados['numero'],
                    'complemento' => $dados['complemento'] ?? null,
                    'bairro' => $dados['bairro'],
                    'cidade' => $dados['cidade'],
                    'estado' => strtoupper($dados['estado']),
                    'forma_pagamento' => $dados['forma_pagamento'],
                    'data_pedido' => now()->toDateString(),
                    'status' => 'novo',
                ]);

                $identificadores[] = (int) $pedido->id_pedido;
            }

            foreach ($necessidades as $idProduto => $quantidade) {
                $produtos->get($idProduto)?->decrement('estoque', $quantidade);
            }

            return $identificadores;
        });

        $carrinho->limpar();

        $request->session()->flash('checkout_sucesso', [
            'pedidos' => count($identificadores),
            'data' => now()->format('d/m/Y'),
        ]);

        return redirect()->route('checkout.success');
    }

    /**
     * Exibe a confirmação enquanto os dados estiverem na sessão.
     */
    public function success(Request $request): View|RedirectResponse
    {
        $dados = $request->session()->get('checkout_sucesso');

        if (! $dados) {
            return redirect()->route('home');
        }

        return view('user.checkout.success', [
            'pedidos' => (int) ($dados['pedidos'] ?? 0),
            'data' => $dados['data'] ?? now()->format('d/m/Y'),
        ]);
    }
}
