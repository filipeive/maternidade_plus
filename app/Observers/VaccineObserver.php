<?php

namespace App\Observers;

use App\Models\Vaccine;
use App\Services\AlertaPrecoceService;

class VaccineObserver
{
    public function saved(Vaccine $vaccine): void
    {
        $patient = $vaccine->patient;
        if ($patient && $patient->ativo) {
            try {
                app(AlertaPrecoceService::class)->avaliarPaciente($patient);
            } catch (\Throwable $e) {
                // Silently log or continue
            }
        }
    }
}
