<?php

namespace Tests\Feature\User;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // Cria um usuário com senha já criptografada.
    private function criarUsuario(string $email, bool $admin = false): Usuario
    {
        return Usuario::query()->create([
            'nome_usuario' => 'Cliente',
            'sobrenome_usuario' => 'Teste',
            'email' => $email,
            'senha' => Hash::make('senha-segura-123'),
            'is_admin' => $admin,
        ]);
    }

    // Autentica o cliente comum e encaminha para a vitrine.
    public function test_login_valido_autentica_e_redireciona_cliente_para_a_home(): void
    {
        $usuario = $this->criarUsuario('cliente@example.test');

        $response = $this->post('/login', [
            'email' => 'cliente@example.test',
            'password' => 'senha-segura-123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($usuario);
    }

    // Encaminha para o painel apenas quando a flag e o e-mail conferem.
    public function test_admin_autorizado_e_redirecionado_para_o_painel(): void
    {
        config(['admin.email' => 'admin@example.test']);
        $admin = $this->criarUsuario('admin@example.test', true);

        $response = $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'senha-segura-123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    // A flag is_admin sozinha não libera o painel quando o e-mail diverge.
    public function test_flag_admin_com_email_divergente_nao_redireciona_para_o_painel(): void
    {
        config(['admin.email' => 'admin@example.test']);
        $this->criarUsuario('outro@example.test', true);

        $response = $this->post('/login', [
            'email' => 'outro@example.test',
            'password' => 'senha-segura-123',
        ]);

        $response->assertRedirect(route('home'));
    }

    // Credenciais incorretas retornam erro de validação sem autenticar.
    public function test_credenciais_invalidas_retornam_erro_de_validacao(): void
    {
        $this->criarUsuario('cliente@example.test');

        $response = $this->from('/login')->post('/login', [
            'email' => 'cliente@example.test',
            'password' => 'senha-errada',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // O logout encerra a sessão autenticada.
    public function test_logout_encerra_a_sessao(): void
    {
        $usuario = $this->criarUsuario('cliente@example.test');

        $response = $this->actingAs($usuario)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
