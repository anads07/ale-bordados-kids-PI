{{-- Itens do carrinho com personalização, imagem e ações de edição. --}}
@forelse ($itens as $itemCarrinho)
    <article class="cart-item" data-cart-item="{{ $itemCarrinho['linha_id'] }}">
        <a href="{{ route('produtos.show', $itemCarrinho['produto']) }}" class="cart-item__media">
            <x-shared::product-image
                :produto="$itemCarrinho['produto']"
                class="cart-item__image"
            />
        </a>

        <div class="cart-item__info">
            <h3>
                <a href="{{ route('produtos.show', $itemCarrinho['produto']) }}">{{ $itemCarrinho['produto']->nome_produto }}</a>
            </h3>
            <p>{{ $itemCarrinho['produto']->preco_formatado }}</p>

            <ul class="cart-item__customization">
                <li>Cor da peça: {{ $itemCarrinho['personalizacao']['cor_produto'] }}</li>
                @if ($itemCarrinho['personalizacao']['escrita'])
                    <li>Texto: {{ $itemCarrinho['personalizacao']['escrita'] }}</li>
                @endif
                @if ($itemCarrinho['personalizacao']['cor_escrita'])
                    <li>Cor da linha: {{ $itemCarrinho['personalizacao']['cor_escrita'] }}</li>
                @endif
                @if ($itemCarrinho['personalizacao']['fonte_escrita'])
                    <li>Fonte: {{ $itemCarrinho['personalizacao']['fonte_escrita'] }}</li>
                @endif
                @if ($itemCarrinho['personalizacao']['desenho'])
                    <li>Desenho: {{ $itemCarrinho['personalizacao']['desenho'] }}</li>
                @endif
            </ul>

            @if ($itemCarrinho['imagem_inspiracao_url'])
                <a
                    href="{{ $itemCarrinho['imagem_inspiracao_url'] }}"
                    class="cart-item__inspiration"
                    target="_blank"
                    rel="noopener"
                >
                    Ver imagem de inspiração
                </a>
            @endif

            <form
                action="{{ route('carrinho.update', $itemCarrinho['linha_id']) }}"
                method="POST"
                class="cart-item__quantity"
                data-cart-update-form
            >
                @csrf
                @method('PATCH')

                <button type="button" class="cart-step" data-cart-step="-1" aria-label="Diminuir quantidade">−</button>
                <input
                    type="number"
                    class="cart-quantity"
                    name="quantidade"
                    value="{{ $itemCarrinho['quantidade'] }}"
                    min="0"
                    max="{{ $itemCarrinho['produto']->estoque }}"
                    inputmode="numeric"
                    aria-label="Quantidade de {{ $itemCarrinho['produto']->nome_produto }}"
                    data-cart-quantity
                >
                <button type="button" class="cart-step" data-cart-step="1" aria-label="Aumentar quantidade">+</button>
            </form>
        </div>

        <div class="cart-item__side">
            <strong>{{ $itemCarrinho['subtotal_formatado'] }}</strong>

            <form
                action="{{ route('carrinho.destroy', $itemCarrinho['linha_id']) }}"
                method="POST"
                data-cart-remove-form
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="cart-remove">Remover</button>
            </form>
        </div>
    </article>
@empty
    <div class="cart-empty">
        <span class="cart-empty__icon" aria-hidden="true">
            <x-lucide-shopping-cart />
        </span>
        <p>Seu carrinho está vazio.</p>
        <a href="{{ route('home') }}#produtos" class="btn btn-outline">Ver produtos</a>
    </div>
@endforelse
