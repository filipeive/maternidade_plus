<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'nome_completo' => 'Maria dos Santos Fernandes',
                'data_nascimento' => '1990-03-15',
                'documento_bi' => '120000123456A',
                'contacto' => '+258 82 123 4567',
                'email' => 'maria.fernandes@email.mz',
                'endereco' => 'Bairro da Polana, Rua 1º de Maio, Nº 45, Maputo',
                'tipo_sanguineo' => 'O+',
                'data_ultima_menstruacao' => '2024-06-01',
                'numero_gestacoes' => 2,
                'numero_partos' => 1
            ],
            [
                'nome_completo' => 'Ana Clara Sousa Machado',
                'data_nascimento' => '1985-07-22',
                'documento_bi' => '120000234567B',
                'contacto' => '+258 84 234 5678',
                'endereco' => 'Bairro da Matola, Av. Julius Nyerere, Nº 123',
                'tipo_sanguineo' => 'A+',
                'data_ultima_menstruacao' => '2024-05-15',
                'numero_gestacoes' => 1,
                'numero_partos' => 0
            ],
            [
                'nome_completo' => 'Carla Benedita Mendes',
                'data_nascimento' => '1992-11-10',
                'documento_bi' => '120000345678C',
                'contacto' => '+258 86 345 6789',
                'endereco' => 'Bairro Central, Rua da Resistência, Nº 67, Maputo',
                'tipo_sanguineo' => 'B+',
                'data_ultima_menstruacao' => '2024-04-20',
                'numero_gestacoes' => 3,
                'numero_partos' => 2
            ]
        ];

        foreach ($patients as $patientData) {
            $patient = Patient::create($patientData);
            
            // Calcular data provável do parto (280 dias após DUM)
            if ($patient->data_ultima_menstruacao) {
                $patient->data_provavel_parto = Carbon::parse($patient->data_ultima_menstruacao)
                    ->addDays(280);
                $patient->save();
            }
        }
    }
}
