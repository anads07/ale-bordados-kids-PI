{{-- Exibe a personalização de uma linha do carrinho ou de um pedido. --}}
@props([
    'personalizacao',
    'mostrarObservacoes' => true,
    'mostrarVazios' => false,
    'formato' => 'lista',
])

@php
    $itens = collect([
        'cor_produto' => ['rotulo' => 'Cor da peça', 'valor' => $personalizacao['cor_produto'] ?? null, 'padrao' => 'Não informada'],
        'escrita' => ['rotulo' => 'Texto', 'valor' => $personalizacao['escrita'] ?? null, 'padrao' => 'Sem texto'],
        'cor_escrita' => ['rotulo' => 'Cor da linha', 'valor' => $personalizacao['cor_escrita'] ?? null, 'padrao' => 'Não informada'],
        'fonte_escrita' => ['rotulo' => 'Fonte', 'valor' => $personalizacao['fonte_escrita'] ?? null, 'padrao' => 'Não informada'],
        'desenho' => ['rotulo' => 'Desenho', 'valor' => $personalizacao['desenho'] ?? null, 'padrao' => 'Não informado'],
        'observacoes' => ['rotulo' => 'Observações', 'valor' => $personalizacao['observacoes'] ?? null, 'padrao' => 'Nenhuma observação'],
    ])->when(! $mostrarObservacoes, fn ($itens) => $itens->forget('observacoes'))
        ->filter(fn (array $item, string $chave) => $chave === 'cor_produto' || $mostrarVazios || filled($item['valor']));
@endphp

@if ($formato === 'definicao')
    @foreach ($itens as $item)
        <div>
            <dt>{{ $item['rotulo'] }}:</dt>
            <dd>{{ $item['valor'] ?: $item['padrao'] }}</dd>
        </div>
    @endforeach
@else
    @foreach ($itens as $item)
        <li>{{ $item['rotulo'] }}: {{ $item['valor'] ?: $item['padrao'] }}</li>
    @endforeach
@endif
