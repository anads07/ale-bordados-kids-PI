{{-- Cabeçalho compartilhado com busca, navegação, conta, favoritos e carrinho. --}}
<header class="site-header" data-site-header>
    <div class="container-app header-inner">
        <a href="{{ route('home') }}" class="logo-script" aria-label="Alê Bordados Kids — Início">
        {{ config('mock.store.name') }}
        <img src="{{ asset('images/pagina_inicial/logo_costura.png') }}" alt="" class="logo-costura">
        </a>

        <form class="search-form" action="{{ route('home') }}" method="GET" role="search" data-filter-form>
            <label class="sr-only" for="busca-produtos">Buscar produtos</label>
            <input
                id="busca-produtos"
                class="campo"
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Buscar produtos...."
                autocomplete="off"
            >
            <button class="search-button" type="submit" aria-label="Buscar produtos">
                <x-lucide-search aria-hidden="true" />
            </button>
        </form>

        <button
            class="mobile-toggle"
            type="button"
            data-menu-toggle
            aria-label="Abrir menu de navegação"
            aria-expanded="false"
            aria-controls="navegacao-principal"
        >
            <x-lucide-menu aria-hidden="true" />
        </button>

        <nav class="header-nav" id="navegacao-principal" aria-label="Navegação principal">
            <div class="nav-links">
                <a
                    href="{{ route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}"
                    @if (request()->routeIs('home')) aria-current="page" @endif
                >
                    Início
                </a>
                <a
                    href="{{ route('sobre') }}"
                    class="nav-link {{ request()->routeIs('sobre') ? 'is-active' : '' }}"
                    @if (request()->routeIs('sobre')) aria-current="page" @endif
                >
                    Sobre Nós
                </a>
            </div>

            <div class="header-actions">
                @auth
                    <a
                        href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('perfil.index') }}"
                        class="account-chip {{ request()->routeIs('perfil.index') ? 'is-active' : '' }}"
                        aria-label="{{ auth()->user()->is_admin ? 'Abrir painel administrativo' : 'Abrir meu perfil' }}"
                        title="{{ auth()->user()->is_admin ? 'Painel administrativo' : 'Meu perfil' }}"
                        @if (request()->routeIs('perfil.index')) aria-current="page" @endif
                    >
                        <span class="account-chip__avatar" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr(auth()->user()->nome_usuario, 0, 1)) }}
                        </span>
                        <span class="account-chip__name">{{ auth()->user()->nome_usuario }}</span>
                        @if (auth()->user()->is_admin)
                            <span class="account-chip__role">ADM</span>
                        @endif
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="header-logout">
                        @csrf
                        <button type="submit" class="icon-link" aria-label="Sair da conta" title="Sair">
                            <x-lucide-log-out aria-hidden="true" />
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="icon-link" aria-label="Minha Conta" title="Minha Conta">
                        <x-lucide-user aria-hidden="true" />
                    </a>
                @endauth

                <a
                    href="{{ route('favoritos.index') }}"
                    class="icon-link {{ request()->routeIs('favoritos.index') ? 'is-active' : '' }}"
                    aria-label="Favoritos"
                    title="Favoritos"
                >
                    <x-lucide-heart aria-hidden="true" />
                </a>

                <button
                    type="button"
                    class="icon-link cart-button"
                    data-cart-open
                    aria-label="Abrir carrinho"
                    title="Carrinho"
                >
                    <x-lucide-shopping-cart aria-hidden="true" />
                    <span class="cart-count" data-cart-count>({{ $quantidadeCarrinho ?? 0 }})</span>
                </button>
            </div>
        </nav>
    </div>
</header>
