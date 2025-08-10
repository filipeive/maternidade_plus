<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'data_consulta',
        'tipo_consulta',
        'semanas_gestacao',
        'peso',
        'pressao_arterial',
        'batimentos_fetais',
        'altura_uterina',
        'observacoes',
        'orientacoes',
        'proxima_consulta',
        'status'
    ];

    protected $casts = [
        'data_consulta' => 'datetime',
        'proxima_consulta' => 'date',
        'peso' => 'decimal:2',
        'altura_uterina' => 'decimal:1'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function getTipoConsultaLabelAttribute(): string
    {
        return match($this->tipo_consulta) {
            '1_trimestre' => '1º Trimestre',
            '2_trimestre' => '2º Trimestre',
            '3_trimestre' => '3º Trimestre',
            'pos_parto' => 'Pós-parto',
            'emergencia' => 'Emergência',
            default => 'Não definido'
        };
    }
}
