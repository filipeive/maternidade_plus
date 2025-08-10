<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class ConsultationSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'Médico'))->get();

        if ($patients->isEmpty() || $users->isEmpty()) {
            $this->command->info('Sem pacientes ou médicos para popular consultas');
            return;
        }

        foreach ($patients as $patient) {
            $user = $users->random();

            Consultation::create([
                'patient_id' => $patient->id,
                'user_id' => $user->id,
                'data_consulta' => Carbon::now()->subDays(rand(1, 60)),
                'tipo_consulta' => ['1_trimestre', '2_trimestre', '3_trimestre', 'pos_parto'][array_rand(['1_trimestre', '2_trimestre', '3_trimestre', 'pos_parto'])],
                'semanas_gestacao' => rand(4, 40),
                'peso' => rand(50, 80) + rand(0, 99)/100,
                'pressao_arterial' => rand(110, 140) . '/' . rand(70, 90),
                'batimentos_fetais' => rand(120, 160),
                'altura_uterina' => rand(20, 35) + 0.5,
                'observacoes' => 'Consulta sem intercorrências.',
                'orientacoes' => 'Manter dieta e repouso.',
                'proxima_consulta' => Carbon::now()->addDays(rand(7, 30)),
                'status' => 'realizada',
            ]);
        }
    }
}
