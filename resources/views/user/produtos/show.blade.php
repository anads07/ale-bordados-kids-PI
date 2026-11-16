@extends('user.layouts.public')

@section('title', $produto->nome_produto.' | '.config('mock.store.name'))

{{-- Detalhes do produto, avaliação e formulário de personalização. --}}
@section('content')
    <section class="container-app product-page">
        <a href="{{ route('home') }}#produtos" class="back-link">
            <x-lucide-chevron-left aria-hidden="true" />
            Voltar / Início
        </a>

        <div class="product-detail">
            <div class="product-gallery">
                <figure class="product-gallery__frame">
                    <x-shared::product-image :produto="$produto" />
                </figure>

                <p class="product-gallery__note">
                    Imagem ilustrativa da peça. As cores e o bordado são definidos na personalização.
                </p>

                <section class="product-description" aria-labelledby="produto-descricao">
                    <h2 id="produto-descricao">Descrição do produto</h2>
                    <p>{{ $produto->descricao }}</p>
                </section>
            </div>

            <div class="product-info">
                <div class="product-info__top">
                    <div>
                        <span class="product-info__category">{{ $produto->categoria }}</span>
                        <h1>{{ $produto->nome_produto }}</h1>
                    </div>

                    <form
                        action="{{ route('favoritos.toggle', $produto) }}"
                        method="POST"
                        class="favorite-form product-info__favorite"
                        data-favorite-form
                        data-product-id="{{ $produto->id_pr }}"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="favorite-button"
                            data-favorite
                            data-add-label="Adicionar {{ $produto->nome_produto }} aos favoritos"
                            data-remove-label="Remover {{ $produto->nome_produto }} dos favoritos"
                            aria-pressed="{{ $favoritado ? 'true' : 'false' }}"
                            aria-label="{{ $favoritado ? 'Remover' : 'Adicionar' }} {{ $produto->nome_produto }} {{ $favoritado ? 'dos' : 'aos' }} favoritos"
                        >
                            <x-lucide-heart aria-hidden="true" />
                        </button>
                    </form>
                </div>

                <div class="product-info__rating">
                    <x-user::star-rating :nota="$produto->nota" :total="$produto->total_avaliacoes" />
                    <button type="button" class="link-button" data-coming-soon="Comentários">
                        Ver comentários
                    </button>
                </div>

                <strong class="product-info__price">{{ $produto->preco_formatado }}</strong>

                <form
                    action="{{ route('carrinho.store', $produto) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="product-form"
                    data-cart-add-form
                >
                    @csrf

                    <div class="product-form__heading">
                        <h2>Personalize do seu jeito</h2>
                        <p>Preencha os detalhes para deixar sua peça ainda mais especial.</p>
                    </div>

                    <div class="product-form__grid">
                        <div class="product-field">
                            <label for="cor-produto">
                                Cor da peça <span class="required-mark" aria-hidden="true">*</span>
                            </label>
                            <select id="cor-produto" name="cor_produto" class="campo" required>
                                @foreach ($personalizacao['cores_produto'] ?? [] as $cor)
                                    <option value="{{ $cor }}" @selected(old('cor_produto', config('mock.personalizacao.cor_padrao')) === $cor)>
                                        {{ $cor }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cor_produto')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="product-field">
                            <label for="cor-escrita">
                                Cor da linha <span class="required-mark" aria-hidden="true">*</span>
                            </label>
                            <select id="cor-escrita" name="cor_escrita" class="campo" required>
                                <option value="">Selecione uma cor</option>
                                @foreach ($personalizacao['cores_linha'] ?? [] as $cor)
                                    <option value="{{ $cor }}" @selected(old('cor_escrita') === $cor)>{{ $cor }}</option>
                                @endforeach
                            </select>
                            @error('cor_escrita')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="product-field">
                        <label for="escrita">
                            Qual o texto desejado? <span class="required-mark" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="escrita"
                            name="escrita"
                            class="campo"
                            type="text"
                            maxlength="40"
                            value="{{ old('escrita') }}"
                            placeholder="Ex: Nome, data de nascimento ou frase"
                            required
                        >
                        @error('escrita')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="product-form__grid">
                        <fieldset class="product-field product-field--full" data-required-group aria-label="Fonte do texto">
                            <legend>Fonte do texto <span class="required-mark" aria-hidden="true">*</span></legend>

                            <div class="font-options">
                                @foreach ($personalizacao['fontes'] ?? [] as $fonte)
                                    <label class="font-option">
                                        <input
                                            type="radio"
                                            name="fonte_escrita"
                                            value="{{ $fonte }}"
                                            class="sr-only font-option__input"
                                            @checked(old('fonte_escrita') === $fonte)
                                        >
                                        <span class="font-option__sample font-option__sample--{{ \Illuminate\Support\Str::slug($fonte) }}">{{ $fonte }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <span class="field-error" data-group-error hidden>Selecione a fonte do texto.</span>

                            @error('fonte_escrita')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </fieldset>

                        <div class="product-field product-field--full">
                            <label for="desenho">
                                Descreva o desenho <span class="required-mark" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="desenho"
                                name="desenho"
                                class="campo"
                                type="text"
                                maxlength="30"
                                value="{{ old('desenho') }}"
                                placeholder="Ex: bebê, flores, anjo"
                                required
                            >
                            @error('desenho')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="product-field">
                        <label for="observacoes">Observações (opcional)</label>
                        <textarea
                            id="observacoes"
                            name="observacoes"
                            class="campo campo-textarea"
                            maxlength="500"
                            rows="3"
                            placeholder="Conte qualquer detalhe importante para o bordado"
                        >{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="product-field">
                        <label for="imagem-inspiracao">Adicione uma foto de inspiração (opcional)</label>
                        <label class="product-upload" for="imagem-inspiracao">
                            <x-lucide-upload aria-hidden="true" />
                            <strong>Clique para selecionar uma imagem</strong>
                            <span>JPG, PNG ou WEBP, até 5 MB</span>
                            <span class="product-upload__name" data-product-image-name>Nenhum arquivo selecionado</span>
                        </label>
                        <input
                            id="imagem-inspiracao"
                            name="imagem_inspiracao"
                            class="sr-only"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            data-product-image-input
                        >
                        <img class="product-upload__preview" alt="Prévia da imagem de inspiração" data-product-image-preview hidden>
                        @error('imagem_inspiracao')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="product-form__footer">
                        <div class="product-quantity">
                            <span>Quantidade:</span>
                            <div class="product-quantity__control">
                                <button type="button" data-product-step="-1" aria-label="Diminuir quantidade">−</button>
                                <input
                                    type="number"
                                    name="quantidade"
                                    value="{{ old('quantidade', 1) }}"
                                    min="1"
                                    max="{{ max(1, (int) $produto->estoque) }}"
                                    inputmode="numeric"
                                    aria-label="Quantidade de {{ $produto->nome_produto }}"
                                    data-product-quantity
                                >
                                <button type="button" data-product-step="1" aria-label="Aumentar quantidade">+</button>
                            </div>
                        </div>

                        <p class="product-form__stock">
                            {{ $produto->estoque > 0 ? $produto->estoque.' unidades disponíveis' : 'Peça sem estoque no momento' }}
                        </p>

                        <div class="product-form__actions">
                            <button type="submit" class="btn btn-primary" @disabled($produto->estoque <= 0)>
                                <x-lucide-shopping-cart width="18" height="18" aria-hidden="true" />
                                Adicionar ao carrinho
                            </button>

                            <button type="button" class="btn btn-outline" data-coming-soon="Perguntar sobre esta peça">
                                Perguntar sobre
                            </button>
                        </div>

                        <p class="product-form__deadline">
                            O prazo estimado é de 5 a 10 dias úteis. O tempo pode variar conforme a complexidade do bordado.
                        </p>
                    </div>
                </form>
            </div>
        </div>

        @if ($relacionados->isNotEmpty())
            <section class="product-related" aria-label="Produtos relacionados">
                <x-user::section-heading titulo="Você também pode gostar" />

                <div class="product-grid">
                    @foreach ($relacionados as $relacionado)
                        <x-user::product-card :produto="$relacionado" />
                    @endforeach
                </div>
            </section>
        @endif
    </section>
@endsection
