@extends('user.layouts.public')

@section('title', 'Orçamento de peça própria | '.config('mock.store.name'))

{{-- Página placeholder da funcionalidade de orçamento. --}}
@section('content')
    <section class="container-app section-gap">
        <a href="{{ route('home') }}" class="back-link">
            <x-lucide-chevron-left aria-hidden="true" />
            Voltar para a página inicial
        </a>

        <div class="coming-soon-dialog__content" style="max-width: 760px; margin: 24px auto 0; padding: 32px; border: 1px solid var(--cor-borda); border-radius: 24px; background: var(--cor-fundo); box-shadow: 0 12px 30px var(--sombra-suave); text-align: center;">
            <div class="coming-soon-dialog__icon" aria-hidden="true" style="margin: 0 auto 16px;">
                <x-lucide-clock-3 />
            </div>
            <p class="section-eyebrow">Função em andamento</p>
            <h1 style="font-size: clamp(1.8rem, 4vw, 2.6rem); margin-bottom: 12px;">Orçamento de peça própria</h1>
            <p class="coming-soon-dialog__text" style="margin: 0 auto 24px; max-width: 560px;">
                Em breve você poderá enviar fotos e detalhes da sua peça para receber um orçamento personalizado. Esta funcionalidade ainda está em desenvolvimento.
            </p>

            <div style="display: grid; gap: 12px; max-width: 560px; margin: 0 auto 24px; text-align: left;">
                <div style="padding: 14px 16px; border: 1px dashed var(--cor-borda); border-radius: 14px; background: var(--cor-fundo-secundario); color: var(--cor-texto-suave);">
                    1. Envio de fotos da peça
                </div>
                <div style="padding: 14px 16px; border: 1px dashed var(--cor-borda); border-radius: 14px; background: var(--cor-fundo-secundario); color: var(--cor-texto-suave);">
                    2. Descrição do bordado desejado
                </div>
                <div style="padding: 14px 16px; border: 1px dashed var(--cor-borda); border-radius: 14px; background: var(--cor-fundo-secundario); color: var(--cor-texto-suave);">
                    3. Recebimento do orçamento
                </div>
            </div>

            <button type="button" class="btn btn-primary" data-coming-soon="Solicitação de orçamento" aria-disabled="true">
                Enviar solicitação
            </button>
        </div>
    </section>
@endsection
