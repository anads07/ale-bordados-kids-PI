// Controla a janela de endereço e pagamento antes de finalizar o pedido.
export function iniciarCheckoutModal() {
    const modal = document.querySelector('[data-checkout-modal]');

    if (!modal) {
        return;
    }

    const abrir = () => {
        if (typeof modal.showModal === 'function' && !modal.open) {
            modal.showModal();
        }
    };

    document.querySelectorAll('[data-checkout-open]').forEach((botao) => {
        botao.addEventListener('click', abrir);
    });

    modal.querySelectorAll('[data-checkout-close]').forEach((botao) => {
        botao.addEventListener('click', () => modal.close());
    });

    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) {
            modal.close();
        }
    });

    if (modal.dataset.checkoutReopen === 'true') {
        abrir();
    }
}
