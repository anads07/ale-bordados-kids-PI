<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\PerfilController;
use App\Http\Controllers\User\ProdutoController;
use App\Http\Controllers\User\SobreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::view('/orcamento', 'user.orcamento.index')->name('orcamento.index');
Route::get('/produtos/{produto}', [ProdutoController::class, 'show'])->name('produtos.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1')
    ->name('login.store');
Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/cadastro', [AuthController::class, 'register'])
    ->middleware('throttle:6,1')
    ->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/favoritos', [FavoriteController::class, 'index'])->name('favoritos.index');
Route::post('/favoritos/{produto}', [FavoriteController::class, 'toggle'])->name('favoritos.toggle');

// Permite compras como visitante até o checkout e encaminha /carrinho para a revisão.
Route::get('/carrinho', fn () => redirect()->route('checkout.index'))->name('carrinho.index');
Route::post('/carrinho/{produto}', [CartController::class, 'adicionar'])->name('carrinho.store');
Route::patch('/carrinho/{linha}', [CartController::class, 'atualizar'])->name('carrinho.update');
Route::delete('/carrinho/{linha}', [CartController::class, 'remover'])->name('carrinho.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/sucesso', [CheckoutController::class, 'success'])->name('checkout.success');
});
