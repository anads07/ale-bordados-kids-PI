<?php

namespace App\Services\User;

use App\Models\Produto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Gerencia as linhas do carrinho na sessão e aplica os limites de estoque.
 *
 * @phpstan-import-type ConteudoCarrinho from CartLineNormalizer
 * @phpstan-import-type PersonalizacaoCarrinho from CartLineNormalizer
 * @phpstan-import-type ItemCarrinho from CartPresenter
 */
class CartService
{
    public const SESSION_KEY = 'carrinho';

    public function __construct(
        private CartLineNormalizer $normalizador,
        private CartPresenter $apresentador,
    ) {}

    /**
     * @return Collection<int, ItemCarrinho>
     */
    public function itens(): Collection
    {
        return $this->apresentador->itens($this->conteudo());
    }

    public function quantidadeTotal(): int
    {
        return (int) collect($this->conteudo())->sum('quantidade');
    }

    public function total(): float
    {
        return $this->apresentador->total($this->itens());
    }

    public function totalFormatado(): string
    {
        return $this->apresentador->formatarValor($this->total());
    }

    /**
     * Cria uma linha única e respeita o estoque somando todas as linhas
     * existentes do mesmo produto antes de salvar.
     *
     * @param  PersonalizacaoCarrinho  $personalizacao
     */
    public function adicionar(Produto $produto, array $personalizacao, int $quantidade = 1): string
    {
        $quantidade = max(1, $quantidade);
        $estoque = (int) $produto->estoque;

        if ($estoque <= 0) {
            throw ValidationException::withMessages([
                'carrinho' => 'Esta peça está sem estoque no momento.',
            ]);
        }

        $carrinho = $this->conteudo();
        $quantidadeNoCarrinho = $this->quantidadeDoProduto($carrinho, (int) $produto->id_pr);
        $disponivel = max(0, $estoque - $quantidadeNoCarrinho);

        if ($disponivel <= 0) {
            throw ValidationException::withMessages([
                'quantidade' => 'Você já adicionou todo o estoque disponível desta peça.',
            ]);
        }

        $idLinha = (string) Str::uuid();
        $carrinho[$idLinha] = $this->normalizador->montarLinha(
            $produto,
            $personalizacao,
            min($quantidade, $disponivel)
        );

        $this->salvar($carrinho);

        return $idLinha;
    }

    public function atualizar(string $idLinha, int $quantidade): void
    {
        $carrinho = $this->conteudo();

        if (! isset($carrinho[$idLinha])) {
            return;
        }

        if ($quantidade <= 0) {
            $this->remover($idLinha);

            return;
        }

        $linha = $carrinho[$idLinha];
        $produto = Produto::query()->find($linha['produto_id']);

        if (! $produto || (int) $produto->estoque <= 0) {
            $this->remover($idLinha);

            return;
        }

        $outrasLinhas = $this->quantidadeDoProduto(
            $carrinho,
            (int) $linha['produto_id'],
            $idLinha
        );
        $disponivel = max(0, (int) $produto->estoque - $outrasLinhas);

        if ($disponivel <= 0) {
            $this->remover($idLinha);

            return;
        }

        $carrinho[$idLinha]['quantidade'] = min($quantidade, $disponivel);

        $this->salvar($carrinho);
    }

    public function remover(string $idLinha): void
    {
        $carrinho = $this->conteudo();

        if (! isset($carrinho[$idLinha])) {
            return;
        }

        $imagem = $carrinho[$idLinha]['imagem_inspiracao'] ?? null;

        unset($carrinho[$idLinha]);
        $this->salvar($carrinho);

        if ($imagem) {
            Storage::disk('public')->delete($imagem);
        }
    }

    // Preserva as imagens, que podem estar vinculadas aos pedidos.
    public function limpar(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @return ConteudoCarrinho
     */
    private function conteudo(): array
    {
        return $this->normalizador->normalizarConteudo(
            session()->get(self::SESSION_KEY, [])
        );
    }

    /**
     * @param  ConteudoCarrinho  $carrinho
     */
    private function salvar(array $carrinho): void
    {
        session()->put(self::SESSION_KEY, $carrinho);
    }

    /**
     * @param  ConteudoCarrinho  $carrinho
     */
    private function quantidadeDoProduto(array $carrinho, int $idProduto, ?string $ignorarLinha = null): int
    {
        return (int) collect($carrinho)
            ->reject(fn (array $linha, string $idLinha) => $idLinha === $ignorarLinha)
            ->where('produto_id', $idProduto)
            ->sum('quantidade');
    }
}
