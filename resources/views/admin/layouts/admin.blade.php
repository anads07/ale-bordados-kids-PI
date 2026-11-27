<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Área administrativa do Alê Bordados Kids.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2" sizes="any">

    <title>@yield('title', 'Painel administrativo | '.config('mock.store.name'))</title>

    {{-- Estrutura da área administrativa, separada da vitrine pública. --}}
    @vite(['resources/css/app.css', 'resources/js/admin/app.js'])
</head>
<body class="admin-body" data-login-url="{{ route('login') }}">
    @php
        $navegacaoAdmin = config('mock.admin.nav', []);
    @endphp

    <header class="admin-header">
        <div class="container-app admin-header__inner">
            <a href="{{ route('admin.dashboard') }}" class="logo-script admin-logo" aria-label="Alê Bordados Kids — Painel administrativo">
                {{ config('mock.store.name') }}
                <img src="{{ asset('images/pagina_inicial/logo_costura.png') }}" alt="" class="logo-costura">
            </a>

            <nav class="admin-nav" aria-label="Navegação administrativa">
                @foreach ($navegacaoAdmin as $itemMenu)
                    @php
                        // Aceita uma ou mais rotas ativas por item de navegação.
                        $menuAtivo = $itemMenu['route'] && request()->routeIs(...(array) $itemMenu['active']);
                    @endphp

                    @if ($itemMenu['route'])
                        <a
                            href="{{ route($itemMenu['route']) }}"
                            class="admin-nav__link {{ $menuAtivo ? 'is-active' : '' }}"
                            @if ($menuAtivo) aria-current="page" @endif
                        >
                            {{ $itemMenu['label'] }}
                        </a>
                    @else
                        <span class="admin-nav__link is-disabled" aria-disabled="true" title="Cadastro de produtos em breve">
                            {{ $itemMenu['label'] }}
                        </span>
                    @endif

                    @if (! $loop->last)
                        <span class="admin-nav__separator" aria-hidden="true">|</span>
                    @endif
                @endforeach
            </nav>

            <div class="admin-header__actions">
                <span class="admin-user">Olá, {{ auth()->user()->nome_usuario }}</span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary admin-btn">Sair</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-main">
        @if (session('admin_status'))
            <div class="container-app admin-main__flash">
                <p class="admin-flash">{{ session('admin_status') }}</p>
            </div>
        @endif

        @if (session('admin_error'))
            <div class="container-app admin-main__flash">
                {{-- Alerta para operações bloqueadas, como excluir produto com pedidos. --}}
                <p class="admin-flash admin-flash--error">{{ session('admin_error') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <x-shared::coming-soon-dialog />
    <x-admin::chat-modal />

    <dialog id="admin-confirm-dialog" class="admin-confirm-dialog" aria-labelledby="admin-confirm-title">
        <div class="admin-confirm-dialog__content">
            <span class="admin-confirm-dialog__icon" aria-hidden="true">
                <x-lucide-trash-2 />
            </span>

            <h2 id="admin-confirm-title">Confirmar exclusão</h2>
            <p data-confirm-text>Deseja realmente continuar?</p>

            <div class="admin-confirm-dialog__actions">
                <button type="button" class="btn btn-outline" data-confirm-cancel>Cancelar</button>
                <button type="button" class="btn admin-confirm-dialog__accept" data-confirm-accept>Excluir</button>
            </div>
        </div>
    </dialog>
</body>
</html>
