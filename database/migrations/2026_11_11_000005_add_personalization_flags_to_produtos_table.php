<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mantém os produtos existentes personalizáveis por padrão.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('permite_texto')->default(true)->after('imagem');
            $table->boolean('permite_cor')->default(true)->after('permite_texto');
            $table->boolean('permite_fonte')->default(true)->after('permite_cor');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['permite_texto', 'permite_cor', 'permite_fonte']);
        });
    }
};
