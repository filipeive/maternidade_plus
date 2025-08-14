<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed das gestantes...');

        // Dados existentes - apenas adicionar se não existirem
        $existingPatients = [
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

        // NOVOS REGISTROS ADICIONAIS
        $newPatients = [
            // Gestantes em diferentes trimestres
            [
                'nome_completo' => 'Rosa Maria Tembe',
                'data_nascimento' => '1989-04-12',
                'documento_bi' => '120001123456K',
                'contacto' => '+258 84 111 2222',
                'email' => 'rosa.tembe@email.mz',
                'endereco' => 'Bairro Chamanculo, Rua A, Nº 78, Maputo',
                'tipo_sanguineo' => 'O+',
                'data_ultima_menstruacao' => now()->subWeeks(8)->format('Y-m-d'), // 8 semanas - 1º trimestre
                'numero_gestacoes' => 1,
                'numero_partos' => 0,
                'numero_abortos' => 0,
            ],
            [
                'nome_completo' => 'Esperança Sitoi Nkomo',
                'data_nascimento' => '1987-11-30',
                'documento_bi' => '120001234567L',
                'contacto' => '+258 85 222 3333',
                'email' => 'esperanca.nkomo@email.mz',
                'endereco' => 'Bairro Hulene, Av. de Angola, Nº 156',
                'tipo_sanguineo' => 'A+',
                'data_ultima_menstruacao' => now()->subWeeks(18)->format('Y-m-d'), // 18 semanas - 2º trimestre
                'numero_gestacoes' => 2,
                'numero_partos' => 1,
                'numero_abortos' => 0,
                'historico_medico' => 'Parto anterior sem complicações',
            ],
            [
                'nome_completo' => 'Graça Mondlane Cossa',
                'data_nascimento' => '1986-02-14',
                'documento_bi' => '120001345678M',
                'contacto' => '+258 86 333 4444',
                'email' => 'graca.cossa@email.mz',
                'endereco' => 'Bairro Malhangalene, Rua 1º de Junho, Nº 89',
                'tipo_sanguineo' => 'B+',
                'data_ultima_menstruacao' => now()->subWeeks(32)->format('Y-m-d'), // 32 semanas - 3º trimestre
                'numero_gestacoes' => 4,
                'numero_partos' => 3,
                'numero_abortos' => 0,
                'historico_medico' => 'Diabetes gestacional controlada',
            ],
            [
                'nome_completo' => 'Benedita Nhachungue Macuácua',
                'data_nascimento' => '1982-08-07',
                'documento_bi' => '120001456789N',
                'contacto' => '+258 87 444 5555',
                'email' => 'benedita.macuacua@email.mz',
                'endereco' => 'Bairro Alto Maé, Rua dos Trabalhadores, Nº 234',
                'tipo_sanguineo' => 'AB+',
                'data_ultima_menstruacao' => now()->subWeeks(36)->format('Y-m-d'), // 36 semanas - quase a termo
                'numero_gestacoes' => 5,
                'numero_partos' => 4,
                'numero_abortos' => 0,
                'alergias' => 'Penicilina',
                'historico_medico' => 'Hipertensão arterial em controle',
            ],
            // Casos de alto risco
            [
                'nome_completo' => 'Alzira José Matsinhe',
                'data_nascimento' => '1978-12-03', // > 35 anos
                'documento_bi' => '120001567890O',
                'contacto' => '+258 84 555 6666',
                'email' => 'alzira.matsinhe@email.mz',
                'endereco' => 'Bairro Bagamoyo, Av. Marginal, Nº 67',
                'tipo_sanguineo' => 'O-',
                'data_ultima_menstruacao' => now()->subWeeks(24)->format('Y-m-d'),
                'numero_gestacoes' => 6,
                'numero_partos' => 4,
                'numero_abortos' => 2, // Alto risco
                'alergias' => 'Sulfonamidas - reação grave',
                'historico_medico' => 'Diabetes tipo 2, hipertensão arterial, cardiopatia',
            ],
            [
                'nome_completo' => 'Catarina Muianga Chissano',
                'data_nascimento' => '2002-05-18', // < 18 anos
                'documento_bi' => '120001678901P',
                'contacto' => '+258 85 666 7777',
                'endereco' => 'Bairro Mafalala, Rua 3, Nº 45',
                'tipo_sanguineo' => 'A-',
                'data_ultima_menstruacao' => now()->subWeeks(14)->format('Y-m-d'),
                'numero_gestacoes' => 1,
                'numero_partos' => 0,
                'numero_abortos' => 0,
                'contacto_emergencia' => '+258 82 777 8888 (Mãe)',
            ],
            // Casos pós-parto
            [
                'nome_completo' => 'Helena Bila Massinga',
                'data_nascimento' => '1991-09-25',
                'documento_bi' => '120001789012Q',
                'contacto' => '+258 86 777 8888',
                'email' => 'helena.massinga@email.mz',
                'endereco' => 'Bairro Jardim, Rua das Acácias, Nº 12',
                'tipo_sanguineo' => 'B-',
                'data_ultima_menstruacao' => now()->subWeeks(45)->format('Y-m-d'), // Já deu à luz
                'numero_gestacoes' => 2,
                'numero_partos' => 2,
                'numero_abortos' => 0,
            ],
            // Sem DUM (casos especiais)
            [
                'nome_completo' => 'Virgínia Macamo Chongo',
                'data_nascimento' => '1993-01-10',
                'documento_bi' => '120001890123R',
                'contacto' => '+258 87 888 9999',
                'endereco' => 'Bairro Xipamanine, Av. 24 de Julho, Nº 333',
                'tipo_sanguineo' => 'AB-',
                'data_ultima_menstruacao' => null, // Sem DUM
                'numero_gestacoes' => 1,
                'numero_partos' => 0,
                'numero_abortos' => 0,
                'historico_medico' => 'Ciclos menstruais irregulares',
            ],
            // Casos variados de outras províncias
            [
                'nome_completo' => 'Lurdes Nhamirre Simbine',
                'data_nascimento' => '1988-07-16',
                'documento_bi' => '110001234567S', // Inhambane
                'contacto' => '+258 84 999 0000',
                'email' => 'lurdes.simbine@email.mz',
                'endereco' => 'Cidade de Inhambane, Bairro Liberdade',
                'tipo_sanguineo' => 'O+',
                'data_ultima_menstruacao' => now()->subWeeks(26)->format('Y-m-d'),
                'numero_gestacoes' => 3,
                'numero_partos' => 2,
                'numero_abortos' => 0,
            ],
            [
                'nome_completo' => 'Palmira Samo Nhanombe',
                'data_nascimento' => '1990-10-22',
                'documento_bi' => '060001234567T', // Sofala
                'contacto' => '+258 85 000 1111',
                'endereco' => 'Cidade da Beira, Bairro Manga',
                'tipo_sanguineo' => 'A+',
                'data_ultima_menstruacao' => now()->subWeeks(12)->format('Y-m-d'),
                'numero_gestacoes' => 1,
                'numero_partos' => 0,
                'numero_abortos' => 0,
            ],
        ];

        $added = 0;
        $skipped = 0;

        // Processar registros existentes primeiro
        foreach ($existingPatients as $patientData) {
            if (!Patient::where('documento_bi', $patientData['documento_bi'])->exists()) {
                $this->createPatient($patientData);
                $added++;
            } else {
                $this->command->warn("⚠️  Paciente {$patientData['nome_completo']} já existe (BI: {$patientData['documento_bi']})");
                $skipped++;
            }
        }

        // Processar novos registros
        foreach ($newPatients as $patientData) {
            if (!Patient::where('documento_bi', $patientData['documento_bi'])->exists()) {
                $this->createPatient($patientData);
                $added++;
            } else {
                $this->command->warn("⚠️  Paciente {$patientData['nome_completo']} já existe (BI: {$patientData['documento_bi']})");
                $skipped++;
            }
        }

        $this->command->info("✅ Seed concluído: {$added} pacientes adicionadas, {$skipped} ignoradas (já existem)");
        
        // Estatísticas finais
        $total = Patient::count();
        $ativas = Patient::where('ativo', true)->count();
        $gestantes = Patient::where('ativo', true)->whereNotNull('data_ultima_menstruacao')->count();
        
        $this->command->line("\n📊 ESTATÍSTICAS:");
        $this->command->line("Total de pacientes: {$total}");
        $this->command->line("Pacientes ativas: {$ativas}");
        $this->command->line("Com DUM registrada: {$gestantes}");
    }

    private function createPatient($patientData)
    {
        $patient = Patient::create($patientData);

        // Calcular DPP se tiver DUM
        if (!empty($patient->data_ultima_menstruacao)) {
            $patient->data_provavel_parto = Carbon::parse($patient->data_ultima_menstruacao)
                ->addDays(280);
            $patient->save();
        }

        return $patient;
    }
}