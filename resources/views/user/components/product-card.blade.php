{{-- Reúne dados do produto, avaliação e atalho para personalização. --}}
@props([
    'produto',
    'favoritado' => false,
])

<article class="product-card" data-product-card="{{ $produto->id_pr }}">
    <div class="product-card__media">
        <a
            href="{{ route('produtos.show', $produto) }}"
            class="product-card__media-link"
            aria-label="Ver detalhes de {{ $produto->nome_produto }}"
        >
            <x-shared::product-image :produto="$produto" />
        </a>

        <form
            action="{{ route('favoritos.toggle', $produto) }}"
            method="POST"
            class="favorite-form"
            data-favorite-form
            data-product-id="{{ $produto->id_pr }}"
        >
            @csrf
            <button
                type="submit"
                class="favorite-button"
                data-favorite
                data-add-label="Adicionar {{ $produto->nome_produto }} aos favoritos"
                data-remove-label="Remover {{ $produto->nome_produto }} dos favoritos"
                aria-pressed="{{ $favoritado ? 'true' : 'false' }}"
                aria-label="{{ $favoritado ? 'Remover' : 'Adicionar' }} {{ $produto->nome_produto }} {{ $favoritado ? 'dos' : 'aos' }} favoritos"
            >
                <x-lucide-heart aria-hidden="true" />
            </button>
        </form>
    </div>

    <div class="product-card__body">
        <span class="product-card__category">{{ $produto->categoria }}</span>
        <h3 class="product-card__title">
            <a href="{{ route('produtos.show', $produto) }}">{{ $produto->nome_produto }}</a>
        </h3>

        <p class="product-card__description">
            {{ \Illuminate\Support\Str::limit((string) $produto->descricao, 88) }}
        </p>

        <div class="product-card__rating">
            <x-user::star-rating :nota="$produto->nota" :total="$produto->total_avaliacoes" />
        </div>

        <div class="product-card__footer">
            <strong class="product-card__price">{{ $produto->preco_formatado }}</strong>

            <a
                href="{{ route('produtos.show', $produto) }}"
                class="btn btn-primary btn-card product-card__cart"
                aria-label="Personalizar e adicionar {{ $produto->nome_produto }} ao carrinho"
            >
                <x-lucide-shopping-cart width="16" height="16" aria-hidden="true" />
                Adicionar
            </a>
        </div>
    </div>
</article>
