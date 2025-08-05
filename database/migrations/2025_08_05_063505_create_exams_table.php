<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->onDelete('cascade');
            $table->enum('tipo_exame', [
                'hemograma_completo',
                'glicemia_jejum',
                'teste_tolerancia_glicose',
                'urina_tipo_1',
                'urocultura',
                'ultrassom_obstetrico',
                'teste_hiv',
                'teste_sifilis',
                'hepatite_b',
                'toxoplasmose',
                'rubéola',
                'estreptococo_grupo_b',
                'outros'
            ]);
            $table->string('descricao_exame')->nullable();
            $table->date('data_solicitacao');
            $table->date('data_realizacao')->nullable();
            $table->text('resultado')->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('status', ['solicitado', 'realizado', 'pendente'])->default('solicitado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
