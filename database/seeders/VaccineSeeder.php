<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vaccine;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class VaccineSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'Médico'))->get();

        if ($patients->isEmpty() || $users->isEmpty()) {
            $this->command->info('Sem pacientes ou médicos para popular vacinas');
            return;
        }

        $tiposVacina = [
            'tetanica', 'hepatite_b', 'influenza', 'covid19', 'febre_amarela', 'iptp'
        ];

        foreach ($patients as $patient) {
            $user = $users->random();

            Vaccine::create([
                'patient_id' => $patient->id,
                'user_id' => $user->id,
                'tipo_vacina' => $tiposVacina[array_rand($tiposVacina)],
                'descricao' => 'Vacina aplicada conforme protocolo',
                'data_administracao' => Carbon::now()->subDays(rand(1, 180)),
                'proxima_dose' => Carbon::now()->addDays(rand(30, 365)),
                'dose_numero' => rand(1, 3),
                'lote' => 'L' . rand(1000, 9999),
                'fabricante' => 'Fabricante X',
                'data_vencimento' => Carbon::now()->addMonths(rand(6, 24)),
                'local_aplicacao' => ['braco_esquerdo', 'braco_direito', 'coxa_esquerda', 'coxa_direita', 'gluteo'][array_rand(['braco_esquerdo', 'braco_direito', 'coxa_esquerda', 'coxa_direita', 'gluteo'])],
                'observacoes' => 'Paciente sem reações adversas.',
                'reacao_adversa' => null,
                'status' => 'administrada',
            ]);
        }
    }
}
