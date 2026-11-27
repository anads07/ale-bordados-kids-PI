// Valida os grupos de opções obrigatórias que ficam ocultos no formulário.
export function iniciarFormularios() {
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

    document.addEventListener('change', (evento) => {
        const grupo = evento.target.closest('[data-required-group]');

        if (grupo && evento.target.matches('input[type="radio"]')) {
            const erro = grupo.querySelector('[data-group-error]');

            if (erro) {
                erro.hidden = true;
            }
        }
    });
}
