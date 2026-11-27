// Inicializa os módulos JavaScript da loja.
import { iniciarAvisoEmBreve } from '../shared/coming-soon';
import { iniciarCarrinho } from './cart';
import { iniciarCheckoutModal } from './checkout-modal';
import { iniciarFavoritos } from './favorites';
import { iniciarFiltros } from './filters';
import { iniciarFormularios } from './forms';
import { iniciarCarrosselHero } from './hero';
import { iniciarMenu } from './menu';
import { iniciarDetalheProduto } from './product';
import { iniciarDepoimentos } from './testimonials';

document.addEventListener('DOMContentLoaded', () => {
    iniciarCarrinho();
    iniciarFavoritos();
    iniciarFiltros();
    iniciarCarrosselHero();
    iniciarDetalheProduto();
    iniciarMenu();
    iniciarAvisoEmBreve();
    iniciarDepoimentos();
    iniciarFormularios();
    iniciarCheckoutModal();
});
