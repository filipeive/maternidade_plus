<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

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

    // Acessores
    public function getIdadeAttribute()
    {
        return Carbon::parse($this->data_nascimento)->age;
    }

    public function getIdadeGestacionalAttribute()
    {
        if (!$this->data_ultima_menstruacao) {
            return null;
        }

        $dum = Carbon::parse($this->data_ultima_menstruacao);
        $semanas = $dum->diffInWeeks(now());
        
        return $semanas > 42 ? null : $semanas; // Limitar a 42 semanas
    }

    public function getTrimestreAttribute()
    {
        $semanas = $this->idade_gestacional;
        
        if (!$semanas) return null;
        
        if ($semanas <= 12) return '1º trimestre';
        if ($semanas <= 28) return '2º trimestre';
        return '3º trimestre';
    }

    public function getStatusGravidezAttribute()
    {
        if (!$this->data_provavel_parto) {
            return 'Não gestante';
        }

        $dpp = Carbon::parse($this->data_provavel_parto);
        
        if ($dpp->isPast()) {
            return 'Pós-parto';
        }

        $diasRestantes = now()->diffInDays($dpp);
        
        if ($diasRestantes <= 14) {
            return 'A termo';
        }

        return 'Gestante';
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

    // Métodos auxiliares
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