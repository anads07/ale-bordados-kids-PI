// Abre e fecha a navegação em telas pequenas.
export function iniciarMenu() {
    const cabecalho = document.querySelector('[data-site-header]');
    const alternador = document.querySelector('[data-menu-toggle]');

    if (!cabecalho || !alternador) {
        return;
    }

    alternador.addEventListener('click', () => {
        const aberto = cabecalho.classList.toggle('nav-open');
        alternador.setAttribute('aria-expanded', String(aberto));
        alternador.setAttribute('aria-label', aberto ? 'Fechar menu de navegação' : 'Abrir menu de navegação');
    });

    const fechar = () => {
        cabecalho.classList.remove('nav-open');
        alternador.setAttribute('aria-expanded', 'false');
        alternador.setAttribute('aria-label', 'Abrir menu de navegação');
    };

    cabecalho.querySelectorAll('.header-nav a, [data-cart-open]').forEach((elemento) => {
        elemento.addEventListener('click', fechar);
    });
}
