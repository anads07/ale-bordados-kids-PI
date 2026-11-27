// Exibe uma confirmação antes de excluir um registro.
export function iniciarConfirmacao() {
    const dialogo = document.getElementById('admin-confirm-dialog');
    const texto = dialogo?.querySelector('[data-confirm-text]');
    const aceitar = dialogo?.querySelector('[data-confirm-accept]');
    const cancelar = dialogo?.querySelector('[data-confirm-cancel]');
    let formularioConfirmacao = null;

    document.querySelectorAll('form[data-confirm]').forEach((formulario) => {
        formulario.addEventListener('submit', (evento) => {
            if (!dialogo || typeof dialogo.showModal !== 'function') {
                if (!window.confirm(formulario.dataset.confirm || 'Deseja continuar?')) {
                    evento.preventDefault();
                }

                return;
            }

            evento.preventDefault();
            formularioConfirmacao = formulario;

            if (texto) {
                texto.textContent = formulario.dataset.confirm || 'Deseja continuar?';
            }

            dialogo.showModal();
        });
    });

    if (!dialogo) {
        return;
    }

    const fechar = () => {
        dialogo.close();
        formularioConfirmacao = null;
    };

    cancelar?.addEventListener('click', fechar);

    dialogo.addEventListener('close', () => {
        formularioConfirmacao = null;
    });

    dialogo.addEventListener('click', (evento) => {
        if (evento.target === dialogo) {
            fechar();
        }
    });

    aceitar?.addEventListener('click', () => {
        const formulario = formularioConfirmacao;
        formularioConfirmacao = null;
        dialogo.close();
        formulario?.submit();
    });
}
