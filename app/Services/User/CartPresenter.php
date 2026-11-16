<?php

namespace App\Services\User;

use App\Models\Produto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CartPresenter
{
    /**
     * @param  ConteudoCarrinho  $carrinho
     * @return Collection<int, ItemCarrinho>
     */
    public function itens(array $carrinho): Collection
    {
        if ($carrinho === []) {
            return collect();
        }

        $produtos = Produto::query()
            ->whereIn('id_pr', array_values(array_unique(array_column($carrinho, 'produto_id'))))
            ->get()
            ->keyBy('id_pr');

        return collect($carrinho)
            ->map(function (array $linha, string $idLinha) use ($produtos): ?array {
                $produto = $produtos->get($linha['produto_id']);

                if (! $produto) {
                    return null;
                }

                $subtotal = (float) $produto->preco * $linha['quantidade'];

                return [
                    'linha_id' => $idLinha,
                    'produto' => $produto,
                    'quantidade' => $linha['quantidade'],
                    'personalizacao' => [
                        'cor_produto' => $linha['cor_produto'],
                        'escrita' => $linha['escrita'],
                        'cor_escrita' => $linha['cor_escrita'],
                        'fonte_escrita' => $linha['fonte_escrita'],
                        'desenho' => $linha['desenho'],
                        'observacoes' => $linha['observacoes'],
                    ],
                    'imagem_inspiracao' => $linha['imagem_inspiracao'],
                    'imagem_inspiracao_url' => $this->urlImagem($linha['imagem_inspiracao']),
                    'subtotal' => $subtotal,
                    'subtotal_formatado' => $this->formatarValor($subtotal),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @param  Collection<int, ItemCarrinho>  $itens
     */
    public function total(Collection $itens): float
    {
        return (float) $itens->sum('subtotal');
    }

    public function formatarValor(float $valor): string
    {
        return 'R$ '.number_format($valor, 2, ',', '.');
    }

    private function urlImagem(?string $caminho): ?string
    {
        if (! $caminho || ! Storage::disk('public')->exists($caminho)) {
            return null;
        }

        return Storage::disk('public')->url($caminho);
    }
}
