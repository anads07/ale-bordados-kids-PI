@extends('user.layouts.public')

@section('title', 'Pedido confirmado | '.config('mock.store.name'))

{{-- Confirma o recebimento do pedido personalizado. --}}
@section('content')
    <section class="container-app checkout-success">
        <div class="checkout-success__card">
            <span class="checkout-success__icon" aria-hidden="true">
                <x-lucide-check />
            </span>

            <p class="checkout-eyebrow">Pedido confirmado</p>
            <h1>Obrigada pelo carinho!</h1>
            <p>
                Seu pedido foi registrado com sucesso. Em breve nossa equipe entrará em contato
                para combinar os detalhes da produção.
            </p>

            <dl class="checkout-success__details">
                <div>
                    <dt>Data</dt>
                    <dd>{{ $data }}</dd>
                </div>
                <div>
                    <dt>Itens registrados</dt>
                    <dd>{{ $pedidos }}</dd>
                </div>
            </dl>

            <div class="checkout-success__actions">
                <a href="{{ route('home') }}#produtos" class="btn btn-primary">Voltar para a vitrine</a>
                <a href="{{ route('perfil.index') }}" class="btn btn-outline">Ver meus pedidos</a>
            </div>
        </div>
    </section>
@endsection
