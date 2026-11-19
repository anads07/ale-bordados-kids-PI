// Controla o carrossel de banners de destaque da página inicial.

const INTERVALO_TROCA = 6000;

export function iniciarCarrosselHero() {
    const carrossel = document.querySelector('[data-hero-carousel]');

    if (!carrossel) {
        return;
    }

    const slides = Array.from(carrossel.querySelectorAll('[data-hero-slide]'));
    const indicadores = Array.from(carrossel.querySelectorAll('[data-hero-dot]'));

    if (slides.length < 2) {
        return;
    }

    let atual = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
    let temporizador = null;
    const reduzMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const mostrar = (indice) => {
        atual = (indice + slides.length) % slides.length;

        slides.forEach((slide, posicao) => {
            const ativo = posicao === atual;
            slide.classList.toggle('is-active', ativo);
            slide.setAttribute('aria-hidden', String(!ativo));
            slide.inert = !ativo;
        });

        indicadores.forEach((indicador, posicao) => {
            const ativo = posicao === atual;
            indicador.classList.toggle('is-active', ativo);
            indicador.setAttribute('aria-current', String(ativo));
        });
    };

    const parar = () => {
        if (temporizador !== null) {
            window.clearInterval(temporizador);
            temporizador = null;
        }
    };

    const iniciar = () => {
        parar();

        if (!reduzMovimento) {
            temporizador = window.setInterval(() => mostrar(atual + 1), INTERVALO_TROCA);
        }
    };

    const irPara = (indice) => {
        mostrar(indice);
        iniciar();
    };

    carrossel.querySelector('[data-hero-prev]')?.addEventListener('click', () => irPara(atual - 1));
    carrossel.querySelector('[data-hero-next]')?.addEventListener('click', () => irPara(atual + 1));

    indicadores.forEach((indicador, posicao) => {
        indicador.addEventListener('click', () => irPara(posicao));
    });

    // Pausa a troca automática enquanto o mouse ou o teclado estiver no banner.
    carrossel.addEventListener('mouseenter', parar);
    carrossel.addEventListener('mouseleave', iniciar);
    carrossel.addEventListener('focusin', parar);
    carrossel.addEventListener('focusout', (evento) => {
        if (!carrossel.contains(evento.relatedTarget)) {
            iniciar();
        }
    });

    carrossel.addEventListener('keydown', (evento) => {
        if (evento.key === 'ArrowLeft') {
            irPara(atual - 1);
        }

        if (evento.key === 'ArrowRight') {
            irPara(atual + 1);
        }
    });

    // Arrastar no celular também troca de banner.
    let inicioToque = null;

    carrossel.addEventListener('touchstart', (evento) => {
        inicioToque = evento.touches[0]?.clientX ?? null;
    }, { passive: true });

    carrossel.addEventListener('touchend', (evento) => {
        if (inicioToque === null) {
            return;
        }

        const deslocamento = (evento.changedTouches[0]?.clientX ?? inicioToque) - inicioToque;

        if (Math.abs(deslocamento) > 40) {
            irPara(atual + (deslocamento < 0 ? 1 : -1));
        }

        inicioToque = null;
    }, { passive: true });

    iniciar();
}
