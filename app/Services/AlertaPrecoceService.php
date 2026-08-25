<?php

namespace App\Services;

use App\Jobs\SendAlertSmsJob;
use App\Models\Alerta;
use App\Models\Consultation;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AlertaPrecoceService
{
    public const PA_SISTOLICA_ALERTA = 140;
    public const PA_DIASTOLICA_ALERTA = 90;
    public const PA_SISTOLICA_GRAVE = 160;
    public const PA_DIASTOLICA_GRAVE = 110;

    public const BCF_MIN = 110;
    public const BCF_MAX = 160;

    public const DIAS_SEM_CONSULTA_ALERTA = 30;
    public const DIAS_ATRASO_CONSULTA_AGENDADA = 3;

    /**
     * Avalia todas as gestantes ativas no sistema e gera alertas precoces.
     *
     * @return array{avaliadas: int, novos_alertas: int}
     */
    public function avaliarTodas(): array
    {
        $gestantes = Patient::ativo()
            ->where('status_atual', Patient::STATUS_GESTANTE)
            ->with([
                'consultations' => fn ($q) => $q->orderByDesc('data_consulta'),
                'vaccines',
                'exams',
                'births',
            ])
            ->get();

        $novos = 0;
        foreach ($gestantes as $gestante) {
            $criados = $this->avaliarPaciente($gestante);
            $novos += count($criados);
        }

        return [
            'avaliadas' => $gestantes->count(),
            'novos_alertas' => $novos,
        ];
    }

    /**
     * Avalia uma paciente gestante específica contra todas as 9 regras clínicas.
     *
     * @param Patient $gestante
     * @return Alerta[]
     */
    public function avaliarPaciente(Patient $gestante): array
    {
        if (!$gestante->ativo || $gestante->status_atual !== Patient::STATUS_GESTANTE) {
            return [];
        }

        $candidatos = array_merge(
            $this->avaliarPressaoArterial($gestante),
            $this->avaliarBatimentosFetais($gestante),
            $this->avaliarAssiduidade($gestante),
            $this->avaliarAltoRiscoSemSeguimento($gestante),
            $this->avaliarVacinasEmAtraso($gestante),
            $this->avaliarExamesCriticos($gestante),
            $this->avaliarGanhoPeso($gestante),
            $this->avaliarPosTermo($gestante),
            $this->avaliarSangramento($gestante)
        );

        $criados = [];
        foreach ($candidatos as $candidato) {
            if ($this->jaTemAlertaAtivo($gestante, $candidato['tipo'])) {
                continue;
            }

            $alerta = Alerta::create(array_merge($candidato, [
                'patient_id' => $gestante->id,
                'status' => Alerta::STATUS_ATIVO,
            ]));

            // Disparar notificação SMS assíncrona se for alerta de nível alto
            if ($alerta->nivel === Alerta::NIVEL_ALTO) {
                SendAlertSmsJob::dispatch($alerta);
            }

            $criados[] = $alerta;
        }

        return $criados;
    }

    /**
     * Verifica se já existe um alerta ativo ou em seguimento do mesmo tipo para a paciente.
     */
    public function jaTemAlertaAtivo(Patient $gestante, string $tipo): bool
    {
        return Alerta::where('patient_id', $gestante->id)
            ->where('tipo', $tipo)
            ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
            ->exists();
    }

    /**
     * Regra 1: Pressão Arterial Elevada / Grave.
     */
    public function avaliarPressaoArterial(Patient $gestante): array
    {
        $ultimaConsulta = $this->getUltimaConsultaComDados($gestante);
        if (!$ultimaConsulta || empty($ultimaConsulta->pressao_arterial)) {
            return [];
        }

        $pa = $this->parsePressaoArterial($ultimaConsulta->pressao_arterial);
        if (!$pa) {
            return [];
        }

        $sistolica = $pa['sistolica'];
        $diastolica = $pa['diastolica'];

        if ($sistolica >= self::PA_SISTOLICA_GRAVE || $diastolica >= self::PA_DIASTOLICA_GRAVE) {
            return [[
                'consultation_id' => $ultimaConsulta->id,
                'tipo' => 'pressao_arterial_grave',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => "Hipertensão arterial grave detectada na última consulta ({$sistolica}/{$diastolica} mmHg). Risco iminente de pré-eclâmpsia grave/eclâmpsia.",
                'dados' => [
                    'sistolica' => $sistolica,
                    'diastolica' => $diastolica,
                    'pressao_arterial' => $ultimaConsulta->pressao_arterial,
                    'consultation_id' => $ultimaConsulta->id,
                ],
            ]];
        }

        if ($sistolica >= self::PA_SISTOLICA_ALERTA || $diastolica >= self::PA_DIASTOLICA_ALERTA) {
            return [[
                'consultation_id' => $ultimaConsulta->id,
                'tipo' => 'pressao_arterial_alta',
                'nivel' => Alerta::NIVEL_MEDIO,
                'mensagem' => "Pressão arterial elevada detectada na última consulta ({$sistolica}/{$diastolica} mmHg). Necessário monitoramento pré-natal rigoroso.",
                'dados' => [
                    'sistolica' => $sistolica,
                    'diastolica' => $diastolica,
                    'pressao_arterial' => $ultimaConsulta->pressao_arterial,
                    'consultation_id' => $ultimaConsulta->id,
                ],
            ]];
        }

        return [];
    }

    /**
     * Parser robusto para pressão arterial em diversos formatos: "120/80", "140 x 90", "160-110", "140/90 mmHg".
     *
     * @return array{sistolica: int, diastolica: int}|null
     */
    public function parsePressaoArterial(?string $pa): ?array
    {
        if (!$pa) {
            return null;
        }

        // Limpeza de unidades e prefixos comuns
        $limpo = preg_replace('/(?i)mmhg|pa[:\s]*/', '', trim($pa));

        if (preg_match('/(\d{2,3})\s*(?:[\/xX\-:]|\s+)\s*(\d{2,3})/', $limpo, $matches)) {
            $sistolica = (int)$matches[1];
            $diastolica = (int)$matches[2];

            // Intervalo plausível fisiológico
            if ($sistolica >= 50 && $sistolica <= 300 && $diastolica >= 30 && $diastolica <= 200) {
                return [
                    'sistolica' => $sistolica,
                    'diastolica' => $diastolica,
                ];
            }
        }

        return null;
    }

    /**
     * Regra 2: Batimentos Cardíacos Fetais Anormais (< 110 ou > 160 bpm).
     */
    public function avaliarBatimentosFetais(Patient $gestante): array
    {
        $ultimaConsulta = $gestante->consultations
            ? $gestante->consultations->whereNotNull('batimentos_fetais')->first()
            : $gestante->consultations()->whereNotNull('batimentos_fetais')->orderByDesc('data_consulta')->first();

        if (!$ultimaConsulta || empty($ultimaConsulta->batimentos_fetais)) {
            return [];
        }

        $bcf = (int)$ultimaConsulta->batimentos_fetais;
        if ($bcf < self::BCF_MIN || $bcf > self::BCF_MAX) {
            return [[
                'consultation_id' => $ultimaConsulta->id,
                'tipo' => 'bcf_anormal',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => "Batimentos cardíacos fetais anormais detectados ({$bcf} bpm). Faixa esperada de normalidade: 110 a 160 bpm.",
                'dados' => [
                    'batimentos_fetais' => $bcf,
                    'consultation_id' => $ultimaConsulta->id,
                ],
            ]];
        }

        return [];
    }

    /**
     * Regra 3: Gestante Faltosa (> 30 dias sem consulta ou falta à consulta agendada há >= 3 dias).
     */
    public function avaliarAssiduidade(Patient $gestante): array
    {
        $hoje = Carbon::now();
        $ultimaConsulta = $this->getUltimaConsultaComDados($gestante);

        // 1. Verificar consulta agendada perdida há >= 3 dias
        $consultasAgendadas = $gestante->consultations
            ? $gestante->consultations->where('status', 'agendada')
            : $gestante->consultations()->where('status', 'agendada')->get();

        foreach ($consultasAgendadas as $agendada) {
            $dataAgendada = $agendada->proxima_consulta ?? $agendada->data_consulta;
            if ($dataAgendada && Carbon::parse($dataAgendada)->lte($hoje->copy()->subDays(self::DIAS_ATRASO_CONSULTA_AGENDADA))) {
                $diasAtraso = Carbon::parse($dataAgendada)->diffInDays($hoje);
                return [[
                    'consultation_id' => $agendada->id,
                    'tipo' => 'gestante_faltosa',
                    'nivel' => Alerta::NIVEL_MEDIO,
                    'mensagem' => "Gestante faltosa: consulta agendada não realizada há {$diasAtraso} dias (data prevista: " . Carbon::parse($dataAgendada)->format('d/m/Y') . ").",
                    'dados' => [
                        'motivo' => 'consulta_agendada_perdida',
                        'data_agendada' => Carbon::parse($dataAgendada)->format('Y-m-d'),
                        'dias_atraso' => $diasAtraso,
                    ],
                ]];
            }
        }

        // 2. Verificar se está há mais de 30 dias sem nenhuma consulta
        if (!$ultimaConsulta) {
            $diasDesdeRegisto = Carbon::parse($gestante->created_at)->diffInDays($hoje);
            if ($diasDesdeRegisto > self::DIAS_SEM_CONSULTA_ALERTA) {
                return [[
                    'consultation_id' => null,
                    'tipo' => 'gestante_faltosa',
                    'nivel' => Alerta::NIVEL_MEDIO,
                    'mensagem' => "Gestante cadastrada há {$diasDesdeRegisto} dias sem nenhuma consulta pré-natal realizada.",
                    'dados' => [
                        'motivo' => 'sem_consultas_desde_registo',
                        'dias_sem_consulta' => $diasDesdeRegisto,
                    ],
                ]];
            }
        } else {
            $diasSemConsulta = Carbon::parse($ultimaConsulta->data_consulta)->diffInDays($hoje);
            if ($diasSemConsulta > self::DIAS_SEM_CONSULTA_ALERTA) {
                // Se a gestante NÃO for de alto risco (alto risco gera o alerta alto na Regra 4)
                if ($gestante->risco_gestacional !== 'Alto') {
                    return [[
                        'consultation_id' => $ultimaConsulta->id,
                        'tipo' => 'gestante_faltosa',
                        'nivel' => Alerta::NIVEL_MEDIO,
                        'mensagem' => "Gestante sem comparecimento a consultas pré-natais há {$diasSemConsulta} dias (última consulta em " . Carbon::parse($ultimaConsulta->data_consulta)->format('d/m/Y') . ").",
                        'dados' => [
                            'motivo' => 'intervalo_consultas_excedido',
                            'dias_sem_consulta' => $diasSemConsulta,
                            'ultima_consulta' => Carbon::parse($ultimaConsulta->data_consulta)->format('Y-m-d'),
                        ],
                    ]];
                }
            }
        }

        return [];
    }

    /**
     * Regra 4: Alto Risco Obstétrico Sem Seguimento (> 30 dias sem consulta).
     */
    public function avaliarAltoRiscoSemSeguimento(Patient $gestante): array
    {
        if ($gestante->risco_gestacional !== 'Alto') {
            return [];
        }

        $hoje = Carbon::now();
        $ultimaConsulta = $this->getUltimaConsultaComDados($gestante);

        if (!$ultimaConsulta) {
            $diasSemConsulta = Carbon::parse($gestante->created_at)->diffInDays($hoje);
            if ($diasSemConsulta > self::DIAS_SEM_CONSULTA_ALERTA) {
                return [[
                    'consultation_id' => null,
                    'tipo' => 'alto_risco_sem_seguimento',
                    'nivel' => Alerta::NIVEL_ALTO,
                    'mensagem' => "Gestante de Alto Risco Obstétrico sem acompanhamento clínico há {$diasSemConsulta} dias desde o cadastro.",
                    'dados' => [
                        'risco_gestacional' => 'Alto',
                        'dias_sem_consulta' => $diasSemConsulta,
                    ],
                ]];
            }
        } else {
            $diasSemConsulta = Carbon::parse($ultimaConsulta->data_consulta)->diffInDays($hoje);
            if ($diasSemConsulta > self::DIAS_SEM_CONSULTA_ALERTA) {
                return [[
                    'consultation_id' => $ultimaConsulta->id,
                    'tipo' => 'alto_risco_sem_seguimento',
                    'nivel' => Alerta::NIVEL_ALTO,
                    'mensagem' => "Gestante de Alto Risco Obstétrico sem consulta de seguimento há {$diasSemConsulta} dias (última consulta: " . Carbon::parse($ultimaConsulta->data_consulta)->format('d/m/Y') . ").",
                    'dados' => [
                        'risco_gestacional' => 'Alto',
                        'dias_sem_consulta' => $diasSemConsulta,
                        'ultima_consulta' => Carbon::parse($ultimaConsulta->data_consulta)->format('Y-m-d'),
                    ],
                ]];
            }
        }

        return [];
    }

    /**
     * Regra 5: Vacinas em Atraso (doses pendentes com proxima_dose vencida).
     */
    public function avaliarVacinasEmAtraso(Patient $gestante): array
    {
        $vacinas = $gestante->vaccines
            ? $gestante->vaccines->filter(fn ($v) => $v->status === 'pendente' && $v->proxima_dose && Carbon::parse($v->proxima_dose)->lt(now()))
            : $gestante->vaccines()->where('status', 'pendente')->where('proxima_dose', '<', now())->get();

        if ($vacinas->count() > 0) {
            $nomesVacinas = $vacinas->map(fn ($v) => $v->vacina_formatada ?? $v->tipo_vacina)->join(', ');

            return [[
                'consultation_id' => null,
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_MEDIO,
                'mensagem' => "Gestante com {$vacinas->count()} dose(s) de vacinas em atraso: {$nomesVacinas}.",
                'dados' => [
                    'total_vacinas' => $vacinas->count(),
                    'vacinas' => $vacinas->map(fn ($v) => [
                        'id' => $v->id,
                        'tipo_vacina' => $v->tipo_vacina,
                        'proxima_dose' => $v->proxima_dose ? Carbon::parse($v->proxima_dose)->format('Y-m-d') : null,
                    ])->values()->toArray(),
                ],
            ]];
        }

        return [];
    }

    /**
     * Regra 6: Exames Laboratoriais Críticos (HIV+, Sífilis+, Anemia grave Hb < 7.0 g/dL).
     */
    public function avaliarExamesCriticos(Patient $gestante): array
    {
        $exames = $gestante->exams
            ? $gestante->exams
            : $gestante->exams()->get();

        $alertas = [];

        foreach ($exames as $exame) {
            if (empty($exame->resultado)) {
                continue;
            }

            $res = mb_strtolower(trim($exame->resultado), 'UTF-8');
            $tipo = $exame->tipo_exame;

            // 1. HIV Positivo / Reagente
            $isHiv = ($tipo === 'teste_hiv' || str_contains($res, 'hiv'));
            if ($isHiv) {
                $isNegativo = preg_match('/(?:n[aã]o\s+reagente|negativo|indetect[aá]vel)/i', $res);
                $isPositivo = preg_match('/(?:reagente|positivo|hiv\+|detect[aá]vel)/i', $res);

                if ($isPositivo && !$isNegativo) {
                    $alertas[] = [
                        'consultation_id' => $exame->consultation_id,
                        'tipo' => 'exames_criticos',
                        'nivel' => Alerta::NIVEL_ALTO,
                        'mensagem' => "Resultado crítico de exame laboratorial: Teste de HIV com resultado reagente/positivo ({$exame->resultado}). Iniciar protocolo de prevenção de transmissão vertical imediatamente.",
                        'dados' => [
                            'exam_id' => $exame->id,
                            'tipo_exame' => $exame->tipo_exame,
                            'resultado' => $exame->resultado,
                            'motivo' => 'hiv_positivo',
                        ],
                    ];
                    continue;
                }
            }

            // 2. Sífilis Positiva / Reagente
            $isSifilis = ($tipo === 'teste_sifilis' || str_contains($res, 'vdrl') || str_contains($res, 'sifilis') || str_contains($res, 'sífilis'));
            if ($isSifilis) {
                $isNegativo = preg_match('/(?:n[aã]o\s+reagente|negativo)/i', $res);
                $isPositivo = preg_match('/(?:reagente|positivo|vdrl\s+reagente)/i', $res);

                if ($isPositivo && !$isNegativo) {
                    $alertas[] = [
                        'consultation_id' => $exame->consultation_id,
                        'tipo' => 'exames_criticos',
                        'nivel' => Alerta::NIVEL_ALTO,
                        'mensagem' => "Resultado crítico de exame laboratorial: Teste de Sífilis (VDRL) reagente/positivo ({$exame->resultado}). Iniciar tratamento com penicilina benzatínica.",
                        'dados' => [
                            'exam_id' => $exame->id,
                            'tipo_exame' => $exame->tipo_exame,
                            'resultado' => $exame->resultado,
                            'motivo' => 'sifilis_positiva',
                        ],
                    ];
                    continue;
                }
            }

            // 3. Anemia Grave (Hb < 7.0 g/dL ou menção textual)
            $isAnemiaGrave = false;
            $motivoAnemia = '';

            if (preg_match('/(?:anemia\s+grave|hb\s*<\s*7|hemoglobina\s*<\s*7)/i', $res)) {
                $isAnemiaGrave = true;
                $motivoAnemia = 'Anemia grave relatada';
            } elseif ($tipo === 'hemograma_completo' || str_contains($res, 'hb') || str_contains($res, 'hemoglobina')) {
                if (preg_match('/(?:hb|hemoglobina)[\s\:\=]*(\d+(?:[\.,]\d+)?)/i', $res, $m) ||
                    preg_match('/(\d+(?:[\.,]\d+)?)\s*(?:g\/dl|g%)/i', $res, $m)) {
                    $hbValor = (float)str_replace(',', '.', $m[1]);
                    if ($hbValor > 0 && $hbValor < 7.0) {
                        $isAnemiaGrave = true;
                        $motivoAnemia = "Hemoglobina de {$hbValor} g/dL (< 7.0 g/dL)";
                    }
                }
            }

            if ($isAnemiaGrave) {
                $alertas[] = [
                    'consultation_id' => $exame->consultation_id,
                    'tipo' => 'exames_criticos',
                    'nivel' => Alerta::NIVEL_ALTO,
                    'mensagem' => "Resultado crítico de exame laboratorial: Anemia grave detectada ({$motivoAnemia}). Risco obstétrico iminente.",
                    'dados' => [
                        'exam_id' => $exame->id,
                        'tipo_exame' => $exame->tipo_exame,
                        'resultado' => $exame->resultado,
                        'motivo' => 'anemia_grave',
                    ],
                ];
            }
        }

        return $alertas;
    }

    /**
     * Regra 7: Ganho de Peso Anormal (Perda ponderal ou ganho > 2.0 kg/semana entre consultas consecutivas).
     */
    public function avaliarGanhoPeso(Patient $gestante): array
    {
        $consultas = $gestante->consultations
            ? $gestante->consultations->whereNotNull('peso')->sortByDesc('data_consulta')->values()
            : $gestante->consultations()->whereNotNull('peso')->orderByDesc('data_consulta')->get();

        if ($consultas->count() < 2) {
            return [];
        }

        $cAtual = $consultas[0];
        $cAnterior = $consultas[1];

        $pesoAtual = (float)$cAtual->peso;
        $pesoAnterior = (float)$cAnterior->peso;

        if ($pesoAtual <= 0 || $pesoAnterior <= 0) {
            return [];
        }

        $dias = max(1, Carbon::parse($cAnterior->data_consulta)->diffInDays(Carbon::parse($cAtual->data_consulta)));
        $semanas = max(0.14, $dias / 7.0);
        $delta = $pesoAtual - $pesoAnterior;
        $taxaSemana = $delta / $semanas;

        // Perda de peso
        if ($delta < -0.01) {
            return [[
                'consultation_id' => $cAtual->id,
                'tipo' => 'ganho_peso_anormal',
                'nivel' => Alerta::NIVEL_MEDIO,
                'mensagem' => "Perda ponderal materna detectada entre consultas (" . number_format(abs($delta), 1) . " kg em {$dias} dias). Investigar desnutrição ou hiperemese.",
                'dados' => [
                    'peso_atual' => $pesoAtual,
                    'peso_anterior' => $pesoAnterior,
                    'delta_kg' => round($delta, 2),
                    'taxa_kg_semana' => round($taxaSemana, 2),
                    'dias' => $dias,
                ],
            ]];
        }

        // Ganho de peso excessivo (> 2.0 kg/semana)
        if ($taxaSemana > 2.0) {
            return [[
                'consultation_id' => $cAtual->id,
                'tipo' => 'ganho_peso_anormal',
                'nivel' => Alerta::NIVEL_MEDIO,
                'mensagem' => "Ganho ponderal excessivo detectado (" . number_format($taxaSemana, 1) . " kg/semana entre consultas). Risco de retenção hídrica/pré-eclâmpsia.",
                'dados' => [
                    'peso_atual' => $pesoAtual,
                    'peso_anterior' => $pesoAnterior,
                    'delta_kg' => round($delta, 2),
                    'taxa_kg_semana' => round($taxaSemana, 2),
                    'dias' => $dias,
                ],
            ]];
        }

        return [];
    }

    /**
     * Regra 8: Idade Gestacional Pós-Termo (> 41 semanas sem parto registrado).
     */
    public function avaliarPosTermo(Patient $gestante): array
    {
        if ($gestante->status_atual !== Patient::STATUS_GESTANTE) {
            return [];
        }

        if (!$gestante->data_ultima_menstruacao) {
            return [];
        }

        $dum = Carbon::parse($gestante->data_ultima_menstruacao);
        $hoje = Carbon::now();

        if ($dum->gt($hoje)) {
            return [];
        }

        $semanas = (int)$dum->diffInWeeks($hoje);

        if ($semanas > 41) {
            return [[
                'consultation_id' => null,
                'tipo' => 'idade_gestacional_pos_termo',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => "Gestação prolongada / pós-termo ({$semanas} semanas de gestação) sem registo de parto. Risco iminente de insuficiência placentária e sofrimento fetal.",
                'dados' => [
                    'semanas_gestacao' => $semanas,
                    'dum' => $dum->format('Y-m-d'),
                ],
            ]];
        }

        return [];
    }

    /**
     * Regra 9: Sangramento Reportado na consulta (excluindo negações).
     */
    public function avaliarSangramento(Patient $gestante): array
    {
        $ultimaConsulta = $this->getUltimaConsultaComDados($gestante);
        if (!$ultimaConsulta) {
            return [];
        }

        $texto = trim(($ultimaConsulta->observacoes ?? '') . ' ' . ($ultimaConsulta->orientacoes ?? ''));
        if (empty($texto)) {
            return [];
        }

        if ($this->temRelatoSangramento($texto)) {
            return [[
                'consultation_id' => $ultimaConsulta->id,
                'tipo' => 'sangramento_reportado',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => "Relato de sangramento/hemorragia obstétrica registado nas observações da consulta. Avaliar urgência.",
                'dados' => [
                    'consultation_id' => $ultimaConsulta->id,
                    'observacoes' => $ultimaConsulta->observacoes,
                ],
            ]];
        }

        return [];
    }

    /**
     * Verifica presença de menção afirmativa a sangramento/hemorragia, excluindo negações médicas.
     */
    public function temRelatoSangramento(?string $texto): bool
    {
        if (empty($texto)) {
            return false;
        }

        $textoLower = mb_strtolower($texto, 'UTF-8');
        $keywords = ['sangramento', 'hemorragia', 'sangue', 'metrorragia', 'perda hematica', 'perda hemática'];

        // Expressões de negação comuns em prontuários médicos
        $padroesNegacao = [
            '/(?:sem|nega|negou|negar|aus[eê]ncia\s+de|n[aã]o\s+(?:refere|relata|apresenta|tem|observa-se|h[aá]|apresentou|registou|constatou)|sem\s+(?:queixa|sinais|relato|ind[ií]cios)\s+de)\s+(?:qualquer\s+)?(?:de\s+)?(?:tipo\s+de\s+)?(?:sangramento|hemorragia|sangue|metrorragia|perda\s+hem[aá]tica)/iu',
        ];

        // Remove ocorrências negadas do texto
        $textoSemNegacoes = preg_replace($padroesNegacao, ' ', $textoLower);

        foreach ($keywords as $kw) {
            if (str_contains($textoSemNegacoes, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Auxiliar para obter a última consulta registrada da paciente.
     */
    protected function getUltimaConsultaComDados(Patient $gestante): ?Consultation
    {
        if ($gestante->relationLoaded('consultations') && $gestante->consultations instanceof Collection) {
            return $gestante->consultations->first();
        }

        return $gestante->consultations()->orderByDesc('data_consulta')->first();
    }
}
