<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('births', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->comment('Profissional que registrou');
            
            // Dados do parto
            $table->datetime('data_hora_parto');
            $table->enum('tipo_parto', [
                'normal',
                'cesariana',
                'forceps',
                'vacuum',
                'outros'
            ]);
            
            // Local e profissionais
            $table->string('local_parto')->nullable();
            $table->string('hospital_unidade')->nullable();
            $table->string('profissional_obstetra')->nullable();
            $table->string('profissional_enfermeiro')->nullable();
            
            // Dados da mãe durante o parto
            $table->integer('idade_gestacional_parto')->comment('Semanas completas no parto');
            $table->decimal('peso_mae_preparto', 5, 2)->nullable();
            $table->text('complicacoes_maternas')->nullable();
            
            // Dados do recém-nascido
            $table->enum('sexo_bebe', ['masculino', 'feminino'])->nullable();
            $table->decimal('peso_nascimento', 4, 1)->nullable()->comment('Peso em gramas');
            $table->decimal('altura_nascimento', 4, 1)->nullable()->comment('Altura em cm');
            $table->integer('apgar_1min')->nullable();
            $table->integer('apgar_5min')->nullable();
            $table->integer('apgar_10min')->nullable();
            $table->text('observacoes_rn')->nullable();
            
            // Status do bebê
            $table->enum('status_bebe', [
                'vivo_saudavel',
                'vivo_complicacoes', 
                'obito_fetal',
                'obito_neonatal'
            ])->default('vivo_saudavel');
            
            // Dados gerais
            $table->text('observacoes_gerais')->nullable();
            $table->text('medicamentos_utilizados')->nullable();
            $table->boolean('parto_multiplo')->default(false);
            $table->integer('numero_bebes')->default(1);
            
            // Pós-parto imediato
            $table->text('condicoes_pos_parto')->nullable();
            $table->datetime('alta_hospitalar')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('data_hora_parto');
            $table->index(['patient_id', 'data_hora_parto']);
        });
        
        // Adicionar apenas o status na tabela patients
        Schema::table('patients', function (Blueprint $table) {
            $table->enum('status_atual', [
                'gestante',
                'pos_parto', 
                'nao_gestante'
            ])->default('gestante')->after('data_provavel_parto');
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('status_atual');
        });
        
        Schema::dropIfExists('births');
    }
};