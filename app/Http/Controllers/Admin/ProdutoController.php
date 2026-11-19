<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

// Gerencia listagem, cadastro, edição e exclusão de produtos.
class ProdutoController extends Controller
{
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

    // Abre o cadastro com as categorias disponíveis.
    public function create(): View
    {
        return view('admin.products.form', [
            'produto' => null,
            'categorias' => $this->categorias(),
        ]);
    }

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

    // Abre a edição do produto selecionado.
    public function edit(Produto $produto): View
    {
        return view('admin.products.form', [
            'produto' => $produto,
            'categorias' => $this->categorias(),
        ]);
    }

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
}
