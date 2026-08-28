<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obstetric_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->unsignedTinyInteger('numero_gravidez')->default(1);
            $table->year('ano')->nullable();
            $table->enum('tipo_aborto', ['nenhum', 'espontaneo', 'provocado'])->default('nenhum');
            $table->enum('local_parto', ['us_maternidade', 'domicilio', 'caminho', 'parteira_tradicional', 'outro'])->default('us_maternidade');
            $table->boolean('prematuro')->default(false);
            $table->enum('tipo_parto', ['eutocico', 'cesariana', 'ventosa_forceps', 'ectopica', 'outro'])->default('eutocico');
            $table->boolean('gemelar')->default(false);
            $table->boolean('nado_morto')->default(false);
            $table->boolean('nato_vivo')->default(true);
            $table->unsignedSmallInteger('peso_rn_gramas')->nullable(); // Ex: 3200
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'numero_gravidez']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obstetric_histories');
    }
};

