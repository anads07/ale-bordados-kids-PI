{{-- Mostra o mês, os dias com pedidos e a data selecionada. --}}
@props([
    'mes' => null,
    'dataSelecionada' => null,
    'diasComPedidos' => [],
    'anosCalendario' => [],
])

@php
    $mes = $mes ?? now()->startOfMonth();
    $dataSelecionada = $dataSelecionada ?? now();
    $diasDaSemana = config('mock.admin.weekdays', []);
    $meses = config('mock.admin.months', []);
    $anosCalendario = collect($anosCalendario ?: range(now()->year - 1, now()->year + 2))
        ->push($mes->year)
        ->unique()
        ->sort()
        ->values()
        ->all();
    $dataCorrente = $mes->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $ultimoDia = $mes->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $dias = [];

    while ($dataCorrente->lte($ultimoDia)) {
        $dias[] = $dataCorrente->copy();
        $dataCorrente->addDay();
    }
@endphp

<div class="admin-calendar">
    <header class="admin-calendar__header">
        <strong>{{ ucfirst($meses[$mes->month] ?? $mes->format('F')) }} {{ $mes->year }}</strong>

        <form action="{{ route('admin.pedidos.index') }}" method="GET" class="admin-calendar__filters">
            <select name="mes" data-admin-auto-submit aria-label="Mês do calendário">
                @foreach ($meses as $numeroMes => $nomeMes)
                    <option value="{{ $numeroMes }}" @selected((int) $numeroMes === $mes->month)>
                        {{ ucfirst($nomeMes) }}
                    </option>
                @endforeach
            </select>

            <select name="ano" data-admin-auto-submit aria-label="Ano do calendário">
                @foreach ($anosCalendario as $ano)
                    <option value="{{ $ano }}" @selected((int) $ano === $mes->year)>{{ $ano }}</option>
                @endforeach
            </select>
        </form>
    </header>

    <div class="admin-calendar__grid" role="grid" aria-label="Calendário de pedidos">
        @foreach ($diasDaSemana as $diaDaSemana)
            <span class="admin-calendar__weekday" role="columnheader">{{ $diaDaSemana }}</span>
        @endforeach

        @foreach ($dias as $dia)
            @php
                $chaveData = $dia->toDateString();
                $foraDoMes = ! $dia->isSameMonth($mes);
                $selecionado = $dia->isSameDay($dataSelecionada);
                $temPedidos = in_array($chaveData, $diasComPedidos, true);
            @endphp

            <a
                href="{{ route('admin.pedidos.index', ['data' => $chaveData]) }}"
                class="admin-calendar__day {{ $foraDoMes ? 'is-outside' : '' }} {{ $selecionado ? 'is-selected' : '' }} {{ $temPedidos ? 'has-orders' : '' }}"
                aria-label="Ver pedidos de {{ $dia->format('d/m/Y') }}"
                @if ($selecionado) aria-current="date" @endif
            >
                {{ $dia->day }}
            </a>
        @endforeach
    </div>
</div>
