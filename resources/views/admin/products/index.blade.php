@extends('admin.layouts.admin')

@section('title', 'Gerenciar produtos | '.config('mock.store.name'))

{{-- Lista os produtos cadastrados e oferece ações de edição e exclusão. --}}
@section('content')
    <div class="container-app admin-container admin-products">
        <header class="admin-products__header">
            <div>
                <h1>Gerenciar produtos</h1>
                <p>Consulte, edite e remova as peças disponíveis na loja.</p>
            </div>

            <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary admin-products__new">
                <x-lucide-plus aria-hidden="true" />
                Cadastrar Novo Produto
            </a>
        </header>

        <form class="admin-products__filters" action="{{ route('admin.produtos.index') }}" method="GET">
            <label class="admin-filter-field">
                <span>Buscar produto</span>
                <span class="admin-filter-input">
                    <x-lucide-search aria-hidden="true" />
                    <input
                        type="search"
                        name="q"
                        class="campo"
                        value="{{ $filtros['busca'] }}"
                        placeholder="Nome ou descrição"
                    >
                </span>
            </label>

            <label class="admin-filter-field">
                <span>Categoria</span>
                <select name="categoria" class="campo">
                    <option value="">Todas as categorias</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria }}" @selected($filtros['categoria'] === $categoria)>
                            {{ $categoria }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="admin-products__filters-actions">
                <button type="submit" class="btn btn-primary">Filtrar</button>

                @if ($filtros['busca'] !== '' || $filtros['categoria'] !== '')
                    <a href="{{ route('admin.produtos.index') }}" class="btn btn-outline">Limpar</a>
                @endif
            </div>
        </form>

        @if ($produtos->isEmpty())
            <section class="admin-card admin-products__empty">
                @if ($filtros['busca'] !== '' || $filtros['categoria'] !== '')
                    <x-lucide-search aria-hidden="true" />
                    <h2>Nenhum produto encontrado</h2>
                    <p>Nenhuma peça corresponde aos filtros selecionados.</p>
                    <a href="{{ route('admin.produtos.index') }}" class="btn btn-outline">Limpar filtros</a>
                @else
                    <x-lucide-package-open aria-hidden="true" />
                    <h2>Nenhum produto cadastrado</h2>
                    <p>Cadastre a primeira peça para começar a montar o catálogo da loja.</p>
                    <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary">Cadastrar Novo Produto</a>
                @endif
            </section>
        @else
            <section class="admin-card admin-products__card">
                <div class="admin-products__summary">
                    <span>
                        {{ $produtos->total() }} produto(s)
                        {{ $filtros['busca'] !== '' || $filtros['categoria'] !== '' ? 'encontrado(s)' : 'no catálogo' }}
                    </span>
                    <span>Exibindo {{ $produtos->firstItem() }}–{{ $produtos->lastItem() }}</span>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table admin-products-table">
                        <thead>
                            <tr>
                                <th scope="col">Imagem</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Categoria</th>
                                <th scope="col">Preço</th>
                                <th scope="col">Estoque</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produtos as $produto)
                                <tr>
                                    <td data-label="Imagem">
                                        <x-shared::product-image
                                            :produto="$produto"
                                            class="admin-product-thumb"
                                        />
                                    </td>

                                    <td data-label="Produto">
                                        <span class="admin-product-name">
                                            <a href="{{ route('admin.produtos.edit', $produto) }}">{{ $produto->nome_produto }}</a>
                                            <small>#{{ $produto->id_pr }}</small>
                                        </span>
                                    </td>

                                    <td data-label="Categoria">
                                        <span class="admin-product-category">{{ $produto->categoria }}</span>
                                    </td>

                                    <td data-label="Preço" class="admin-product-price">
                                        {{ $produto->preco_formatado }}
                                    </td>

                                    <td data-label="Estoque">
                                        <span class="admin-stock {{ $produto->estoque <= 0 ? 'is-empty' : ($produto->estoque <= 5 ? 'is-low' : '') }}">
                                            {{ $produto->estoque <= 0 ? 'Esgotado' : $produto->estoque.' un.' }}
                                        </span>
                                    </td>

                                    <td data-label="Ações">
                                        <div class="admin-products__actions">
                                            <a
                                                href="{{ route('admin.produtos.edit', $produto) }}"
                                                class="admin-action-button"
                                                aria-label="Editar {{ $produto->nome_produto }}"
                                                title="Editar produto"
                                            >
                                                <x-lucide-square-pen aria-hidden="true" />
                                            </a>

                                            <form
                                                action="{{ route('admin.produtos.destroy', $produto) }}"
                                                method="POST"
                                                data-confirm="Excluir o produto {{ $produto->nome_produto }}? A imagem correspondente também será removida."
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="admin-action-button admin-action-button--danger"
                                                    aria-label="Excluir {{ $produto->nome_produto }}"
                                                    title="Excluir produto"
                                                >
                                                    <x-lucide-trash-2 aria-hidden="true" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($produtos->hasPages())
                    <div class="admin-pagination">
                        {{ $produtos->onEachSide(1)->links() }}
                    </div>
                @endif
            </section>
        @endif
    </div>
@endsection
