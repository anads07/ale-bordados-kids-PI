<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\User\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller da vitrine pública. Centraliza a busca, os filtros de categoria,
 * a faixa de preço e a ordenação dos produtos reais cadastrados no banco.
 */
class HomeController extends Controller
{
    /**
     * Monta a vitrine com filtros, depoimentos e estado dos favoritos.
     */
    public function index(Request $request, FavoriteService $favoritos): View
    {
        $busca = $this->texto($request->query('q'));
        $categoria = $this->texto($request->query('categoria'));
        $precoMinimo = $this->numero($request->query('preco_min'));
        $precoMaximo = $this->numero($request->query('preco_max'));
        $ordem = $this->texto($request->query('ordem')) ?: 'relevancia';

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

        if ($precoMinimo !== null) {
            $consulta->where('preco', '>=', $precoMinimo);
        }

        if ($precoMaximo !== null) {
            $consulta->where('preco', '<=', $precoMaximo);
        }

        // Mantém a ordem do seeder como relevância padrão.
        switch ($ordem) {
            case 'menor_preco':
                $consulta->orderBy('preco');
                break;
            case 'maior_preco':
                $consulta->orderByDesc('preco');
                break;
            case 'nome':
                $consulta->orderBy('nome_produto');
                break;
            default:
                $consulta->orderBy('id_pr');
                break;
        }

        $produtos = $consulta->get();

        $categorias = Produto::query()
            ->select('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $depoimentos = config('mock.testimonials', []);
        $destaque = config('mock.hero', []);
        $slidesHero = $this->slidesHero($destaque);
        $ownPiece = config('mock.own_piece', []);

        $filtros = [
            'busca' => $busca,
            'categoria' => $categoria,
            'precoMin' => $precoMinimo,
            'precoMax' => $precoMaximo,
            'ordem' => $ordem,
        ];

        $idsFavoritos = $favoritos->ids();

        return view('user.home', compact(
            'produtos',
            'categorias',
            'depoimentos',
            'destaque',
            'slidesHero',
            'ownPiece',
            'filtros',
            'idsFavoritos'
        ));
    }

    /**
     * Monta os slides do hero com fallback para o destaque legado.
     *
     * @param  array<string, mixed>  $destaque
     * @return array<int, array<string, string>>
     */
    private function slidesHero(array $destaque): array
    {
        $slides = config('mock.hero_slides', []);

        if (! is_array($slides) || $slides === []) {
            $slides = [[
                'imagem' => 'images/pagina_inicial/banner_1.png',
                'alt' => 'Banner de bordado com ursinha Sara Maria',
                'ajuste' => 'cover',
                'posicao' => 'center center',
                'eyebrow' => (string) ($destaque['eyebrow'] ?? 'Bordados personalizados'),
                'title' => (string) ($destaque['title'] ?? 'Carinho em cada ponto,'),
                'highlight' => (string) ($destaque['highlight'] ?? 'feito para o seu bebê'),
                'description' => (string) ($destaque['description'] ?? 'Enxovais, roupas e toalhas infantis com bordados delicados e personalizados.'),
                'acao' => 'Ver coleção',
                'link' => '#produtos',
            ]];
        }

        return array_values(array_map(function (mixed $slide): array {
            $slide = is_array($slide) ? $slide : [];

            return [
                'imagem' => (string) ($slide['imagem'] ?? 'images/pagina_inicial/banner_1.png'),
                'alt' => (string) ($slide['alt'] ?? ''),
                'ajuste' => (string) ($slide['ajuste'] ?? 'cover'),
                'posicao' => (string) ($slide['posicao'] ?? 'center center'),
                'eyebrow' => (string) ($slide['eyebrow'] ?? ''),
                'title' => (string) ($slide['title'] ?? ''),
                'highlight' => (string) ($slide['highlight'] ?? ''),
                'description' => (string) ($slide['description'] ?? ''),
                'acao' => (string) ($slide['acao'] ?? 'Ver coleção'),
                'link' => (string) ($slide['link'] ?? '#produtos'),
            ];
        }, $slides));
    }

    /**
     * Normaliza parâmetros de texto vindos da URL para evitar arrays ou nulos.
     */
    private function texto(mixed $valor): string
    {
        return is_string($valor) ? trim($valor) : '';
    }

    /**
     * Normaliza parâmetros numéricos, descartando valores inválidos.
     */
    private function numero(mixed $valor): ?float
    {
        if (! is_numeric($valor)) {
            return null;
        }

        return max(0, (float) $valor);
    }
}
