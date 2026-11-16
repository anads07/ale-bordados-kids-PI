<?php

namespace App\Providers;

use App\Services\User\CartService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

// Registra componentes e compartilha dados usados pelas páginas.
class AppServiceProvider extends ServiceProvider
{
    // Registra os componentes Blade e compartilha os dados do carrinho.
    public function boot(): void
    {
        // Mantém os componentes próximos das páginas de cada área.
        Blade::anonymousComponentPath(resource_path('views/admin/components'), 'admin');
        Blade::anonymousComponentPath(resource_path('views/user/components'), 'user');
        Blade::anonymousComponentPath(resource_path('views/shared/components'), 'shared');

        // Compartilha os dados do carrinho com o cabeçalho e o painel lateral.
        View::composer(['user.partials.header', 'user.partials.cart-drawer'], function ($visualizacao) {
            $carrinho = app(CartService::class);

            $visualizacao->with([
                'quantidadeCarrinho' => $carrinho->quantidadeTotal(),
                'itensCarrinho' => $carrinho->itens(),
                'totalCarrinhoFormatado' => $carrinho->totalFormatado(),
            ]);
        });
    }
}
