{{-- Mostra personalização, entrega e ações disponíveis para o pedido. --}}
@props([
    'pedido',
    'situacoes',
    'pedidosMock',
])

<dialog
    id="pedido-modal-{{ $pedido->id_pedido }}"
    class="admin-order-modal"
    data-admin-modal
    aria-labelledby="pedido-modal-title-{{ $pedido->id_pedido }}"
>
    <div class="admin-order-modal__panel">
        <header class="admin-order-modal__header">
            <h2 id="pedido-modal-title-{{ $pedido->id_pedido }}">Detalhes do pedido: #{{ $pedido->id_pedido }}</h2>

            <button type="button" class="admin-modal-close" data-admin-modal-close aria-label="Fechar detalhes do pedido">
                <x-lucide-x aria-hidden="true" />
            </button>
        </header>

        <div class="admin-order-modal__body">
            <section class="admin-order-modal__main">
                <p class="admin-order-modal__product">
                    <span>Produto:</span>
                    {{ $pedido->produtoRelacionado?->nome_produto ?? 'Produto removido' }}
                </p>

                <h3>Personalização:</h3>
                <dl class="admin-order-modal__customization">
                    <div>
                        <dt>Cor da peça:</dt>
                        <dd>{{ $pedido->cor_produto ?: 'Não informada' }}</dd>
                    </div>
                    <div>
                        <dt>Texto:</dt>
                        <dd>{{ $pedido->escrita ?: 'Sem texto' }}</dd>
                    </div>
                    <div>
                        <dt>Cor(es) da linha:</dt>
                        <dd>{{ $pedido->cor_escrita ?: 'Não informada' }}</dd>
                    </div>
                    <div>
                        <dt>Fonte:</dt>
                        <dd>{{ $pedido->fonte_escrita ?: 'Não informada' }}</dd>
                    </div>
                    <div>
                        <dt>Desenho:</dt>
                        <dd>{{ $pedido->desenho ?: 'Não informado' }}</dd>
                    </div>
                    <div>
                        <dt>Observações:</dt>
                        <dd>{{ $pedido->observacoes ?: 'Nenhuma observação' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="admin-order-modal__inspiration">
                <h3>Inspiração:</h3>

                @if ($pedido->imagem_inspiracao_url)
                    <a href="{{ $pedido->imagem_inspiracao_url }}" target="_blank" rel="noopener" class="admin-order-modal__image-link">
                        <img src="{{ $pedido->imagem_inspiracao_url }}" alt="Imagem de inspiração do pedido #{{ $pedido->id_pedido }}">
                    </a>
                @else
                    <div class="admin-order-modal__image-placeholder">
                        <x-lucide-image-off aria-hidden="true" />
                        <span>Imagem não enviada</span>
                    </div>
                @endif
            </aside>
        </div>

        <div class="admin-order-modal__facts">
            <div>
                <span>Quantidade:</span>
                <strong>{{ $pedido->quantidade }}</strong>
            </div>
            <div>
                <span>Preço Total:</span>
                <strong>{{ $pedido->preco_total_formatado }}</strong>
            </div>
            <div>
                <span>Prazo de entrega:</span>
                <strong>{{ $pedidosMock['deadline_days'] }} dias</strong>
            </div>
            <div>
                <span>Endereço:</span>
                <strong>{{ $pedido->endereco_formatado }}</strong>
            </div>
            <div>
                <span>Pagamento:</span>
                <strong>{{ $pedido->forma_pagamento ?: 'Não informado' }}</strong>
            </div>
        </div>

        <div class="admin-order-modal__actions">
            <form
                action="{{ route('admin.pedidos.status', $pedido) }}"
                method="POST"
                class="admin-order-modal__status"
            >
                @csrf
                @method('PATCH')

                <label for="pedido-status-{{ $pedido->id_pedido }}">Status do pedido</label>

                <div class="admin-order-modal__status-row">
                    <select id="pedido-status-{{ $pedido->id_pedido }}" name="status" class="admin-select">
                        @foreach ($situacoes as $chaveStatus => $configuracaoStatus)
                            <option value="{{ $chaveStatus }}" @selected($pedido->status === $chaveStatus)>{{ $configuracaoStatus['label'] }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-outline">Atualizar status</button>
                </div>
            </form>

            <button
                type="button"
                class="btn btn-outline"
                data-chat-open
                data-chat-name="{{ $pedido->cliente?->nome_usuario ?? 'Cliente' }}"
                data-chat-order="#{{ $pedido->id_pedido }}"
                data-chat-message="Olá! Podemos ajudar com o pedido #{{ $pedido->id_pedido }}?"
            >
                Chat com o cliente
            </button>
        </div>
    </div>
</dialog>
