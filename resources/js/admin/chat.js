// Abre o chat centralizado e avisa que o envio ainda é simulado.
export function iniciarChat() {
    const modal = document.querySelector('[data-chat-modal]');

    if (!modal) {
        return;
    }

    const nome = modal.querySelector('[data-chat-modal-name]');
    const tag = modal.querySelector('[data-chat-modal-tag]');
    const mensagem = modal.querySelector('[data-chat-modal-message]');
    const aviso = modal.querySelector('[data-chat-modal-notice]');
    const campo = modal.querySelector('[data-chat-modal-input]');
    const formulario = modal.querySelector('[data-chat-modal-composer]');

    document.querySelectorAll('[data-chat-contact], [data-chat-open]').forEach((contato) => {
        contato.addEventListener('click', () => {
            if (nome) {
                nome.textContent = contato.dataset.chatName || 'Conversa';
            }

            if (tag) {
                tag.textContent = contato.dataset.chatTag || contato.dataset.chatOrder || '';
            }

            if (mensagem) {
                mensagem.textContent = contato.dataset.chatMessage || 'Olá! Como podemos ajudar?';
            }

            if (aviso) {
                aviso.hidden = true;
            }

            if (campo) {
                campo.value = '';
            }

            if (typeof modal.showModal === 'function' && !modal.open) {
                modal.showModal();
            }
        });
    });

    modal.querySelectorAll('[data-chat-modal-close]').forEach((botao) => {
        botao.addEventListener('click', () => modal.close());
    });

    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) {
            modal.close();
        }
    });

    formulario?.addEventListener('submit', (evento) => {
        evento.preventDefault();

        if (aviso) {
            aviso.hidden = false;
        }

        campo?.focus();
    });
}
