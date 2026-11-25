{{-- Painel lateral do carrinho, atualizado pelas ações da página. --}}
<dialog id="cart-drawer" class="cart-drawer" data-cart-drawer aria-labelledby="cart-drawer-title">
    <div class="cart-drawer__panel">
        <header class="cart-drawer__header">
            <div>
                <p class="cart-drawer__eyebrow">Sua sacola</p>
                <h2 id="cart-drawer-title">Carrinho</h2>
            </div>

            <button type="button" class="cart-drawer__close" data-cart-close aria-label="Fechar carrinho">
                <x-lucide-x aria-hidden="true" />
            </button>
        </header>

        <p class="cart-drawer__message" data-cart-message role="status" aria-live="polite"></p>

        <div class="cart-drawer__body" data-cart-items>
            @include('user.partials.cart-items', ['itens' => $itensCarrinho])
        </div>

        <footer class="cart-drawer__footer">
            <div class="cart-drawer__total">
                <span>Total</span>
                <strong data-cart-total>{{ $totalCarrinhoFormatado }}</strong>
            </div>

            {{-- Sempre navega para o checkout: visitantes são levados ao login
                 e, após autenticar, retornam ao checkout com a sacola preservada. --}}
            <a href="{{ route('checkout.index') }}" class="btn btn-primary cart-drawer__checkout">
                Finalizar compra
            </a>
        </footer>
    </div>
</dialog>
