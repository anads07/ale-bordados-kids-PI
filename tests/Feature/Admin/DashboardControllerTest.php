<?php

namespace Tests\Feature\Admin;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_array_status_query_renders_the_dashboard_without_an_error(): void
    {
        config(['admin.email' => 'admin@example.test']);

        $admin = Usuario::query()->create([
            'nome_usuario' => 'Admin',
            'sobrenome_usuario' => 'Teste',
            'email' => 'admin@example.test',
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin?status[]=novo');

        $response->assertOk();
    }
}
