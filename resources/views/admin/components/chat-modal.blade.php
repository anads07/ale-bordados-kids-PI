{{-- Exibe a conversa simulada do cliente em um diálogo central. --}}
<dialog class="admin-chat-modal" data-chat-modal aria-labelledby="admin-chat-modal-title">
    <div class="admin-chat-modal__panel">
        <header class="admin-chat-modal__header">
            <div class="admin-chat-modal__contact">
                <strong id="admin-chat-modal-title" data-chat-modal-name>Conversa</strong>
                <span class="admin-chat__tag" data-chat-modal-tag></span>
            </div>

            <button type="button" class="admin-modal-close" data-chat-modal-close aria-label="Fechar chat">
                <x-lucide-x aria-hidden="true" />
            </button>
        </header>

        <div class="admin-chat-modal__messages">
            <p class="admin-chat__bubble" data-chat-modal-message></p>
        </div>

        <form class="admin-chat-modal__composer" data-chat-modal-composer>
            <input type="text" placeholder="Digite aqui..." aria-label="Mensagem para o cliente" data-chat-modal-input>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        <p class="admin-chat-modal__notice" data-chat-modal-notice hidden>
            O envio de mensagens estará disponível em breve.
        </p>
    </div>
</dialog>
