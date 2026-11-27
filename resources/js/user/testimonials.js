// Controla as avaliações deslizantes e o estado das setas.
export function iniciarDepoimentos() {
    const carrossel = document.querySelector('[data-testimonial-carousel]');

    if (!carrossel) {
        return;
    }

    const trilha = carrossel.querySelector('[data-testimonial-track]');
    const anterior = carrossel.querySelector('[data-testimonial-prev]');
    const proximo = carrossel.querySelector('[data-testimonial-next]');

    if (!trilha || !anterior || !proximo) {
        return;
    }

    const passo = () => Math.max(240, Math.round(trilha.clientWidth * 0.8));

    const atualizarSetas = () => {
        const maximo = trilha.scrollWidth - trilha.clientWidth;
        anterior.disabled = trilha.scrollLeft <= 4;
        proximo.disabled = trilha.scrollLeft >= maximo - 4;
    };

    anterior.addEventListener('click', () => trilha.scrollBy({ left: -passo(), behavior: 'smooth' }));
    proximo.addEventListener('click', () => trilha.scrollBy({ left: passo(), behavior: 'smooth' }));
    trilha.addEventListener('scroll', atualizarSetas, { passive: true });
    window.addEventListener('resize', atualizarSetas);
    atualizarSetas();
}
