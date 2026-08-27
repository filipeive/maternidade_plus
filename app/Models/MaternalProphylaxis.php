<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaternalProphylaxis extends Model
{
    use HasFactory;

    protected $table = 'maternal_prophylaxes';

    protected $fillable = [
        'patient_id',
        'consultation_id',
        'user_id',
        'vat_1_dose',
        'vat_2_dose',
        'vat_3_dose',
        'vat_4_dose',
        'vat_5_dose',
        'vat_reforco',
        'sp_1_dose',
        'sp_2_dose',
        'sp_3_dose',
        'sp_4_dose',
        'remtil_entregue',
        'remtil_data_entrega',
        'sal_ferroso_folico_3doses',
        'doses_sal_ferroso_entregues',
        'mebendazol_administrado',
        'hiv_status_entrada',
        'hiv_teste_data',
        'hiv_resultado_cpn',
        'parceiro_testado_hiv',
        'parceiro_resultado_hiv',
        'ctz_iniciado',
        'esquema_ptv',
        'tarv_inicio_data',
        'sifilis_resultado',
        'sifilis_teste_data',
        'sifilis_tratamento_mulher',
        'sifilis_tratamento_parceiro',
        'misoprostol_entregue',
        'misoprostol_data_entrega'
    ];

    protected $casts = [
        'vat_1_dose' => 'date',
        'vat_2_dose' => 'date',
        'vat_3_dose' => 'date',
        'vat_4_dose' => 'date',
        'vat_5_dose' => 'date',
        'vat_reforco' => 'date',
        'sp_1_dose' => 'date',
        'sp_2_dose' => 'date',
        'sp_3_dose' => 'date',
        'sp_4_dose' => 'date',
        'remtil_entregue' => 'boolean',
        'remtil_data_entrega' => 'date',
        'sal_ferroso_folico_3doses' => 'boolean',
        'mebendazol_administrado' => 'date',
        'hiv_teste_data' => 'date',
        'parceiro_testado_hiv' => 'boolean',
        'ctz_iniciado' => 'boolean',
        'tarv_inicio_data' => 'date',
        'sifilis_teste_data' => 'date',
        'sifilis_tratamento_mulher' => 'boolean',
        'sifilis_tratamento_parceiro' => 'boolean',
        'misoprostol_entregue' => 'boolean',
        'misoprostol_data_entrega' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
