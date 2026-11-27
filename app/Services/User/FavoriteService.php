<?php

namespace App\Services\User;

use App\Models\Produto;
use Illuminate\Support\Collection;

/**
 * Favoritos persistidos na sessão como uma lista de IDs de produtos.
 */
class FavoriteService
{
    public const SESSION_KEY = 'favoritos';

    // Retorna apenas IDs válidos, únicos e numéricos.
    public function ids(): array
    {
        $dados = session()->get(self::SESSION_KEY, []);

        if (! is_array($dados)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map(fn ($id) => is_numeric($id) ? (int) $id : null, $dados),
            fn ($id) => $id !== null && $id > 0
        )));
    }

    // Verifica se o produto já está entre os favoritos.
    public function has(Produto|int $produto): bool
    {
        $id = $produto instanceof Produto ? (int) $produto->id_pr : (int) $produto;

        return in_array($id, $this->ids(), true);
    }

    // Alterna o favorito e devolve o novo estado.
    public function toggle(Produto $produto): bool
    {
        $identificadores = $this->ids();
        $id = (int) $produto->id_pr;
        $indice = array_search($id, $identificadores, true);

        if ($indice === false) {
            $identificadores[] = $id;
            $favoritado = true;
        } else {
            unset($identificadores[$indice]);
            $favoritado = false;
        }

        session()->put(self::SESSION_KEY, array_values($identificadores));

        return $favoritado;
    }

    // Conta os favoritos ativos.
    public function count(): int
    {
        return count($this->ids());
    }

    // Busca os produtos na ordem do catálogo, ignorando IDs removidos.
    public function produtos(): Collection
    {
        $identificadores = $this->ids();

        if ($identificadores === []) {
            return collect();
        }

        return Produto::query()
            ->whereIn('id_pr', $identificadores)
            ->orderBy('id_pr')
            ->get();
    }
}
