<?php

namespace App\Observers;

use App\Models\Patient;
use App\Services\AlertaPrecoceService;

class PatientObserver
{
    public function saved(Patient $patient): void
    {
        if ($patient->ativo && $patient->status_atual === Patient::STATUS_GESTANTE) {
            try {
                app(AlertaPrecoceService::class)->avaliarPaciente($patient);
            } catch (\Throwable $e) {
                // Silently log or continue
            }
        }
    }
}
