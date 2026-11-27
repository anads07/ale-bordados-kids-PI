@extends('user.layouts.public')

@section('title', 'Finalizar pedido | '.config('mock.store.name'))

{{-- Revisão do carrinho e confirmação do pedido. --}}
@section('content')
    <section class="container-app checkout-page">
        <a href="{{ route('home') }}#produtos" class="back-link">
            <x-lucide-chevron-left aria-hidden="true" />
            Continuar comprando
        </a>

        <div class="checkout-heading">
            <p class="checkout-eyebrow">Quase lá</p>
            <h1>Finalizar pedido</h1>
            <p>Revise as peças, as personalizações e confirme seu pedido.</p>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" class="checkout-layout">
            @csrf

            <div class="checkout-items">
                @foreach ($itens as $itemCarrinho)
                    <article class="checkout-item">
                        <x-shared::product-image
                            :produto="$itemCarrinho['produto']"
                            class="checkout-item__image"
                        />

                        <div class="checkout-item__info">
                            <h2>
                                <a href="{{ route('produtos.show', $itemCarrinho['produto']) }}">{{ $itemCarrinho['produto']->nome_produto }}</a>
                            </h2>
                            <p>{{ $itemCarrinho['produto']->preco_formatado }} · {{ $itemCarrinho['quantidade'] }} unidade(s)</p>

                            <ul class="checkout-item__customization">
                                <x-shared::product-customization :personalizacao="$itemCarrinho['personalizacao']" />
                            </ul>

                            @if ($itemCarrinho['imagem_inspiracao_url'])
                                <a
                                    href="{{ $itemCarrinho['imagem_inspiracao_url'] }}"
                                    class="checkout-item__link"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    Ver imagem de inspiração
                                </a>
                            @endif
                        </div>

                        <strong class="checkout-item__subtotal">{{ $itemCarrinho['subtotal_formatado'] }}</strong>
                    </article>
                @endforeach
            </div>

            <aside class="checkout-summary">
                <h2>Resumo do pedido</h2>

                <div class="checkout-customer">
                    <span>Cliente</span>
                    <strong>{{ auth()->user()->nome_usuario }} {{ auth()->user()->sobrenome_usuario }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                </div>

                <div class="checkout-delivery-summary">
                    <span>Entrega e pagamento</span>
                    <p>Você informará o endereço e a forma de pagamento no próximo passo.</p>
                </div>

                <div class="checkout-total">
                    <span>Total</span>
                    <strong>{{ $totalFormatado }}</strong>
                </div>

                <button type="button" class="btn btn-primary checkout-submit" data-checkout-open>
                    Finalizar compra
                </button>

                <p class="checkout-note">
                    Nenhum pagamento será realizado agora. O site está em testes e em construção.
                </p>
            </aside>

            <x-shared::address-payment-modal :pagamentos="$pagamentos" />
        </form>
    </section>
@endsection
