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
