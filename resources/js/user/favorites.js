// Atualiza os favoritos e sincroniza cartões e ícones do mesmo produto.

import { enviarFormulario, exibirAviso } from './http';

// Registra os eventos dos favoritos.
export function iniciarFavoritos() {
    document.addEventListener('submit', async (evento) => {
        const formulario = evento.target.closest('[data-favorite-form]');

        if (!formulario) {
            return;
        }

        evento.preventDefault();

        const botao = formulario.querySelector('[data-favorite]');
        const identificador = formulario.dataset.productId;
        const cartao = formulario.closest('[data-product-card]');
        const grade = formulario.closest('[data-favorites-grid]');

        botao?.setAttribute('disabled', 'disabled');

        try {
            const dados = await enviarFormulario(formulario);
            const selecionado = Boolean(dados.favorited);

            document.querySelectorAll(`[data-favorite-form][data-product-id="${identificador}"]`).forEach((formularioFavorito) => {
                const coracao = formularioFavorito.querySelector('[data-favorite]');

                if (!coracao) {
                    return;
                }

                coracao.setAttribute('aria-pressed', String(selecionado));
                coracao.setAttribute('aria-label', selecionado ? coracao.dataset.removeLabel : coracao.dataset.addLabel);
            });

            if (grade && !selecionado && cartao) {
                cartao.remove();
                atualizarResumoFavoritos(grade);
            }
        } catch (erro) {
            exibirAviso(erro.message);
        } finally {
            botao?.removeAttribute('disabled');
        }
    });
}

// Atualiza o contador e o estado vazio após remover um favorito.
function atualizarResumoFavoritos(grade) {
    const restantes = grade.querySelectorAll('[data-product-card]').length;
    const resumo = document.querySelector('[data-favorites-summary]');
    const vazio = document.querySelector('[data-favorites-empty]');

    if (resumo) {
        resumo.textContent = `${restantes} ${restantes === 1 ? 'peça salva' : 'peças salvas'}`;
    }

    if (restantes === 0) {
        grade.hidden = true;

        if (vazio) {
            vazio.hidden = false;
        }
    }
}
