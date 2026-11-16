{{-- Desenha a nota com estrelas cheias, meia estrela e estrelas vazias. --}}
@props([
    'nota' => 4.5,
    'total' => null,
])

@php
    // Limita a nota entre zero e cinco antes de calcular as estrelas.
    $notaNormalizada = max(0, min(5, (float) $nota));
    $estrelasCheias = (int) floor($notaNormalizada);
    $meiaEstrela = ($notaNormalizada - $estrelasCheias) >= 0.5;
    $estrelasVazias = 5 - $estrelasCheias - ($meiaEstrela ? 1 : 0);
@endphp

<span class="stars" role="img" aria-label="Avaliação {{ number_format($notaNormalizada, 1, ',', '.') }} de 5">
    @for ($indice = 0; $indice < $estrelasCheias; $indice++)
        <x-lucide-star class="star-full" aria-hidden="true" />
    @endfor

    @if ($meiaEstrela)
        <span class="star-half" aria-hidden="true">
            <x-lucide-star class="star-half__base" />
            <x-lucide-star class="star-half__fill" />
        </span>
    @endif

    @for ($indice = 0; $indice < $estrelasVazias; $indice++)
        <x-lucide-star class="star-empty" aria-hidden="true" />
    @endfor

    @if (! is_null($total))
        <span class="stars-count">({{ $total }})</span>
    @endif
</span>
