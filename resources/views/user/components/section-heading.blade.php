{{-- Padroniza o título e o subtítulo das seções da vitrine. --}}
@props([
    'titulo',
    'subtitulo' => null,
])

<div class="section-heading">
    <h2>{{ $titulo }}</h2>

    @if ($subtitulo)
        <p>{{ $subtitulo }}</p>
    @endif
</div>
