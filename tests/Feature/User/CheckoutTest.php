<?php

namespace Tests\Feature\User;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    // Cria um cliente autenticável para o checkout.
    private function criarUsuario(): Usuario
    {
        return Usuario::query()->create([
            'nome_usuario' => 'Cliente',
            'sobrenome_usuario' => 'Teste',
            'email' => 'cliente@example.test',
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => false,
        ]);
    }

    // Cria um produto com o estoque informado.
    private function criarProduto(int $estoque, float $preco = 50): Produto
    {
        return Produto::query()->create([
            'nome_produto' => 'Body bordado',
            'categoria' => 'Roupas',
            'preco' => $preco,
            'estoque' => $estoque,
        ]);
    }

    // Monta uma linha no formato usado pela sessão do carrinho.
    private function linha(int $produtoId, int $quantidade): array
    {
        return [
            'produto_id' => $produtoId,
            'quantidade' => $quantidade,
            'cor_produto' => 'Branco',
            'escrita' => 'Ana',
            'cor_escrita' => 'Rosa',
            'fonte_escrita' => 'Cursiva',
            'desenho' => 'Ursinha',
            'observacoes' => null,
            'imagem_inspiracao' => null,
        ];
    }

    // Dados válidos de entrega e pagamento.
    private function dadosCheckout(): array
    {
        return [
            'cep' => '12345-678',
            'endereco' => 'Rua das Flores',
            'numero' => '10',
            'complemento' => '',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'sp',
            'forma_pagamento' => 'Pix',
        ];
    }

    // Visitantes são enviados ao login antes de finalizar.
    public function test_checkout_exige_autenticacao(): void
    {
        $this->get('/checkout')->assertRedirect(route('login'));
    }

    // Carrinho vazio volta para a vitrine com aviso.
    public function test_checkout_com_carrinho_vazio_redireciona_com_erro(): void
    {
        $response = $this->actingAs($this->criarUsuario())->get('/checkout');

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('carrinho');
    }

    // Cada linha personalizada vira um pedido e o estoque é baixado.
    public function test_checkout_cria_um_pedido_por_linha_e_baixa_o_estoque(): void
    {
        $usuario = $this->criarUsuario();
        $primeiro = $this->criarProduto(5);
        $segundo = $this->criarProduto(4, 80);

        $response = $this->actingAs($usuario)
            ->withSession(['carrinho' => [
                'linha-a' => $this->linha((int) $primeiro->id_pr, 2),
                'linha-b' => $this->linha((int) $segundo->id_pr, 1),
            ]])
            ->post('/checkout', $this->dadosCheckout());

        $response->assertRedirect(route('checkout.success'));
        $this->assertDatabaseCount('pedidos', 2);
        $this->assertSame(3, (int) $primeiro->fresh()->estoque);
        $this->assertSame(3, (int) $segundo->fresh()->estoque);
        $this->assertNull(session('carrinho'));
        $this->assertNotNull(session('checkout_sucesso'));
    }

    // Linhas do mesmo produto somam para a baixa de estoque.
    public function test_checkout_soma_linhas_do_mesmo_produto_antes_de_baixar_o_estoque(): void
    {
        $usuario = $this->criarUsuario();
        $produto = $this->criarProduto(2);

        $response = $this->actingAs($usuario)
            ->withSession(['carrinho' => [
                'linha-a' => $this->linha((int) $produto->id_pr, 1),
                'linha-b' => $this->linha((int) $produto->id_pr, 1),
            ]])
            ->post('/checkout', $this->dadosCheckout());

        $response->assertRedirect(route('checkout.success'));
        $this->assertDatabaseCount('pedidos', 2);
        $this->assertSame(0, (int) $produto->fresh()->estoque);
    }

    // Estoque insuficiente desfaz a transação e mantém o carrinho.
    public function test_checkout_com_estoque_insuficiente_faz_rollback(): void
    {
        $usuario = $this->criarUsuario();
        $produto = $this->criarProduto(1);

        $response = $this->actingAs($usuario)
            ->withSession(['carrinho' => [
                'linha-a' => $this->linha((int) $produto->id_pr, 3),
            ]])
            ->post('/checkout', $this->dadosCheckout());

        $response->assertSessionHasErrors('carrinho');
        $this->assertDatabaseCount('pedidos', 0);
        $this->assertSame(1, (int) $produto->fresh()->estoque);
        $this->assertNotNull(session('carrinho'));
    }

    // A confirmação só aparece quando existe um pedido recente na sessão.
    public function test_sucesso_sem_dados_redireciona_para_a_home(): void
    {
        $response = $this->actingAs($this->criarUsuario())->get('/checkout/sucesso');

        $response->assertRedirect(route('home'));
    }

    // Persiste os dados de entrega e pagamento informados.
    public function test_checkout_grava_endereco_e_pagamento_no_pedido(): void
    {
        $usuario = $this->criarUsuario();
        $produto = $this->criarProduto(3);

        $this->actingAs($usuario)
            ->withSession(['carrinho' => [
                'linha-a' => $this->linha((int) $produto->id_pr, 1),
            ]])
            ->post('/checkout', $this->dadosCheckout());

        $pedido = Pedido::query()->firstOrFail();

        $this->assertSame('SP', $pedido->estado);
        $this->assertSame('Pix', $pedido->forma_pagamento);
        $this->assertSame('novo', $pedido->status);
        $this->assertSame((int) $usuario->id_us, (int) $pedido->usuario);
    }
}
