// Controla janelas, avisos e filtros do painel administrativo.

document.addEventListener('DOMContentLoaded', () => {
    const dialogoEmBreve = document.getElementById('coming-soon-dialog');
    const textoEmBreve = document.getElementById('coming-soon-text');

    // Abre a janela compartilhada para funções ainda indisponíveis.
    const abrirAviso = (funcionalidade) => {
        if (!dialogoEmBreve) {
            return;
        }

        if (textoEmBreve) {
            textoEmBreve.textContent = `${funcionalidade} está em andamento e estará disponível em breve.`;
        }

        if (!dialogoEmBreve.open) {
            dialogoEmBreve.showModal();
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

    if (dialogoEmBreve) {
        dialogoEmBreve.querySelectorAll('[data-dialog-close]').forEach((botao) => {
            botao.addEventListener('click', () => dialogoEmBreve.close());
        });

        dialogoEmBreve.addEventListener('click', (evento) => {
            if (evento.target === dialogoEmBreve) {
                dialogoEmBreve.close();
            }
        });
    }

    // Abre os detalhes do pedido selecionado.
    document.querySelectorAll('[data-admin-modal-target]').forEach((botao) => {
        botao.addEventListener('click', () => {
            const modal = document.getElementById(botao.dataset.adminModalTarget);

            if (modal && typeof modal.showModal === 'function') {
                modal.showModal();
            }
        });
    });

    document.querySelectorAll('[data-admin-modal-close]').forEach((botao) => {
        botao.addEventListener('click', () => {
            botao.closest('dialog')?.close();
        });
    });

    document.querySelectorAll('[data-admin-modal]').forEach((modal) => {
        modal.addEventListener('click', (evento) => {
            if (evento.target === modal) {
                modal.close();
            }
        });
    });

    // Aplica o filtro de status sem exigir outro botão.
    document.querySelectorAll('[data-admin-auto-submit]').forEach((seletor) => {
        seletor.addEventListener('change', () => seletor.form?.submit());
    });

    // Exibe uma confirmação antes de excluir.
    const dialogoConfirmacao = document.getElementById('admin-confirm-dialog');
    const textoConfirmacao = dialogoConfirmacao?.querySelector('[data-confirm-text]');
    const aceitarConfirmacao = dialogoConfirmacao?.querySelector('[data-confirm-accept]');
    const cancelarConfirmacao = dialogoConfirmacao?.querySelector('[data-confirm-cancel]');
    let formularioConfirmacao = null;

    document.querySelectorAll('form[data-confirm]').forEach((formulario) => {
        formulario.addEventListener('submit', (evento) => {
            if (!dialogoConfirmacao || typeof dialogoConfirmacao.showModal !== 'function') {
                if (!window.confirm(formulario.dataset.confirm || 'Deseja continuar?')) {
                    evento.preventDefault();
                }

                return;
            }

            evento.preventDefault();
            formularioConfirmacao = formulario;

            if (textoConfirmacao) {
                textoConfirmacao.textContent = formulario.dataset.confirm || 'Deseja continuar?';
            }

            dialogoConfirmacao.showModal();
        });
    });

    if (dialogoConfirmacao) {
        const fecharConfirmacao = () => {
            dialogoConfirmacao.close();
            formularioConfirmacao = null;
        };

        cancelarConfirmacao?.addEventListener('click', fecharConfirmacao);

        dialogoConfirmacao.addEventListener('close', () => {
            formularioConfirmacao = null;
        });

        dialogoConfirmacao.addEventListener('click', (evento) => {
            if (evento.target === dialogoConfirmacao) {
                fecharConfirmacao();
            }
        });

        aceitarConfirmacao?.addEventListener('click', () => {
            const formulario = formularioConfirmacao;
            formularioConfirmacao = null;
            dialogoConfirmacao.close();
            formulario?.submit();
        });
    }

    // Abre o chat centralizado e avisa que o envio ainda é simulado.
    const modalChat = document.querySelector('[data-chat-modal]');

    if (modalChat) {
        const nomeChat = modalChat.querySelector('[data-chat-modal-name]');
        const tagChat = modalChat.querySelector('[data-chat-modal-tag]');
        const mensagemChat = modalChat.querySelector('[data-chat-modal-message]');
        const avisoChat = modalChat.querySelector('[data-chat-modal-notice]');
        const campoChat = modalChat.querySelector('[data-chat-modal-input]');
        const formularioChat = modalChat.querySelector('[data-chat-modal-composer]');

        document.querySelectorAll('[data-chat-contact], [data-chat-open]').forEach((contato) => {
            contato.addEventListener('click', () => {
                if (nomeChat) {
                    nomeChat.textContent = contato.dataset.chatName || 'Conversa';
                }

                if (tagChat) {
                    tagChat.textContent = contato.dataset.chatTag || contato.dataset.chatOrder || '';
                }

                if (mensagemChat) {
                    mensagemChat.textContent = contato.dataset.chatMessage || 'Olá! Como podemos ajudar?';
                }

                if (avisoChat) {
                    avisoChat.hidden = true;
                }

                if (campoChat) {
                    campoChat.value = '';
                }

                if (typeof modalChat.showModal === 'function' && !modalChat.open) {
                    modalChat.showModal();
                }
            });
        });

        modalChat.querySelectorAll('[data-chat-modal-close]').forEach((botao) => {
            botao.addEventListener('click', () => modalChat.close());
        });

        modalChat.addEventListener('click', (evento) => {
            if (evento.target === modalChat) {
                modalChat.close();
            }
        });

        formularioChat?.addEventListener('submit', (evento) => {
            evento.preventDefault();

            if (avisoChat) {
                avisoChat.hidden = false;
            }

            campoChat?.focus();
        });
    }

    // Mostra o nome e a prévia local da imagem escolhida no formulário.
    document.querySelectorAll('[data-admin-product-image]').forEach((campo) => {
        const nomeArquivo = document.querySelector('[data-admin-product-image-name]');
        const previa = document.querySelector('[data-admin-product-image-preview]');
        let urlPrevia = null;

        campo.addEventListener('change', () => {
            const arquivo = campo.files?.[0];

            if (urlPrevia) {
                URL.revokeObjectURL(urlPrevia);
                urlPrevia = null;
            }

            if (nomeArquivo) {
                nomeArquivo.textContent = arquivo ? arquivo.name : 'Nenhum arquivo selecionado';
            }

            if (!previa) {
                return;
            }

            if (!arquivo || !arquivo.type.startsWith('image/')) {
                previa.hidden = true;
                previa.removeAttribute('src');
                return;
            }

            urlPrevia = URL.createObjectURL(arquivo);
            previa.src = urlPrevia;
            previa.hidden = false;
        });
    });
});
