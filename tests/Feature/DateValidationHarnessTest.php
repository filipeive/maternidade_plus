<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DateValidationHarnessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_invalid_date_strings_in_metrics_and_alerts()
    {
        $user = User::create([
            'name' => 'Admin Teste',
            'email' => 'admin.teste@maternidade.mz',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);

        // 1. Invalid date in /alertas
        $respAlertas = $this->actingAs($user)->get('/alertas?data_inicio=not-a-date');
        echo "\n/alertas status with invalid date: " . $respAlertas->getStatusCode() . "\n";

        // 2. Invalid date in /alertas/metricas
        $respMetricas = $this->actingAs($user)->get('/alertas/metricas?data_inicio=not-a-date');
        echo "/alertas/metricas status with invalid date: " . $respMetricas->getStatusCode() . "\n";

        // 3. Invalid date in /alertas/metricas/export-pdf
        $respPdf = $this->actingAs($user)->get('/alertas/metricas/export-pdf?data_inicio=not-a-date');
        echo "/alertas/metricas/export-pdf status with invalid date: " . $respPdf->getStatusCode() . "\n";

        $this->assertTrue(true);
    }
}
