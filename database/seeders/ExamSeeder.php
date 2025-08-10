<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Consultation;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $consultations = Consultation::all();
        if ($consultations->isEmpty()) {
            $this->command->info('Sem consultas para popular exames');
            return;
        }

        $examTypes = [
            'hemograma_completo',
            'glicemia_jejum',
            'teste_tolerancia_glicose',
            'urina_tipo_1',
            'urocultura',
            'ultrassom_obstetrico',
            'teste_hiv',
            'teste_sifilis',
            'hepatite_b',
            'toxoplasmose',
            'rubéola',
            'estreptococo_grupo_b',
            'outros'
        ];

        foreach ($consultations as $consultation) {
            Exam::create([
                'consultation_id' => $consultation->id,
                'tipo_exame' => $examTypes[array_rand($examTypes)],
                'descricao_exame' => 'Exame de rotina solicitado.',
                'data_solicitacao' => Carbon::now()->subDays(rand(10, 50)),
                'data_realizacao' => Carbon::now()->subDays(rand(5, 9)),
                'resultado' => 'Resultado dentro dos parâmetros normais.',
                'observacoes' => null,
                'status' => 'realizado',
            ]);
        }
    }
}
