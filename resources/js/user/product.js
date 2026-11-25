// Controla a quantidade e a prévia da imagem na página do produto.

// Registra os controles de quantidade e de imagem.
export function iniciarDetalheProduto() {
    const campoQuantidade = document.querySelector('[data-product-quantity]');
    const botoesPasso = document.querySelectorAll('[data-product-step]');

    // Mantém a quantidade dentro dos limites exibidos pelo próprio campo.
    botoesPasso.forEach((botao) => {
        botao.addEventListener('click', () => {
            if (!campoQuantidade) {
                return;
            }

            const minimo = Number(campoQuantidade.min || 1);
            const maximo = Number(campoQuantidade.max || 99);
            const proximo = Math.min(
                maximo,
                Math.max(minimo, Number(campoQuantidade.value || minimo) + Number(botao.dataset.productStep))
            );

            campoQuantidade.value = String(proximo);
        });
    });

    const campoImagem = document.querySelector('[data-product-image-input]');
    const previa = document.querySelector('[data-product-image-preview]');
    const nomeArquivo = document.querySelector('[data-product-image-name]');

    let urlPrevia = null;

    const limparPrevia = () => {
        if (urlPrevia) {
            URL.revokeObjectURL(urlPrevia);
            urlPrevia = null;
        }

        if (previa) {
            previa.removeAttribute('src');
            previa.hidden = true;
        }

        if (nomeArquivo) {
            nomeArquivo.textContent = 'Nenhum arquivo selecionado';
        }
    };

    // Mostra a imagem escolhida antes do envio do formulário.
    campoImagem?.addEventListener('change', () => {
        const arquivo = campoImagem.files?.[0];

        if (!arquivo) {
            limparPrevia();
            return;
        }

        if (!arquivo.type.startsWith('image/')) {
            campoImagem.value = '';
            limparPrevia();
            return;
        }

        if (urlPrevia) {
            URL.revokeObjectURL(urlPrevia);
        }

        urlPrevia = URL.createObjectURL(arquivo);

        if (nomeArquivo) {
            nomeArquivo.textContent = arquivo.name;
        }

        if (previa) {
            previa.src = urlPrevia;
            previa.hidden = false;
        }
    });

    window.addEventListener('pagehide', () => {
        if (urlPrevia) {
            URL.revokeObjectURL(urlPrevia);
            urlPrevia = null;
        }
    });
}
