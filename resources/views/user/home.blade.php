@extends('user.layouts.public')

@section('title', config('mock.store.name').' | Bordados personalizados')

{{-- Página inicial da vitrine com produtos, filtros, avaliações e apresentação da marca. --}}
@section('content')
    @php
        // Mantém os filtros atuais nos links de categoria e remove apenas valores vazios.
        $consultaBase = array_filter([
            'q' => $filtros['busca'] !== '' ? $filtros['busca'] : null,
            'preco_min' => $filtros['precoMin'] !== null ? $filtros['precoMin'] : null,
            'preco_max' => $filtros['precoMax'] !== null ? $filtros['precoMax'] : null,
            'ordem' => $filtros['ordem'] !== 'relevancia' ? $filtros['ordem'] : null,
        ], fn ($valor) => $valor !== null && $valor !== '');
    @endphp

    <section class="hero" aria-label="Destaques da loja" data-hero-carousel>
        @foreach ($slidesHero as $indice => $slide)
            <article
                class="hero__slide {{ $loop->first ? 'is-active' : '' }}"
                data-hero-slide
                aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                style="--hero-fit: {{ $slide['ajuste'] ?? 'cover' }}; --hero-posicao: {{ $slide['posicao'] ?? 'center' }}"
                @if (! $loop->first) inert @endif
            >
                <img
                    class="hero__image"
                    src="{{ asset($slide['imagem'] ?? 'images/pagina_inicial/banner_1.png') }}"
                    alt="{{ $slide['alt'] ?? '' }}"
                    @if (! $loop->first) loading="lazy" @endif
                >

                <div class="container-app hero__inner">
                    <div class="hero__content">
                        <p class="hero__eyebrow">{{ $slide['eyebrow'] ?? '' }}</p>
                        @php($tagTitulo = $loop->first ? 'h1' : 'p')
                        <{{ $tagTitulo }} class="hero__title" @if (! $loop->first) aria-hidden="true" @endif>
                            {{ $slide['title'] ?? '' }}
                            <span>{{ $slide['highlight'] ?? '' }}</span>
                        </{{ $tagTitulo }}>
                        <p class="hero__description">{{ $slide['description'] ?? '' }}</p>
                        <div class="hero__actions">
                            <a href="{{ $slide['link'] ?? '#produtos' }}" class="btn btn-primary">
                                {{ $slide['acao'] ?? 'Ver coleção' }}
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach

        @if (count($slidesHero) > 1)
            <button
                type="button"
                class="hero__arrow hero__arrow--prev"
                data-hero-prev
                aria-label="Banner anterior"
            >
                <x-lucide-chevron-left aria-hidden="true" />
            </button>

            <button
                type="button"
                class="hero__arrow hero__arrow--next"
                data-hero-next
                aria-label="Próximo banner"
            >
                <x-lucide-chevron-right aria-hidden="true" />
            </button>

            <div class="hero__dots" role="group" aria-label="Escolher banner">
                @foreach ($slidesHero as $indice => $slide)
                    <button
                        type="button"
                        class="hero__dot {{ $loop->first ? 'is-active' : '' }}"
                        data-hero-dot="{{ $indice }}"
                        aria-label="Banner {{ $indice + 1 }}"
                        aria-current="{{ $loop->first ? 'true' : 'false' }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </section>

    <section class="container-app own-piece" aria-labelledby="peca-propria-titulo">
        <div class="own-piece__inner">
            <img
                class="own-piece__icon {{ ($ownPiece['ajuste'] ?? 'cover') === 'contain' ? 'own-piece__icon--contain' : '' }}"
                src="{{ asset($ownPiece['imagem'] ?? 'images/pagina_inicial/body.png') }}"
                alt="{{ $ownPiece['alt'] ?? 'Peça infantil com bordado personalizado' }}"
                style="--own-piece-fit: {{ $ownPiece['ajuste'] ?? 'cover' }}"
                loading="lazy"
            >

            <div class="own-piece__intro">
                <h2 id="peca-propria-titulo">Tem uma peça própria?</h2>
                <p>Fazemos bordados personalizados em roupas, toalhas, uniformes e muito mais!</p>

                <ol class="own-piece__steps">
                    <li class="own-piece__step">
                        <span class="own-piece__step-icon" aria-hidden="true">
                            <x-lucide-camera />
                        </span>
                        <span class="own-piece__step-text">
                            <strong>1. Envie a foto</strong>
                            <span>da sua peça</span>
                        </span>
                        <span class="own-piece__step-arrow" aria-hidden="true">
                            <x-lucide-arrow-right />
                        </span>
                    </li>
                    <li class="own-piece__step">
                        <span class="own-piece__step-icon" aria-hidden="true">
                            <x-lucide-message-circle-more />
                        </span>
                        <span class="own-piece__step-text">
                            <strong>2. Conte sua ideia</strong>
                            <span>e o que deseja</span>
                        </span>
                        <span class="own-piece__step-arrow" aria-hidden="true">
                            <x-lucide-arrow-right />
                        </span>
                    </li>
                    <li class="own-piece__step">
                        <span class="own-piece__step-icon" aria-hidden="true">
                            <x-lucide-circle-dollar-sign />
                        </span>
                        <span class="own-piece__step-text">
                            <strong>3. Receba um orçamento</strong>
                            <span>personalizado</span>
                        </span>
                    </li>
                </ol>
            </div>

            <div class="own-piece__cta">
                <h3>Seu bordado do jeito que você imagina!</h3>
                <a href="{{ route('orcamento.index') }}" class="btn btn-primary">
                    Solicitar orçamento
                </a>
            </div>
        </div>
    </section>

    <section class="container-app home-content" id="produtos" aria-label="Catálogo de produtos">
        <aside class="filter-panel" aria-labelledby="filtros-titulo" data-filter-area>
            <form method="GET" action="{{ route('home') }}">
                <h2 id="filtros-titulo">Filtros</h2>

                @if ($filtros['busca'] !== '')
                    <input type="hidden" name="q" value="{{ $filtros['busca'] }}">
                @endif

                @if ($filtros['categoria'] !== '')
                    <input type="hidden" name="categoria" value="{{ $filtros['categoria'] }}">
                @endif

                <div class="filter-group">
                    <h3>Categorias</h3>
                    <div class="filter-pills">
                        <a
                            href="{{ route('home', $consultaBase) }}"
                            class="filter-pill {{ $filtros['categoria'] === '' ? 'is-active' : '' }}"
                        >
                            Todas
                        </a>

                        @foreach ($categorias as $categoria)
                            <a
                                href="{{ route('home', array_merge($consultaBase, ['categoria' => $categoria])) }}"
                                class="filter-pill {{ $filtros['categoria'] === $categoria ? 'is-active' : '' }}"
                            >
                                {{ $categoria }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Faixa de Preço</h3>
                    <div class="price-grid">
                        <label for="preco-min">
                            De
                            <input
                                id="preco-min"
                                class="campo"
                                type="number"
                                name="preco_min"
                                min="0"
                                step="0.01"
                                value="{{ $filtros['precoMin'] }}"
                                placeholder="R$ 0"
                            >
                        </label>
                        <label for="preco-max">
                            Até
                            <input
                                id="preco-max"
                                class="campo"
                                type="number"
                                name="preco_max"
                                min="0"
                                step="0.01"
                                value="{{ $filtros['precoMax'] }}"
                                placeholder="R$ 300"
                            >
                        </label>
                    </div>
                </div>

                <div class="filter-group">
                    <h3 id="ordem-label">Ordenar por:</h3>
                    <select id="ordem" name="ordem" class="campo" aria-labelledby="ordem-label">
                        <option value="relevancia" @selected($filtros['ordem'] === 'relevancia')>Relevância</option>
                        <option value="menor_preco" @selected($filtros['ordem'] === 'menor_preco')>Menor preço</option>
                        <option value="maior_preco" @selected($filtros['ordem'] === 'maior_preco')>Maior preço</option>
                        <option value="nome" @selected($filtros['ordem'] === 'nome')>Nome A-Z</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                    <a href="{{ route('home') }}" class="btn btn-outline">Limpar filtros</a>
                </div>
            </form>
        </aside>

        <div>
            <x-user::section-heading titulo="Peças favoritas dos nossos clientes" />

            @if ($produtos->isEmpty())
                <div class="empty-state">
                    <p>Nenhuma peça encontrada com os filtros selecionados.</p>
                    <a href="{{ route('home') }}" class="btn btn-outline">Ver todos os produtos</a>
                </div>
            @else
                <div class="product-grid">
                    @foreach ($produtos as $produto)
                        <x-user::product-card
                            :produto="$produto"
                            :favoritado="in_array($produto->id_pr, $idsFavoritos, true)"
                        />
                    @endforeach
                </div>

                <p class="results-count">
                    Mostrando {{ $produtos->count() }} {{ $produtos->count() === 1 ? 'peça' : 'peças' }}.
                </p>
            @endif
        </div>
    </section>

    <section class="container-app testimonials" aria-label="Avaliações de clientes">
        <x-user::section-heading titulo="Avaliações" />

        <div class="testimonial-carousel" data-testimonial-carousel>
            <div class="testimonial-track" data-testimonial-track tabindex="0">
                @foreach ($depoimentos as $depoimento)
                    <article class="testimonial-card">
                        <div class="testimonial-card__header">
                            <span class="testimonial-avatar" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($depoimento['nome'], 0, 1)) }}
                            </span>
                            <div>
                                <strong class="testimonial-name">{{ $depoimento['nome'] }}</strong>
                                <x-user::star-rating :nota="$depoimento['nota']" />
                            </div>
                        </div>

                        <p class="testimonial-text">{{ $depoimento['texto'] }}</p>
                    </article>
                @endforeach
            </div>

            <button
                type="button"
                class="testimonial-arrow testimonial-arrow--prev"
                data-testimonial-prev
                aria-label="Avaliações anteriores"
            >
                <x-lucide-chevron-left aria-hidden="true" />
            </button>

            <button
                type="button"
                class="testimonial-arrow testimonial-arrow--next"
                data-testimonial-next
                aria-label="Próximas avaliações"
            >
                <x-lucide-chevron-right aria-hidden="true" />
            </button>
        </div>
    </section>
@endsection
