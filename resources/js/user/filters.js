// Mantém a posição da página ao aplicar os filtros da vitrine.

const CHAVE_POSICAO = 'filtros:posicao';

// Lê um valor do sessionStorage sem interromper a execução.
function lerPosicaoSalva() {
    try {
        return sessionStorage.getItem(CHAVE_POSICAO);
    } catch {
        return null;
    }
}

// Salva ou remove a posição sem falhar em ambientes restritos.
function escreverPosicaoSalva(posicao) {
    try {
        if (posicao === null) {
            sessionStorage.removeItem(CHAVE_POSICAO);
            return;
        }

        sessionStorage.setItem(CHAVE_POSICAO, String(posicao));
    } catch {
        // Ignora falhas de armazenamento para não quebrar os filtros.
    }
}

// Restaura a posição salva e registra os eventos que a atualizam.
export function iniciarFiltros() {
    const areaFiltros = document.querySelector('[data-filter-area]');
    const ehHome = window.location.pathname === '/';

    if (ehHome) {
        const posicaoSalva = lerPosicaoSalva();
        const posicao = posicaoSalva === null ? NaN : Number(posicaoSalva);

        if (Number.isFinite(posicao)) {
            history.scrollRestoration = 'manual';
            window.scrollTo(0, posicao);
        }

        if (posicaoSalva !== null) {
            escreverPosicaoSalva(null);
        }
    }

    if (!areaFiltros) {
        return;
    }

    const guardarPosicao = () => {
        if (window.location.pathname === '/') {
            escreverPosicaoSalva(window.scrollY);
        }
    };

    areaFiltros.addEventListener('submit', guardarPosicao);
    areaFiltros.addEventListener('click', (evento) => {
        if (evento.target.closest('a')) {
            guardarPosicao();
        }
    });

    document.querySelectorAll('[data-filter-form]').forEach((formulario) => {
        formulario.addEventListener('submit', guardarPosicao);
    });
}
