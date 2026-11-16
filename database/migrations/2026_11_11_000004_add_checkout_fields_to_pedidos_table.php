<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Armazena os dados do checkout e o status de cada pedido.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->unsignedSmallInteger('quantidade')->default(1)->after('produto');
            $table->text('observacoes')->nullable()->after('desenho');
            $table->string('imagem_inspiracao')->nullable()->after('observacoes');
            $table->string('status', 20)->default('novo')->after('data_pedido');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['quantidade', 'observacoes', 'imagem_inspiracao', 'status']);
        });
    }
};
