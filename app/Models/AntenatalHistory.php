<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntenatalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'num_gestas',
        'num_paras',
        'num_abortos_espontaneos',
        'num_abortos_provocados',
        'num_nados_mortos',
        'num_nados_vivos',
        'num_filhos_vivos_atuais',
        'num_cesarianas',
        'num_gravidezes_ectopicas',
        'historico_gemelar',
        'historico_rn_baixo_peso',
        'historico_rn_macrossomico',
        'historico_hemorragia_postpartum',
        'historico_remocao_manual_placenta',
        'data_ultimo_parto',
        'local_ultimo_parto',
        'is_aro',
        'fatores_aro',
        'nivel_referencia_aro',
        'pip_local_parto_previsto',
        'pip_necessita_casa_espera',
        'pip_meio_transporte',
        'pip_nome_acompanhante',
        'pip_contacto_acompanhante',
        'pip_doador_sangue_designado',
        'altura_cm',
        'peso_inicial_kg',
        'imc_inicial',
        'perimetro_braquial_cm',
        'estado_nutricional_inicial'
    ];

    protected $casts = [
        'data_ultimo_parto' => 'date',
        'historico_gemelar' => 'boolean',
        'historico_rn_baixo_peso' => 'boolean',
        'historico_rn_macrossomico' => 'boolean',
        'historico_hemorragia_postpartum' => 'boolean',
        'historico_remocao_manual_placenta' => 'boolean',
        'is_aro' => 'boolean',
        'fatores_aro' => 'array',
        'pip_necessita_casa_espera' => 'boolean',
        'altura_cm' => 'decimal:1',
        'peso_inicial_kg' => 'decimal:2',
        'imc_inicial' => 'decimal:1',
        'perimetro_braquial_cm' => 'decimal:1'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
