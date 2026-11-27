// Mostra o nome e a prévia local da imagem escolhida no formulário.
export function iniciarPreviaImagem() {
    document.querySelectorAll('[data-admin-product-image]').forEach((campo) => {
        const nomeArquivo = document.querySelector('[data-admin-product-image-name]');
        const previa = document.querySelector('[data-admin-product-image-preview]');
        let urlPrevia = null;

        campo.addEventListener('change', () => {
            const arquivo = campo.files?.[0];

            if (urlPrevia) {
                URL.revokeObjectURL(urlPrevia);
                urlPrevia = null;
            }

            if (nomeArquivo) {
                nomeArquivo.textContent = arquivo ? arquivo.name : 'Nenhum arquivo selecionado';
            }

            if (!previa) {
                return;
            }

            if (!arquivo || !arquivo.type.startsWith('image/')) {
                previa.hidden = true;
                previa.removeAttribute('src');
                return;
            }

            urlPrevia = URL.createObjectURL(arquivo);
            previa.src = urlPrevia;
            previa.hidden = false;
        });
    });
}
