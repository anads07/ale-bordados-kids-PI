@extends('user.layouts.public')

@section('title', 'Entrar | '.config('mock.store.name'))

{{-- Formulário de acesso com validação e opção de cadastro. --}}
@section('content')
    <section class="auth-page">
        <div class="container-app">
            <a href="{{ route('home') }}" class="back-link">
                <x-lucide-chevron-left aria-hidden="true" />
                Voltar / Início
            </a>

            <div class="auth-card">
                <div class="auth-card__heading">
                    <p class="auth-card__eyebrow">Bem-vinda de volta</p>
                    <h1>Entrar em sua conta</h1>
                </div>

                @if ($finalizandoCompra)
                    <p class="auth-card__notice">
                        Entre para finalizar sua compra — suas peças continuam guardadas na sacola.
                    </p>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="auth-form">
                    @csrf

                    <div class="auth-field">
                        <label for="login-email">E-mail</label>
                        <input
                            id="login-email"
                            class="campo @error('email') campo-erro @enderror"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            maxlength="50"
                            required
                        >
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="login-password">Senha</label>
                        <input
                            id="login-password"
                            class="campo @error('password') campo-erro @enderror"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-forgot">
                        <button type="button" class="link-button" data-coming-soon="Recuperação de senha">
                            Esqueceu sua senha?
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary auth-submit">Entrar</button>
                </form>

                <p class="auth-switch">
                    Não tem conta?
                    <a href="{{ route('register') }}">Cadastre-se</a>
                </p>
            </div>
        </div>
    </section>
@endsection
