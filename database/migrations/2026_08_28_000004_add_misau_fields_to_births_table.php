<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('births', function (Blueprint $table) {
            $table->decimal('perimetro_craniano', 4, 1)->nullable()->after('altura_nascimento');
            $table->boolean('reanimacao_rn')->default(false)->after('observacoes_rn');
            $table->boolean('aspiracao_rn')->default(false)->after('reanimacao_rn');
            $table->boolean('profilaxia_ocular')->default(true)->after('aspiracao_rn');
            $table->boolean('vitamina_k')->default(true)->after('profilaxia_ocular');
            $table->boolean('anomalia_congenita')->default(false)->after('vitamina_k');
            $table->string('anomalia_descricao')->nullable()->after('anomalia_congenita');
            $table->boolean('aleitamento_primeira_hora')->default(true)->after('anomalia_descricao');
            $table->boolean('vacina_bcg')->default(false)->after('aleitamento_primeira_hora');
            $table->boolean('vacina_polio_0')->default(false)->after('vacina_bcg');
            $table->boolean('mae_vitamina_a')->default(true)->after('vacina_polio_0');
            $table->string('mae_tarv_parto')->nullable()->after('mae_vitamina_a');
            $table->string('apresentacao_parto')->nullable()->after('tipo_parto'); // cefalica, pelvica, transversa
            $table->string('estado_perineo')->nullable()->after('apresentacao_parto'); // integro, episiotomia, laceracao
            $table->boolean('puerperio_febre')->default(false)->after('condicoes_pos_parto');
            $table->boolean('puerperio_hemorragia')->default(false)->after('puerperio_febre');
            $table->string('nome_parteira_enfermeira')->nullable()->after('puerperio_hemorragia');
        });
    }

    public function down(): void
    {
        Schema::table('births', function (Blueprint $table) {
            $table->dropColumn([
                'perimetro_craniano',
                'reanimacao_rn',
                'aspiracao_rn',
                'profilaxia_ocular',
                'vitamina_k',
                'anomalia_congenita',
                'anomalia_descricao',
                'aleitamento_primeira_hora',
                'vacina_bcg',
                'vacina_polio_0',
                'mae_vitamina_a',
                'mae_tarv_parto',
                'apresentacao_parto',
                'estado_perineo',
                'puerperio_febre',
                'puerperio_hemorragia',
                'nome_parteira_enfermeira',
            ]);
        });
    }
};

