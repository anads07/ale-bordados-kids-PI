<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller de autenticação: exibe login e cadastro, valida credenciais,
 * cria usuários na tabela oficial e encerra a sessão com segurança.
 */
class AuthController extends Controller
{
    /**
     * Exibe o login ou encaminha o usuário já autenticado.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirecionarUsuarioAutenticado(Auth::user());
        }

        // Só avisa sobre o checkout quando a visita ao login veio do próprio
        // checkout ou de uma tentativa de login que falhou.
        $intencao = (string) session('url.intended');
        $finalizandoCompra = str_contains($intencao, '/checkout')
            && (url()->previous() === route('checkout.index') || session()->has('_old_input'));

        return view('user.auth.login', [
            'finalizandoCompra' => $finalizandoCompra,
        ]);
    }

    /**
     * Tenta autenticar pelo e-mail e senha, regenera a sessão e encaminha
     * administradores para /admin e clientes para a vitrine.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credenciais = $request->validated();

        if (! Auth::attempt($credenciais)) {
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha inválidos.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirecionarUsuarioAutenticado($request->user());
    }

    /**
     * Exibe o cadastro ou encaminha o usuário já autenticado.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirecionarUsuarioAutenticado(Auth::user());
        }

        return view('user.auth.register');
    }

    /**
     * Cria o usuário comum, autentica automaticamente e preserva a sessão
     * de carrinho/favoritos que já existia antes do cadastro.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        [$nome, $sobrenome] = $request->partesDoNome();
        $dados = $request->validated();

        $usuario = Usuario::create([
            'nome_usuario' => $nome,
            'sobrenome_usuario' => $sobrenome,
            'email' => $dados['email'],
            'senha' => $dados['password'],
            'is_admin' => false,
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        // Cadastro partiu do checkout: sinaliza para reabrir o modal de endereço.
        if (str_contains((string) session('url.intended'), '/checkout')) {
            session()->flash('checkout_pos_login', true);
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Encerra a autenticação e invalida a sessão para evitar reutilização do token.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // Centraliza o destino pós-login conforme o perfil do usuário e preserva
    // o destino original quando o acesso partiu de uma rota protegida.
    private function redirecionarUsuarioAutenticado(?Usuario $usuario): RedirectResponse
    {
        $destinoPadrao = $usuario?->is_admin
            ? route('admin.dashboard')
            : route('home');

        // Voltando ao checkout via login/cadastro, sinaliza para a página
        // abrir sozinha o modal de endereço e pagamento.
        if (str_contains((string) session('url.intended'), '/checkout')) {
            session()->flash('checkout_pos_login', true);
        }

        return redirect()->intended($destinoPadrao);
    }
}
