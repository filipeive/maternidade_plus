<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maternal_prophylaxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Profissional que administrou

            // Vacinação Anti-Tetânica (VAT) - MISAU
            $table->date('vat_1_dose')->nullable();
            $table->date('vat_2_dose')->nullable();
            $table->date('vat_3_dose')->nullable();
            $table->date('vat_4_dose')->nullable();
            $table->date('vat_5_dose')->nullable();
            $table->date('vat_reforco')->nullable();

            // Prevenção da Malária (TIP / Fansidar SP) - MISAU
            $table->date('sp_1_dose')->nullable(); // A partir de 20 semanas
            $table->date('sp_2_dose')->nullable(); // A partir de 28 semanas
            $table->date('sp_3_dose')->nullable(); // A partir de 32 semanas
            $table->date('sp_4_dose')->nullable();
            $table->boolean('remtil_entregue')->default(false); // Rede Mosquiteira Tratada
            $table->date('remtil_data_entrega')->nullable();

            // Nutrição & Anemia
            $table->boolean('sal_ferroso_folico_3doses')->default(false);
            $table->integer('doses_sal_ferroso_entregues')->default(0);
            $table->date('mebendazol_administrado')->nullable(); // Desparasitação a partir das 12 semanas

            // PTV / HIV & Sífilis
            $table->string('hiv_status_entrada')->nullable(); // Negativo, Positivo, Desconhecido
            $table->date('hiv_teste_data')->nullable();
            $table->string('hiv_resultado_cpn')->nullable();
            $table->boolean('parceiro_testado_hiv')->default(false);
            $table->string('parceiro_resultado_hiv')->nullable();
            $table->boolean('ctz_iniciado')->default(false); // Cotrimoxazol
            $table->string('esquema_ptv')->nullable(); // Monoprofilaxia NVP, Biprofilaxia NVP+AZT, TARV
            $table->date('tarv_inicio_data')->nullable();

            // Sífilis & Tratamento do Casal
            $table->string('sifilis_resultado')->nullable(); // Negativo, Positivo, Não Feito
            $table->date('sifilis_teste_data')->nullable();
            $table->boolean('sifilis_tratamento_mulher')->default(false); // Penicilina Benzatínica 3 doses
            $table->boolean('sifilis_tratamento_parceiro')->default(false);

            // Prevenção da Hemorragia Pós-Parto (HPP)
            $table->boolean('misoprostol_entregue')->default(false); // Entregue na 4ª CPN / >28 semanas para parto no domicílio/comunidade
            $table->date('misoprostol_data_entrega')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maternal_prophylaxes');
    }
};
