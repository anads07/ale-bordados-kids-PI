// Mantém a posição da página ao aplicar os filtros da vitrine.

const CHAVE_POSICAO = 'filtros:posicao';

// Restaura a posição salva e registra os eventos que a atualizam.
export function iniciarFiltros() {
    const areaFiltros = document.querySelector('[data-filter-area]');

    if (!areaFiltros) {
        return;
    }

    const posicaoSalva = sessionStorage.getItem(CHAVE_POSICAO);

    if (posicaoSalva !== null) {
        // Evita que o navegador restaure a rolagem por conta própria.
        history.scrollRestoration = 'manual';
        window.scrollTo(0, Number(posicaoSalva));
        sessionStorage.removeItem(CHAVE_POSICAO);
    }

    const guardarPosicao = () => {
        sessionStorage.setItem(CHAVE_POSICAO, String(window.scrollY));
    };

    areaFiltros.addEventListener('submit', guardarPosicao);
    areaFiltros.addEventListener('click', (evento) => {
        if (evento.target.closest('a')) {
            guardarPosicao();
        }
    });

    // A busca do cabeçalho também recarrega a vitrine.
    document.querySelectorAll('[data-filter-form]').forEach((formulario) => {
        formulario.addEventListener('submit', guardarPosicao);
    });
}
