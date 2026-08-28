<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('motivo_inativacao')->nullable()->after('ativo'); // 'transferencia_us', 'transferencia_provincia', 'mudanca_residencia', 'obito', 'abandono', 'outro'
            $table->dateTime('data_transferencia')->nullable()->after('motivo_inativacao');
            $table->string('unidade_sanitaria_destino')->nullable()->after('data_transferencia');
            $table->string('provincia_destino')->nullable()->after('unidade_sanitaria_destino');
            $table->string('distrito_destino')->nullable()->after('provincia_destino');
            $table->string('motivo_transferencia')->nullable()->after('distrito_destino');
            $table->string('guia_transferencia_numero')->nullable()->after('motivo_transferencia');
            $table->text('resumo_clinico_transferencia')->nullable()->after('guia_transferencia_numero');
            $table->foreignId('profissional_transferencia_id')->nullable()->after('resumo_clinico_transferencia')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['profissional_transferencia_id']);
            $table->dropColumn([
                'motivo_inativacao',
                'data_transferencia',
                'unidade_sanitaria_destino',
                'provincia_destino',
                'distrito_destino',
                'motivo_transferencia',
                'guia_transferencia_numero',
                'resumo_clinico_transferencia',
                'profissional_transferencia_id'
            ]);
        });
    }
};
