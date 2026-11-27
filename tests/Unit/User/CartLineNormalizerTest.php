<?php

namespace Tests\Unit\User;

use App\Models\Produto;
use App\Services\User\CartLineNormalizer;
use Tests\TestCase;

class CartLineNormalizerTest extends TestCase
{
    // Monta a linha com os campos esperados e aplica o limite de texto.
    public function test_montar_linha_normaliza_campos_e_trunca_textos_longos(): void
    {
        $produto = new Produto;
        $produto->setAttribute('id_pr', 9);

        $linha = app(CartLineNormalizer::class)->montarLinha($produto, [
            'cor_produto' => '  Rosa  ',
            'escrita' => str_repeat('A', 50),
            'desenho' => str_repeat('B', 40),
        ], 2);

        $this->assertSame(9, $linha['produto_id']);
        $this->assertSame(2, $linha['quantidade']);
        $this->assertSame('Rosa', $linha['cor_produto']);
        $this->assertSame(str_repeat('A', 40), $linha['escrita']);
        $this->assertSame(str_repeat('B', 30), $linha['desenho']);
        $this->assertNull($linha['cor_escrita']);
    }

    // Usa a cor padrão quando a personalização não informa a cor da peça.
    public function test_montar_linha_usa_cor_padrao_quando_ausente(): void
    {
        $produto = new Produto;
        $produto->setAttribute('id_pr', 4);

        $linha = app(CartLineNormalizer::class)->montarLinha($produto, [], 1);

        $this->assertSame((string) config('mock.personalizacao.cor_padrao'), $linha['cor_produto']);
    }

    // Converte linhas legadas e descarta registros inválidos.
    public function test_normalizar_conteudo_converte_linhas_legadas_e_ignora_invalidas(): void
    {
        $conteudo = app(CartLineNormalizer::class)->normalizarConteudo([
            12 => 3,
            'linha-nova' => [
                'produto_id' => 15,
                'quantidade' => 1,
                'cor_produto' => 'Azul',
            ],
            'sem-produto' => ['produto_id' => 0, 'quantidade' => 2],
            'quantidade-zero' => ['produto_id' => 8, 'quantidade' => 0],
            'texto-solto' => 'invalido',
        ]);

        $this->assertArrayHasKey('legacy-12', $conteudo);
        $this->assertSame(3, $conteudo['legacy-12']['quantidade']);
        $this->assertArrayHasKey('linha-nova', $conteudo);
        $this->assertSame('Azul', $conteudo['linha-nova']['cor_produto']);
        $this->assertArrayNotHasKey('sem-produto', $conteudo);
        $this->assertArrayNotHasKey('quantidade-zero', $conteudo);
        $this->assertArrayNotHasKey('texto-solto', $conteudo);
    }
}
