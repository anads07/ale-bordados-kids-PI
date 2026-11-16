<?php

namespace Tests\Feature\User;

use App\Models\Produto;
use App\Services\User\CartService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    public function test_additions_with_different_customizations_respect_available_stock(): void
    {
        $produto = new Produto;
        $produto->setAttribute('id_pr', 7);
        $produto->setAttribute('estoque', 5);

        $carrinho = app(CartService::class);
        $primeiraLinha = $carrinho->adicionar($produto, ['escrita' => 'Ana'], 3);
        $segundaLinha = $carrinho->adicionar($produto, ['escrita' => 'Bia'], 4);
        $linhas = session()->get(CartService::SESSION_KEY);

        $this->assertSame(3, $linhas[$primeiraLinha]['quantidade']);
        $this->assertSame(2, $linhas[$segundaLinha]['quantidade']);
        $this->assertSame(5, $carrinho->quantidadeTotal());
    }

    public function test_legacy_and_invalid_session_rows_are_normalized_before_counting(): void
    {
        session()->put(CartService::SESSION_KEY, [
            12 => 2,
            'linha-invalida' => ['produto_id' => 0, 'quantidade' => 3],
            'valor-invalido' => 'sem quantidade',
        ]);

        $this->assertSame(2, app(CartService::class)->quantidadeTotal());
    }

    public function test_removing_a_line_deletes_its_inspiration_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('inspiracoes/referencia.png', 'imagem');

        session()->put(CartService::SESSION_KEY, [
            'linha-teste' => [
                'produto_id' => 3,
                'quantidade' => 1,
                'imagem_inspiracao' => 'inspiracoes/referencia.png',
            ],
        ]);

        app(CartService::class)->remover('linha-teste');

        Storage::disk('public')->assertMissing('inspiracoes/referencia.png');
        $this->assertSame([], session()->get(CartService::SESSION_KEY));
    }
}
