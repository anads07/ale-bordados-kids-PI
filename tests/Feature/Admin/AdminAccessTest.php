<?php

namespace Tests\Feature\Admin;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    // Cria um usuário com a flag de admin conforme o e-mail.
    private function criarUsuario(string $email, bool $admin): Usuario
    {
        return Usuario::query()->create([
            'nome_usuario' => 'Usuario',
            'sobrenome_usuario' => 'Teste',
            'email' => $email,
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => $admin,
        ]);
    }

    // Visitantes são enviados ao login.
    public function test_visitante_e_redirecionado_para_o_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    // Cliente comum recebe 403.
    public function test_cliente_comum_recebe_403(): void
    {
        $cliente = $this->criarUsuario('cliente@example.test', false);

        $this->actingAs($cliente)->get('/admin')->assertForbidden();
    }

    // A flag is_admin sozinha não libera o painel sem o e-mail configurado.
    public function test_flag_admin_sem_email_configurado_recebe_403(): void
    {
        config(['admin.email' => 'admin@example.test']);
        $usuario = $this->criarUsuario('outro@example.test', true);

        $this->actingAs($usuario)->get('/admin')->assertForbidden();
    }

    // Administrador autorizado acessa o painel.
    public function test_admin_autorizado_acessa_o_painel(): void
    {
        config(['admin.email' => 'admin@example.test']);
        $admin = $this->criarUsuario('admin@example.test', true);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
