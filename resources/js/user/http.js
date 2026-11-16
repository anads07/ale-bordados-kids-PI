// Reúne o envio de formulários e os avisos de erro da interface.

/**
 * Envia o formulário com CSRF e mantém o método definido pelo Laravel.
 */
export async function enviarFormulario(formulario) {
    const resposta = await fetch(formulario.action, {
        method: 'POST',
        body: new FormData(formulario),
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': obterTokenCsrf(),
        },
    });

    let dados;

    try {
        dados = await resposta.json();
    } catch {
        if (resposta.ok) {
            throw new Error('O servidor retornou uma resposta inválida.');
        }

        dados = {};
    }

    if (!resposta.ok) {
        if (resposta.status === 401) {
            // Leva ao login quando a sessão expirou ou falta autenticação.
            window.location.assign('/login');

            throw new Error('Redirecionando para o login...');
        }

        if (resposta.status === 419) {
            // Recarrega a página para renovar a sessão e o token CSRF.
            window.location.reload();

            throw new Error('Página expirada. Recarregando...');
        }

        const primeiroErro = dados.errors ? Object.values(dados.errors)[0]?.[0] : null;
        const mensagem = primeiroErro || dados.message || 'Não foi possível concluir a ação.';

        throw new Error(mensagem);
    }

    return dados;
}

// Mostra um aviso de erro no início do conteúdo.
export function exibirAviso(mensagem) {
    let area = document.querySelector('[data-flash-area]');

    if (!area) {
        area = document.createElement('div');
        area.className = 'container-app flash-area';
        area.dataset.flashArea = '';

        const conteudoPrincipal = document.querySelector('main');
        conteudoPrincipal?.parentNode?.insertBefore(area, conteudoPrincipal);
    }

    const aviso = document.createElement('div');
    aviso.className = 'flash-message flash-message--error';
    aviso.setAttribute('role', 'alert');
    aviso.textContent = mensagem;

    area.appendChild(aviso);

    window.setTimeout(() => aviso.remove(), 5000);
}

// Lê o token CSRF da página ou informa quando ele está ausente.
function obterTokenCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!token) {
        throw new Error('Não foi possível validar esta ação. Atualize a página e tente novamente.');
    }

    return token;
}
