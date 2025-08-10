<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'tipo_vacina',
        'descricao',
        'data_administracao',
        'dose_numero',
        'lote',
        'fabricante',
        'data_vencimento',
        'local_aplicacao',
        'observacoes',
        'reacao_adversa',
        'proxima_dose',
        'status'
    ];

    protected $casts = [
        'data_administracao' => 'datetime',
        'data_vencimento' => 'date',
        'proxima_dose' => 'date',
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

    public function scopePendingDoses($query)
    {
        return $query->where('status', 'pendente')
                    ->whereDate('proxima_dose', '<=', now()->addDays(7));
    }

    // Acessores
    public function getVacinaFormatadaAttribute()
    {
        $tipos = [
            'tetanica' => 'Antitetânica (dT)',
            'hepatite_b' => 'Hepatite B',
            'influenza' => 'Influenza (Gripe)',
            'covid19' => 'COVID-19',
            'febre_amarela' => 'Febre Amarela',
            'iptp' => 'Prevenção Malária (IPTp)',
        ];

        return $tipos[$this->tipo_vacina] ?? $this->tipo_vacina;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'administrada' => 'success',
            'pendente' => 'warning',
            'vencida' => 'danger',
            'reagenda' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Métodos auxiliares
    public static function getVacinasPrenatal()
    {
        return [
            'tetanica' => [
                'nome' => 'Antitetânica (dT)',
                'doses' => 2,
                'intervalo_dias' => 30,
                'descricao' => 'Proteção contra tétano neonatal'
            ],
            'hepatite_b' => [
                'nome' => 'Hepatite B',
                'doses' => 3,
                'intervalo_dias' => 30,
                'descricao' => 'Proteção contra hepatite B'
            ],
            'influenza' => [
                'nome' => 'Influenza',
                'doses' => 1,
                'intervalo_dias' => 365,
                'descricao' => 'Proteção contra gripe sazonal'
            ],
            'iptp' => [
                'nome' => 'IPTp (Malária)',
                'doses' => 3,
                'intervalo_dias' => 28,
                'descricao' => 'Prevenção da malária na gravidez'
            ]
        ];
    }

    public function calcularProximaDose()
    {
        $esquemas = self::getVacinasPrenatal();
        
        if (isset($esquemas[$this->tipo_vacina])) {
            $intervalo = $esquemas[$this->tipo_vacina]['intervalo_dias'];
            return Carbon::parse($this->data_administracao)->addDays($intervalo);
        }

        return null;
    }
}