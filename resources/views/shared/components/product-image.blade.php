{{-- Mostra a foto do produto ou um ícone quando ela não existe. --}}
@props([
    'produto',
    'class' => '',
    'loading' => 'lazy',
])

@php
    $url = $produto->imagem_url;
@endphp

@if ($url)
    <img
        src="{{ $url }}"
        alt="{{ $produto->nome_produto }}"
        @if ($class !== '') class="{{ $class }}" @endif
        loading="{{ $loading }}"
    >
@else
    <span class="{{ trim($class.' product-image-fallback') }}" role="img" aria-label="{{ $produto->nome_produto }}">
        <x-lucide-shirt aria-hidden="true" />
    </span>
@endif
