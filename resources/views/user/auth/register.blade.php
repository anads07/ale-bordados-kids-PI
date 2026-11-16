@extends('user.layouts.public')

@section('title', 'Criar conta | '.config('mock.store.name'))

{{-- Formulário de cadastro com nome, e-mail e senha. --}}
@section('content')
    <section class="auth-page">
        <div class="container-app">
            <a href="{{ route('home') }}" class="back-link">
                <x-lucide-chevron-left aria-hidden="true" />
                Voltar / Início
            </a>

            <div class="auth-card">
                <div class="auth-card__heading">
                    <p class="auth-card__eyebrow">Faça parte</p>
                    <h1>Criar uma conta</h1>
                </div>

                <form action="{{ route('register.store') }}" method="POST" class="auth-form">
                    @csrf

                    <div class="auth-field">
                        <label for="register-name">Nome completo</label>
                        <input
                            id="register-name"
                            class="campo @error('nome_completo') campo-erro @enderror"
                            type="text"
                            name="nome_completo"
                            value="{{ old('nome_completo') }}"
                            autocomplete="name"
                            maxlength="70"
                            required
                        >
                        @error('nome_completo')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="register-email">E-mail</label>
                        <input
                            id="register-email"
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
                        <label for="register-password">Senha</label>
                        <input
                            id="register-password"
                            class="campo @error('password') campo-erro @enderror"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            minlength="6"
                            required
                        >
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="register-password-confirmation">Confirmar senha</label>
                        <input
                            id="register-password-confirmation"
                            class="campo"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            minlength="6"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary auth-submit">Cadastrar</button>
                </form>

                <p class="auth-switch">
                    Já tem conta?
                    <a href="{{ route('login') }}">Faça login</a>
                </p>
            </div>
        </div>
    </section>
@endsection
