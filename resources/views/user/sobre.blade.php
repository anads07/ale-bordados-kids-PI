@extends('user.layouts.public')

@section('title', 'Sobre Nós | '.config('mock.store.name'))

{{-- Apresenta a história e os valores da marca. --}}
@section('content')
    <section class="about-page">
        <div class="container-app about-intro">
            <p class="about-eyebrow">Feito com carinho, ponto por ponto</p>
            <h1>Sobre Nós</h1>
            <p>
                O Alê Bordados Kids nasceu do desejo de transformar enxovais e roupas infantis
                em lembranças afetivas. Cada peça é escolhida com cuidado e recebe bordados
                delicados, pensados para acompanhar os primeiros meses e anos de vida.
            </p>
        </div>

        <div class="container-app about-grid">
            <figure class="about-media about-media--large">
                <img
                    src="{{ asset('images/produtos/kit-fralda.jpeg') }}"
                    alt="Kit de fralda com bordado delicado"
                    loading="lazy"
                >
            </figure>

            <div class="about-copy">
                <h2>História e cuidado</h2>
                <p>
                    Trabalhamos com enxovais, toalhas, bodies e peças personalizadas para bebês
                    e crianças. Nosso processo valoriza o acabamento manual, a escolha dos
                    materiais e a conversa com cada família para entender o que a peça representa.
                </p>
                <p>
                    A loja reúne produtos prontos e também recebe peças próprias para
                    personalização, ampliando as possibilidades de presentear com significado.
                </p>
            </div>

            <div class="about-copy">
                <h2>Feito para durar</h2>
                <p>
                    Acreditamos que um bordado bem feito atravessa gerações. Por isso, prezamos
                    pela costura firme, pelos detalhes bem acabados e por uma produção atenta,
                    sem pressa e com olhar artesanal.
                </p>
                <p>
                    Nosso compromisso é entregar peças macias, seguras e cheias de afeto para
                    acompanhar os momentos mais especiais da infância.
                </p>
            </div>

            <figure class="about-media about-media--small">
                <img
                    src="{{ asset('images/produtos/body-calca.jpeg') }}"
                    alt="Conjunto infantil com bordado personalizado"
                    loading="lazy"
                >
            </figure>
        </div>

        <div class="container-app about-values">
            <article class="about-value">
                <span class="about-value__icon" aria-hidden="true">
                    <x-lucide-heart />
                </span>
                <h2>Afeto nos detalhes</h2>
                <p>Cada bordado é pensado para tornar a peça única e cheia de significado.</p>
            </article>

            <article class="about-value">
                <span class="about-value__icon" aria-hidden="true">
                    <x-lucide-shield-check />
                </span>
                <h2>Qualidade e segurança</h2>
                <p>Materiais macios, costura cuidadosa e atenção total ao conforto infantil.</p>
            </article>

            <article class="about-value">
                <span class="about-value__icon" aria-hidden="true">
                    <x-lucide-message-circle />
                </span>
                <h2>Atendimento próximo</h2>
                <p>Conversamos com cada cliente para entender a ideia e personalizar do jeito certo.</p>
            </article>
        </div>
    </section>
@endsection
