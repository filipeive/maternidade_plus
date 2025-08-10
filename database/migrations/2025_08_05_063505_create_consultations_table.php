<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Médico/Enfermeiro
            $table->datetime('data_consulta');
            $table->enum('tipo_consulta', [
                '1_trimestre', 
                '2_trimestre', 
                '3_trimestre', 
                'pos_parto',
                'emergencia'
            ]);
            $table->integer('semanas_gestacao')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->string('pressao_arterial')->nullable();
            $table->integer('batimentos_fetais')->nullable();
            $table->decimal('altura_uterina', 4, 1)->nullable();
            $table->text('observacoes')->nullable();
            $table->text('orientacoes')->nullable();
            $table->date('proxima_consulta')->nullable();
            $table->enum('status', ['agendada', 'confirmada', 'realizada', 'cancelada'])->default('agendada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
