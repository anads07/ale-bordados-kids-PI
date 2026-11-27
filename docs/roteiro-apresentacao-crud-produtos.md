# Pessoa 2 — Create: cadastrar produto

## Objetivo

Apresentar o fluxo completo de criação de um produto: a rota que abre o formulário, a View Blade, a autorização e validação, o tratamento da imagem e a criação do registro na tabela `produtos`. No banco de dados, essa operação corresponde a um `INSERT`.

## Etapa 1 — Rotas do cadastro

**Fala da apresentação**

“Bom, agora eu vou explicar a parte de código, mostrando como a gente desenvolveu a função de cadastrar produtos. Primeiro, precisamos definir as rotas que abrem o formulário e recebem os dados enviados pelo administrador.”

**Onde encontrar**

`routes/admin.php`

**Código correspondente — trecho integral do arquivo**

```php
<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('produtos', ProdutoController::class)->except(['show']);

        Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::patch('/pedidos/{pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.status');
    });
```

**Explicação para estudo**

- `Route::middleware(['auth', 'admin'])` protege as rotas do grupo. `auth` exige que a pessoa esteja autenticada e `admin` restringe o acesso a administradores.
- `prefix('admin')` coloca `/admin` antes do caminho das rotas do grupo.
- `name('admin.')` acrescenta `admin.` ao nome das rotas, permitindo referenciá-las com nomes como `admin.produtos.create`.
- `Route::resource('produtos', ProdutoController::class)` registra as rotas padrão de um recurso e associa suas ações ao `ProdutoController`.
- `except(['show'])` exclui a rota administrativa de detalhe. O detalhe público tem uma rota própria.
- O cadastro usa `GET /admin/produtos/create` para abrir o formulário e `POST /admin/produtos` para enviar os dados.

**Continuação da fala**

“Com as rotas definidas, o Laravel encaminha o pedido de abertura do formulário para o método `create()` do controller. Vou mostrar agora como esse método prepara a página.”

## Etapa 2 — Controller abre o formulário

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — classe `ProdutoController`, método `create()`, linhas 54–60.

**Código correspondente — método completo**

```php
// Abre o cadastro com as categorias disponíveis.
public function create(): View
{
    return view('admin.products.form', [
        'produto' => null,
        'categorias' => $this->categorias(),
    ]);
}
```

**Explicação para estudo**

- `create(): View` recebe a rota `GET` e retorna uma página, sem criar um registro ainda.
- `view('admin.products.form', ...)` seleciona `resources/views/admin/products/form.blade.php`.
- `produto => null` indica que ainda não existe produto selecionado; a View usa isso para saber que está em modo de cadastro.
- `categorias => $this->categorias()` envia as opções que serão exibidas no campo de categoria.
- A consulta de categorias usa um método auxiliar do mesmo controller, apresentado a seguir.

**Continuação da fala**

“O formulário precisa oferecer categorias disponíveis. Por isso, junto com a View, o controller envia uma lista que combina as categorias já cadastradas com algumas opções padrão.”

## Etapa 3 — Controller prepara as categorias

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — classe `ProdutoController`, método `categorias()`, linhas 204–215.

**Código correspondente — método completo**

```php
// Combina as categorias cadastradas com as opções padrão do formulário.
private function categorias(): Collection
{
    $padrao = collect(['Fraldas', 'Toalhas', 'Roupas', 'Kits', 'Conjuntos', 'Geral']);

    return Produto::query()
        ->select('categoria')
        ->distinct()
        ->orderBy('categoria')
        ->pluck('categoria')
        ->merge($padrao)
        ->filter()
        ->unique(fn (string $categoria) => mb_strtolower($categoria))
        ->sort()
        ->values();
}
```

**Explicação para estudo**

- `$padrao` cria uma coleção com as categorias padrão.
- `Produto::query()->select('categoria')` inicia uma consulta à tabela pelo Model e seleciona somente a coluna de categoria.
- `distinct()` evita repetir categorias iguais já cadastradas.
- `orderBy('categoria')` organiza os valores; `pluck('categoria')` retorna apenas a lista dessa coluna.
- `merge($padrao)` junta categorias do banco com as opções padrão.
- `filter()` remove valores vazios.
- `unique(...)` elimina duplicatas sem diferenciar letras maiúsculas de minúsculas.
- `sort()->values()` ordena a lista e reorganiza seus índices.

**Continuação da fala**

“Agora vou mostrar a View que recebe essas categorias e apresenta os campos. É a mesma página usada depois para edição, mas, neste momento, `produto` é nulo e ela funciona como cadastro.”

## Etapa 4 — View e formulário de cadastro

**Onde encontrar**

`resources/views/admin/products/form.blade.php` — formulário completo, linhas 1–230.

**Código correspondente — arquivo completo**

```blade
@extends('admin.layouts.admin')

@section('title', ($produto ? 'Editar produto' : 'Cadastrar produto').' | '.config('mock.store.name'))

{{-- Formulário de cadastro e edição de produtos, imagem e personalização. --}}
@section('content')
    @php
        $editando = $produto !== null;
    @endphp

    <div class="container-app admin-container admin-product-form-page">
        @if ($editando)
            <a href="{{ route('admin.produtos.index') }}" class="admin-back-link">
                <x-lucide-chevron-left aria-hidden="true" />
                Voltar para produtos
            </a>
        @endif

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
```

**Explicação para estudo**

- `@extends` usa o layout administrativo.
- `$editando` distingue cadastro de edição. Como `create()` envia `produto => null`, a expressão resulta em `false`.
- O formulário usa a rota `admin.produtos.store` no cadastro.
- `method="POST"` envia os campos ao servidor; `enctype="multipart/form-data"` é necessário para enviar a imagem.
- `@csrf` insere o token de segurança exigido pelo middleware web.
- Os campos com `name` são as chaves que o Laravel recebe: `nome_produto`, `descricao`, `imagem`, `categoria`, `preco` e `estoque`.
- `old('campo', $produto?->campo)` recupera o valor digitado depois de uma falha de validação ou, em edição, o valor do banco.
- `@error` apresenta erros para cada campo.
- A imagem é obrigatória quando não está editando, conforme `@required(! $editando)`.
- Limites e requisitos visuais no navegador ajudam o preenchimento, mas a validação do servidor é a regra efetiva de segurança e integridade.

**Continuação da fala**

“O formulário já organiza os campos, mas os dados também precisam ser conferidos no servidor. A próxima etapa é o Form Request, que autoriza o administrador e valida o conteúdo antes de chegar ao método que grava.”

## Etapa 5 — Autorização e validação

**Onde encontrar**

`app/Http/Requests/Admin/ProdutoRequest.php` — classe completa, linhas 1–74.

**Código correspondente — classe completa**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

// Valida os dados enviados pelo formulário de produtos.
class ProdutoRequest extends FormRequest
{
    // Confirma que somente o administrador autorizado envie o formulário.
    public function authorize(): bool
    {
        $usuario = $this->user();
        $emailAdministrativo = (string) config('admin.email');

        return (bool) $usuario?->is_admin
            && strcasecmp((string) $usuario->email, $emailAdministrativo) === 0;
    }

    // Define as regras de cadastro e edição.
    public function rules(): array
    {
        $editando = $this->route('produto') !== null;

        return [
            'nome_produto' => ['required', 'string', 'max:50'],
            'categoria' => ['required', 'string', 'max:50'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'preco' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'estoque' => ['required', 'integer', 'min:0', 'max:999999'],
            'imagem' => [
                $editando ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'extensions:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    // Define mensagens de validação em português.
    public function messages(): array
    {
        return [
            'nome_produto.required' => 'Informe o nome do produto.',
            'nome_produto.max' => 'O nome do produto deve ter no máximo 50 caracteres.',
            'categoria.required' => 'Informe a categoria do produto.',
            'categoria.max' => 'A categoria deve ter no máximo 50 caracteres.',
            'preco.required' => 'Informe o preço do produto.',
            'preco.numeric' => 'O preço deve ser um valor numérico.',
            'preco.min' => 'O preço não pode ser negativo.',
            'estoque.required' => 'Informe o estoque disponível.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min' => 'O estoque não pode ser negativo.',
            'imagem.required' => 'Envie a imagem principal do produto.',
            'imagem.image' => 'O arquivo enviado precisa ser uma imagem.',
            'imagem.mimes' => 'Envie uma imagem JPG, JPEG, PNG ou WEBP.',
            'imagem.extensions' => 'A extensão do arquivo deve ser JPG, JPEG, PNG ou WEBP.',
            'imagem.max' => 'A imagem deve ter no máximo 5 MB.',
        ];
    }

    // Usa nomes claros nas mensagens de validação.
    public function attributes(): array
    {
        return [
            'nome_produto' => 'nome do produto',
            'categoria' => 'categoria',
            'descricao' => 'descrição',
            'preco' => 'preço',
            'estoque' => 'estoque',
            'imagem' => 'imagem principal',
        ];
    }
}
```

**Explicação para estudo**

- Um `FormRequest` é executado antes do controller. Se a autorização ou validação falhar, o método `store()` não continua.
- `authorize()` verifica a flag administrativa e compara o e-mail com `config('admin.email')`.
- `rules()` define as regras de cada campo. No cadastro, não existe parâmetro `produto` na rota, então a imagem é `required`.
- A descrição aceita `null`; preço deve ser numérico e não negativo; estoque deve ser inteiro e não negativo.
- Para imagem, `image`, `mimes` e `extensions` verificam o arquivo e seus formatos aceitos. `max:5120` limita a cinco megabytes.
- `messages()` fornece mensagens em português.
- `attributes()` define rótulos legíveis para os nomes de campo.
- O controller usa `$request->validated()` para trabalhar apenas com os dados que passaram pelas regras.

**Continuação da fala**

“Com autorização e dados aprovados, a requisição chega ao método `store()`. Ele prepara o arquivo e chama o Model para gravar a nova linha.”

## Etapa 6 — Controller grava o produto

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — classe `ProdutoController`, método `store()`, linhas 62–78.

**Código correspondente — método completo**

```php
// Cria um produto com os dados validados.
public function store(ProdutoRequest $request): RedirectResponse
{
    $dados = $request->validated();
    $imagem = $this->salvarImagem($request->file('imagem'), $dados['nome_produto']);

    try {
        $produto = Produto::query()->create($this->montarAtributos($request, $imagem));
    } catch (Throwable $erro) {
        $this->removerImagemSeNaoUsada($imagem);

        throw $erro;
    }

    return redirect()
        ->route('admin.produtos.index')
        ->with('admin_status', 'Produto "'.$produto->nome_produto.'" cadastrado com sucesso.');
}
```

**Explicação para estudo**

- `$request->validated()` devolve os dados aprovados pelo `ProdutoRequest`.
- `file('imagem')` obtém o arquivo enviado.
- `salvarImagem()` grava a imagem no diretório público e retorna seu nome para persistência.
- `Produto::query()->create(...)` cria o registro pelo Eloquent.
- `montarAtributos()` normaliza e seleciona os dados que serão atribuídos ao Model.
- O `try/catch` trata a falha da persistência: se o registro não for salvo, tenta remover a imagem recém-criada e relança o erro original. O erro não é ocultado.
- Ao terminar, o controller redireciona à listagem administrativa e adiciona uma mensagem temporária de sucesso.

**Continuação da fala**

“O `store()` chama dois auxiliares para preparar os valores e salvar a imagem. Vou apresentá-los agora porque fazem parte do fluxo real antes do `INSERT`.”

## Etapa 7 — Normalização e tratamento da imagem

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — métodos `montarAtributos()`, `salvarImagem()` e `removerImagemSeNaoUsada()`, linhas 134–202.

**Código correspondente — métodos completos**

```php
// Normaliza os campos e trata caixas de seleção ausentes como desmarcadas.
private function montarAtributos(ProdutoRequest $request, ?string $imagem = null): array
{
    $dados = $request->validated();

    $atributos = [
        'nome_produto' => trim($dados['nome_produto']),
        'categoria' => trim($dados['categoria']),
        'descricao' => filled($dados['descricao'] ?? null) ? trim($dados['descricao']) : null,
        'preco' => $dados['preco'],
        'estoque' => (int) $dados['estoque'],
        // Toda peça é personalizável: texto, cor e fonte ficam sempre ativos.
        'permite_texto' => true,
        'permite_cor' => true,
        'permite_fonte' => true,
    ];

    if ($imagem) {
        $atributos['imagem'] = $imagem;
    }

    return $atributos;
}

// Salva a imagem validada com um nome seguro e único.
private function salvarImagem(?UploadedFile $imagem, string $nomeProduto): ?string
{
    if (! $imagem) {
        return null;
    }

    $diretorio = public_path('images/produtos');

    if (! is_dir($diretorio)) {
        mkdir($diretorio, 0775, true);
    }

    $nomeBase = Str::slug(Str::limit($nomeProduto, 40, ''));
    // Usa a extensão identificada pelo conteúdo, nunca a informada pelo navegador.
    $extensao = strtolower($imagem->extension());
    $arquivo = ($nomeBase ?: 'produto').'-'.Str::lower(Str::random(8)).'.'.$extensao;

    $imagem->move($diretorio, $arquivo);

    return $arquivo;
}

// Remove uma imagem antiga apenas quando nenhum outro produto ainda a utiliza.
private function removerImagemSeNaoUsada(?string $imagem): void
{
    if (! $imagem) {
        return;
    }

    if (Produto::query()->where('imagem', $imagem)->exists()) {
        return;
    }

    $caminho = public_path('images/produtos/'.$imagem);

    if (is_file($caminho)) {
        unlink($caminho);
    }
}
```

**Explicação para estudo**

- `montarAtributos()` retorna exatamente os campos preparados para o Model.
- `trim()` remove espaços do início e do fim; descrição vazia vira `null`; estoque é convertido para inteiro.
- Os três campos de personalização são sempre definidos como `true` no comportamento atual.
- A imagem só é adicionada ao array quando existe um nome de arquivo.
- `salvarImagem()` permite `null`, cria `public/images/produtos` se não existir, monta nome amigável e acrescenta uma sequência aleatória.
- `UploadedFile::extension()` determina a extensão pelo conteúdo reconhecido do arquivo, após a validação.
- `move()` salva o arquivo no diretório indicado. No banco, o sistema armazena o nome do arquivo, não o conteúdo binário.
- `removerImagemSeNaoUsada()` consulta se algum produto ainda usa o mesmo nome. Só apaga o arquivo físico se não houver referência.
- No cadastro, o método de remoção é usado como limpeza caso a gravação falhe depois do upload.

**Continuação da fala**

“A criação usa o Model `Produto`. Vou mostrar as configurações que permitem ao Eloquent saber qual tabela usar, quais campos pode preencher e qual é a chave primária.”

## Etapa 8 — Model e mapeamento com a tabela

**Onde encontrar**

`app/Models/Produto.php` — classe `Produto`, linhas 1–50.

**Código correspondente — Model completo**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model da tabela oficial de produtos e dos controles de personalização.
 */
class Produto extends Model
{
    protected $table = 'produtos';

    protected $primaryKey = 'id_pr';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'nome_produto',
        'categoria',
        'preco',
        'estoque',
        'descricao',
        'imagem',
        'permite_texto',
        'permite_cor',
        'permite_fonte',
    ];

    /**
     * Mantém os controles de personalização tipados como booleanos para que
     * as views administrativas e futuras regras da vitrine os interpretem bem.
     */
    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'permite_texto' => 'boolean',
        'permite_cor' => 'boolean',
        'permite_fonte' => 'boolean',
    ];

    /**
     * Lista os pedidos personalizados que usam este produto.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'produto', 'id_pr');
    }

    /**
     * Resolve a URL da imagem real do produto ou retorna null quando o arquivo
     * ainda não existe em public/images/produtos.
     */
    public function getImagemUrlAttribute(): ?string
    {
        if ($this->imagem && file_exists(public_path('images/produtos/'.$this->imagem))) {
            return asset('images/produtos/'.$this->imagem);
        }

        return null;
    }

    /**
     * Formata o preço no padrão brasileiro para exibição nas views.
     */
    public function getPrecoFormatadoAttribute(): string
    {
        return 'R$ '.number_format((float) $this->preco, 2, ',', '.');
    }

    /**
     * Nota simulada compartilhada pelos cartões enquanto as avaliações não são reais.
     */
    public function getNotaAttribute(): float
    {
        return (float) config('mock.rating.nota', 4.5);
    }

    /**
     * Quantidade simulada de avaliações exibida ao lado das estrelas.
     */
    public function getTotalAvaliacoesAttribute(): int
    {
        return (int) config('mock.rating.total', 23);
    }
}
```

**Explicação para estudo**

- `$table` declara o nome literal da tabela.
- `$primaryKey` é necessário porque a chave se chama `id_pr`, em vez do nome padrão `id`.
- `$incrementing = true` indica que o banco gera o identificador.
- `$timestamps = false` corresponde à migration, que não criou `created_at` e `updated_at`.
- `$fillable` autoriza o preenchimento em massa somente dos campos listados. Isso permite o `create()` do controller sem deixar o Model aberto a qualquer atributo enviado.
- `$casts` converte preço, estoque e flags para os tipos esperados na aplicação.
- `pedidos()` define a relação que será usada na operação Delete.

**Continuação da fala**

“As configurações do Model precisam corresponder à estrutura criada no banco. A migration define os campos e os tipos que recebem os dados do formulário.”

## Etapa 9 — Estrutura da tabela `produtos`

**Onde encontrar**

`database/migrations/2026_11_11_000002_create_produtos_table.php` — arquivo completo.

**Código correspondente — migration completa**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mantém o esquema oficial da tabela produtos.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->integer('id_pr', true);
            $table->string('nome_produto', 50);
            $table->string('categoria', 50)->default('Geral');
            $table->decimal('preco', 10, 2);
            $table->integer('estoque')->default(10);
            $table->text('descricao')->nullable();
            $table->string('imagem', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
```

**Explicação para estudo**

- `up()` cria a tabela durante a migração.
- `id_pr` é uma coluna inteira com chave primária e incremento automático.
- `nome_produto` aceita até 50 caracteres.
- `categoria` aceita 50 caracteres e tem valor padrão `Geral`.
- `preco` é `DECIMAL(10,2)`, formato numérico com duas casas decimais.
- `estoque` é inteiro e tem padrão 10 no banco.
- `descricao` e `imagem` permitem `NULL` na estrutura do banco. A aplicação, porém, exige imagem no cadastro pelo Form Request.
- `down()` remove a tabela se a migração precisar ser revertida.

**Continuação da fala**

“Além dos dados básicos, uma migration posterior adiciona três flags de personalização. Elas também fazem parte do Model e são definidas como ativas durante a criação.”

## Etapa 10 — Campos de personalização

**Onde encontrar**

`database/migrations/2026_11_11_000005_add_personalization_flags_to_produtos_table.php` — arquivo completo.

**Código correspondente**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mantém os produtos existentes personalizáveis por padrão.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('permite_texto')->default(true)->after('imagem');
            $table->boolean('permite_cor')->default(true)->after('permite_texto');
            $table->boolean('permite_fonte')->default(true)->after('permite_cor');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['permite_texto', 'permite_cor', 'permite_fonte']);
        });
    }
};
```

**Explicação para estudo**

- `Schema::table` altera a tabela já existente, em vez de criá-la de novo.
- `boolean` cria os campos `permite_texto`, `permite_cor` e `permite_fonte`.
- Todos têm padrão `true`, inclusive para produtos anteriores à migration.
- `down()` remove as três colunas ao reverter a migração.

**Continuação da fala**

“Com isso, o fluxo de criação chegou até a estrutura do banco. O método `create()` do Eloquent grava os valores validados como uma nova linha, equivalente a um `INSERT` na tabela `produtos`.”

**Código correspondente — SQL conceitual**

```sql
INSERT INTO produtos (
    nome_produto,
    categoria,
    preco,
    estoque,
    descricao,
    imagem,
    permite_texto,
    permite_cor,
    permite_fonte
) VALUES (
    'Nome informado',
    'Categoria informada',
    25.90,
    10,
    'Descrição informada',
    'nome-gerado-aleatorio.jpg',
    true,
    true,
    true
);
```

**Explicação para estudo**

Esse exemplo mostra a forma conceitual de uma inserção com os campos da tabela real. A aplicação não executa essa string SQL: `Produto::query()->create(...)` faz o Eloquent montar a operação usando os atributos preparados pelo controller.

---

# Pessoa 3 — Read: consultar e exibir produtos

## Objetivo

Apresentar a consulta da listagem administrativa: rota, filtros, consulta Eloquent, paginação e apresentação dos registros na View. A operação corresponde ao `SELECT` do SQL.

## Etapa 1 — Rota que solicita a listagem

**Fala da apresentação**

“Agora eu vou explicar como o sistema consulta os produtos cadastrados e mostra os resultados na área administrativa. Vou começar pela rota que recebe a solicitação de listagem.”

**Onde encontrar**

`routes/admin.php` — dentro do grupo administrativo, linha 14.

**Código correspondente — declaração da rota**

```php
Route::resource('produtos', ProdutoController::class)->except(['show']);
```

**Explicação para estudo**

- A declaração de recurso gera a rota `GET /admin/produtos`, de nome `admin.produtos.index`.
- Essa rota encaminha a solicitação ao método `index()` do `ProdutoController`.
- As demais rotas do arquivo estão protegidas pelo grupo `auth` e `admin` e usam o prefixo `/admin`.
- `show` é excluída desse conjunto administrativo; a listagem administrativa é o Read trabalhado nesta etapa.

**Continuação da fala**

“Quando o navegador solicita essa rota, o método `index()` lê os filtros enviados, monta a consulta pelo Model e entrega os produtos à página Blade.”

## Etapa 2 — Controller consulta o banco

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — classe `ProdutoController`, método `index()`, linhas 20–49.

**Código correspondente — método completo**

```php
// Lista os produtos mais recentes, com busca por nome e filtro por categoria.
public function index(Request $request): View
{
    $busca = is_string($request->query('q')) ? trim($request->query('q')) : '';
    $categoria = is_string($request->query('categoria')) ? trim($request->query('categoria')) : '';

    $consulta = Produto::query();

    if ($busca !== '') {
        $consulta->where(function ($filtro) use ($busca) {
            $filtro->where('nome_produto', 'like', "%{$busca}%")
                ->orWhere('descricao', 'like', "%{$busca}%");
        });
    }

    if ($categoria !== '') {
        $consulta->where('categoria', $categoria);
    }

    $produtos = $consulta
        ->orderByDesc('id_pr')
        ->paginate(10)
        ->withQueryString();

    return view('admin.products.index', [
        'produtos' => $produtos,
        'categorias' => $this->categorias(),
        'filtros' => [
            'busca' => $busca,
            'categoria' => $categoria,
        ],
    ]);
}
```

**Explicação para estudo**

- O método recebe `Request` para ler parâmetros opcionais da URL.
- `query('q')` lê o texto de busca e só o aceita quando for uma string; `trim()` tira espaços nas pontas.
- `query('categoria')` faz a mesma normalização para o filtro de categoria.
- `Produto::query()` inicia a consulta através do Model associado à tabela `produtos`.
- Se houver busca, o `where()` agrupado procura o texto em `nome_produto` **ou** `descricao`. O operador `like` com `%` significa que o texto pode estar em qualquer parte do campo.
- Se houver categoria, outro `where()` restringe os registros àquela categoria.
- `orderByDesc('id_pr')` coloca primeiro os produtos com maior identificador.
- `paginate(10)` busca dez resultados por página e cria os dados de paginação.
- `withQueryString()` preserva os filtros quando a pessoa navega entre páginas.
- `view(...)` envia os produtos, as categorias e os valores dos filtros para a Blade.

**Relação com SQL**

Sem filtros, a consulta equivale conceitualmente a:

```sql
SELECT *
FROM produtos
ORDER BY id_pr DESC
LIMIT 10;
```

Com busca, o Eloquent adiciona condições equivalentes a:

```sql
WHERE nome_produto LIKE '%texto%'
   OR descricao LIKE '%texto%'
```

Esses comandos SQL são uma explicação conceitual. O código da aplicação usa Eloquent e não concatena o texto do usuário em uma consulta SQL manual.

**Continuação da fala**

“A consulta está pronta e o controller envia uma variável chamada `produtos` à View. Agora vou mostrar como a página usa esse resultado para montar a tabela.”

## Etapa 3 — View administrativa apresenta a listagem

**Onde encontrar**

`resources/views/admin/products/index.blade.php` — View completa da consulta, linhas 1–163.

**Código correspondente — arquivo completo**

```blade
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
```

**Explicação para estudo**

- A página estende o layout administrativo e é preenchida na seção `content`.
- O formulário dos filtros usa `GET`, pois consultar não altera dados.
- `name="q"` e `name="categoria"` correspondem aos parâmetros lidos pelo controller.
- `@foreach ($categorias as $categoria)` cria as opções do filtro.
- `$produtos->isEmpty()` permite exibir um estado vazio quando não há registros.
- `$produtos->total()` e os métodos de paginação mostram a quantidade e a faixa de resultados.
- `@foreach ($produtos as $produto)` percorre os objetos Eloquent retornados pelo controller.
- Os valores de nome, identificador, categoria e estoque vêm do Model.
- `preco_formatado` é um atributo acessor do Model, usado para exibição.
- `links()` apresenta navegação de páginas quando há mais resultados.
- Os links de edição e o formulário de exclusão são outras operações do CRUD, explicadas nas seções Update e Delete.

**Continuação da fala**

“Essa é a consulta administrativa. O catálogo público também lê a tabela `produtos`, mas para esta apresentação o Read do CRUD fica demonstrado pela rota de listagem, sua consulta no Model e a tabela Blade.”

---

# Pessoa 4 — Update: editar produto

## Objetivo

Apresentar como o sistema localiza o registro pela chave `id_pr`, carrega seus dados no formulário, valida a alteração e atualiza o mesmo registro. No banco de dados, a operação corresponde a um `UPDATE`.

## Etapa 1 — Link e rota de edição

**Fala da apresentação**

“Agora eu vou explicar como alteramos um produto que já existe. O processo começa na listagem: cada produto possui uma ação de edição, que abre o formulário com o registro correspondente.”

**Onde encontrar**

`routes/admin.php` — rota gerada por `Route::resource`;  
`resources/views/admin/products/index.blade.php` — link de edição na linha do produto.

**Código correspondente**

```php
Route::resource('produtos', ProdutoController::class)->except(['show']);
```

```blade
<a
    href="{{ route('admin.produtos.edit', $produto) }}"
    class="admin-action-button"
    aria-label="Editar {{ $produto->nome_produto }}"
    title="Editar produto"
>
    <x-lucide-square-pen aria-hidden="true" />
</a>
```

**Explicação para estudo**

- O `resource` cria `GET /admin/produtos/{produto}/edit`, nomeada `admin.produtos.edit`.
- A rota `route(...)` recebe o objeto `$produto`; o Laravel usa sua chave para construir o endereço.
- O verbo `GET` solicita a página de edição, sem alterar ainda o registro.

**Continuação da fala**

“Quando a URL contém o identificador, o route model binding transforma esse valor no Model `Produto`. O controller recebe o registro encontrado e o entrega ao formulário.”

## Etapa 2 — Identificação do produto e abertura da edição

**Onde encontrar**

`app/Models/Produto.php` — configuração da chave primária, linhas 13–19;  
`app/Http/Controllers/Admin/ProdutoController.php` — método `edit()`, linhas 80–88.

**Código correspondente — configuração relevante do Model**

```php
protected $table = 'produtos';

protected $primaryKey = 'id_pr';

public $incrementing = true;

public $timestamps = false;
```

**Código correspondente — método `edit()` completo**

```php
// Abre a edição do produto selecionado.
public function edit(Produto $produto): View
{
    return view('admin.products.form', [
        'produto' => $produto,
        'categorias' => $this->categorias(),
    ]);
}
```

**Explicação para estudo**

- `$table` informa que o Model consulta `produtos`.
- `$primaryKey` declara que a chave é `id_pr`, não o nome padrão `id`.
- Como o parâmetro da rota e o argumento tipado têm o mesmo nome, Laravel resolve o objeto `Produto` antes de executar `edit()`.
- `edit()` retorna a View `admin.products.form` e passa o produto já encontrado, além da lista de categorias.
- Se o ID da URL não corresponder a um produto, o route model binding responde como recurso não encontrado.

**Continuação da fala**

“A View recebe esse objeto e muda para o modo de edição. Vou mostrar agora o formulário real e como ele carrega os valores atuais.”

## Etapa 3 — View preenche os dados atuais

**Onde encontrar**

`resources/views/admin/products/form.blade.php` — lógica do formulário comum a criação e edição, linhas 1–230.

**Código correspondente — arquivo completo**

```blade
@extends('admin.layouts.admin')

@section('title', ($produto ? 'Editar produto' : 'Cadastrar produto').' | '.config('mock.store.name'))

{{-- Formulário de cadastro e edição de produtos, imagem e personalização. --}}
@section('content')
    @php
        $editando = $produto !== null;
    @endphp

    <div class="container-app admin-container admin-product-form-page">
        @if ($editando)
            <a href="{{ route('admin.produtos.index') }}" class="admin-back-link">
                <x-lucide-chevron-left aria-hidden="true" />
                Voltar para produtos
            </a>
        @endif

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
```

**Explicação para estudo**

- A View é a mesma do cadastro. Agora `$produto` é um objeto, então `$editando` é `true`.
- O título e o identificador exibido vêm do produto existente.
- A ação do formulário vira `admin.produtos.update`, recebendo o produto para compor a URL.
- `@method('PUT')` informa ao Laravel que a intenção é atualizar. HTML só envia `GET` ou `POST` em formulários.
- `old('nome_produto', $produto?->nome_produto)` usa primeiro um valor submetido anteriormente; caso não exista, mostra o valor carregado do banco.
- O mesmo padrão é aplicado à descrição, categoria, preço e estoque.
- A imagem atual é exibida se `imagem_url` resolver para um arquivo existente.
- A imagem nova é opcional durante edição, pois é permitido manter a atual.
- `@csrf` protege o formulário e `@error` apresenta falhas de validação.

**Continuação da fala**

“O formulário mantém os valores atuais, mas os dados enviados ainda passam pelas mesmas regras de validação. A diferença é que, na edição, não é obrigatório enviar uma nova imagem.”

## Etapa 4 — Regras de validação na edição

**Onde encontrar**

`app/Http/Requests/Admin/ProdutoRequest.php` — métodos `authorize()` e `rules()`, linhas 11–40.

**Código correspondente — métodos completos**

```php
public function authorize(): bool
{
    $usuario = $this->user();
    $emailAdministrativo = (string) config('admin.email');

    return (bool) $usuario?->is_admin
        && strcasecmp((string) $usuario->email, $emailAdministrativo) === 0;
}

public function rules(): array
{
    $editando = $this->route('produto') !== null;

    return [
        'nome_produto' => ['required', 'string', 'max:50'],
        'categoria' => ['required', 'string', 'max:50'],
        'descricao' => ['nullable', 'string', 'max:5000'],
        'preco' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        'estoque' => ['required', 'integer', 'min:0', 'max:999999'],
        'imagem' => [
            $editando ? 'nullable' : 'required',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'extensions:jpg,jpeg,png,webp',
            'max:5120',
        ],
    ];
}
```

**Explicação para estudo**

- A autorização continua exigindo administrador com o e-mail configurado.
- `$this->route('produto') !== null` detecta a rota de atualização, que contém `{produto}`.
- Em edição, a regra para imagem se torna `nullable`; se a pessoa enviar uma imagem, ela ainda precisa passar por todas as regras seguintes.
- Os demais campos mantêm as regras de nome, categoria, descrição, preço e estoque explicadas na operação Create.
- A validação acontece antes de `update()`, então os dados inválidos não são persistidos.

**Continuação da fala**

“Depois que o Form Request autoriza e valida a requisição, o método `update()` decide se há imagem nova e atualiza o objeto que a rota já encontrou.”

## Etapa 5 — Controller atualiza o registro

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — classe `ProdutoController`, método `update()`, linhas 91–115.

**Código correspondente — método completo**

```php
// Atualiza os dados e, se necessário, substitui a imagem.
public function update(ProdutoRequest $request, Produto $produto): RedirectResponse
{
    $imagemAntiga = $produto->imagem;
    $novaImagem = $request->hasFile('imagem')
        ? $this->salvarImagem($request->file('imagem'), $request->validated('nome_produto'))
        : null;

    try {
        $produto->update($this->montarAtributos($request, $novaImagem));
    } catch (Throwable $erro) {
        if ($novaImagem) {
            $this->removerImagemSeNaoUsada($novaImagem);
        }

        throw $erro;
    }

    if ($novaImagem) {
        $this->removerImagemSeNaoUsada($imagemAntiga);
    }

    return redirect()
        ->route('admin.produtos.index')
        ->with('admin_status', 'Produto "'.$produto->nome_produto.'" atualizado com sucesso.');
}
```

**Explicação para estudo**

- O controller recebe `ProdutoRequest`, já sujeito à autorização e às regras, e o objeto `$produto` encontrado pela rota.
- `$imagemAntiga` preserva o nome anterior antes de alterar o registro.
- `hasFile('imagem')` verifica se foi enviado um arquivo novo. Caso contrário, `$novaImagem` fica nulo.
- Quando há arquivo, `salvarImagem()` grava a nova imagem usando o nome atual validado.
- `$produto->update(...)` altera o registro existente por meio do Eloquent, usando a chave primária `id_pr`.
- Se ocorrer uma falha ao atualizar, o arquivo novo é removido quando não estiver sendo utilizado, e a exceção é relançada.
- Se a atualização funcionar e houver arquivo novo, o controller verifica se a imagem antiga pode ser removida.
- Por fim, retorna à listagem com uma mensagem de atualização.

**Continuação da fala**

“O método envia ao Model o resultado de `montarAtributos()`. Vou rever esse auxiliar para entender quais colunas realmente mudam e o que acontece com a imagem quando ela não é substituída.”

## Etapa 6 — Campos enviados ao `UPDATE`

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — método `montarAtributos()`, linhas 134–158.

**Código correspondente — método completo**

```php
// Normaliza os campos e trata caixas de seleção ausentes como desmarcadas.
private function montarAtributos(ProdutoRequest $request, ?string $imagem = null): array
{
    $dados = $request->validated();

    $atributos = [
        'nome_produto' => trim($dados['nome_produto']),
        'categoria' => trim($dados['categoria']),
        'descricao' => filled($dados['descricao'] ?? null) ? trim($dados['descricao']) : null,
        'preco' => $dados['preco'],
        'estoque' => (int) $dados['estoque'],
        // Toda peça é personalizável: texto, cor e fonte ficam sempre ativos.
        'permite_texto' => true,
        'permite_cor' => true,
        'permite_fonte' => true,
    ];

    if ($imagem) {
        $atributos['imagem'] = $imagem;
    }

    return $atributos;
}
```

**Explicação para estudo**

- O método usa novamente `validated()` para obter os valores aprovados.
- Nome e categoria são aparados com `trim()`.
- Descrição vazia é persistida como `null`.
- Estoque é convertido para inteiro.
- As flags de personalização continuam verdadeiras na implementação atual.
- A chave `imagem` só é incluída quando há um nome de arquivo novo. Sem nova imagem, o Model conserva o valor atual.

**Continuação da fala**

“No Eloquent, essa chamada de `update()` corresponde a atualizar uma linha da tabela. A tabela e sua chave precisam estar mapeadas corretamente no Model.”

## Etapa 7 — Model e correspondência com SQL

**Onde encontrar**

`app/Models/Produto.php` — classe `Produto`, configuração das linhas 13–43.

**Código correspondente — trecho completo de configuração**

```php
protected $table = 'produtos';

protected $primaryKey = 'id_pr';

public $incrementing = true;

public $timestamps = false;

protected $fillable = [
    'nome_produto',
    'categoria',
    'preco',
    'estoque',
    'descricao',
    'imagem',
    'permite_texto',
    'permite_cor',
    'permite_fonte',
];

protected $casts = [
    'preco' => 'decimal:2',
    'estoque' => 'integer',
    'permite_texto' => 'boolean',
    'permite_cor' => 'boolean',
    'permite_fonte' => 'boolean',
];
```

**Explicação para estudo**

- `primaryKey = 'id_pr'` é essencial para que o objeto represente a linha correta.
- `fillable` permite que `update()` atribua apenas os campos listados.
- O Eloquent gera uma atualização conceitualmente equivalente ao SQL abaixo:

```sql
UPDATE produtos
SET nome_produto = 'Novo nome',
    categoria = 'Nova categoria',
    preco = 30.00,
    estoque = 12,
    descricao = 'Nova descrição'
WHERE id_pr = 1;
```

O exemplo SQL ilustra o resultado lógico; o controller não concatena nem executa esse texto. A persistência é feita pelo Model.

---

# Pessoa 5 — Delete: excluir produto

## Objetivo

Apresentar como a solicitação de exclusão é enviada, como o Laravel localiza o produto, como o sistema verifica pedidos vinculados e como o registro e, quando possível, a imagem são removidos. No banco, a operação corresponde a `DELETE`.

## Etapa 1 — Formulário envia a solicitação de exclusão

**Fala da apresentação**

“Por último, eu vou explicar como o sistema exclui um produto. A solicitação parte de um formulário na linha do produto e chega à rota de exclusão com o identificador correspondente.”

**Onde encontrar**

`resources/views/admin/products/index.blade.php` — formulário de exclusão, linhas 134–150.

**Código correspondente — formulário completo**

```blade
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
```

**Explicação para estudo**

- `route('admin.produtos.destroy', $produto)` monta a URL incluindo o produto selecionado.
- O HTML de formulário não envia `DELETE` diretamente; por isso usa `method="POST"` e `@method('DELETE')` para o Laravel interpretar o verbo correto.
- `@csrf` insere o token exigido para requisições que alteram dados.
- O atributo `data-confirm` contém a mensagem de confirmação usada pela interface.
- O botão `submit` envia o formulário.

**Continuação da fala**

“A declaração de recurso cria essa rota `DELETE`. Ela está dentro do mesmo grupo administrativo, então continua protegida pelos middlewares.”

## Etapa 2 — Rota e identificação do registro

**Onde encontrar**

`routes/admin.php` — linha 14;  
`app/Models/Produto.php` — configuração de tabela e chave primária, linhas 13–19.

**Código correspondente — declaração da rota**

```php
Route::resource('produtos', ProdutoController::class)->except(['show']);
```

**Código correspondente — configuração relevante do Model**

```php
protected $table = 'produtos';

protected $primaryKey = 'id_pr';

public $incrementing = true;

public $timestamps = false;
```

**Explicação para estudo**

- O `resource` gera `DELETE /admin/produtos/{produto}`, nomeada `admin.produtos.destroy`.
- O parâmetro `{produto}` indica qual registro deve ser operado.
- O route model binding converte esse parâmetro em um objeto `Produto`.
- O Model informa que a tabela é `produtos` e a chave identificadora é `id_pr`.
- `destroy(Produto $produto)` recebe o registro localizado; se o identificador não existir, a resolução da rota não encontra o modelo.

**Continuação da fala**

“Antes de excluir, o controller consulta se existem pedidos ligados a esse produto. Vou mostrar primeiro o relacionamento que permite fazer essa verificação.”

## Etapa 3 — Relacionamento entre produto e pedidos

**Onde encontrar**

`app/Models/Produto.php` — método `pedidos()`, linhas 48–51.

**Código correspondente — método completo**

```php
public function pedidos(): HasMany
{
    return $this->hasMany(Pedido::class, 'produto', 'id_pr');
}
```

**Explicação para estudo**

- `HasMany` descreve uma relação em que um produto pode estar associado a vários pedidos.
- `Pedido::class` é o Model relacionado.
- `'produto'` é a chave estrangeira na tabela `pedidos`.
- `'id_pr'` é a chave local na tabela `produtos`.
- A migration da tabela de pedidos também declara essa relação como uma chave estrangeira.

**Continuação da fala**

“Com essa relação, o controller consegue verificar se ao menos um pedido utiliza o produto. Se houver, ele interrompe a exclusão para não remover um registro ainda referenciado.”

## Etapa 4 — Controller verifica e exclui

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — classe `ProdutoController`, método `destroy()`, linhas 118–137.

**Código correspondente — método completo**

```php
// Impede a exclusão quando o produto está ligado a pedidos.
public function destroy(Produto $produto): RedirectResponse
{
    if ($produto->pedidos()->exists()) {
        return back()->with(
            'admin_error',
            'Não foi possível excluir "'.$produto->nome_produto.'": existem pedidos vinculados a este produto.'
        );
    }

    $nome = $produto->nome_produto;
    $imagem = $produto->imagem;

    $produto->delete();
    $this->removerImagemSeNaoUsada($imagem);

    return redirect()
        ->route('admin.produtos.index')
        ->with('admin_status', 'Produto "'.$nome.'" excluído com sucesso.');
}
```

**Explicação para estudo**

- O argumento `Produto $produto` já é o registro resolvido a partir da rota.
- `pedidos()->exists()` consulta o relacionamento e verifica se existe ao menos uma linha vinculada.
- Se existir pedido, `back()` retorna à página anterior e envia `admin_error`. O fluxo termina antes de `delete()`.
- Se não houver pedidos, o controller guarda o nome e a imagem antes de apagar o registro.
- `$produto->delete()` pede ao Eloquent para excluir aquela linha da tabela `produtos`.
- A imagem física é tratada pelo método auxiliar mostrado na próxima etapa.
- Ao final, o controller volta à listagem e adiciona uma mensagem de sucesso.

**Relação com SQL**

Quando a verificação de pedidos não encontra vínculo, o método `$produto->delete()` equivale conceitualmente a:

```sql
DELETE FROM produtos
WHERE id_pr = 1;
```

O valor `1` é ilustrativo. Na execução real, o Eloquent usa a chave `id_pr` do objeto recebido pelo método. A aplicação não concatena esse comando SQL manualmente.

**Continuação da fala**

“A regra de bloqueio depende de um vínculo verdadeiro no banco. Vou mostrar a coluna `produto` da tabela de pedidos e sua referência à chave `id_pr`.”

## Etapa 5 — Chave estrangeira em `pedidos`

**Onde encontrar**

`database/migrations/2026_11_11_000003_create_pedidos_table.php` — migration completa.

**Código correspondente — arquivo completo**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mantém os vínculos definidos no esquema oficial de pedidos.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->integer('id_pedido', true);
            $table->integer('usuario');
            $table->integer('produto');
            $table->string('cor_produto', 20);
            $table->string('escrita', 40)->nullable();
            $table->string('cor_escrita', 40)->nullable();
            $table->string('fonte_escrita', 40)->nullable();
            $table->string('desenho', 30)->nullable();
            $table->date('data_pedido');

            $table->foreign('usuario')->references('id_us')->on('usuarios');
            $table->foreign('produto')->references('id_pr')->on('produtos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
```

**Explicação para estudo**

- A tabela `pedidos` armazena o identificador do produto na coluna inteira `produto`.
- A instrução `foreign('produto')->references('id_pr')->on('produtos')` declara que esse valor aponta para `produtos.id_pr`.
- O controller consulta o vínculo antes de excluir. A migration não configura `ON DELETE CASCADE`; o bloqueio apresentado é uma regra explícita da aplicação.

**Continuação da fala**

“Quando não há pedido relacionado, o registro é removido. Depois disso, o arquivo de imagem só deve ser apagado se nenhum outro produto ainda estiver usando-o.”

## Etapa 6 — Remoção segura da imagem

**Onde encontrar**

`app/Http/Controllers/Admin/ProdutoController.php` — método `removerImagemSeNaoUsada()`, linhas 188–202.

**Código correspondente — método completo**

```php
// Remove uma imagem antiga apenas quando nenhum outro produto ainda a utiliza.
private function removerImagemSeNaoUsada(?string $imagem): void
{
    if (! $imagem) {
        return;
    }

    if (Produto::query()->where('imagem', $imagem)->exists()) {
        return;
    }

    $caminho = public_path('images/produtos/'.$imagem);

    if (is_file($caminho)) {
        unlink($caminho);
    }
}
```

**Explicação para estudo**

- Se não houver nome de imagem, o método não tem arquivo para remover e retorna.
- `where('imagem', $imagem)->exists()` consulta se algum produto ainda referencia o arquivo.
- Se houver uso, o arquivo é preservado.
- Se não houver uso, `public_path()` resolve o caminho real em `public/images/produtos`.
- `is_file()` confirma a existência do arquivo; `unlink()` o remove.

**Continuação da fala**

“Assim, o Delete remove a linha de `produtos` por meio do Model, mas respeita os pedidos relacionados e evita apagar uma imagem ainda compartilhada por outro produto.”
