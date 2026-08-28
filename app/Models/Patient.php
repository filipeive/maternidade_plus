<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;
    const STATUS_NAO_GESTANTE = 'nao_gestante';
    const STATUS_GESTANTE = 'gestante';
    const STATUS_POS_PARTO = 'pos_parto';

    protected $fillable = [
        'nome_completo',
        'filiacao',
        'data_nascimento',
        'estado_civil',
        'local_trabalho',
        'documento_bi',
        'codigo_ptv',
        'contacto',
        'email',
        'contacto_emergencia',
        'pessoa_referencia_nome',
        'pessoa_referencia_contacto',
        'tem_parceiro',
        'parceiro_nome',
        'parceiro_contacto',
        'parceiro_notificar_sms',
        'acompanhante_nome',
        'acompanhante_parentesco',
        'acompanhante_contacto',
        'acompanhante_notificar_sms',
        'endereco',
        'distrito',
        'bairro',
        'ponto_referencia_residencia',
        'tipo_sanguineo',
        'tipo_sanguineo_parceiro',
        'altura_cm',
        'alergias',
        'uso_rede_mosquiteira',
        'alergia_penicilina',
        'alergia_cotrimoxazol',
        'alergia_sp',
        'historico_medico',
        'data_ultima_menstruacao',
        'data_provavel_parto',
        'status_atual',
        'numero_gestacoes',
        'numero_partos',
        'numero_abortos',
        'ativo'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_ultima_menstruacao' => 'date',
        'data_provavel_parto' => 'date',
        'altura_cm' => 'integer',
        'tem_parceiro' => 'boolean',
        'parceiro_notificar_sms' => 'boolean',
        'acompanhante_notificar_sms' => 'boolean',
        'uso_rede_mosquiteira' => 'boolean',
        'alergia_penicilina' => 'boolean',
        'alergia_cotrimoxazol' => 'boolean',
        'alergia_sp' => 'boolean',
        'ativo' => 'boolean'
    ];

    // Relacionamentos
    public function obstetricHistories()
    {
        return $this->hasMany(ObstetricHistory::class)->orderBy('numero_gravidez', 'asc');
    }
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function vaccines()
    {
        return $this->hasMany(Vaccine::class);
    }

    public function homeVisits()
    {
        return $this->hasMany(HomeVisit::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Consultation::class);
    }

    public function births()
    {
        return $this->hasMany(Birth::class)->orderBy('data_hora_parto', 'desc');
    }

    public function ultimoParto()
    {
        return $this->hasOne(Birth::class)->latestOfMany('data_hora_parto');
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class);
    }

    public function alertasAtivos()
    {
        return $this->hasMany(Alerta::class)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO]);
    }

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class);
    }

    public function antenatalHistory()
    {
        return $this->hasOne(AntenatalHistory::class);
    }

    public function prophylaxis()
    {
        return $this->hasOne(MaternalProphylaxis::class)->withDefault();
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeAltoRisco($query)
    {
        return $query->where(function($q) {
            $q->where('numero_abortos', '>', 0)
              ->orWhere('historico_medico', 'like', '%diabetes%')
              ->orWhere('historico_medico', 'like', '%hipertensao%')
              ->orWhere('alergias', '!=', null);
        });
    }

    public function scopeGestantes($query)
    {
        return $query->whereNotNull('data_ultima_menstruacao')
                    ->where('data_provavel_parto', '>', now());
    }
    
    public function scopePosParto($query)
    {
        return $query->where('status_atual', 'pos_parto');
    }

    // Acessores
    public function getIdadeAttribute()
    {
        return Carbon::parse($this->data_nascimento)->age;
    }

    /**
     * DUM Efetiva: obtém a DUM direta ou calcula a partir da DPP (DPP - 280 dias)
     */
    public function getEffectiveDum(): ?Carbon
    {
        if ($this->data_ultima_menstruacao) {
            return Carbon::parse($this->data_ultima_menstruacao);
        }

        if ($this->data_provavel_parto) {
            return Carbon::parse($this->data_provavel_parto)->subDays(280);
        }

        return null;
    }

    /**
     * Calcula o total de dias gestacionais na data especificada (padrão: hoje)
     */
    public function getDiasGestacionaisNaData(?Carbon $targetDate = null): ?int
    {
        if ($this->status_atual === 'pos_parto' || $this->status_atual === 'nao_gestante') {
            return null;
        }

        $dum = $this->getEffectiveDum();
        if (!$dum) {
            return null;
        }

        $ref = $targetDate ? $targetDate->copy() : Carbon::now();
        if ($dum->gt($ref)) {
            return null;
        }

        return (int) $dum->diffInDays($ref);
    }

    /**
     * Calcula as semanas gestacionais completas na data especificada (padrão: hoje)
     */
    public function getSemanasGestacionaisNaData(?Carbon $targetDate = null): ?int
    {
        $dias = $this->getDiasGestacionaisNaData($targetDate);
        if ($dias === null) {
            return null;
        }

        return intdiv($dias, 7);
    }

    /**
     * Idade Gestacional em semanas completas (compatível com $patient->idade_gestacional)
     */
    public function getIdadeGestacionalAttribute(): ?int
    {
        return $this->getSemanasGestacionaisNaData();
    }

    public function getSemanasGestacaoAttribute(): ?int
    {
        return $this->idade_gestacional;
    }

    public function getDiasGestacionaisAttribute(): ?int
    {
        return $this->getDiasGestacionaisNaData();
    }

    /**
     * Idade gestacional formatada detalhada (ex: "24 sem + 3d" ou "24ª semana")
     */
    public function getIdadeGestacionalDetalhadaAttribute(): ?string
    {
        $dias = $this->getDiasGestacionaisNaData();
        if ($dias === null) {
            return null;
        }

        $semanas = intdiv($dias, 7);
        $diasRestantes = $dias % 7;

        if ($diasRestantes === 0) {
            return "{$semanas}ª semana";
        }

        return "{$semanas} sem + {$diasRestantes}d";
    }

    /**
     * Trimestre gestacional harmonizado (1º: 1-13 sem, 2º: 14-27 sem, 3º: 28+ sem)
     */
    public function getTrimestreAttribute(): ?string
    {
        $semanas = $this->idade_gestacional;
        if ($semanas === null || $semanas < 0) {
            return null;
        }

        if ($semanas <= 13) return '1º trimestre';
        if ($semanas <= 27) return '2º trimestre';
        return '3º trimestre';
    }

    public function getStatusGravidezAttribute()
    {
        // Usar o status_atual como base
        switch ($this->status_atual) {
            case 'pos_parto':
                return 'Pós-parto';
            case 'nao_gestante':
                return 'Não gestante';
            case 'gestante':
                if (!$this->data_provavel_parto) {
                    return 'Gestante';
                }

                $dpp = Carbon::parse($this->data_provavel_parto);
                $diasRestantes = now()->diffInDays($dpp, false);
                
                if ($diasRestantes <= 14 && $diasRestantes >= 0) {
                    return 'A termo';
                }
                
                return 'Gestante';
            default:
                return 'Não definido';
        }
    }

    public function getRiscoGestacionalAttribute()
    {
        $fatoresRisco = 0;
        
        // Idade
        if ($this->idade < 18 || $this->idade > 35) {
            $fatoresRisco++;
        }
        
        // Histórico obstétrico
        if ($this->numero_abortos > 1) {
            $fatoresRisco++;
        }
        
        if ($this->numero_gestacoes > 5) {
            $fatoresRisco++;
        }
        
        // Condições médicas
        $condicoes = ['diabetes', 'hipertensao', 'cardiopatia', 'nefropatia'];
        foreach ($condicoes as $condicao) {
            if (stripos($this->historico_medico, $condicao) !== false) {
                $fatoresRisco++;
            }
        }
        
        // Alergias graves
        if ($this->alergias && stripos($this->alergias, 'grave') !== false) {
            $fatoresRisco++;
        }

        if ($fatoresRisco >= 2) return 'Alto';
        if ($fatoresRisco == 1) return 'Moderado';
        return 'Baixo';
    }

    /**
     * Estratificação Oficial de Alto Risco Obstétrico (ARO) do MISAU por Nível de Referência Hospitalar
     */
    public function getEstratificacaoAroMisauAttribute(): array
    {
        $nivel = 'Nivel_I'; // Nível Primário (Centro de Saúde)
        $nivelLabel = 'Nível I — Cuidados Primários (Centro de Saúde)';
        $motivos = [];
        $cuidadosTransferencia = [];

        $idade = $this->idade;
        $isPrimigesta = ($this->numero_gestacoes <= 1 || $this->numero_partos == 0);
        $altura = $this->altura_cm;

        // --- Critérios NÍVEL III (Hospital Provincial / Central) ---
        $criteriosNivel3 = [];

        if ($isPrimigesta && $altura && $altura < 150) {
            $criteriosNivel3[] = 'Primigesta com baixa estatura (< 1,50 m) — risco de desproporção céfalo-pélvica (DCP)';
        }

        if ($isPrimigesta && $idade && $idade < 16) {
            $criteriosNivel3[] = 'Primigesta adolescente (< 16 anos) — bacia óssea imatura';
        }

        if ($this->obstetricHistories->where('tipo_parto', 'cesariana')->count() > 0) {
            $criteriosNivel3[] = 'Antecedente de cesariana prévia — risco de rotura uterina';
        }

        if ($this->obstetricHistories->where('tipo_parto', 'ventosa_forceps')->count() > 0) {
            $criteriosNivel3[] = 'Antecedente de parto instrumentado (ventosa/fórceps)';
        }

        // --- Critérios NÍVEL II (Hospital Rural / Geral às 32 semanas) ---
        $criteriosNivel2 = [];

        if ($idade && $idade >= 35) {
            $criteriosNivel2[] = 'Idade materna avançada (≥ 35 anos)';
        }

        if ($this->numero_partos >= 5) {
            $criteriosNivel2[] = 'Grande multípara (≥ 5 partos anteriores) — risco de atonia uterina e hemorragia pós-parto';
        }

        if ($this->numero_abortos >= 2) {
            $criteriosNivel2[] = 'Histórico de abortos recorrentes (≥ 2 abortos)';
        }

        if ($this->obstetricHistories->where('nado_morto', true)->count() > 0) {
            $criteriosNivel2[] = 'Histórico de natimorto / óbito fetal em gestação anterior';
        }

        if ($this->obstetricHistories->where('peso_rn_gramas', '>', 4000)->count() > 0) {
            $criteriosNivel2[] = 'Antecedente de recém-nascido macrossómico (> 4,0 kg)';
        }

        // --- Critérios NÍVEL I (Consulta Médica / TM no Centro de Saúde) ---
        $criteriosNivel1 = [];

        if ($this->historico_medico && (stripos($this->historico_medico, 'diabetes') !== false || stripos($this->historico_medico, 'glicemia') !== false)) {
            $criteriosNivel1[] = 'Histórico / suspeita de Diabetes Gestacional';
        }

        if ($this->historico_medico && (stripos($this->historico_medico, 'tuberculose') !== false || stripos($this->historico_medico, 'tosse') !== false)) {
            $criteriosNivel1[] = 'Sintomática respiratória / Tuberculose em rastreio ou tratamento';
        }

        // Decisão do nível
        if (!empty($criteriosNivel3)) {
            $nivel = 'Nivel_III';
            $nivelLabel = 'Nível III — Cuidados Terciários (Hospital Provincial / Central)';
            $motivos = $criteriosNivel3;
            $cuidadosTransferencia = [
                'Programar parto para Hospital Provincial/Central',
                'Acesso venoso calibroso com Soro Ringer Lactato / Fisiológico',
                'Algaliação se retenção ou emergência',
                'Acompanhamento por profissional de saúde (ESMI ou servente)',
                'Acompanhante familiar jovem apto para doação de sangue',
                'Ficha Pré-Natal (FPN) e todos os exames originais em anexo'
            ];
        } elseif (!empty($criteriosNivel2)) {
            $nivel = 'Nivel_II';
            $nivelLabel = 'Nível II — Cuidados Secundários (Hospital Rural / Geral às 32 sem)';
            $motivos = $criteriosNivel2;
            $cuidadosTransferencia = [
                'Referir para consulta de Obstetrícia às 32 semanas',
                'Encaminhar para Casa de Espera da Maternidade',
                'Ficha Pré-Natal (FPN) com registo de profilaxias atualizado'
            ];
        } elseif (!empty($criteriosNivel1)) {
            $nivel = 'Nivel_I_Especial';
            $nivelLabel = 'Nível I — Consulta de Médico / Técnico de Medicina no CS';
            $motivos = $criteriosNivel1;
            $cuidadosTransferencia = [
                'Avaliação clínica detalhada na Unidade Sanitária primária',
                'Requisição de exames laboratoriais complementares'
            ];
        }

        return [
            'nivel' => $nivel,
            'label' => $nivelLabel,
            'is_aro' => ($nivel !== 'Nivel_I'),
            'motivos' => $motivos,
            'checklist_transferencia' => $cuidadosTransferencia
        ];
    }

    /**
     * Rastreio de Risco de Isoimunização Rh (Mãe Rh- / Parceiro Rh+)
     */
    public function getRiscoIsoimunizacaoRhAttribute(): bool
    {
        $maeRhNeg = in_array($this->tipo_sanguineo, ['A-', 'B-', 'AB-', 'O-']);
        $parceiroRhPos = in_array($this->tipo_sanguineo_parceiro, ['A+', 'B+', 'AB+', 'O+']);

        return ($maeRhNeg && $parceiroRhPos);
    }

    public function debugIdadeGestacional()
    {
        return [
            'data_ultima_menstruacao' => $this->data_ultima_menstruacao,
            'data_ultima_menstruacao_formatted' => $this->data_ultima_menstruacao ? $this->data_ultima_menstruacao->format('Y-m-d') : 'null',
            'dias_desde_dum' => $this->dias_gestacionais,
            'semanas_gestacao' => $this->idade_gestacional,
            'hoje' => Carbon::now()->format('Y-m-d'),
            'diferenca_em_dias' => $this->data_ultima_menstruacao ? Carbon::parse($this->data_ultima_menstruacao)->diffInDays(Carbon::now()) : 'N/A'
        ];
    }
    
    // NOVO: Verificar se pode dar à luz
    public function podeRegistrarParto()
    {
        return $this->status_atual === 'gestante' && 
               $this->data_ultima_menstruacao && 
               $this->idade_gestacional >= 22; // Viabilidade fetal
    }

    // NOVO: Registrar parto
    public function registrarParto($dadosParto)
    {
        $birth = $this->births()->create($dadosParto);
        
        // Atualizar status da paciente
        $this->update([
            'status_atual' => 'pos_parto',
            'numero_partos' => $this->numero_partos + 1
        ]);

        return $birth;
    }

    // NOVO: Dados do último parto
    public function getDadosUltimoPartoAttribute()
    {
        $ultimoParto = $this->ultimoParto;
        
        if (!$ultimoParto) {
            return null;
        }

        return [
            'data' => $ultimoParto->data_hora_parto,
            'tipo' => $ultimoParto->tipo_parto_formatado,
            'local' => $ultimoParto->local_parto,
            'peso_bebe' => $ultimoParto->peso_formatado,
            'apgar' => $ultimoParto->apgar_formatado,
            'status_bebe' => $ultimoParto->status_bebe_formatado
        ];
    }

    public function getProximaConsulta()
    {
        return $this->consultations()
                   ->where('status', 'agendada')
                   ->where('data_consulta', '>', now())
                   ->orderBy('data_consulta')
                   ->first();
    }

    public function getUltimaConsulta()
    {
        return $this->consultations()
                   ->where('status', 'realizada')
                   ->orderBy('data_consulta', 'desc')
                   ->first();
    }

    // NOVO: Método para nova gestação
    public function iniciarNovaGestacao($dataUltimaMenstruacao)
    {
        $this->update([
            'data_ultima_menstruacao' => $dataUltimaMenstruacao,
            'data_provavel_parto' => Carbon::parse($dataUltimaMenstruacao)->addDays(280),
            'numero_gestacoes' => $this->numero_gestacoes + 1,
            'status_atual' => 'gestante'
        ]);
    }
    
    public function getVacinasEmAtraso()
    {
        return $this->vaccines()
                   ->where('status', 'pendente')
                   ->where('proxima_dose', '<', now())
                   ->get();
    }

    public function getProximasVacinas()
    {
        return $this->vaccines()
                   ->where('status', 'pendente')
                   ->where('proxima_dose', '<=', now()->addDays(7))
                   ->orderBy('proxima_dose')
                   ->get();
    }

    public function getExamesPendentes()
    {
        return $this->exams()
                   ->where('status', 'solicitado')
                   ->orWhere('status', 'pendente')
                   ->get();
    }

    public function necessitaVisitaDomiciliaria()
    {
        // Verificar se é gestante faltosa
        $consultasPerdidas = $this->consultations()
                                 ->where('status', 'agendada')
                                 ->where('data_consulta', '<', now()->subDays(3))
                                 ->count();
        
        if ($consultasPerdidas > 0) {
            return [
                'necessita' => true,
                'motivo' => 'Gestante faltosa às consultas',
                'prioridade' => 'alta'
            ];
        }

        // Verificar se é alto risco sem consulta recente
        if ($this->risco_gestacional === 'Alto') {
            $ultimaConsulta = $this->getUltimaConsulta();
            if (!$ultimaConsulta || $ultimaConsulta->data_consulta < now()->subDays(30)) {
                return [
                    'necessita' => true,
                    'motivo' => 'Gestação de alto risco sem acompanhamento recente',
                    'prioridade' => 'alta'
                ];
            }
        }

        return ['necessita' => false];
    }

    public function getEsquemaVacinalCompleto()
    {
        $esquemas = Vaccine::getVacinasPrenatal();
        $vacinasAdministradas = $this->vaccines()
                                    ->where('status', 'administrada')
                                    ->get()
                                    ->groupBy('tipo_vacina');
        
        $resumo = [];
        
        foreach ($esquemas as $tipo => $info) {
            $dosesCompletas = $vacinasAdministradas->get($tipo, collect())->count();
            $resumo[$tipo] = [
                'info' => $info,
                'doses_completas' => $dosesCompletas,
                'doses_necessarias' => $info['doses'],
                'completo' => $dosesCompletas >= $info['doses'],
                'percentual' => round(($dosesCompletas / $info['doses']) * 100, 1)
            ];
        }
        
        return $resumo;
    }

    public function getIndicadoresANC()
    {
        $consultas = $this->consultations()->where('status', 'realizada')->get();
        
        return [
            'total_consultas' => $consultas->count(),
            'primeira_consulta' => $consultas->sortBy('data_consulta')->first(),
            'consultas_no_prazo' => $this->verificarConsultasNoPrazo(),
            'exames_basicos_realizados' => $this->verificarExamesBasicos(),
            'esquema_vacinal' => $this->getEsquemaVacinalCompleto()
        ];
    }

    private function verificarConsultasNoPrazo()
    {
        // Implementar lógica para verificar se as consultas estão conforme protocolo MISAU
        // Por agora, retorna valor simulado
        return [
            'no_prazo' => 8,
            'total_esperadas' => 10,
            'percentual' => 80
        ];
    }

    private function verificarExamesBasicos()
    {
        $examesBasicos = [
            'hemograma_completo',
            'glicemia_jejum', 
            'urina_tipo_1',
            'teste_hiv',
            'teste_sifilis'
        ];
        
        $examesRealizados = $this->exams()
                               ->where('status', 'realizado')
                               ->whereIn('tipo_exame', $examesBasicos)
                               ->pluck('tipo_exame')
                               ->unique()
                               ->count();
        
        return [
            'realizados' => $examesRealizados,
            'total' => count($examesBasicos),
            'percentual' => round(($examesRealizados / count($examesBasicos)) * 100, 1)
        ];
    }

    // Método para busca
    public static function search($term)
    {
        return self::where('ativo', true)
                  ->where(function($query) use ($term) {
                      $query->where('nome_completo', 'LIKE', "%{$term}%")
                            ->orWhere('documento_bi', 'LIKE', "%{$term}%")
                            ->orWhere('contacto', 'LIKE', "%{$term}%");
                  });
    }
}