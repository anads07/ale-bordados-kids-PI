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

        $filtros = [
            'busca' => $busca,
            'categoria' => $categoria,
            'precoMin' => $precoMinimo,
            'precoMax' => $precoMaximo,
            'ordem' => $ordem,
        ];

        $idsFavoritos = $favoritos->ids();

        return view('user.home', compact('produtos', 'categorias', 'depoimentos', 'destaque', 'filtros', 'idsFavoritos'));
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
        return is_numeric($valor) ? (float) $valor : null;
    }
}
