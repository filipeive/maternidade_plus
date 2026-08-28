<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Inserir definições padrão da Unidade Sanitária
        DB::table('settings')->insert([
            ['key' => 'unidade_sanitaria', 'value' => 'Centro de Saúde Urbano & Maternidade', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'provincia', 'value' => 'Maputo Cidade', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'distrito', 'value' => 'Kamubukwana', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'codigo_misau', 'value' => 'US-0421', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
