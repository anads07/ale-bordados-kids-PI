// Abre e fecha as janelas de detalhes do painel administrativo.
export function iniciarModais() {
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
}
