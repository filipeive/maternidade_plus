<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo');
            $table->date('data_nascimento');
            $table->string('documento_bi')->unique();
            $table->string('contacto');
            $table->string('email')->nullable();
            $table->string('contacto_emergencia')->nullable();
            $table->text('endereco');
            $table->enum('tipo_sanguineo', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('alergias')->nullable();
            $table->text('historico_medico')->nullable();
            $table->date('data_ultima_menstruacao')->nullable();
            $table->date('data_provavel_parto')->nullable();
            $table->integer('numero_gestacoes')->default(1);
            $table->integer('numero_partos')->default(0);
            $table->integer('numero_abortos')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
