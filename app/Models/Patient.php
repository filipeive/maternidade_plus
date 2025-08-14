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
        'data_nascimento',
        'documento_bi',
        'contacto',
        'email',
        'contacto_emergencia',
        'endereco',
        'tipo_sanguineo',
        'alergias',
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
        'ativo' => 'boolean'
    ];

    // Relacionamentos
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

    // Melhorar o cálculo da idade gestacional
    public function getIdadeGestacionalAttribute()
    {
        // Se já deu à luz, retorna null
        if ($this->status_atual === 'pos_parto') {
            return null;
        }

        if (!$this->data_ultima_menstruacao) {
            return null;
        }

        try {
            $dum = Carbon::parse($this->data_ultima_menstruacao);
            $hoje = Carbon::now();
            
            if ($dum->gt($hoje)) {
                return null;
            }
            
            $semanas = $dum->diffInWeeks($hoje);
            
            if ($semanas > 42) {
                return null;
            }
            
            return $semanas;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getSemanasGestacaoAttribute()
    {
        return $this->idade_gestacional;
    }

    public function getDiasGestacionaisAttribute()
    {
        if (!$this->data_ultima_menstruacao) {
            return null;
        }

        try {
            $dum = Carbon::parse($this->data_ultima_menstruacao);
            $hoje = Carbon::now();
            
            if ($dum->gt($hoje)) {
                return null;
            }
            
            $dias = $dum->diffInDays($hoje);
            
            // Limitar a 294 dias (42 semanas)
            if ($dias > 294) {
                return null;
            }
            
            return $dias;
        } catch (\Exception $e) {
            return null;
        }
    }

    // MELHORADO: Trimestre com verificação adicional
    public function getTrimestreAttribute()
    {
        $semanas = $this->idade_gestacional;
        
        if (!$semanas || $semanas <= 0) return null;
        
        if ($semanas <= 13) return '1º trimestre';
        if ($semanas <= 27) return '2º trimestre';
        if ($semanas <= 42) return '3º trimestre';
        
        return null; // Caso exceda 42 semanas
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