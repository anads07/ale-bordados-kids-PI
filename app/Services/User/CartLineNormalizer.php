<?php

namespace App\Services\User;

use App\Models\Produto;

/**
 * Normaliza e monta as linhas do carrinho guardadas na sessão.
 *
 * @phpstan-type LinhaCarrinho array{
 *     produto_id: int,
 *     quantidade: int,
 *     cor_produto: string,
 *     escrita: string|null,
 *     cor_escrita: string|null,
 *     fonte_escrita: string|null,
 *     desenho: string|null,
 *     observacoes: string|null,
 *     imagem_inspiracao: string|null
 * }
 * @phpstan-type PersonalizacaoCarrinho array<string, mixed>
 * @phpstan-type ConteudoCarrinho array<string, LinhaCarrinho>
 */
class CartLineNormalizer
{
    /**
     * @return ConteudoCarrinho
     */
    public function normalizarConteudo(mixed $dados): array
    {
        if (! is_array($dados)) {
            return [];
        }

        $carrinho = [];

        foreach ($dados as $chave => $valor) {
            if (is_int($chave) || ctype_digit((string) $chave)) {
                if (! is_numeric($valor) || (int) $valor <= 0) {
                    continue;
                }

                $linha = $this->normalizarLinha([
                    'produto_id' => (int) $chave,
                    'quantidade' => (int) $valor,
                ]);

                if ($linha !== null) {
                    $carrinho['legacy-'.$chave] = $linha;
                }

                continue;
            }

            if (! is_array($valor)) {
                continue;
            }

            $linha = $this->normalizarLinha($valor);

            if ($linha !== null) {
                $carrinho[(string) $chave] = $linha;
            }
        }

        return $carrinho;
    }

    /**
     * @param  PersonalizacaoCarrinho  $personalizacao
     * @return LinhaCarrinho
     */
    public function montarLinha(Produto $produto, array $personalizacao, int $quantidade): array
    {
        return $this->linha((int) $produto->id_pr, $quantidade, $personalizacao);
    }

    /**
     * @param  array<string, mixed>  $linha
     * @return LinhaCarrinho|null
     */
    private function normalizarLinha(array $linha): ?array
    {
        $idProduto = (int) ($linha['produto_id'] ?? 0);
        $quantidade = (int) ($linha['quantidade'] ?? 0);

        if ($idProduto <= 0 || $quantidade <= 0) {
            return null;
        }

        return $this->linha($idProduto, $quantidade, $linha);
    }

    /**
     * @param  array<string, mixed>  $dados
     * @return LinhaCarrinho
     */
    private function linha(int $idProduto, int $quantidade, array $dados): array
    {
        return [
            'produto_id' => $idProduto,
            'quantidade' => $quantidade,
            'cor_produto' => $this->texto($dados['cor_produto'] ?? null, 40)
                ?: (string) config('mock.personalizacao.cor_padrao', 'Branco'),
            'escrita' => $this->texto($dados['escrita'] ?? null, 40),
            'cor_escrita' => $this->texto($dados['cor_escrita'] ?? null, 40),
            'fonte_escrita' => $this->texto($dados['fonte_escrita'] ?? null, 40),
            'desenho' => $this->texto($dados['desenho'] ?? null, 30),
            'observacoes' => $this->texto($dados['observacoes'] ?? null, 500),
            'imagem_inspiracao' => $this->texto($dados['imagem_inspiracao'] ?? null, 255),
        ];
    }

    private function texto(mixed $valor, int $limite): ?string
    {
        if (! is_string($valor)) {
            return null;
        }

        $valor = trim($valor);

        return $valor === '' ? null : mb_substr($valor, 0, $limite);
    }
}
