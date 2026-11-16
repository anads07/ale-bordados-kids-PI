{{-- Mostra um indicador do painel com seu ícone e valor. --}}
@props([
    'icone' => 'box',
    'rotulo' => '',
    'valor' => '',
])

<article class="admin-metric">
    <span class="admin-metric__icon" aria-hidden="true">
        @switch($icone)
            @case('cart')
                <x-lucide-shopping-cart />
            @break

            @case('document')
                <x-lucide-file-text />
            @break

            @case('card')
                <x-lucide-credit-card />
            @break

            @default
                <x-lucide-package />
        @endswitch
    </span>

    <span class="admin-metric__label">{{ $rotulo }}</span>
    <strong class="admin-metric__value">{{ $valor }}</strong>
</article>
