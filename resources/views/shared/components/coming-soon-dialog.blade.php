{{-- Informa quando uma funcionalidade ainda não está disponível. --}}
<dialog id="coming-soon-dialog" class="coming-soon-dialog" aria-labelledby="coming-soon-title">
    <div class="coming-soon-dialog__content">
        <span class="coming-soon-dialog__icon" aria-hidden="true">
            <x-lucide-clock />
        </span>

        <h2 id="coming-soon-title">Função em andamento</h2>
        <p id="coming-soon-text">Esta funcionalidade estará disponível em breve.</p>

        <button type="button" class="btn btn-primary" data-dialog-close>Entendi</button>
    </div>
</dialog>
