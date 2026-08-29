<?php

namespace App\Observers;

use App\Models\Exam;
use App\Services\AlertaPrecoceService;

class ExamObserver
{
    public function saved(Exam $exam): void
    {
        $patient = $exam->patient;
        if ($patient && $patient->ativo) {
            try {
                app(AlertaPrecoceService::class)->avaliarPaciente($patient);
            } catch (\Throwable $e) {
                // Silently log or continue
            }
        }
    }
}
