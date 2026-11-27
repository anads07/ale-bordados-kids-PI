<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\User\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller da vitrine pública com busca, filtros e ordenação.
 */
class HomeController extends Controller
{
    // Monta a vitrine com filtros, depoimentos e favoritos.
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
        $padrao = [
            'imagem' => 'images/pagina_inicial/banner_1.png',
            'alt' => '',
            'ajuste' => 'cover',
            'posicao' => 'center center',
            'eyebrow' => '',
            'title' => '',
            'highlight' => '',
            'description' => '',
            'acao' => 'Ver coleção',
            'link' => '#produtos',
        ];

        $slides = config('mock.hero_slides', []);

        if (! is_array($slides) || $slides === []) {
            $slides = [[
                'alt' => 'Banner de bordado com ursinha Sara Maria',
                'eyebrow' => (string) ($destaque['eyebrow'] ?? 'Bordados personalizados'),
                'title' => (string) ($destaque['title'] ?? 'Carinho em cada ponto,'),
                'highlight' => (string) ($destaque['highlight'] ?? 'feito para o seu bebê'),
                'description' => (string) ($destaque['description'] ?? 'Enxovais, roupas e toalhas infantis com bordados delicados e personalizados.'),
            ]];
        }

        return array_values(array_map(function (mixed $slide) use ($padrao): array {
            $slide = is_array($slide) ? $slide : [];

            return [
                'imagem' => (string) ($slide['imagem'] ?? $padrao['imagem']),
                'alt' => (string) ($slide['alt'] ?? $padrao['alt']),
                'ajuste' => (string) ($slide['ajuste'] ?? $padrao['ajuste']),
                'posicao' => (string) ($slide['posicao'] ?? $padrao['posicao']),
                'eyebrow' => (string) ($slide['eyebrow'] ?? $padrao['eyebrow']),
                'title' => (string) ($slide['title'] ?? $padrao['title']),
                'highlight' => (string) ($slide['highlight'] ?? $padrao['highlight']),
                'description' => (string) ($slide['description'] ?? $padrao['description']),
                'acao' => (string) ($slide['acao'] ?? $padrao['acao']),
                'link' => (string) ($slide['link'] ?? $padrao['link']),
            ];
        }, $slides));
    }

    // Normaliza parâmetros de texto vindos da URL.
    private function texto(mixed $valor): string
    {
        return is_string($valor) ? trim($valor) : '';
    }

    // Normaliza parâmetros numéricos e descarta valores inválidos.
    private function numero(mixed $valor): ?float
    {
        if (! is_numeric($valor)) {
            return null;
        }

        return max(0, (float) $valor);
    }
}
