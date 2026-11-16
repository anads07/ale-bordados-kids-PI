@extends('admin.layouts.admin')

@section('title', 'Gerenciar pedidos | '.config('mock.store.name'))

{{-- Lista os pedidos do dia e reúne calendário, gráfico e atualização de status. --}}
@section('content')
    @php
        $mesSelecionado = config('mock.admin.months.'.$dataSelecionada->month, $dataSelecionada->format('F'));
    @endphp

    <div class="container-app admin-container admin-orders">
        <div class="admin-orders__layout">
            <aside class="admin-orders__sidebar">
                <section class="admin-card admin-calendar-card">
                    <x-admin::calendar
                        :mes="$mesCalendario"
                        :data-selecionada="$dataSelecionada"
                        :dias-com-pedidos="$diasComPedidos"
                        :anos-calendario="$anosCalendario"
                    />
                </section>

                <section class="admin-card admin-chart-card">
                    <h2 class="admin-card__title">{{ $pedidosMock['chart']['title'] }}</h2>

                    <x-admin::line-chart
                        :rotulos="$pedidosMock['chart']['labels']"
                        :series="$pedidosMock['chart']['series']"
                        :minimo="$pedidosMock['chart']['min']"
                        :maximo="$pedidosMock['chart']['max']"
                        :formato="$pedidosMock['chart']['format']"
                        :altura="230"
                        rotulo-aria="Pedidos e entregas por mês, dados simulados"
                    />
                </section>
            </aside>

            <section class="admin-orders__main">
                <header class="admin-orders__heading">
                    <span class="admin-date-chip" aria-hidden="true">
                        <x-lucide-calendar-days />
                        <strong>{{ $dataSelecionada->format('d') }}</strong>
                    </span>

                    <div class="admin-orders__heading-text">
                        <p>Pedidos | {{ $dataSelecionada->day }} de {{ ucfirst($mesSelecionado) }} de {{ $dataSelecionada->year }}</p>
                        <small>{{ $pedidosDoDia->count() }} pedido(s) neste dia</small>
                    </div>

                    <nav class="admin-day-nav" aria-label="Navegação por dia">
                        <a
                            href="{{ route('admin.pedidos.index', ['data' => $dataAnterior->toDateString()]) }}"
                            aria-label="Ver pedidos de {{ $dataAnterior->format('d/m/Y') }}"
                        >
                            <x-lucide-chevron-left aria-hidden="true" />
                        </a>
                        <a
                            href="{{ route('admin.pedidos.index', ['data' => $dataProxima->toDateString()]) }}"
                            aria-label="Ver pedidos de {{ $dataProxima->format('d/m/Y') }}"
                        >
                            <x-lucide-chevron-right aria-hidden="true" />
                        </a>
                    </nav>
                </header>

                @if ($pedidosDoDia->isEmpty())
                    <div class="admin-card admin-orders__empty">
                        <x-lucide-calendar-days aria-hidden="true" />
                        <p>Nenhum pedido cadastrado para esta data.</p>
                    </div>
                @else
                    <div class="admin-order-board-grid">
                        @foreach ($pedidosDoDia as $pedido)
                            <article class="admin-order-board">
                                <h2>
                                    id: {{ $pedido->id_pedido }} |
                                    {{ $pedido->cliente?->nome_usuario }} {{ $pedido->cliente?->sobrenome_usuario }}
                                </h2>

                                <div class="admin-order-board__stages">
                                    @foreach ($pedido->etapas as $etapa)
                                        <span class="admin-order-board__stage {{ $etapa['done'] ? 'is-done' : '' }}">
                                            {{ $etapa['label'] }}
                                        </span>
                                    @endforeach
                                </div>

                                <button
                                    type="button"
                                    class="btn admin-order-board__details"
                                    data-admin-modal-target="pedido-modal-{{ $pedido->id_pedido }}"
                                >
                                    ver detalhes
                                </button>
                            </article>
                        @endforeach
                    </div>

                    @foreach ($pedidosDoDia as $pedido)
                        <x-admin::order-details-modal
                            :pedido="$pedido"
                            :situacoes="$situacoes"
                            :pedidos-mock="$pedidosMock"
                        />
                    @endforeach
                @endif
            </section>
        </div>
    </div>
@endsection
