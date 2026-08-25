<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\AlertaAcao;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaMetricasTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctorUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctorUser = User::factory()->create([
            'name' => 'Dr. Gestor Metricas',
            'email' => 'gestor.metricas@maternidade.mz',
        ]);
    }

    private function createPatient(string $nome = 'Lucia Matsinhe'): Patient
    {
        return Patient::create([
            'nome_completo' => $nome . ' ' . uniqid(),
            'data_nascimento' => '1999-04-12',
            'documento_bi' => '110100' . rand(100000, 999999) . 'M',
            'contacto' => '+25884' . rand(1000000, 9999999),
            'email' => 'lucia.' . uniqid() . '@example.com',
            'endereco' => 'Marracuene, Maputo',
            'tipo_sanguineo' => 'O+',
            'data_ultima_menstruacao' => Carbon::now()->subWeeks(16)->format('Y-m-d'),
            'data_provavel_parto' => Carbon::now()->addWeeks(24)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_GESTANTE,
            'numero_gestacoes' => 1,
            'numero_partos' => 0,
            'numero_abortos' => 0,
            'ativo' => true,
        ]);
    }

    private function createAlerta(Patient $patient, array $attributes = []): Alerta
    {
        return Alerta::create(array_merge([
            'patient_id' => $patient->id,
            'consultation_id' => null,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'Crise hipertensiva.',
            'dados' => ['pressao' => '165/110'],
            'status' => Alerta::STATUS_ATIVO,
        ], $attributes));
    }

    // ==========================================
    // TIER 1: AUTH & METRICS PAGE RENDERING
    // ==========================================

    public function test_metricas_page_requires_authentication(): void
    {
        $response = $this->get('/alertas/metricas');
        $response->assertRedirect('/login');
    }

    public function test_metricas_page_returns_200_and_renders_kpis_for_authenticated_user(): void
    {
        $patient = $this->createPatient();

        // 2 Active High alerts
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_ALTO, 'status' => Alerta::STATUS_ATIVO]);
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_ALTO, 'status' => Alerta::STATUS_ATIVO]);

        // 1 Resolved Alert
        $this->createAlerta($patient, [
            'nivel' => Alerta::NIVEL_MEDIO,
            'status' => Alerta::STATUS_RESOLVIDO,
            'resolvido_por' => $this->doctorUser->id,
            'nota_resolucao' => 'Resolvido com sucesso.',
            'resolvido_em' => Carbon::now(),
            'created_at' => Carbon::now()->subDays(2),
        ]);

        $response = $this->actingAs($this->doctorUser)->get('/alertas/metricas');

        $response->assertStatus(200);

        // Verify view data contains expected KPI calculations
        $response->assertViewHas('totalGestantes');
        $response->assertViewHas('totalAlertas');
        $response->assertViewHas('alertasAltosAtivos');
        $response->assertViewHas('taxaResolucao');
        $response->assertViewHas('tempoMedioResolucao');
    }

    public function test_metricas_page_provides_chart_datasets(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, ['tipo' => 'pressao_arterial_grave', 'nivel' => Alerta::NIVEL_ALTO]);
        $this->createAlerta($patient, ['tipo' => 'bcf_anormal', 'nivel' => Alerta::NIVEL_ALTO]);
        $this->createAlerta($patient, ['tipo' => 'vacinas_em_atraso', 'nivel' => Alerta::NIVEL_MEDIO]);

        $response = $this->actingAs($this->doctorUser)->get('/alertas/metricas');

        $response->assertStatus(200);

        // Verify chart datasets exist in view
        $response->assertViewHas('chartAlertasPorTipo');
        $response->assertViewHas('chartAlertasPorNivel');
        $response->assertViewHas('chartTaxaResolucao');
        $response->assertViewHas('chartDistribuicaoTempo');
    }

    // ==========================================
    // TIER 2: DATE RANGE FILTERING
    // ==========================================

    public function test_metricas_date_range_filter_restricts_calculations(): void
    {
        $patient = $this->createPatient();

        // Alert from last month (July)
        $this->createAlerta($patient, [
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'created_at' => Carbon::parse('2026-07-15 10:00:00'),
        ]);

        // Alert from this month (August)
        $this->createAlerta($patient, [
            'tipo' => 'bcf_anormal',
            'nivel' => Alerta::NIVEL_ALTO,
            'created_at' => Carbon::parse('2026-08-10 10:00:00'),
        ]);

        $response = $this->actingAs($this->doctorUser)->get('/alertas/metricas?data_inicio=2026-08-01&data_fim=2026-08-31');

        $response->assertStatus(200);
        $totalAlertas = $response->viewData('totalAlertas');
        $this->assertEquals(1, $totalAlertas);
    }

    // ==========================================
    // TIER 3 & 4: PDF REPORT EXPORT
    // ==========================================

    public function test_pdf_export_requires_authentication(): void
    {
        $response = $this->get('/alertas/metricas/export-pdf');
        $response->assertRedirect('/login');
    }

    public function test_pdf_export_returns_valid_pdf_response(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, [
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'status' => Alerta::STATUS_ATIVO,
        ]);
        $this->createAlerta($patient, [
            'tipo' => 'vacinas_em_atraso',
            'nivel' => Alerta::NIVEL_MEDIO,
            'status' => Alerta::STATUS_RESOLVIDO,
            'resolvido_por' => $this->doctorUser->id,
            'nota_resolucao' => 'Vacina administrada.',
            'resolvido_em' => Carbon::now(),
            'created_at' => Carbon::now()->subDay(),
        ]);

        $response = $this->actingAs($this->doctorUser)->get('/alertas/metricas/export-pdf');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Check PDF binary magic header %PDF
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_export_with_date_range_filter(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, [
            'created_at' => Carbon::parse('2026-08-10 12:00:00'),
        ]);

        $response = $this->actingAs($this->doctorUser)->get('/alertas/metricas/export-pdf?data_inicio=2026-08-01&data_fim=2026-08-31');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
