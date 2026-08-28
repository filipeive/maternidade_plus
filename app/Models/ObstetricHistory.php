<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObstetricHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'numero_gravidez',
        'ano',
        'tipo_aborto',
        'local_parto',
        'prematuro',
        'tipo_parto',
        'gemelar',
        'nado_morto',
        'nato_vivo',
        'peso_rn_gramas',
        'comentarios',
    ];

    protected $casts = [
        'numero_gravidez' => 'integer',
        'ano' => 'integer',
        'prematuro' => 'boolean',
        'gemelar' => 'boolean',
        'nado_morto' => 'boolean',
        'nato_vivo' => 'boolean',
        'peso_rn_gramas' => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function getLocalPartoLabelAttribute(): string
    {
        return match ($this->local_parto) {
            'us_maternidade' => 'Unidade Sanitária / Maternidade',
            'domicilio' => 'Domicílio (Casa)',
            'caminho' => 'A caminho da US',
            'parteira_tradicional' => 'Parteira Tradicional',
            default => 'Outro'
        };
    }

    public function getTipoPartoLabelAttribute(): string
    {
        return match ($this->tipo_parto) {
            'eutocico' => 'Parto Normal (Eutócico)',
            'cesariana' => 'Cesariana',
            'ventosa_forceps' => 'Parto com Fórceps / Ventosa',
            'ectopica' => 'Gravidez Ectópica',
            default => 'Outro'
        };
    }
}

