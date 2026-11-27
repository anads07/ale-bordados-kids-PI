// Inicializa os módulos JavaScript do painel administrativo.
import { iniciarAvisoEmBreve } from '../shared/coming-soon';
import { iniciarChat } from './chat';
import { iniciarConfirmacao } from './confirm';
import { iniciarFiltrosAdmin } from './filters';
import { iniciarModais } from './modals';
import { iniciarPreviaImagem } from './product-image';

document.addEventListener('DOMContentLoaded', () => {
    iniciarAvisoEmBreve();
    iniciarModais();
    iniciarConfirmacao();
    iniciarChat();
    iniciarPreviaImagem();
    iniciarFiltrosAdmin();
});
