<?php

namespace Tests\Feature\User;

use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    // Cria um produto para os filtros da vitrine.
    private function criarProduto(string $nome, string $categoria, float $preco): Produto
    {
        return Produto::query()->create([
            'nome_produto' => $nome,
            'categoria' => $categoria,
            'preco' => $preco,
            'estoque' => 5,
        ]);
    }

    // Filtra os produtos pelo termo de busca.
    public function test_busca_filtra_os_produtos_pelo_nome(): void
    {
        $this->criarProduto('Body Ursinha', 'Roupas', 50);
        $this->criarProduto('Toalha Nuvem', 'Toalhas', 120);

        $response = $this->get('/?q=Ursinha');

        $response->assertOk();
        $response->assertSee('Body Ursinha');
        $response->assertDontSee('Toalha Nuvem');
    }

    // Filtra pela categoria informada.
    public function test_categoria_filtra_os_produtos(): void
    {
        $this->criarProduto('Body Ursinha', 'Roupas', 50);
        $this->criarProduto('Toalha Nuvem', 'Toalhas', 120);

        $response = $this->get('/?categoria=Roupas');

        $response->assertOk();
        $response->assertSee('Body Ursinha');
        $response->assertDontSee('Toalha Nuvem');
    }

    // Respeita a faixa de preço máxima.
    public function test_faixa_de_preco_limita_os_resultados(): void
    {
        $this->criarProduto('Body Ursinha', 'Roupas', 50);
        $this->criarProduto('Toalha Nuvem', 'Toalhas', 120);

        $response = $this->get('/?preco_max=100');

        $response->assertOk();
        $response->assertSee('Body Ursinha');
        $response->assertDontSee('Toalha Nuvem');
    }

    // Parâmetros enviados como array não quebram a vitrine.
    public function test_parametros_em_array_nao_quebram_a_vitrine(): void
    {
        $this->criarProduto('Body Ursinha', 'Roupas', 50);

        $response = $this->get('/?q[]=x&categoria[]=y&preco_min[]=1&preco_max[]=2&ordem[]=z');

        $response->assertOk();
    }
}
