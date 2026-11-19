@extends('admin.layouts.admin')

@section('title', ($produto ? 'Editar produto' : 'Cadastrar produto').' | '.config('mock.store.name'))

{{-- Formulário de cadastro e edição de produtos, imagem e personalização. --}}
@section('content')
    @php
        $editando = $produto !== null;
    @endphp

    <div class="container-app admin-container admin-product-form-page">
        <a href="{{ route('admin.produtos.index') }}" class="admin-back-link">
            <x-lucide-chevron-left aria-hidden="true" />
            Voltar para produtos
        </a>

        <header class="admin-product-form__heading">
            <div>
                <h1>{{ $editando ? 'Editar produto' : 'Cadastrar produto' }}</h1>
                <p>{{ $editando ? 'Atualize as informações da peça e salve as alterações.' : 'Preencha os dados da nova peça para adicioná-la à loja.' }}</p>
            </div>

            @if ($editando)
                <span class="admin-product-form__id">Produto #{{ $produto->id_pr }}</span>
            @endif
        </header>

        @if ($errors->any())
            <div class="admin-form-alert" role="alert">
                Não foi possível salvar o produto. Revise os campos destacados abaixo.
            </div>
        @endif

        <form
            action="{{ $editando ? route('admin.produtos.update', $produto) : route('admin.produtos.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="admin-product-form"
        >
            @csrf
            @if ($editando)
                @method('PUT')
            @endif

            <div class="admin-product-form__layout">
                <div class="admin-product-form__main">
                    <section class="admin-card admin-form-section">
                        <header class="admin-form-section__header">
                            <span class="admin-form-section__icon" aria-hidden="true">
                                <x-lucide-package />
                            </span>
                            <div>
                                <h2>Informações do produto</h2>
                                <p>Todas as peças recebem personalização completa: texto, cor e fonte.</p>
                            </div>
                        </header>

                        <div class="admin-field">
                            <label for="nome_produto">Nome do Produto *</label>
                            <input
                                id="nome_produto"
                                name="nome_produto"
                                type="text"
                                class="campo @error('nome_produto') campo-erro @enderror"
                                maxlength="50"
                                value="{{ old('nome_produto', $produto?->nome_produto) }}"
                                placeholder="Ex: Fraldinha de Boca"
                                required
                            >
                            @error('nome_produto')
                                <span class="admin-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="admin-field">
                            <div class="admin-field__label-row">
                                <label for="descricao">Descrição</label>
                                <button
                                    type="button"
                                    class="admin-ai-button"
                                    data-coming-soon="Gerar descrição com IA"
                                >
                                    <x-lucide-sparkles aria-hidden="true" />
                                    Gerar com IA
                                </button>
                            </div>
                            <textarea
                                id="descricao"
                                name="descricao"
                                class="campo campo-textarea @error('descricao') campo-erro @enderror"
                                rows="5"
                                maxlength="5000"
                                placeholder="Descreva a peça, o tecido, o acabamento e os detalhes do bordado."
                            >{{ old('descricao', $produto?->descricao) }}</textarea>
                            @error('descricao')
                                <span class="admin-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="admin-field">
                            <label for="imagem">Upload de Imagem Principal {{ $editando ? '' : '*' }}</label>

                            @if ($editando && $produto->imagem_url)
                                <figure class="admin-current-image">
                                    <img src="{{ $produto->imagem_url }}" alt="Imagem atual de {{ $produto->nome_produto }}">
                                    <figcaption>Imagem atual</figcaption>
                                </figure>
                            @endif

                            <label class="admin-upload" for="imagem">
                                <x-lucide-upload aria-hidden="true" />
                                <strong>{{ $editando ? 'Substituir imagem principal' : 'Selecione a imagem principal' }}</strong>
                                <span>JPG, PNG ou WEBP, até 5 MB</span>
                                <span class="admin-upload__name" data-admin-product-image-name>Nenhum arquivo selecionado</span>
                            </label>

                            <input
                                id="imagem"
                                name="imagem"
                                type="file"
                                class="sr-only @error('imagem') campo-erro @enderror"
                                accept="image/jpeg,image/png,image/webp"
                                data-admin-product-image
                                @required(! $editando)
                            >

                            <img
                                class="admin-upload-preview"
                                alt="Prévia da nova imagem do produto"
                                data-admin-product-image-preview
                                hidden
                            >

                            @error('imagem')
                                <span class="admin-field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </section>
                </div>

                <aside class="admin-product-form__side">
                    <section class="admin-card admin-form-section">
                        <header class="admin-form-section__header">
                            <span class="admin-form-section__icon" aria-hidden="true">
                                <x-lucide-tag />
                            </span>
                            <div>
                                <h2>Categoria e estoque</h2>
                                <p>Defina onde a peça aparece e sua disponibilidade.</p>
                            </div>
                        </header>

                        <div class="admin-field">
                            <label for="categoria">Categoria do produto *</label>
                            <select
                                id="categoria"
                                name="categoria"
                                class="campo @error('categoria') campo-erro @enderror"
                                required
                            >
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria }}" @selected(old('categoria', $produto?->categoria) === $categoria)>
                                        {{ $categoria }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria')
                                <span class="admin-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="admin-product-form__grid">
                            <div class="admin-field">
                                <label for="preco">Preço (R$) *</label>
                                <div class="admin-money-field">
                                    <span>R$</span>
                                    <input
                                        id="preco"
                                        name="preco"
                                        type="number"
                                        class="campo @error('preco') campo-erro @enderror"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('preco', $produto?->preco) }}"
                                        placeholder="0,00"
                                        required
                                    >
                                </div>
                                @error('preco')
                                    <span class="admin-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="admin-field">
                                <label for="estoque">Estoque (quantidade) *</label>
                                <input
                                    id="estoque"
                                    name="estoque"
                                    type="number"
                                    class="campo @error('estoque') campo-erro @enderror"
                                    min="0"
                                    step="1"
                                    value="{{ old('estoque', $produto?->estoque ?? 0) }}"
                                    required
                                >
                                @error('estoque')
                                    <span class="admin-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="admin-card admin-product-form__actions">
                        <button type="submit" class="btn btn-primary">
                            <x-lucide-check aria-hidden="true" />
                            Salvar produto
                        </button>

                        <a href="{{ route('admin.produtos.index') }}" class="btn btn-outline">Cancelar</a>

                        <p class="admin-product-form__hint">Ao salvar, a peça é publicada imediatamente na vitrine da loja.</p>
                    </section>
                </aside>
            </div>
        </form>
    </div>
@endsection
