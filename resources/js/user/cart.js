// Controla a abertura, os itens e os totais do carrinho.

import { enviarFormulario } from './http';

// Registra os eventos do painel lateral do carrinho.
export function iniciarCarrinho() {
    const gaveta = document.querySelector('[data-cart-drawer]');

    if (!gaveta) {
        return;
    }

    const itens = gaveta.querySelector('[data-cart-items]');
    const total = gaveta.querySelector('[data-cart-total]');
    const mensagem = gaveta.querySelector('[data-cart-message]');
    const contadores = document.querySelectorAll('[data-cart-count]');

    const abrir = () => {
        if (typeof gaveta.showModal === 'function' && !gaveta.open) {
            gaveta.showModal();
        } else {
            gaveta.setAttribute('open', '');
        }

        document.body.classList.add('cart-drawer-open');
    };

    const fechar = () => {
        if (typeof gaveta.close === 'function') {
            gaveta.close();
        } else {
            gaveta.removeAttribute('open');
        }

        document.body.classList.remove('cart-drawer-open');
    };

    document.querySelectorAll('[data-cart-open]').forEach((botao) => {
        botao.addEventListener('click', abrir);
    });

    gaveta.querySelectorAll('[data-cart-close]').forEach((botao) => {
        botao.addEventListener('click', fechar);
    });

    gaveta.addEventListener('click', (evento) => {
        if (evento.target === gaveta) {
            fechar();
        }
    });

    gaveta.addEventListener('close', () => {
        document.body.classList.remove('cart-drawer-open');
    });

    document.addEventListener('click', async (evento) => {
        const botaoAdicionar = evento.target.closest('[data-cart-add-form] button[type="submit"]');

        if (!botaoAdicionar) {
            return;
        }

        evento.preventDefault();
        await processarFormulario(botaoAdicionar.form, botaoAdicionar, true);
    });

    gaveta.addEventListener('submit', async (evento) => {
        const formulario = evento.target.closest('[data-cart-update-form], [data-cart-remove-form]');

        if (!formulario) {
            return;
        }

        evento.preventDefault();
        await processarFormulario(formulario, formulario.querySelector('button'));
    });

    // Os botões de mais e menos atualizam a quantidade enviada.
    gaveta.addEventListener('click', (evento) => {
        const passo = evento.target.closest('[data-cart-step]');

        if (!passo) {
            return;
        }

        const formulario = passo.closest('[data-cart-update-form]');
        const campo = formulario?.querySelector('[data-cart-quantity]');

        if (!campo) {
            return;
        }

        const minimo = Number(campo.min || 0);
        const maximo = Number(campo.max || 99);
        const proximo = Math.min(maximo, Math.max(minimo, Number(campo.value || 0) + Number(passo.dataset.cartStep)));

        campo.value = String(proximo);
        formulario.requestSubmit();
    });

    gaveta.addEventListener('change', (evento) => {
        if (evento.target.matches('[data-cart-quantity]')) {
            evento.target.form?.requestSubmit();
        }
    });

    // Envia alterações e atualiza os trechos devolvidos pelo servidor.
    async function processarFormulario(formulario, botao, abrirAoFinal = false) {
        if (!formulario || formulario.dataset.enviando === 'true') {
            return;
        }

        if (!formulario.checkValidity()) {
            formulario.reportValidity();

            return;
        }

        formulario.dataset.enviando = 'true';
        botao?.setAttribute('disabled', 'disabled');

        try {
            const dados = await enviarFormulario(formulario);

            if (itens && typeof dados.items_html === 'string') {
                itens.innerHTML = dados.items_html;
            }

            if (total) {
                total.textContent = dados.cart_total_formatado || 'R$ 0,00';
            }

            contadores.forEach((contador) => {
                contador.textContent = `(${dados.cart_count ?? 0})`;
            });

            mostrarMensagem(mensagem, '', false);

            if (abrirAoFinal) {
                abrir();
            }
        } catch (erro) {
            mostrarMensagem(mensagem, erro.message, true);
        } finally {
            formulario.dataset.enviando = 'false';
            botao?.removeAttribute('disabled');
        }
    }
}

// Atualiza a mensagem exibida no painel lateral.
function mostrarMensagem(elemento, texto, erro = false) {
    if (!elemento) {
        return;
    }

    elemento.textContent = texto;
    elemento.classList.toggle('is-error', erro && texto !== '');
}
