@extends('admin.layouts.admin')

@section('title', 'Painel administrativo | '.config('mock.store.name'))

{{-- Painel administrativo com indicadores, chats, pedidos recentes e resumo financeiro. --}}
@section('content')
    <div class="container-app admin-container admin-dashboard">
        <aside class="admin-dashboard__sidebar">
            <div class="admin-metrics">
                @foreach ($painel['metrics'] as $metrica)
                    <x-admin::metric-card
                        :icone="$metrica['icon']"
                        :rotulo="$metrica['label']"
                        :valor="$metrica['value']"
                    />
                @endforeach
            </div>

            <section class="admin-card admin-financial">
                <header class="admin-financial__header">
                    <h2 class="admin-card__title">Resumo financeiro:</h2>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="admin-financial__filter">
                        <select name="mes" class="admin-select" data-admin-auto-submit aria-label="Mês do resumo financeiro">
                            @foreach ($painel['financial']['month_options'] as $valorMes => $nomeMes)
                                <option value="{{ $valorMes }}" @selected((int) $painel['financial']['selected_month'] === (int) $valorMes)>
                                    {{ ucfirst($nomeMes) }}
                                </option>
                            @endforeach
                        </select>

                        <select name="ano" class="admin-select" data-admin-auto-submit aria-label="Ano do resumo financeiro">
                            @foreach ($painel['financial']['year_options'] as $ano)
                                <option value="{{ $ano }}" @selected((int) $painel['financial']['selected_year'] === (int) $ano)>
                                    {{ $ano }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </header>

                <p class="admin-financial__period">{{ $painel['financial']['period'] }}</p>

                <dl class="admin-financial__list">
                    <div>
                        <dt>Faturamento do mês:</dt>
                        <dd>{{ $painel['financial']['revenue'] }}</dd>
                    </div>
                    <div>
                        <dt>Pedidos concluídos:</dt>
                        <dd>{{ $painel['financial']['completed'] }}</dd>
                    </div>
                    <div>
                        <dt>Produtos vendidos:</dt>
                        <dd>{{ $painel['financial']['sold'] }}</dd>
                    </div>
                </dl>
            </section>
        </aside>

        <section class="admin-dashboard__center">
            <section class="admin-card admin-pendings-card">
                <h2 class="admin-card__title">Pendências importantes:</h2>

                <ul class="admin-pendings">
                    @foreach ($painel['pendings'] as $pendente)
                        <li class="admin-pending">
                            <span class="admin-pending__icon" aria-hidden="true">
                                <x-lucide-triangle-alert />
                            </span>

                            <span class="admin-pending__title">{{ $pendente['title'] }}</span>
                            <span class="admin-pending__description">{{ $pendente['description'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="admin-card admin-recent">
                <header class="admin-recent__header">
                    <h2 class="admin-card__title">Pedidos recentes:</h2>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="admin-filter">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="admin-select" data-admin-auto-submit>
                            <option value="">Todos</option>
                            @foreach ($situacoes as $valorStatus => $configuracaoStatus)
                                <option value="{{ $valorStatus }}" @selected($situacaoAtual === $valorStatus)>{{ $configuracaoStatus['label'] }}</option>
                            @endforeach
                        </select>
                    </form>
                </header>

                <div class="admin-table-wrap">
                    <table class="admin-table admin-table--recent">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pedidosRecentes as $pedido)
                                <tr>
                                    <td class="admin-order__id">#{{ $pedido->id_pedido }}</td>
                                    <td>
                                        <span class="admin-order__customer">
                                            {{ $pedido->cliente?->nome_usuario }} {{ $pedido->cliente?->sobrenome_usuario }}
                                            <small>{{ $pedido->cliente?->email }}</small>
                                        </span>
                                    </td>
                                    <td><x-shared::status-badge :situacao="$pedido->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="admin-table__empty">Nenhum pedido recente no momento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pedidosRecentes->hasPages())
                    <div class="admin-pagination">
                        {{ $pedidosRecentes->onEachSide(1)->links() }}
                    </div>
                @endif
            </section>
        </section>

        <aside class="admin-dashboard__right">
            <section class="admin-card admin-chat-contacts" aria-labelledby="admin-chat-contacts-title">
                <header class="admin-chat-contacts__header">
                    <h2 class="admin-card__title" id="admin-chat-contacts-title">Chats:</h2>
                    <span class="admin-chat-contacts__description">Conversas recentes</span>
                </header>

                <ul class="admin-chat-contacts__list">
                    @forelse ($painel['chat']['contacts'] ?? [] as $contato)
                        <li class="admin-chat-contacts__item">
                            <button
                                type="button"
                                class="admin-chat-contact"
                                data-chat-contact
                                data-chat-name="{{ $contato['name'] }}"
                                data-chat-tag="{{ $contato['tag'] }}"
                                data-chat-message="{{ $contato['message'] }}"
                                aria-label="Abrir conversa com {{ $contato['name'] }}, {{ $contato['unread'] ?? 0 }} mensagens não lidas"
                            >
                                <span class="admin-chat-contact__name">{{ $contato['name'] }}</span>

                                <span class="admin-chat-contact__notifications">
                                    @if (($contato['unread'] ?? 0) > 0)
                                        <span class="admin-chat-contact__unread">({{ $contato['unread'] }})</span>
                                    @endif

                                    <span class="admin-chat-contact__bell" aria-hidden="true">
                                        <x-lucide-bell />
                                    </span>
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="admin-chat-contacts__empty">Nenhuma conversa no momento.</li>
                    @endforelse
                </ul>
            </section>

            <section class="admin-card admin-chart-card">
                <header class="admin-chart-card__header">
                    <h2 class="admin-card__title">{{ $painel['revenue']['title'] }}</h2>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="admin-financial__filter">
                        <select name="ano_receita" class="admin-select" data-admin-auto-submit aria-label="Ano do faturamento">
                            @foreach ($painel['revenue']['year_options'] as $anoReceita)
                                <option value="{{ $anoReceita }}" @selected((int) $painel['revenue']['selected_year'] === (int) $anoReceita)>
                                    {{ $anoReceita }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </header>

                <x-admin::line-chart
                    :rotulos="$painel['revenue']['labels']"
                    :series="[[
                        'label' => 'Faturamento',
                        'color' => $painel['revenue']['color'],
                        'values' => $painel['revenue']['values'],
                        'fill' => $painel['revenue']['fill'],
                    ]]"
                    :minimo="$painel['revenue']['min']"
                    :maximo="$painel['revenue']['max']"
                    :formato="$painel['revenue']['format']"
                    :mostrar-legenda="false"
                    :altura="210"
                    rotulo-aria="Faturamento mensal simulado"
                />
            </section>
        </aside>
    </div>
@endsection
