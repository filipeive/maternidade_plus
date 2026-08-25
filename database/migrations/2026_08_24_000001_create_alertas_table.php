<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->onDelete('set null');
            $table->string('tipo');
            $table->enum('nivel', ['baixo', 'medio', 'alto']);
            $table->text('mensagem');
            $table->json('dados')->nullable();
            $table->enum('status', ['ativo', 'em_seguimento', 'resolvido', 'ignorado'])->default('ativo');
            $table->foreignId('resolvido_por')->nullable()->constrained('users')->onDelete('set null');
            $table->text('nota_resolucao')->nullable();
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status']);
            $table->index(['nivel', 'status']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
