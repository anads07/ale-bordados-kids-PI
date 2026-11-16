{{-- Exibe o rótulo e a classe definidos para o status do pedido. --}}
@props([
    'situacao',
])

@php
    $mapa = config('mock.status', []);
    $registro = $mapa[$situacao] ?? [
        'label' => ucfirst((string) $situacao),
        'class' => 'badge-cancelado',
    ];
@endphp

<span class="badge {{ $registro['class'] }}">{{ $registro['label'] }}</span>
