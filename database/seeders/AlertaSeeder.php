<?php

namespace Database\Seeders;

use App\Models\Alerta;
use App\Models\AlertaAcao;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\User;
use App\Models\Vaccine;
use App\Services\AlertaPrecoceService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AlertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚨 Iniciando seed do Módulo de Alerta Precoce...');

        $doctor = User::whereHas('roles', fn($q) => $q->where('name', 'Médico'))->first()
            ?? User::whereHas('roles', fn($q) => $q->where('name', 'Administrador'))->first()
            ?? User::first();

        $patients = Patient::all();
        if ($patients->isEmpty()) {
            $this->command->warn('Nenhum paciente encontrado para criar alertas.');
            return;
        }

        // Executar o motor de regras automático sobre os dados já existentes
        try {
            $engineService = app(AlertaPrecoceService::class);
            $engineService->avaliarTodas();
        } catch (\Throwable $e) {
            $this->command->warn('Avaliação automática do motor falhou ou incompleta: ' . $e->getMessage());
        }

        // Casos clínicos específicos para garantir cobertura total das 9 regras e distribuição de severidade
        $casosClinicos = [
            // Regra 1: Pressão Arterial Grave (Alto)
            [
                'tipo' => 'pressao_arterial_grave',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Crise hipertensiva grave detectada: PA 165/110 mmHg. Risco iminente de pré-eclâmpsia/eclâmpsia.',
                'dados' => ['pressao_arterial' => '165/110', 'sistolica' => 165, 'diastolica' => 110],
            ],
            [
                'tipo' => 'pressao_arterial_alta',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Hipertensão gestacional moderada: PA 145/95 mmHg. Requer monitoria ambulatorial rigorosa.',
                'dados' => ['pressao_arterial' => '145/95', 'sistolica' => 145, 'diastolica' => 95],
            ],

            // Regra 2: Batimento Cardíaco Fetal Anormal (Alto)
            [
                'tipo' => 'bcf_anormal',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Bradicardia fetal severa (BCF 98 bpm). Indicativo urgente de sofrimento fetal intrauterino.',
                'dados' => ['batimentos_fetais' => 98, 'referencia' => '110-160 bpm'],
            ],
            [
                'tipo' => 'bcf_anormal',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_EM_SEGUIMENTO,
                'mensagem' => 'Taquicardia fetal persistente (BCF 178 bpm). Avaliar febre materna ou infecção amniótica.',
                'dados' => ['batimentos_fetais' => 178, 'referencia' => '110-160 bpm'],
            ],

            // Regra 3: Gestante Faltosa / Consulta Atrasada (Médio)
            [
                'tipo' => 'gestante_sem_consulta',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Gestante sem comparecer a consultas de pré-natal há mais de 45 dias. Risco de abandono do seguimento.',
                'dados' => ['dias_sem_consulta' => 45],
            ],
            [
                'tipo' => 'consulta_agendada_perdida',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Consulta agendada perdida há 14 dias sem remarcação. Contactar activista comunitário.',
                'dados' => ['dias_atraso' => 14],
            ],

            // Regra 4: Alto Risco sem Seguimento Recente (Alto)
            [
                'tipo' => 'alto_risco_sem_seguimento',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Gestante classificada com gravidez de alto risco sem consulta médica nos últimos 25 dias.',
                'dados' => ['fatores_risco' => 'Idade avançada, Cesariana anterior, Diabetes gestacional'],
            ],

            // Regra 5: Vacinas em Atraso (Médio / Baixo)
            [
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Dose de Toxoide Tetânico (TT2) e IPTp-SP 2 em atraso no calendário de imunização nacional.',
                'dados' => ['vacinas' => ['TT2', 'IPTp2']],
            ],
            [
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_BAIXO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Primeira dose de Toxoide Tetânico pendente de administração na primeira consulta.',
                'dados' => ['vacinas' => ['TT1']],
            ],

            // Regra 6: Exames Laboratoriais Críticos (Alto / Médio)
            [
                'tipo' => 'exame_critico_hiv',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Teste rápido de HIV Positivo (Reactivo). Iniciar protocolo TARV / PTV imediatamente.',
                'dados' => ['exame' => 'HIV', 'resultado' => 'Positivo'],
            ],
            [
                'tipo' => 'exame_critico_sifilis',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Teste VDRL/Sífilis Positivo. Prescrever Penicilina Benzatínica e convocar parceiro sexual.',
                'dados' => ['exame' => 'VDRL', 'resultado' => 'Reactivo 1:8'],
            ],
            [
                'tipo' => 'exame_critico_anemia',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Hemoglobina crítica (Hb 7.8 g/dL). Anemia moderada a severa, reforçar suplementação com Ferro e Ácido Fólico.',
                'dados' => ['exame' => 'Hemoglobina', 'resultado' => '7.8 g/dL'],
            ],

            // Regra 7: Ganho de Peso Anormal (Médio / Baixo)
            [
                'tipo' => 'ganho_peso_anormal',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Perda de peso involuntária de 3.2 kg entre o 2º e 3º trimestre gestacional.',
                'dados' => ['variacao_kg' => -3.2, 'semanas_intervalo' => 4],
            ],
            [
                'tipo' => 'ganho_peso_anormal',
                'nivel' => Alerta::NIVEL_BAIXO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Ganho ponderal excessivo (>1.5 kg/semana). Rastrear edema e diabetes gestacional.',
                'dados' => ['variacao_kg' => 4.1, 'semanas_intervalo' => 2],
            ],

            // Regra 8: Gravidez Pós-Termo (Alto / Médio)
            [
                'tipo' => 'pos_termo',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Gestação atingiu 42 semanas e 2 dias (Gravidez Pós-Termo). Encaminhar para indução de parto imediata.',
                'dados' => ['semanas_gestacao' => 42.2, 'dpp' => Carbon::now()->subDays(16)->format('Y-m-d')],
            ],
            [
                'tipo' => 'pos_termo',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_EM_SEGUIMENTO,
                'mensagem' => 'Gestação em 41 semanas completas (Termo tardio). Avaliar índice de líquido amniótico.',
                'dados' => ['semanas_gestacao' => 41.0],
            ],

            // Regra 9: Sangramento Vaginal Reportado (Alto)
            [
                'tipo' => 'sangramento_reportado',
                'nivel' => Alerta::NIVEL_ALTO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Queixa de sangramento vaginal vivo no 3º trimestre. Suspeita de placenta prévia ou descolamento prematuro.',
                'dados' => ['trimestre' => '3º Trimestre', 'intensidade' => 'Moderada a intensa'],
            ],

            // Alertas Adicionais Médio e Baixo para atingir >= 5 Alto, >= 8 Médio, >= 3 Baixo
            [
                'tipo' => 'pressao_arterial_alta',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Pressão diastólica limítrofe (138/88 mmHg) em gestante primípara.',
                'dados' => ['pressao_arterial' => '138/88'],
            ],
            [
                'tipo' => 'ganho_peso_anormal',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Baixo ganho de peso acumulado (< 4 kg) até à 28ª semana.',
                'dados' => ['peso_total_ganho' => 3.8],
            ],
            [
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_MEDIO,
                'status' => Alerta::STATUS_EM_SEGUIMENTO,
                'mensagem' => 'Dose de reforço IPTp-SP 3 pendente em zona de alta transmissão de malária.',
                'dados' => ['vacina' => 'IPTp-SP 3'],
            ],
            [
                'tipo' => 'gestante_sem_consulta',
                'nivel' => Alerta::NIVEL_BAIXO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Lembrete preventivo: Gestante sem agendamento prévio nos últimos 20 dias.',
                'dados' => ['dias' => 20],
            ],
            [
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_BAIXO,
                'status' => Alerta::STATUS_ATIVO,
                'mensagem' => 'Lembrete de vacina contra tétano programada para próxima consulta.',
                'dados' => ['vacina' => 'TT3'],
            ],
        ];

        // Inserir os alertas garantindo vínculo aos pacientes
        foreach ($casosClinicos as $index => $caso) {
            $patient = $patients[$index % $patients->count()];

            // Verificar se já existe um alerta ativo similar para evitar duplicatas excessivas
            $exists = Alerta::where('patient_id', $patient->id)
                ->where('tipo', $caso['tipo'])
                ->where('status', $caso['status'])
                ->exists();

            if (!$exists) {
                Alerta::create([
                    'patient_id' => $patient->id,
                    'consultation_id' => null,
                    'tipo' => $caso['tipo'],
                    'nivel' => $caso['nivel'],
                    'status' => $caso['status'],
                    'mensagem' => $caso['mensagem'],
                    'dados' => $caso['dados'],
                    'created_at' => Carbon::now()->subDays(rand(1, 20))->subHours(rand(1, 23)),
                ]);
            }
        }

        // Casos Pré-Resolvidos com trilha de auditoria completa (AlertaAcao)
        $casosResolvidos = [
            [
                'tipo' => 'pressao_arterial_grave',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => 'Pico pressórico de 170/110 mmHg resolvido com conduta anti-hipertensiva.',
                'dados' => ['pressao_inicial' => '170/110', 'pressao_final' => '120/80'],
                'dias_atras' => 12,
                'nota_seguimento' => 'Gestante admitida na enfermaria de alto risco e iniciada hidralazina IV.',
                'nota_resolucao' => 'Pressão estabilizada em 120/80 mmHg após 48h de vigilância. Alta hospitalar com Metildopa oral.',
            ],
            [
                'tipo' => 'bcf_anormal',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => 'Cardiotocografia inicial alterada normalizada após hidratação venosa e oxigenoterapia.',
                'dados' => ['bcf_inicial' => 102, 'bcf_final' => 140],
                'dias_atras' => 8,
                'nota_seguimento' => 'Realizada hidratação vigorosa e decúbito lateral esquerdo.',
                'nota_resolucao' => 'BCF normalizado em 140 bpm, perfil biofísico fetal 8/8. Gestante liberada.',
            ],
            [
                'tipo' => 'exame_critico_sifilis',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => 'VDRL reactivo tratado com 3 doses de Penicilina Benzatínica.',
                'dados' => ['vdrl' => '1:16', 'tratamento' => '3x 2.4M UI'],
                'dias_atras' => 25,
                'nota_seguimento' => 'Iniciado esquema de 3 doses semanais de Penicilina Benzatínica 2.4 MU.',
                'nota_resolucao' => 'Tratamento completo concluído com sucesso. Parceiro tratado.',
            ],
            [
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_MEDIO,
                'mensagem' => 'Vacinação e quimioprofilaxia de malária actualizadas em visita à US.',
                'dados' => ['administradas' => ['TT2', 'IPTp2']],
                'dias_atras' => 5,
                'nota_seguimento' => 'Gestante convocada via chamada telefónica.',
                'nota_resolucao' => 'Compareceu à US e recebeu doses pendentes de TT2 e SP2 com registo no cartão da gestante.',
            ],
        ];

        foreach ($casosResolvidos as $i => $casoRes) {
            $patient = $patients[($i + 5) % $patients->count()];
            $createdAt = Carbon::now()->subDays($casoRes['dias_atras']);
            $emSeguimentoAt = $createdAt->copy()->addHours(6);
            $resolvidoAt = $createdAt->copy()->addDays(2);

            $alerta = Alerta::create([
                'patient_id' => $patient->id,
                'consultation_id' => null,
                'tipo' => $casoRes['tipo'],
                'nivel' => $casoRes['nivel'],
                'status' => Alerta::STATUS_RESOLVIDO,
                'mensagem' => $casoRes['mensagem'],
                'dados' => $casoRes['dados'],
                'resolvido_por' => $doctor?->id,
                'nota_resolucao' => $casoRes['nota_resolucao'],
                'resolvido_em' => $resolvidoAt,
                'created_at' => $createdAt,
            ]);

            // Trilha de Auditoria 1: Ativo -> Em Seguimento
            AlertaAcao::create([
                'alerta_id' => $alerta->id,
                'user_id' => $doctor?->id,
                'status_anterior' => Alerta::STATUS_ATIVO,
                'status_novo' => Alerta::STATUS_EM_SEGUIMENTO,
                'de_status' => Alerta::STATUS_ATIVO,
                'para_status' => Alerta::STATUS_EM_SEGUIMENTO,
                'nota' => $casoRes['nota_seguimento'],
                'created_at' => $emSeguimentoAt,
            ]);

            // Trilha de Auditoria 2: Em Seguimento -> Resolvido
            AlertaAcao::create([
                'alerta_id' => $alerta->id,
                'user_id' => $doctor?->id,
                'status_anterior' => Alerta::STATUS_EM_SEGUIMENTO,
                'status_novo' => Alerta::STATUS_RESOLVIDO,
                'de_status' => Alerta::STATUS_EM_SEGUIMENTO,
                'para_status' => Alerta::STATUS_RESOLVIDO,
                'nota' => $casoRes['nota_resolucao'],
                'created_at' => $resolvidoAt,
            ]);
        }

        $total = Alerta::count();
        $altos = Alerta::where('nivel', Alerta::NIVEL_ALTO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count();
        $medios = Alerta::where('nivel', Alerta::NIVEL_MEDIO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count();
        $baixos = Alerta::where('nivel', Alerta::NIVEL_BAIXO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count();
        $resolvidos = Alerta::where('status', Alerta::STATUS_RESOLVIDO)->count();
        $audits = AlertaAcao::count();

        $this->command->info("✅ Alertas semeados: {$total} total (Ativos: {$altos} Altos, {$medios} Médios, {$baixos} Baixos | Resolvidos: {$resolvidos} | Ações Auditoria: {$audits})");
    }
}
