<?php

namespace App\Helpers;

class VisitTypes
{
    public static function all()
    {
        return [
            'rotina' => 'Visita de Rotina',
            'pos_parto' => 'Visita Pós-parto',
            'alto_risco' => 'Visita de Alto Risco',
            'faltosa' => 'Visita a Gestante Faltosa',
            'emergencia' => 'Visita de Emergência',
            'educacao' => 'Visita Educativa',
            'seguimento' => 'Visita de Seguimento'
        ];
    }
}