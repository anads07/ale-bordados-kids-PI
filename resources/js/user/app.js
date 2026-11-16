// Controla o menu, as janelas, as avaliações e as ações da loja.

import { iniciarCarrinho } from './cart';
import { iniciarFavoritos } from './favorites';
import { iniciarDetalheProduto } from './product';

document.addEventListener('DOMContentLoaded', () => {
    iniciarCarrinho();
    iniciarFavoritos();
    iniciarDetalheProduto();

    const cabecalho = document.querySelector('[data-site-header]');
    const alternadorMenu = document.querySelector('[data-menu-toggle]');

    // Abre e fecha a navegação em telas pequenas.
    if (cabecalho && alternadorMenu) {
        alternadorMenu.addEventListener('click', () => {
            const aberto = cabecalho.classList.toggle('nav-open');
            alternadorMenu.setAttribute('aria-expanded', String(aberto));
            alternadorMenu.setAttribute('aria-label', aberto ? 'Fechar menu de navegação' : 'Abrir menu de navegação');
        });

        cabecalho.querySelectorAll('.header-nav a').forEach((link) => {
            link.addEventListener('click', () => {
                cabecalho.classList.remove('nav-open');
                alternadorMenu.setAttribute('aria-expanded', 'false');
            });
        });
    }

    const dialogo = document.getElementById('coming-soon-dialog');
    const textoDialogo = document.getElementById('coming-soon-text');

    // Mostra um aviso nos elementos com o atributo data-coming-soon.
    const abrirAviso = (funcionalidade) => {
        if (!dialogo) {
            window.alert('Função em andamento. Em breve!');
            return;
        }

        if (textoDialogo) {
            textoDialogo.textContent = `${funcionalidade} está em andamento e estará disponível em breve.`;
        }

        if (!dialogo.open) {
            dialogo.showModal();
        }
    };

    document.addEventListener('click', (evento) => {
        const gatilho = evento.target.closest('[data-coming-soon]');

        if (!gatilho) {
            return;
        }

        evento.preventDefault();
        abrirAviso(gatilho.dataset.comingSoon || 'Esta função');
    });

    if (dialogo) {
        dialogo.querySelectorAll('[data-dialog-close]').forEach((botao) => {
            botao.addEventListener('click', () => dialogo.close());
        });

        dialogo.addEventListener('click', (evento) => {
            if (evento.target === dialogo) {
                dialogo.close();
            }
        });
    }

    // Controla as avaliações deslizantes e o estado das setas.
    const carrosselDepoimentos = document.querySelector('[data-testimonial-carousel]');

    if (carrosselDepoimentos) {
        const trilha = carrosselDepoimentos.querySelector('[data-testimonial-track]');
        const anterior = carrosselDepoimentos.querySelector('[data-testimonial-prev]');
        const proximo = carrosselDepoimentos.querySelector('[data-testimonial-next]');

        if (trilha && anterior && proximo) {
            const passo = () => Math.max(240, Math.round(trilha.clientWidth * 0.8));

            const atualizarSetas = () => {
                const maximo = trilha.scrollWidth - trilha.clientWidth;
                anterior.disabled = trilha.scrollLeft <= 4;
                proximo.disabled = trilha.scrollLeft >= maximo - 4;
            };

            anterior.addEventListener('click', () => trilha.scrollBy({ left: -passo(), behavior: 'smooth' }));
            proximo.addEventListener('click', () => trilha.scrollBy({ left: passo(), behavior: 'smooth' }));
            trilha.addEventListener('scroll', atualizarSetas, { passive: true });
            window.addEventListener('resize', atualizarSetas);
            atualizarSetas();
        }
    }

    // Opções ocultas não mostram avisos do navegador; a validação é exibida aqui.
    document.addEventListener(
        'submit',
        (evento) => {
            const formulario = evento.target;

            if (!(formulario instanceof HTMLFormElement) || !formulario.querySelector('[data-required-group]')) {
                return;
            }

            let valido = true;

            formulario.querySelectorAll('[data-required-group]').forEach((grupo) => {
                const erro = grupo.querySelector('[data-group-error]');
                const selecionado = grupo.querySelector('input[type="radio"]:checked');

                if (erro) {
                    erro.hidden = Boolean(selecionado);
                }

                if (!selecionado) {
                    valido = false;
                    grupo.querySelector('input[type="radio"]')?.focus();
                }
            });

            if (!valido) {
                evento.preventDefault();
            }
        },
        true
    );

    // Esconde a mensagem do grupo assim que uma opção é escolhida.
    document.addEventListener('change', (evento) => {
        const grupo = evento.target.closest('[data-required-group]');

        if (grupo && evento.target.matches('input[type="radio"]')) {
            const erro = grupo.querySelector('[data-group-error]');

            if (erro) {
                erro.hidden = true;
            }
        }
    });

    // Endereço e pagamento são confirmados em uma janela antes do pedido.
    const modalCheckout = document.querySelector('[data-checkout-modal]');

    if (modalCheckout) {
        const abrirCheckout = () => {
            if (typeof modalCheckout.showModal === 'function' && !modalCheckout.open) {
                modalCheckout.showModal();
            }
        };

        document.querySelectorAll('[data-checkout-open]').forEach((botao) => {
            botao.addEventListener('click', abrirCheckout);
        });

        modalCheckout.querySelectorAll('[data-checkout-close]').forEach((botao) => {
            botao.addEventListener('click', () => modalCheckout.close());
        });

        modalCheckout.addEventListener('click', (evento) => {
            if (evento.target === modalCheckout) {
                modalCheckout.close();
            }
        });

        // Reabre o modal ao retornar com erros de validação.
        if (modalCheckout.dataset.checkoutReopen === 'true') {
            abrirCheckout();
        }
    }
});
