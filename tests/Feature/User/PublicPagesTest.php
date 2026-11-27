<?php

namespace Tests\Feature\User;

use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    // Cria um produto para a página de detalhes.
    private function criarProduto(): Produto
    {
        return Produto::query()->create([
            'nome_produto' => 'Body Ursinha',
            'categoria' => 'Roupas',
            'preco' => 50,
            'estoque' => 5,
        ]);
    }

    // A página institucional renderiza a identidade da marca.
    public function test_pagina_sobre_renderiza(): void
    {
        $response = $this->get('/sobre');

        $response->assertOk();
        $response->assertSee('Alê Bordados Kids');
    }

    // A página de orçamento mantém o aviso de funcionalidade em andamento.
    public function test_pagina_de_orcamento_renderiza(): void
    {
        $response = $this->get('/orcamento');

        $response->assertOk();
        $response->assertSee('Orçamento de peça própria');
        $response->assertSee('Função em andamento');
    }

    // A página de detalhes exibe o produto e o formulário de personalização.
    public function test_pagina_de_produto_renderiza_com_personalizacao(): void
    {
        $produto = $this->criarProduto();

        $response = $this->get('/produtos/'.$produto->id_pr);

        $response->assertOk();
        $response->assertSee('Body Ursinha');
        $response->assertSee('Adicionar ao carrinho');
    }
}
