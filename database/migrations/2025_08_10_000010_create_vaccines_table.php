<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informações da vacina
            $table->enum('tipo_vacina', [
                'tetanica', 
                'hepatite_b', 
                'influenza', 
                'covid19', 
                'febre_amarela', 
                'iptp'
            ]);
            $table->string('descricao')->nullable();
            
            // Datas
            $table->datetime('data_administracao');
            $table->date('proxima_dose')->nullable();
            
            // Detalhes da administração
            $table->integer('dose_numero');
            $table->string('lote', 100)->nullable();
            $table->string('fabricante', 100)->nullable();
            $table->date('data_vencimento')->nullable();
            $table->enum('local_aplicacao', [
                'braco_esquerdo',
                'braco_direito', 
                'coxa_esquerda',
                'coxa_direita',
                'gluteo'
            ]);
            
            // Observações e reações
            $table->text('observacoes')->nullable();
            $table->text('reacao_adversa')->nullable();
            
            // Status
            $table->enum('status', [
                'administrada',
                'pendente', 
                'vencida',
                'reagenda'
            ])->default('administrada');
            
            $table->timestamps();
            
            // Índices
            $table->index(['patient_id', 'tipo_vacina']);
            $table->index(['status', 'proxima_dose']);
            $table->index('data_administracao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vaccines');
    }
};