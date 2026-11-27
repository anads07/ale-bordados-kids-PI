<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\User\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller do checkout: revisa o carrinho, grava os pedidos e baixa o estoque.
 */
class CheckoutController extends Controller
{
    private const MENSAGEM_ESTOQUE = 'O estoque de uma das peças foi atualizado. Revise o carrinho.';

    // Exibe a revisão do pedido ou redireciona quando o carrinho está vazio.
    public function index(CartService $carrinho): View|RedirectResponse
    {
        $itens = $carrinho->itens();

        if ($itens->isEmpty()) {
            return $this->redirecionarCarrinhoVazio();
        }

        return view('user.checkout.index', [
            'itens' => $itens,
            'total' => $carrinho->total(),
            'totalFormatado' => $carrinho->totalFormatado(),
            'pagamentos' => config('mock.payments', []),
        ]);
    }

    // Finaliza a compra em transação, baixa o estoque e limpa o carrinho.
    public function store(Request $request, CartService $carrinho): RedirectResponse
    {
        $itens = $carrinho->itens();

        if ($itens->isEmpty()) {
            return $this->redirecionarCarrinhoVazio();
        }

        $dados = $this->validarCheckout($request);

        $identificadores = DB::transaction(function () use ($itens, $dados) {
            $this->reservarEstoque($itens);

            return $this->criarPedidos($itens, $dados);
        });

        $carrinho->limpar();

        $request->session()->flash('checkout_sucesso', [
            'pedidos' => count($identificadores),
            'data' => now()->format('d/m/Y'),
        ]);

        return redirect()->route('checkout.success');
    }

    // Encaminha o visitante para a vitrine quando não há itens no carrinho.
    private function redirecionarCarrinhoVazio(): RedirectResponse
    {
        return redirect()->route('home')->withErrors(['carrinho' => 'Seu carrinho está vazio.']);
    }

    /**
     * Valida o endereço e a forma de pagamento escolhidos no checkout.
     *
     * @return array<string, string|null>
     */
    private function validarCheckout(Request $request): array
    {
        return $request->validate([
            'cep' => ['required', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'regex:/^[A-Za-z]{2}$/'],
            'forma_pagamento' => ['required', 'string', Rule::in(config('mock.payments', []))],
        ], [
            'cep.required' => 'Informe o CEP de entrega.',
            'cep.regex' => 'Informe um CEP válido.',
            'endereco.required' => 'Informe o endereço de entrega.',
            'numero.required' => 'Informe o número do endereço.',
            'bairro.required' => 'Informe o bairro.',
            'cidade.required' => 'Informe a cidade.',
            'estado.required' => 'Informe o estado.',
            'estado.regex' => 'Use a sigla do estado com 2 letras.',
            'forma_pagamento.required' => 'Selecione a forma de pagamento.',
            'forma_pagamento.in' => 'Selecione uma forma de pagamento válida.',
        ]);
    }

    /**
     * Trava os produtos e baixa o estoque somando todas as linhas do mesmo item.
     *
     * @param  Collection<int, array<string, mixed>>  $itens
     */
    private function reservarEstoque(Collection $itens): void
    {
        $necessidades = $this->necessidadesPorProduto($itens);

        $produtos = Produto::query()
            ->whereIn('id_pr', $necessidades->keys())
            ->lockForUpdate()
            ->get()
            ->keyBy('id_pr');

        foreach ($necessidades as $idProduto => $quantidade) {
            $produto = $produtos->get($idProduto);

            if (! $produto || (int) $produto->estoque < $quantidade) {
                throw ValidationException::withMessages(['carrinho' => self::MENSAGEM_ESTOQUE]);
            }
        }

        foreach ($necessidades as $idProduto => $quantidade) {
            $atualizado = Produto::query()
                ->whereKey($idProduto)
                ->where('estoque', '>=', $quantidade)
                ->decrement('estoque', $quantidade);

            if ($atualizado === 0) {
                throw ValidationException::withMessages(['carrinho' => self::MENSAGEM_ESTOQUE]);
            }
        }
    }

    /**
     * Cria um pedido por linha personalizada e devolve os identificadores.
     *
     * @param  Collection<int, array<string, mixed>>  $itens
     * @param  array<string, string|null>  $dados
     * @return array<int, int>
     */
    private function criarPedidos(Collection $itens, array $dados): array
    {
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

        return $identificadores;
    }

    /**
     * Soma a quantidade de cada produto considerando todas as linhas.
     *
     * @param  Collection<int, array<string, mixed>>  $itens
     * @return Collection<int, int>
     */
    private function necessidadesPorProduto(Collection $itens): Collection
    {
        return $itens
            ->groupBy(fn (array $item) => (int) $item['produto']->id_pr)
            ->map(fn ($linhas) => (int) $linhas->sum('quantidade'));
    }

    // Exibe a confirmação enquanto os dados estiverem na sessão.
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
