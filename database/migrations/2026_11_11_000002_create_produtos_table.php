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
