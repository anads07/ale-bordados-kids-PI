// Abre e fecha o aviso compartilhado de funcionalidades em andamento.
export function iniciarAvisoEmBreve() {
    const dialogo = document.getElementById('coming-soon-dialog');
    const texto = document.getElementById('coming-soon-text');

    const abrir = (funcionalidade) => {
        if (!dialogo) {
            window.alert('Função em andamento. Em breve!');
            return;
        }

        if (texto) {
            texto.textContent = `${funcionalidade} está em andamento e estará disponível em breve.`;
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
        abrir(gatilho.dataset.comingSoon || 'Esta função');
    });

    if (!dialogo) {
        return;
    }

    dialogo.querySelectorAll('[data-dialog-close]').forEach((botao) => {
        botao.addEventListener('click', () => dialogo.close());
    });

    dialogo.addEventListener('click', (evento) => {
        if (evento.target === dialogo) {
            dialogo.close();
        }
    });
}
