<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HomeVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'data_visita',
        'motivo_visita',
        'tipo_visita',
        'endereco_visita',
        'status_visita',
        'observacoes_ambiente',
        'condicoes_higiene',
        'apoio_familiar',
        'estado_nutricional',
        'sinais_vitais',
        'queixas_principais',
        'orientacoes_dadas',
        'materiais_entregues',
        'proxima_visita',
        'acompanhante_presente',
        'necessita_referencia',
        'observacoes_gerais',
        'status',
        'coordenadas_gps'
    ];

    protected $casts = [
        'data_visita' => 'datetime',
        'proxima_visita' => 'date',
        'sinais_vitais' => 'json',
        'materiais_entregues' => 'json',
        'coordenadas_gps' => 'json',
        'necessita_referencia' => 'boolean',
        'acompanhante_presente' => 'boolean'
    ];

    // Relacionamentos
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeScheduledToday($query)
    {
        return $query->whereDate('data_visita', today())
                    ->where('status', 'agendada');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'agendada')
                    ->whereDate('data_visita', '<', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('data_visita', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Acessores
    public function getTipoVisitaFormatadaAttribute()
    {
        $tipos = [
            'rotina' => 'Visita de Rotina',
            'pos_parto' => 'Pós-Parto',
            'alto_risco' => 'Alto Risco',
            'faltosa' => 'Gestante Faltosa',
            'emergencia' => 'Emergência',
            'educacao' => 'Educação em Saúde',
            'seguimento' => 'Seguimento'
        ];

        return $tipos[$this->tipo_visita] ?? $this->tipo_visita;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'agendada' => 'primary',
            'realizada' => 'success',
            'cancelada' => 'danger',
            'reagendada' => 'warning',
            'nao_encontrada' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusVisitaFormatadaAttribute()
    {
        $status = [
            'agendada' => 'Agendada',
            'realizada' => 'Realizada',
            'cancelada' => 'Cancelada',
            'reagendada' => 'Reagendada',
            'nao_encontrada' => 'Não Encontrada'
        ];

        return $status[$this->status] ?? $this->status;
    }

    // Métodos auxiliares
    public function getDuration()
    {
        if ($this->status !== 'realizada' || !$this->updated_at) {
            return null;
        }

        $inicio = Carbon::parse($this->data_visita);
        $fim = Carbon::parse($this->updated_at);
        
        return $inicio->diffInMinutes($fim);
    }

    public function isOverdue()
    {
        return $this->status === 'agendada' && 
               Carbon::parse($this->data_visita)->isPast();
    }

    public function canBeCompleted()
    {
        return in_array($this->status, ['agendada', 'reagendada']);
    }

    public static function getTiposVisita()
    {
        return [
            'rotina' => [
                'nome' => 'Visita de Rotina',
                'descricao' => 'Acompanhamento regular da gestante',
                'frequencia' => 'Mensal'
            ],
            'pos_parto' => [
                'nome' => 'Pós-Parto',
                'descricao' => 'Visita após o parto para acompanhamento',
                'frequencia' => '1ª semana, 1º mês'
            ],
            'alto_risco' => [
                'nome' => 'Alto Risco',
                'descricao' => 'Gestação de alto risco que necessita acompanhamento especial',
                'frequencia' => 'Quinzenal'
            ],
            'faltosa' => [
                'nome' => 'Gestante Faltosa',
                'descricao' => 'Busca ativa de gestantes que faltaram às consultas',
                'frequencia' => 'Conforme necessidade'
            ],
            'emergencia' => [
                'nome' => 'Emergência',
                'descricao' => 'Situação de emergência ou urgência',
                'frequencia' => 'Imediata'
            ],
            'educacao' => [
                'nome' => 'Educação em Saúde',
                'descricao' => 'Orientações sobre cuidados pré-natais',
                'frequencia' => 'Conforme necessidade'
            ],
            'seguimento' => [
                'nome' => 'Seguimento',
                'descricao' => 'Seguimento após intercorrências ou procedimentos',
                'frequencia' => 'Conforme protocolo'
            ]
        ];
    }

    public function getDistanceFromHealthCenter($coordinates = null)
    {
        if (!$this->coordenadas_gps || !$coordinates) {
            return null;
        }

        // Implementar cálculo de distância usando coordenadas GPS
        // Por agora, retorna valor simulado
        return rand(500, 5000); // metros
    }

    public function generateRoute($startCoordinates)
    {
        if (!$this->coordenadas_gps) {
            return null;
        }

        // Aqui seria integração com APIs de mapas (Google Maps, etc.)
        // Por agora, retorna dados simulados
        return [
            'distance' => $this->getDistanceFromHealthCenter($startCoordinates),
            'duration' => rand(15, 60), // minutos
            'route_url' => "https://maps.google.com/maps?q={$this->coordenadas_gps['lat']},{$this->coordenadas_gps['lng']}"
        ];
    }
}