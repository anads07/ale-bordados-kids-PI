<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('cep', 9)->nullable()->after('imagem_inspiracao');
            $table->string('endereco')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro', 100)->nullable()->after('complemento');
            $table->string('cidade', 100)->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('forma_pagamento', 40)->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'cep',
                'endereco',
                'numero',
                'complemento',
                'bairro',
                'cidade',
                'estado',
                'forma_pagamento',
            ]);
        });
    }
};
