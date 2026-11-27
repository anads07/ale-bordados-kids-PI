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
 * Controller de autenticação: login, cadastro e logout.
 */
class AuthController extends Controller
{
    // Exibe o login ou encaminha o usuário já autenticado.
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirecionarUsuarioAutenticado(Auth::user());
        }

        // Avisa sobre o checkout apenas quando a visita veio do próprio checkout.
        $intencao = (string) session('url.intended');
        $finalizandoCompra = str_contains($intencao, '/checkout')
            && (url()->previous() === route('checkout.index') || session()->has('_old_input'));

        return view('user.auth.login', [
            'finalizandoCompra' => $finalizandoCompra,
        ]);
    }

    // Autentica, regenera a sessão e encaminha admin ou cliente.
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

    // Exibe o cadastro ou encaminha o usuário já autenticado.
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirecionarUsuarioAutenticado(Auth::user());
        }

        return view('user.auth.register');
    }

    // Cria o usuário comum e preserva carrinho/favoritos da sessão.
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

        // Cadastro partiu do checkout: sinaliza para reabrir o modal.
        if (str_contains((string) session('url.intended'), '/checkout')) {
            session()->flash('checkout_pos_login', true);
        }

        return redirect()->intended(route('home'));
    }

    // Encerra a autenticação e invalida a sessão.
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // Define o destino pós-login e preserva a rota protegida original.
    private function redirecionarUsuarioAutenticado(?Usuario $usuario): RedirectResponse
    {
        $destinoPadrao = $usuario?->isAdminAutorizado()
            ? route('admin.dashboard')
            : route('home');

        // Voltando ao checkout, sinaliza para abrir o modal automaticamente.
        if (str_contains((string) session('url.intended'), '/checkout')) {
            session()->flash('checkout_pos_login', true);
        }

        return redirect()->intended($destinoPadrao);
    }
}
