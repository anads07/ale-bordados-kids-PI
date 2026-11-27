@extends('user.layouts.public')

@section('title', 'Orçamento de peça própria | '.config('mock.store.name'))

{{-- Página placeholder da funcionalidade de orçamento. --}}
@section('content')
    <section class="container-app section-gap">
        <a href="{{ route('home') }}" class="back-link">
            <x-lucide-chevron-left aria-hidden="true" />
            Voltar para a página inicial
        </a>

        <div class="budget-card">
            <div class="budget-card__icon" aria-hidden="true">
                <x-lucide-clock-3 aria-hidden="true" />
            </div>
            <p class="section-eyebrow">Função em andamento</p>
            <h1 class="budget-card__title">Orçamento de peça própria</h1>
            <p class="budget-card__text">
                Em breve você poderá enviar fotos e detalhes da sua peça para receber um orçamento personalizado. Esta funcionalidade ainda está em desenvolvimento.
            </p>

            <div class="budget-steps">
                <div class="budget-step">1. Envio de fotos da peça</div>
                <div class="budget-step">2. Descrição do bordado desejado</div>
                <div class="budget-step">3. Recebimento do orçamento</div>
            </div>

            <button type="button" class="btn btn-primary" data-coming-soon="Solicitação de orçamento" aria-disabled="true">
                Enviar solicitação
            </button>
        </div>
    </section>
@endsection
