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
