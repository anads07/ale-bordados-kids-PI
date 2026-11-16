<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mantém o esquema oficial de autenticação.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('id_us', true);
            $table->string('nome_usuario', 20);
            $table->string('sobrenome_usuario', 50);
            $table->string('email', 50)->unique();
            $table->string('senha', 255);
            $table->boolean('is_admin')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
