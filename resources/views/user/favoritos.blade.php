@extends('user.layouts.public')

@section('title', 'Meus favoritos | '.config('mock.store.name'))

{{-- Lista os produtos favoritos e permite removê-los ou voltar à vitrine. --}}
@section('content')
    <section class="container-app favorites-page">
        <x-user::section-heading titulo="Meus favoritos" />

        <p class="favorites-summary" data-favorites-summary aria-live="polite">
            {{ $produtos->count() }} {{ $produtos->count() === 1 ? 'peça salva' : 'peças salvas' }}
        </p>

        <div class="product-grid" data-favorites-grid @if ($produtos->isEmpty()) hidden @endif>
            @foreach ($produtos as $produto)
                <x-user::product-card :produto="$produto" :favoritado="true" />
            @endforeach
        </div>

        <div class="empty-state favorites-empty" data-favorites-empty @if ($produtos->isNotEmpty()) hidden @endif>
            <p>Você ainda não salvou nenhuma peça.</p>
            <a href="{{ route('home') }}#produtos" class="btn btn-outline">Explorar a coleção</a>
        </div>
    </section>
@endsection
