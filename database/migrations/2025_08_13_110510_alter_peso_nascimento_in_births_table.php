<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('births', function (Blueprint $table) {
            // Alterar para smallInteger unsigned (suporta até 65535)
            $table->smallInteger('peso_nascimento')
                  ->unsigned()
                  ->nullable()
                  ->comment('Peso em gramas')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('births', function (Blueprint $table) {
            // Voltar para o formato anterior (se precisar reverter)
            $table->decimal('peso_nascimento', 4, 1)
                  ->nullable()
                  ->comment('Peso em gramas')
                  ->change();
        });
    }
};
