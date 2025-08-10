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
                'email' => 'ana.machado@email.mz',
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
                'email' => 'carla.mendes@email.mz',
                'endereco' => 'Bairro Central, Rua da Resistência, Nº 67, Maputo',
                'tipo_sanguineo' => 'B+',
                'data_ultima_menstruacao' => '2024-04-20',
                'numero_gestacoes' => 3,
                'numero_partos' => 2
            ],
            [
                'nome_completo' => 'Sofia Almeida Nhampossa',
                'data_nascimento' => '1994-01-05',
                'documento_bi' => '120000456789D',
                'contacto' => '+258 85 456 7890',
                'email' => 'sofia.almeida@email.mz',
                'endereco' => 'Bairro Sommerschield, Rua 7, Nº 100, Maputo',
                'tipo_sanguineo' => 'AB+',
                'data_ultima_menstruacao' => '2024-07-10',
                'numero_gestacoes' => 1,
                'numero_partos' => 0
            ],
            [
                'nome_completo' => 'Juliana Francisco Chongo',
                'data_nascimento' => '1988-09-30',
                'documento_bi' => '120000567890E',
                'contacto' => '+258 83 567 8901',
                'email' => 'juliana.chongo@email.mz',
                'endereco' => 'Bairro Central, Av. 25 de Setembro, Nº 22',
                'tipo_sanguineo' => 'O-',
                'data_ultima_menstruacao' => '2024-05-25',
                'numero_gestacoes' => 2,
                'numero_partos' => 2
            ],
            [
                'nome_completo' => 'Isabel Gonçalves Nhaca',
                'data_nascimento' => '1991-12-17',
                'documento_bi' => '120000678901F',
                'contacto' => '+258 84 678 9012',
                'email' => 'isabel.nhaca@email.mz',
                'endereco' => 'Bairro da Polana, Rua Samora Machel, Nº 9',
                'tipo_sanguineo' => 'A-',
                'data_ultima_menstruacao' => '2024-06-05',
                'numero_gestacoes' => 1,
                'numero_partos' => 1
            ],
            [
                'nome_completo' => 'Lúcia Daniel Mucavele',
                'data_nascimento' => '1993-08-19',
                'documento_bi' => '120000789012G',
                'contacto' => '+258 82 789 0123',
                'email' => 'lucia.mucavele@email.mz',
                'endereco' => 'Bairro do Maxaquene, Rua das Flores, Nº 12',
                'tipo_sanguineo' => 'B-',
                'data_ultima_menstruacao' => '2024-07-01',
                'numero_gestacoes' => 2,
                'numero_partos' => 1
            ],
            [
                'nome_completo' => 'Patrícia Manuel Da Silva',
                'data_nascimento' => '1987-05-24',
                'documento_bi' => '120000890123H',
                'contacto' => '+258 83 890 1234',
                'email' => 'patricia.silva@email.mz',
                'endereco' => 'Bairro Central, Av. Julius Nyerere, Nº 56',
                'tipo_sanguineo' => 'AB-',
                'data_ultima_menstruacao' => '2024-06-15',
                'numero_gestacoes' => 3,
                'numero_partos' => 2
            ],
            [
                'nome_completo' => 'Fernanda João Macamo',
                'data_nascimento' => '1995-10-13',
                'documento_bi' => '120000901234I',
                'contacto' => '+258 84 901 2345',
                'email' => 'fernanda.macamo@email.mz',
                'endereco' => 'Bairro da Matola, Rua Eduardo Mondlane, Nº 11',
                'tipo_sanguineo' => 'O+',
                'data_ultima_menstruacao' => '2024-05-30',
                'numero_gestacoes' => 1,
                'numero_partos' => 0
            ],
            [
                'nome_completo' => 'Marta Pinto Nhantumbo',
                'data_nascimento' => '1990-06-28',
                'documento_bi' => '120000012345J',
                'contacto' => '+258 85 012 3456',
                'email' => 'marta.nhantumbo@email.mz',
                'endereco' => 'Bairro da Polana, Rua 24 de Julho, Nº 45',
                'tipo_sanguineo' => 'A+',
                'data_ultima_menstruacao' => '2024-06-20',
                'numero_gestacoes' => 2,
                'numero_partos' => 1
            ]
        ];

        foreach ($patients as $patientData) {
            $patient = Patient::create($patientData);

            if (!empty($patient->data_ultima_menstruacao)) {
                $patient->data_provavel_parto = Carbon::parse($patient->data_ultima_menstruacao)
                    ->addDays(280);
                $patient->save();
            }
        }
    }
}
