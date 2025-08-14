<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Birth extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'data_hora_parto',
        'tipo_parto',
        'local_parto',
        'hospital_unidade', 
        'profissional_obstetra',
        'profissional_enfermeiro',
        'idade_gestacional_parto',
        'peso_mae_preparto',
        'complicacoes_maternas',
        'sexo_bebe',
        'peso_nascimento',
        'altura_nascimento',
        'apgar_1min',
        'apgar_5min',
        'apgar_10min',
        'observacoes_rn',
        'status_bebe',
        'observacoes_gerais',
        'medicamentos_utilizados',
        'parto_multiplo',
        'numero_bebes',
        'condicoes_pos_parto',
        'alta_hospitalar'
    ];

    protected $casts = [
        'data_hora_parto' => 'datetime',
        'alta_hospitalar' => 'datetime',
        'peso_mae_preparto' => 'decimal:2',
        'peso_nascimento' => 'decimal:1',
        'altura_nascimento' => 'decimal:1',
        'parto_multiplo' => 'boolean'
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

    // Acessores
    public function getApgarFormatadoAttribute()
    {
        return "{$this->apgar_1min}/{$this->apgar_5min}" . 
               ($this->apgar_10min ? "/{$this->apgar_10min}" : '');
    }

    public function getPesoFormatadoAttribute()
    {
        return $this->peso_nascimento ? number_format($this->peso_nascimento, 1) . 'g' : null;
    }

    public function getAlturaFormatadaAttribute()
    {
        return $this->altura_nascimento ? number_format($this->altura_nascimento, 1) . 'cm' : null;
    }

    public function getTipoPartoFormatadoAttribute()
    {
        return match($this->tipo_parto) {
            'normal' => 'Parto Normal',
            'cesariana' => 'Cesariana', 
            'forceps' => 'Parto com Fórceps',
            'vacuum' => 'Parto com Vácuo',
            'outros' => 'Outros',
            default => $this->tipo_parto
        };
    }

    public function getStatusBebeFormatadoAttribute()
    {
        return match($this->status_bebe) {
            'vivo_saudavel' => 'Vivo e Saudável',
            'vivo_complicacoes' => 'Vivo com Complicações',
            'obito_fetal' => 'Óbito Fetal',
            'obito_neonatal' => 'Óbito Neonatal',
            default => $this->status_bebe
        };
    }

    // Scopes
    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('data_hora_parto', '>=', now()->subDays($dias));
    }

    public function scopePartoNormal($query)
    {
        return $query->where('tipo_parto', 'normal');
    }

    public function scopeCesariana($query) 
    {
        return $query->where('tipo_parto', 'cesariana');
    }

    public function scopeBebeSaudavel($query)
    {
        return $query->where('status_bebe', 'vivo_saudavel');
    }
}