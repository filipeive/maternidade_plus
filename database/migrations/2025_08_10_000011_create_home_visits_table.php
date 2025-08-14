<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('home_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informações básicas da visita
            $table->datetime('data_visita');
            $table->text('motivo_visita');
            $table->enum('tipo_visita', [
                'rotina',
                'pos_parto',
                'alto_risco',
                'faltosa',
                'emergencia',
                'educacao',
                'seguimento'
            ]);
            $table->text('endereco_visita');
            
            // Status da visita
            $table->enum('status', [
                'agendada',
                'realizada',
                'cancelada', 
                'reagendada',
                'nao_encontrada'
            ])->default('agendada');
            
            // Dados coletados durante a visita (preenchidos após realização)
            $table->text('observacoes_ambiente')->nullable();
            $table->enum('condicoes_higiene', ['bom', 'regular', 'ruim'])->nullable();
            $table->enum('apoio_familiar', ['adequado', 'parcial', 'inadequado'])->nullable();
            $table->text('estado_nutricional')->nullable();
            
            // Dados clínicos
            $table->json('sinais_vitais')->nullable(); // PA, peso, temperatura, etc.
            $table->text('queixas_principais')->nullable();
            
            // Intervenções realizadas
            $table->text('orientacoes_dadas')->nullable();
            $table->json('materiais_entregues')->nullable(); // Folhetos, medicamentos, etc.
            
            // Planejamento
            $table->date('proxima_visita')->nullable();
            
            // Informações adicionais
            $table->boolean('acompanhante_presente')->default(false);
            $table->boolean('necessita_referencia')->default(false);
            $table->text('observacoes_gerais')->nullable();
            
            // Geolocalização
            $table->json('coordenadas_gps')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['patient_id', 'data_visita']);
            $table->index(['user_id', 'data_visita']);
            $table->index(['status', 'data_visita']);
            $table->index('tipo_visita');
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_visits');
    }
};