<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    protected $fillable = [
        'consultation_id',
        'tipo_exame',
        'descricao_exame',
        'data_solicitacao',
        'data_realizacao',
        'resultado',
        'observacoes',
        'status'
    ];

    protected $casts = [
        'data_solicitacao' => 'date',
        'data_realizacao' => 'date'
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function getTipoExameLabelAttribute(): string
    {
        return match($this->tipo_exame) {
            'hemograma_completo' => 'Hemograma Completo',
            'glicemia_jejum' => 'Glicemia de Jejum',
            'teste_tolerancia_glicose' => 'Teste de Tolerância à Glicose',
            'urina_tipo_1' => 'EAS (Urina Tipo 1)',
            'urocultura' => 'Urocultura',
            'ultrassom_obstetrico' => 'Ultrassom Obstétrico',
            'teste_hiv' => 'Teste HIV',
            'teste_sifilis' => 'Teste de Sífilis',
            'hepatite_b' => 'Hepatite B',
            'toxoplasmose' => 'Toxoplasmose',
            'rubeola' => 'Rubéola',
            'estreptococo_grupo_b' => 'Estreptococo Grupo B',
            'outros' => 'Outros',
            default => 'Não especificado'
        };
    }
}
