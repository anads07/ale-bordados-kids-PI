// Aplica o filtro de status sem exigir outro botão.
export function iniciarFiltrosAdmin() {
    document.querySelectorAll('[data-admin-auto-submit]').forEach((seletor) => {
        seletor.addEventListener('change', () => seletor.form?.submit());
    });
}
