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

    // Mostra a imagem escolhida antes do envio do formulário.
    campoImagem?.addEventListener('change', () => {
        const arquivo = campoImagem.files?.[0];

        if (!arquivo) {
            if (previa) {
                previa.removeAttribute('src');
                previa.hidden = true;
            }

            if (nomeArquivo) {
                nomeArquivo.textContent = 'Nenhum arquivo selecionado';
            }

            return;
        }

        if (nomeArquivo) {
            nomeArquivo.textContent = arquivo.name;
        }

        if (previa) {
            previa.src = URL.createObjectURL(arquivo);
            previa.hidden = false;
        }
    });
}
