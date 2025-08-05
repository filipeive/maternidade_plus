<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Patient extends Model
{
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

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function getIdadeAttribute(): int
    {
        return Carbon::parse($this->data_nascimento)->age;
    }

    public function getSemanasGestacaoAttribute(): ?int
    {
        if (!$this->data_ultima_menstruacao) {
            return null;
        }
        
        return Carbon::parse($this->data_ultima_menstruacao)->diffInWeeks(now());
    }

    public function getProximaConsultaAttribute()
    {
        return $this->consultations()
            ->where('data_consulta', '>', now())
            ->orderBy('data_consulta')
            ->first();
    }
}
