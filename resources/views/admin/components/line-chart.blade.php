{{-- Desenha o gráfico administrativo a partir das séries recebidas. --}}
@props([
    'rotulos' => [],
    'series' => [],
    'minimo' => 0,
    'maximo' => 20,
    'altura' => 220,
    'formato' => 'number',
    'mostrarLegenda' => true,
    'rotuloAria' => 'Gráfico administrativo',
])

@php
    $altura = max((int) $altura, 120);
    $intervalo = max($maximo - $minimo, 1);
    $formatarValor = fn (float $valor) => $formato === 'currency'
        ? number_format($valor, 0, ',', '.')
        : (string) round($valor);
    $marcacoes = collect(range(0, 4))->map(fn (int $indice) => $maximo - (($intervalo * $indice) / 4));
    $mostrarTodosRotulos = count($rotulos) <= 8;
@endphp

<div class="admin-chart">
    <div class="admin-chart__body" style="--admin-chart-height: {{ $altura }}px">
        <div class="admin-chart__y-axis" aria-hidden="true">
            @foreach ($marcacoes as $marcacao)
                <span>{{ $formatarValor($marcacao) }}</span>
            @endforeach
        </div>

        <div class="admin-chart__plot" role="img" aria-label="{{ $rotuloAria }}">
            <div class="admin-chart__grid" aria-hidden="true">
                @foreach ($marcacoes as $marcacao)
                    <span></span>
                @endforeach
            </div>

            <div class="admin-chart__groups">
                @foreach ($rotulos as $indice => $rotulo)
                    <div class="admin-chart__group">
                        @foreach ($series as $serie)
                            @php
                                $valores = array_values($serie['values'] ?? []);
                                $valor = (float) ($valores[$indice] ?? 0);
                                $percentual = max(0, min(100, (($valor - $minimo) / $intervalo) * 100));
                            @endphp

                            <span
                                class="admin-chart__bar"
                                style="height: {{ $percentual }}%; background-color: {{ $serie['color'] }};"
                                title="{{ $serie['label'] }}: {{ $formatarValor($valor) }}"
                            ></span>
                        @endforeach

                        @if ($mostrarTodosRotulos || $indice % 2 === 0)
                            <span class="admin-chart__label">{{ $rotulo }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($mostrarLegenda)
        <div class="admin-chart__legend">
            @foreach ($series as $serie)
                <span>
                    <i style="background-color: {{ $serie['color'] }}"></i>
                    {{ $serie['label'] }}
                </span>
            @endforeach
        </div>
    @endif
</div>
