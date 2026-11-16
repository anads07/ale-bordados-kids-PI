<?php

namespace Tests\Feature\Admin;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProdutoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_upload_rejects_an_unapproved_client_extension(): void
    {
        config(['admin.email' => 'admin@example.test']);

        $admin = Usuario::query()->create([
            'nome_usuario' => 'Admin',
            'sobrenome_usuario' => 'Teste',
            'email' => 'admin@example.test',
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/produtos', [
            'nome_produto' => 'Produto de teste',
            'categoria' => 'Roupas',
            'descricao' => 'Descrição de teste',
            'preco' => 25,
            'estoque' => 1,
            'imagem' => UploadedFile::fake()->image('produto.php'),
        ]);

        $response->assertSessionHasErrors('imagem');
    }
}
