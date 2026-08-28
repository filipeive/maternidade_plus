<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('filiacao')->nullable()->after('nome_completo');
            $table->enum('estado_civil', ['solteira', 'casada', 'uniao_de_facto', 'viuva', 'divorciada'])->default('solteira')->after('data_nascimento');
            $table->string('local_trabalho')->nullable()->after('estado_civil');
            $table->string('distrito')->nullable()->default('Quelimane')->after('endereco');
            $table->string('bairro')->nullable()->after('distrito');
            $table->string('ponto_referencia_residencia')->nullable()->after('bairro');
            $table->string('pessoa_referencia_nome')->nullable()->after('contacto_emergencia');
            $table->string('pessoa_referencia_contacto')->nullable()->after('pessoa_referencia_nome');
            $table->unsignedSmallInteger('altura_cm')->nullable()->after('tipo_sanguineo'); // Ex: 155 cm (para triagem <150cm)
            $table->enum('tipo_sanguineo_parceiro', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('tipo_sanguineo');
            $table->string('codigo_ptv')->nullable()->after('documento_bi');
            $table->boolean('uso_rede_mosquiteira')->default(true)->after('alergias');
            $table->boolean('alergia_penicilina')->default(false)->after('uso_rede_mosquiteira');
            $table->boolean('alergia_cotrimoxazol')->default(false)->after('alergia_penicilina');
            $table->boolean('alergia_sp')->default(false)->after('alergia_cotrimoxazol');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'filiacao',
                'estado_civil',
                'local_trabalho',
                'distrito',
                'bairro',
                'ponto_referencia_residencia',
                'pessoa_referencia_nome',
                'pessoa_referencia_contacto',
                'altura_cm',
                'tipo_sanguineo_parceiro',
                'codigo_ptv',
                'uso_rede_mosquiteira',
                'alergia_penicilina',
                'alergia_cotrimoxazol',
                'alergia_sp',
            ]);
        });
    }
};
