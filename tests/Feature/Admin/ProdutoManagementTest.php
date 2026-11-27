<?php

namespace Tests\Feature\Admin;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProdutoManagementTest extends TestCase
{
    use RefreshDatabase;

    // Cria o administrador autorizado usado nas requisições.
    private function criarAdmin(): Usuario
    {
        config(['admin.email' => 'admin@example.test']);

        return Usuario::query()->create([
            'nome_usuario' => 'Admin',
            'sobrenome_usuario' => 'Teste',
            'email' => 'admin@example.test',
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => true,
        ]);
    }

    // Cria um produto vinculável a pedidos.
    private function criarProduto(): Produto
    {
        return Produto::query()->create([
            'nome_produto' => 'Body bordado',
            'categoria' => 'Roupas',
            'preco' => 50,
            'estoque' => 5,
        ]);
    }

    // Produtos com pedidos vinculados não podem ser excluídos.
    public function test_produto_com_pedidos_nao_pode_ser_excluido(): void
    {
        $admin = $this->criarAdmin();
        $produto = $this->criarProduto();

        Pedido::query()->create([
            'usuario' => (int) $admin->id_us,
            'produto' => (int) $produto->id_pr,
            'quantidade' => 1,
            'cor_produto' => 'Branco',
            'data_pedido' => now()->toDateString(),
            'status' => 'novo',
        ]);

        $response = $this->actingAs($admin)->delete('/admin/produtos/'.$produto->id_pr);

        $response->assertSessionHas('admin_error');
        $this->assertDatabaseHas('produtos', ['id_pr' => $produto->id_pr]);
    }

    // Produtos sem vínculos são removidos normalmente.
    public function test_produto_sem_pedidos_pode_ser_excluido(): void
    {
        $admin = $this->criarAdmin();
        $produto = $this->criarProduto();

        $response = $this->actingAs($admin)->delete('/admin/produtos/'.$produto->id_pr);

        $response->assertRedirect(route('admin.produtos.index'));
        $this->assertDatabaseMissing('produtos', ['id_pr' => $produto->id_pr]);
    }
}
