<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('system_notifications')) {
            Schema::create('system_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
                $table->string('tipo', 50)->default('sistema'); // alerta_clinico, consulta_faltosa, exame_pronto, vacina_atraso, visita_referencia, sms_alerta, sistema
                $table->string('titulo');
                $table->text('mensagem');
                $table->string('icone', 50)->default('bell');
                $table->string('cor', 30)->default('info'); // success, info, warning, danger
                $table->string('url', 500)->nullable();
                $table->boolean('lido')->default(false);
                $table->timestamp('lido_em')->nullable();
                $table->foreignId('lido_por')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['user_id', 'lido']);
                $table->index(['tipo', 'lido']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};

