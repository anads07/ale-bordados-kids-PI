<?php

namespace Tests\Feature\Admin;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PedidoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_array_date_query_falls_back_to_today_without_an_error(): void
    {
        config(['admin.email' => 'admin@example.test']);

        $admin = Usuario::query()->create([
            'nome_usuario' => 'Admin',
            'sobrenome_usuario' => 'Teste',
            'email' => 'admin@example.test',
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/pedidos?data[]=invalida');

        $response->assertOk();
    }
}
