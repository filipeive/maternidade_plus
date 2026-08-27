<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antenatal_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            
            // Antecedentes Obstétricos Pregressos
            $table->integer('num_gestas')->default(1);
            $table->integer('num_paras')->default(0);
            $table->integer('num_abortos_espontaneos')->default(0);
            $table->integer('num_abortos_provocados')->default(0);
            $table->integer('num_nados_mortos')->default(0);
            $table->integer('num_nados_vivos')->default(0);
            $table->integer('num_filhos_vivos_atuais')->default(0);
            $table->integer('num_cesarianas')->default(0);
            $table->integer('num_gravidezes_ectopicas')->default(0);
            $table->boolean('historico_gemelar')->default(false);
            $table->boolean('historico_rn_baixo_peso')->default(false); // < 2500g
            $table->boolean('historico_rn_macrossomico')->default(false); // > 4000g
            $table->boolean('historico_hemorragia_postpartum')->default(false);
            $table->boolean('historico_remocao_manual_placenta')->default(false);
            $table->date('data_ultimo_parto')->nullable();
            $table->string('local_ultimo_parto')->nullable();
            
            // Triagem de Alto Risco Obstétrico (ARO) - MISAU
            $table->boolean('is_aro')->default(false);
            $table->json('fatores_aro')->nullable(); // Array de códigos ou descrições dos fatores ARO
            $table->string('nivel_referencia_aro')->default('Primário'); // Primário, Secundário (Hospital Rural), Terciário (Hospital Provincial)

            // Plano Individual de Parto (PIP) - MISAU
            $table->string('pip_local_parto_previsto')->nullable(); // US de Referência
            $table->boolean('pip_necessita_casa_espera')->default(false);
            $table->string('pip_meio_transporte')->nullable();
            $table->string('pip_nome_acompanhante')->nullable();
            $table->string('pip_contacto_acompanhante')->nullable();
            $table->string('pip_doador_sangue_designado')->nullable();

            // Antropometria & Exame Físico de Base
            $table->decimal('altura_cm', 5, 1)->nullable(); // Se < 150cm é fator ARO
            $table->decimal('peso_inicial_kg', 5, 2)->nullable();
            $table->decimal('imc_inicial', 4, 1)->nullable();
            $table->decimal('perimetro_braquial_cm', 4, 1)->nullable(); // <21cm DAG, 21-23cm DAM
            $table->string('estado_nutricional_inicial')->nullable(); // Adequado, DAM, DAG, Sobrepeso

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antenatal_histories');
    }
};
