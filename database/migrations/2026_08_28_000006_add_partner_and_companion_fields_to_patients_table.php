<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('tem_parceiro')->default(true)->after('pessoa_referencia_contacto');
            $table->string('parceiro_nome')->nullable()->after('tem_parceiro');
            $table->string('parceiro_contacto')->nullable()->after('parceiro_nome');
            $table->boolean('parceiro_notificar_sms')->default(true)->after('parceiro_contacto');
            
            $table->string('acompanhante_nome')->nullable()->after('parceiro_notificar_sms');
            $table->string('acompanhante_parentesco')->nullable()->after('acompanhante_nome'); // Mãe, Tia, Irmã, Sogra, Vizinha, etc.
            $table->string('acompanhante_contacto')->nullable()->after('acompanhante_parentesco');
            $table->boolean('acompanhante_notificar_sms')->default(true)->after('acompanhante_contacto');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'tem_parceiro',
                'parceiro_nome',
                'parceiro_contacto',
                'parceiro_notificar_sms',
                'acompanhante_nome',
                'acompanhante_parentesco',
                'acompanhante_contacto',
                'acompanhante_notificar_sms',
            ]);
        });
    }
};

