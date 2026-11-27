<?php

namespace Tests\Feature\User;

use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // Cria um produto simples para os testes de favoritos.
    private function criarProduto(string $nome = 'Body bordado'): Produto
    {
        return Produto::query()->create([
            'nome_produto' => $nome,
            'categoria' => 'Roupas',
            'preco' => 50,
            'estoque' => 5,
        ]);
    }

    // Alterna o favorito via JSON e devolve o novo estado.
    public function test_toggle_adiciona_e_remove_o_favorito(): void
    {
        $produto = $this->criarProduto();

        $this->postJson('/favoritos/'.$produto->id_pr)
            ->assertOk()
            ->assertJson([
                'product_id' => (int) $produto->id_pr,
                'favorited' => true,
                'favorites_count' => 1,
            ]);

        $this->postJson('/favoritos/'.$produto->id_pr)
            ->assertOk()
            ->assertJson([
                'favorited' => false,
                'favorites_count' => 0,
            ]);
    }

    // A página lista os produtos salvos na sessão.
    public function test_pagina_de_favoritos_lista_os_produtos_salvos(): void
    {
        $produto = $this->criarProduto('Toalha bordada');

        $response = $this->withSession(['favoritos' => [(int) $produto->id_pr]])
            ->get('/favoritos');

        $response->assertOk();
        $response->assertSee('Toalha bordada');
    }
}
