<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeVisit;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class HomeVisitSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'Enfermeiro'))->get();

        if ($patients->isEmpty() || $users->isEmpty()) {
            $this->command->info('Sem pacientes ou enfermeiros para popular visitas domiciliares');
            return;
        }

        foreach ($patients as $patient) {
            $user = $users->random();

            HomeVisit::create([
                'patient_id' => $patient->id,
                'user_id' => $user->id,
                'data_visita' => Carbon::now()->subDays(rand(1, 30)),
                'motivo_visita' => 'Visita de rotina pós-parto',
                'tipo_visita' => 'pos_parto',
                'endereco_visita' => $patient->endereco,
                'status' => 'realizada',
                'observacoes_ambiente' => 'Ambiente limpo e organizado',
                'condicoes_higiene' => 'bom',
                'apoio_familiar' => 'adequado',
                'estado_nutricional' => 'Paciente com boa alimentação',
                'sinais_vitais' => json_encode(['PA' => '120/80', 'peso' => '68kg', 'temperatura' => '36.8']),
                'queixas_principais' => 'Nenhuma queixa relevante',
                'orientacoes_dadas' => 'Orientações sobre amamentação',
                'materiais_entregues' => json_encode(['folheto amamentação']),
                'proxima_visita' => Carbon::now()->addDays(15),
                'acompanhante_presente' => true,
                'necessita_referencia' => false,
                'observacoes_gerais' => 'Paciente motivada e em bom estado',
                'coordenadas_gps' => json_encode(['lat' => '-25.9652', 'lng' => '32.5892']),
            ]);
        }
    }
}
