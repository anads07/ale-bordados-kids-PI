@extends('user.layouts.public')

@section('title', 'Meu perfil | '.config('mock.store.name'))

{{-- Área do cliente com dados da conta e histórico de pedidos. --}}
@section('content')
    <section class="container-app profile-page">
        <header class="profile-heading">
            <p class="profile-eyebrow">Minha conta</p>
            <h1>Olá, {{ $usuario->nome_usuario }}!</h1>
            <p>Acompanhe seus últimos pedidos, personalizações e o status de cada peça.</p>
        </header>

        <div class="profile-layout">
            <aside class="profile-summary">
                <span class="profile-summary__avatar" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($usuario->nome_usuario, 0, 1)) }}
                </span>

                <span class="profile-summary__role">Cliente</span>
                <h2>{{ $usuario->nome_usuario }} {{ $usuario->sobrenome_usuario }}</h2>
                <p>{{ $usuario->email }}</p>

                <dl class="profile-summary__stats">
                    <div>
                        <dt>Pedidos</dt>
                        <dd>{{ $totalPedidos }}</dd>
                    </div>
                    <div>
                        <dt>Peças</dt>
                        <dd>{{ $totalPecas }}</dd>
                    </div>
                    <div>
                        <dt>Em andamento</dt>
                        <dd>{{ $pedidosEmAndamento }}</dd>
                    </div>
                </dl>

                <a href="{{ route('home') }}#produtos" class="btn btn-primary btn-card">Continuar comprando</a>
            </aside>

            <section class="profile-orders" aria-labelledby="ultimos-pedidos-titulo">
                <header class="profile-orders__header">
                    <div>
                        <h2 id="ultimos-pedidos-titulo">Últimos pedidos</h2>
                        <p>{{ $pedidos->total() }} pedido(s) encontrados</p>
                    </div>
                </header>

                @if ($pedidos->isEmpty())
                    <div class="profile-orders__empty">
                        <x-lucide-shopping-cart aria-hidden="true" />
                        <p>Você ainda não fez nenhum pedido.</p>
                        <a href="{{ route('home') }}#produtos" class="btn btn-outline">Ver produtos</a>
                    </div>
                @else
                    <div class="profile-orders__list">
                        @foreach ($pedidos as $pedido)
                            @php $produto = $pedido->produtoRelacionado; @endphp

                            <article class="profile-order">
                                <header class="profile-order__header">
                                    <div>
                                        <strong>Pedido #{{ $pedido->id_pedido }}</strong>
                                        <span>{{ $pedido->data_pedido?->format('d/m/Y') ?? 'Data não informada' }}</span>
                                    </div>

                                    <x-shared::status-badge :situacao="$pedido->status" />
                                </header>

                                <div class="profile-order__body">
                                    @if ($produto)
                                        <x-shared::product-image
                                            :produto="$produto"
                                            class="profile-order__image"
                                        />
                                    @else
                                        <span class="profile-order__image profile-order__image--empty" aria-hidden="true">
                                            <x-lucide-image-off />
                                        </span>
                                    @endif

                                    <div class="profile-order__info">
                                        <h3>
                                            @if ($produto)
                                                <a href="{{ route('produtos.show', $produto) }}">{{ $produto->nome_produto }}</a>
                                            @else
                                                Produto removido
                                            @endif
                                        </h3>

                                        <p>
                                            {{ $produto?->preco_formatado ?? 'Preço indisponível' }}
                                            · {{ $pedido->quantidade }} unidade(s)
                                        </p>

                                        <ul class="profile-order__customization">
                                            <li>Cor da peça: {{ $pedido->cor_produto ?: 'Não informada' }}</li>
                                            @if ($pedido->escrita)
                                                <li>Texto: {{ $pedido->escrita }}</li>
                                            @endif
                                            @if ($pedido->cor_escrita)
                                                <li>Cor da linha: {{ $pedido->cor_escrita }}</li>
                                            @endif
                                            @if ($pedido->fonte_escrita)
                                                <li>Fonte: {{ $pedido->fonte_escrita }}</li>
                                            @endif
                                            @if ($pedido->desenho)
                                                <li>Desenho: {{ $pedido->desenho }}</li>
                                            @endif
                                            @if ($pedido->observacoes)
                                                <li>Observações: {{ $pedido->observacoes }}</li>
                                            @endif
                                        </ul>

                                        @if ($pedido->imagem_inspiracao_url)
                                            <a
                                                href="{{ $pedido->imagem_inspiracao_url }}"
                                                class="profile-order__inspiration"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                Ver imagem de inspiração
                                            </a>
                                        @endif
                                    </div>

                                    <strong class="profile-order__total">{{ $pedido->preco_total_formatado }}</strong>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($pedidos->hasPages())
                        <div class="profile-orders__pagination">
                            {{ $pedidos->onEachSide(1)->links() }}
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </section>
@endsection
