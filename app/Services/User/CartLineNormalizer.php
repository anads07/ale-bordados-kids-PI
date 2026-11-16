<?php

namespace App\Services\User;

use App\Models\Produto;

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
        return [
            'produto_id' => (int) $produto->id_pr,
            'quantidade' => $quantidade,
            'cor_produto' => $this->texto($personalizacao['cor_produto'] ?? null, 40)
                ?: (string) config('mock.personalizacao.cor_padrao', 'Branco'),
            'escrita' => $this->texto($personalizacao['escrita'] ?? null, 40),
            'cor_escrita' => $this->texto($personalizacao['cor_escrita'] ?? null, 40),
            'fonte_escrita' => $this->texto($personalizacao['fonte_escrita'] ?? null, 40),
            'desenho' => $this->texto($personalizacao['desenho'] ?? null, 30),
            'observacoes' => $this->texto($personalizacao['observacoes'] ?? null, 500),
            'imagem_inspiracao' => $this->texto($personalizacao['imagem_inspiracao'] ?? null, 255),
        ];
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

        return [
            'produto_id' => $idProduto,
            'quantidade' => $quantidade,
            'cor_produto' => $this->texto($linha['cor_produto'] ?? null, 40)
                ?: (string) config('mock.personalizacao.cor_padrao', 'Branco'),
            'escrita' => $this->texto($linha['escrita'] ?? null, 40),
            'cor_escrita' => $this->texto($linha['cor_escrita'] ?? null, 40),
            'fonte_escrita' => $this->texto($linha['fonte_escrita'] ?? null, 40),
            'desenho' => $this->texto($linha['desenho'] ?? null, 30),
            'observacoes' => $this->texto($linha['observacoes'] ?? null, 500),
            'imagem_inspiracao' => $this->texto($linha['imagem_inspiracao'] ?? null, 255),
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
