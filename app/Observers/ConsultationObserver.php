<?php

namespace App\Observers;

use App\Models\Consultation;
use App\Services\AlertaPrecoceService;

class ConsultationObserver
{
    /**
     * Handle the Consultation "saved" event.
     */
    public function saved(Consultation $consultation): void
    {
        $this->avaliar($consultation);
    }

    /**
     * Avalia a paciente associada à consulta.
     */
    protected function avaliar(Consultation $consultation): void
    {
        $patient = $consultation->patient;
        if ($patient) {
            app(AlertaPrecoceService::class)->avaliarPaciente($patient);
        }
    }
}
