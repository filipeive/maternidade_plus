<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\AlertaAcao;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\User;
use App\Models\Vaccine;
use App\Services\AlertaPrecoceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AlertaAdversarialTest extends TestCase
{
    use RefreshDatabase;

    protected AlertaPrecoceService $service;
    protected User $adminUser;
    protected User $medicoUser;
    protected User $enfermeiroUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlertaPrecoceService();

        // Seed roles & permissions
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->adminUser = User::where('email', 'admin@maternidade.mz')->first();
        $this->medicoUser = User::where('email', 'medico@maternidade.mz')->first();

        $this->enfermeiroUser = User::create([
            'name' => 'Enfermeira Ana',
            'email' => 'enfermeira.ana@maternidade.mz',
            'password' => bcrypt('enfermeira123'),
            'email_verified_at' => now(),
        ]);
        $this->enfermeiroUser->assignRole('Enfermeiro');
    }

    protected function criarGestante(array $atributos = []): Patient
    {
        static $seq = 5000;
        $seq++;

        return Patient::create(array_merge([
            'nome_completo' => 'Paciente Teste ' . $seq,
            'data_nascimento' => '1996-03-20',
            'documento_bi' => '120200' . $seq . 'F',
            'contacto' => '+25884999' . sprintf('%04d', $seq % 10000),
            'endereco' => 'Bairro Chamanculo, Maputo',
            'data_ultima_menstruacao' => now()->subWeeks(20)->format('Y-m-d'),
            'data_provavel_parto' => now()->addWeeks(20)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_GESTANTE,
            'numero_gestacoes' => 1,
            'numero_partos' => 0,
            'numero_abortos' => 0,
            'ativo' => true,
        ], $atributos));
    }

    // =========================================================================
    // 1. ADVERSARIAL STRESS TESTING: parsePressaoArterial()
    // =========================================================================

    /** @test */
    public function test_adversarial_parse_pressao_arterial_valid_edge_variations()
    {
        $validCases = [
            '120/80' => ['sistolica' => 120, 'diastolica' => 80],
            '140/90' => ['sistolica' => 140, 'diastolica' => 90],
            '160/110' => ['sistolica' => 160, 'diastolica' => 110],
            '  140   /   90  ' => ['sistolica' => 140, 'diastolica' => 90],
            "120\t/\t80" => ['sistolica' => 120, 'diastolica' => 80],
            "130\n/\n85" => ['sistolica' => 130, 'diastolica' => 85],
            '140x90' => ['sistolica' => 140, 'diastolica' => 90],
            '140X90' => ['sistolica' => 140, 'diastolica' => 90],
            '140 x 90' => ['sistolica' => 140, 'diastolica' => 90],
            '160-110' => ['sistolica' => 160, 'diastolica' => 110],
            '160 - 110' => ['sistolica' => 160, 'diastolica' => 110],
            '130:85' => ['sistolica' => 130, 'diastolica' => 85],
            '130 : 85' => ['sistolica' => 130, 'diastolica' => 85],
            '120 80' => ['sistolica' => 120, 'diastolica' => 80],
            '120    80' => ['sistolica' => 120, 'diastolica' => 80],
            'PA: 120/80' => ['sistolica' => 120, 'diastolica' => 80],
            'pa: 140 x 90 mmHg' => ['sistolica' => 140, 'diastolica' => 90],
            'PA 160/110 MMHG' => ['sistolica' => 160, 'diastolica' => 110],
            '120/80 mmHg' => ['sistolica' => 120, 'diastolica' => 80],
            '50/30' => ['sistolica' => 50, 'diastolica' => 30], // Min physiological limits
            '300/200' => ['sistolica' => 300, 'diastolica' => 200], // Max physiological limits
        ];

        foreach ($validCases as $input => $expected) {
            $parsed = $this->service->parsePressaoArterial($input);
            $this->assertNotNull($parsed, "Failed to parse valid input: '{$input}'");
            $this->assertEquals($expected['sistolica'], $parsed['sistolica'], "Systolic mismatch for '{$input}'");
            $this->assertEquals($expected['diastolica'], $parsed['diastolica'], "Diastolic mismatch for '{$input}'");
        }
    }

    /** @test */
    public function test_adversarial_parse_pressao_arterial_invalid_and_malicious_inputs()
    {
        $invalidCases = [
            null,
            '',
            '    ',
            "\t\n\r",
            'normal',
            'estavel',
            'sem alteracoes',
            '120',
            '/80',
            '120/',
            'PA:',
            'mmHg',
            '49/80',      // Sistolica < 50
            '301/80',     // Sistolica > 300
            '120/29',     // Diastolica < 30
            '120/201',    // Diastolica > 200
            '999/999',    // Out of physiological range
            '0/0',        // Zeroes
            '12/8',       // 1-digit
            'abc/def',
            '<script>alert("xss")</script>',
            "' OR 1=1 --",
            '{{ 7*7 }}',
            str_repeat('A', 5000), // Buffer overload attempt
        ];

        foreach ($invalidCases as $input) {
            $parsed = $this->service->parsePressaoArterial($input);
            $this->assertNull($parsed, "Expected null for invalid input: '" . substr((string)$input, 0, 50) . "'");
        }
    }

    // =========================================================================
    // 2. CLINICAL RULE BOUNDARY & EDGE CASES
    // =========================================================================

    /** @test */
    public function test_bcf_boundary_values_109_110_160_161()
    {
        Queue::fake();

        // BCF = 109 bpm -> ALERTO ALTO (Bradicardia Fetal)
        $g109 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g109->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'batimentos_fetais' => 109,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $alerta109 = Alerta::where('patient_id', $g109->id)->where('tipo', 'bcf_anormal')->first();
        $this->assertNotNull($alerta109, "BCF 109 should trigger bcf_anormal alert");
        $this->assertEquals(Alerta::NIVEL_ALTO, $alerta109->nivel);

        // BCF = 110 bpm -> NORMAL (Lower boundary limit)
        $g110 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g110->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'batimentos_fetais' => 110,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $alerta110 = Alerta::where('patient_id', $g110->id)->where('tipo', 'bcf_anormal')->first();
        $this->assertNull($alerta110, "BCF 110 is normal lower bound and should NOT trigger alert");

        // BCF = 160 bpm -> NORMAL (Upper boundary limit)
        $g160 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g160->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'batimentos_fetais' => 160,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $alerta160 = Alerta::where('patient_id', $g160->id)->where('tipo', 'bcf_anormal')->first();
        $this->assertNull($alerta160, "BCF 160 is normal upper bound and should NOT trigger alert");

        // BCF = 161 bpm -> ALERTO ALTO (Taquicardia Fetal)
        $g161 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g161->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'batimentos_fetais' => 161,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $alerta161 = Alerta::where('patient_id', $g161->id)->where('tipo', 'bcf_anormal')->first();
        $this->assertNotNull($alerta161, "BCF 161 should trigger bcf_anormal alert");
        $this->assertEquals(Alerta::NIVEL_ALTO, $alerta161->nivel);
    }

    /** @test */
    public function test_consultation_days_boundaries_29_30_31_and_risk_differentiation()
    {
        Queue::fake();

        // 1. Regular risk patient (não é alto risco)
        // 29 days ago -> No alert
        $g29 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g29->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now()->subDays(29),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $this->service->avaliarPaciente($g29);
        $this->assertNull(Alerta::where('patient_id', $g29->id)->where('tipo', 'gestante_faltosa')->first());

        // 30 days ago -> No alert (rule requires > 30 days)
        $g30 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g30->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now()->subDays(30),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $this->service->avaliarPaciente($g30);
        $this->assertNull(Alerta::where('patient_id', $g30->id)->where('tipo', 'gestante_faltosa')->first());

        // 31 days ago -> Gestante Faltosa (Nível Médio)
        $g31 = $this->criarGestante();
        Consultation::create([
            'patient_id' => $g31->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now()->subDays(31),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $this->service->avaliarPaciente($g31);
        $alerta31 = Alerta::where('patient_id', $g31->id)->where('tipo', 'gestante_faltosa')->first();
        $this->assertNotNull($alerta31, "Regular patient 31 days without consultation must trigger gestante_faltosa");
        $this->assertEquals(Alerta::NIVEL_MEDIO, $alerta31->nivel);

        // 2. High risk patient (risco_gestacional === 'Alto')
        // 30 days ago -> No alert
        $gAlto30 = $this->criarGestante([
            'data_nascimento' => '1980-01-01', // Idade > 35 -> Alto risco
            'historico_medico' => 'hipertensao cronica',
        ]);
        $this->assertEquals('Alto', $gAlto30->risco_gestacional);
        Consultation::create([
            'patient_id' => $gAlto30->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now()->subDays(30),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $this->service->avaliarPaciente($gAlto30);
        $this->assertNull(Alerta::where('patient_id', $gAlto30->id)->where('tipo', 'alto_risco_sem_seguimento')->first());

        // 31 days ago -> Alto Risco Sem Seguimento (Nível Alto)
        $gAlto31 = $this->criarGestante([
            'data_nascimento' => '1980-01-01',
            'historico_medico' => 'hipertensao cronica',
        ]);
        Consultation::create([
            'patient_id' => $gAlto31->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now()->subDays(31),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        $this->service->avaliarPaciente($gAlto31);
        $alertaAlto31 = Alerta::where('patient_id', $gAlto31->id)->where('tipo', 'alto_risco_sem_seguimento')->first();
        $this->assertNotNull($alertaAlto31, "High risk patient 31 days without consultation must trigger alto_risco_sem_seguimento");
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertaAlto31->nivel);

        // High risk patient should NOT duplicate as gestante_faltosa
        $this->assertNull(Alerta::where('patient_id', $gAlto31->id)->where('tipo', 'gestante_faltosa')->first());
    }

    /** @test */
    public function test_gestational_age_post_term_boundaries_40_41_42_weeks()
    {
        Queue::fake();

        // 40 weeks -> No alert
        $g40 = $this->criarGestante(['data_ultima_menstruacao' => now()->subWeeks(40)->format('Y-m-d')]);
        $alertas40 = $this->service->avaliarPaciente($g40);
        $this->assertNull(collect($alertas40)->firstWhere('tipo', 'idade_gestacional_pos_termo'));

        // 41 weeks -> No alert (> 41 required)
        $g41 = $this->criarGestante(['data_ultima_menstruacao' => now()->subWeeks(41)->format('Y-m-d')]);
        $alertas41 = $this->service->avaliarPaciente($g41);
        $this->assertNull(collect($alertas41)->firstWhere('tipo', 'idade_gestacional_pos_termo'));

        // 42 weeks -> Alert (Nível Alto)
        $g42 = $this->criarGestante(['data_ultima_menstruacao' => now()->subWeeks(42)->format('Y-m-d')]);
        $alertas42 = $this->service->avaliarPaciente($g42);
        $alerta42 = collect($alertas42)->firstWhere('tipo', 'idade_gestacional_pos_termo');
        $this->assertNotNull($alerta42);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alerta42->nivel);

        // Non pregnant patient with old DUM (e.g. status pos_parto) -> No alert
        $gPosParto = $this->criarGestante([
            'data_ultima_menstruacao' => now()->subWeeks(45)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_POS_PARTO,
        ]);
        $alertasPosParto = $this->service->avaliarPaciente($gPosParto);
        $this->assertCount(0, $alertasPosParto);
    }

    /** @test */
    public function test_bleeding_negation_and_affirmation_detection()
    {
        $supportedNegationCases = [
            'Sem sangramento',
            'sem sangramento',
            'SEM SANGRAMENTO',
            'Nega hemorragia',
            'nega hemorragia',
            'NEGA HEMORRAGIA',
            'Paciente nega sangramento vaginal.',
            'Ausência de sangramento activo.',
            'Não refere sangramento ou corrimento.',
            'Sem queixa de sangramento.',
            'Não relata sangue.',
            'Não apresenta hemorragia.',
            'Nega qualquer tipo de sangramento.',
            'Ausência de perda hemática.',
        ];

        foreach ($supportedNegationCases as $phrase) {
            $this->assertFalse(
                $this->service->temRelatoSangramento($phrase),
                "False positive detected: Phrase '{$phrase}' was incorrectly recognized as active bleeding."
            );
        }

        $affirmativeCases = [
            'Paciente relata sangramento abundante em casa.',
            'Perda hemática observada no exame físico.',
            'Episódio agudo de hemorragia vaginal.',
            'Presença de sangue vivo na vulva.',
            'Metrorragia persistente há 2 dias.',
            'Nega dor de cabeça, mas refere sangramento activo.',
            'Sem febre. Sangramento escuro moderado.',
        ];

        foreach ($affirmativeCases as $phrase) {
            $this->assertTrue(
                $this->service->temRelatoSangramento($phrase),
                "False negative detected: Phrase '{$phrase}' was NOT recognized as active bleeding."
            );
        }
    }

    // =========================================================================
    // 3. CONCURRENT & MULTIPLE ALERT GENERATION AND DEDUPLICATION
    // =========================================================================

    /** @test */
    public function test_multiple_distinct_alerts_generated_and_deduplication_integrity()
    {
        Queue::fake();

        // Create patient with 4 simultaneous risk factors:
        // 1. PA Grave (170/115)
        // 2. BCF Taquicardia (175)
        // 3. Vacinas em atraso
        // 4. Exame HIV+
        $gestante = $this->criarGestante([
            'data_nascimento' => '1995-01-01',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->medicoUser->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '170/115',
            'batimentos_fetais' => 175,
            'status' => 'realizada',
        ]);

        Vaccine::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->medicoUser->id,
            'tipo_vacina' => 'tetanica',
            'data_administracao' => now()->subMonths(3),
            'proxima_dose' => now()->subDays(15),
            'dose_numero' => 1,
            'local_aplicacao' => 'braco_esquerdo',
            'status' => 'pendente',
        ]);

        Exam::create([
            'consultation_id' => $consultation->id,
            'tipo_exame' => 'teste_hiv',
            'resultado' => 'Reagente',
            'status' => 'realizado',
            'data_solicitacao' => now()->subDays(3),
            'data_realizacao' => now()->subDays(1),
        ]);

        // Evaluate
        $criados = $this->service->avaliarPaciente($gestante);
        
        $tiposCriados = Alerta::where('patient_id', $gestante->id)->pluck('tipo')->toArray();
        $this->assertContains('pressao_arterial_grave', $tiposCriados);
        $this->assertContains('bcf_anormal', $tiposCriados);
        $this->assertContains('vacinas_em_atraso', $tiposCriados);
        $this->assertContains('exames_criticos', $tiposCriados);

        $countInicial = Alerta::where('patient_id', $gestante->id)->count();
        $this->assertEquals(4, $countInicial);

        // Re-evaluating 5 times consecutively must NOT duplicate any alert
        for ($i = 0; $i < 5; $i++) {
            $novos = $this->service->avaliarPaciente($gestante);
            $this->assertCount(0, $novos);
            $this->assertEquals(4, Alerta::where('patient_id', $gestante->id)->count());
        }

        // Move one alert to em_seguimento: must still NOT duplicate
        $alertaPa = Alerta::where('patient_id', $gestante->id)->where('tipo', 'pressao_arterial_grave')->first();
        $alertaPa->marcarEmSeguimento($this->medicoUser, 'Paciente internada na enfermaria');
        $this->assertEquals('em_seguimento', $alertaPa->fresh()->status);

        $novosAposSeguimento = $this->service->avaliarPaciente($gestante);
        $this->assertCount(0, $novosAposSeguimento);
        $this->assertEquals(4, Alerta::where('patient_id', $gestante->id)->count());

        // Resolve one alert: if clinical trigger persists, a new active alert is allowed
        $alertaBcf = Alerta::where('patient_id', $gestante->id)->where('tipo', 'bcf_anormal')->first();
        $alertaBcf->marcarResolvido($this->medicoUser, 'Reavaliação solicitada');
        $this->assertEquals('resolvido', $alertaBcf->fresh()->status);

        $novosAposResolucao = $this->service->avaliarPaciente($gestante);
        $this->assertCount(1, $novosAposResolucao);
        $this->assertEquals('bcf_anormal', $novosAposResolucao[0]->tipo);
        $this->assertEquals(5, Alerta::where('patient_id', $gestante->id)->count());
    }

    // =========================================================================
    // 4. RBAC & PERMISSION BYPASS ATTEMPTS
    // =========================================================================

    /** @test */
    public function test_unauthenticated_user_cannot_access_or_resolve_alerts()
    {
        $gestante = $this->criarGestante();
        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA 170/110 mmHg',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        // 1. GET /alertas -> Redirect 302 to login
        $responseIndex = $this->get('/alertas');
        $responseIndex->assertRedirect('/login');

        // 2. POST /alertas/{id}/resolver -> Redirect 302 to login
        $responseResolve = $this->post("/alertas/{$alerta->id}/resolver", [
            'status' => 'resolvido',
            'nota' => 'Tentativa não autenticada',
        ]);
        $responseResolve->assertRedirect('/login');
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->fresh()->status);
    }

    /** @test */
    public function test_enfermeiro_is_strictly_forbidden_from_resolving_or_transitioning_alerts()
    {
        $gestante = $this->criarGestante();
        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'pressao_arterial_alta',
            'nivel' => Alerta::NIVEL_MEDIO,
            'mensagem' => 'PA 145/95 mmHg',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        // Enfermeiro CAN view alertas
        $responseView = $this->actingAs($this->enfermeiroUser)->get('/alertas');
        $responseView->assertStatus(200);

        // Enfermeiro CANNOT resolve alert -> 403 Forbidden
        $responseResolve = $this->actingAs($this->enfermeiroUser)->post("/alertas/{$alerta->id}/resolver", [
            'status' => 'resolvido',
            'nota' => 'Tentativa de resolução por enfermeiro',
        ]);
        $responseResolve->assertStatus(403);
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->fresh()->status);

        // Enfermeiro CANNOT transition alert -> 403 Forbidden
        $responseTransitar = $this->actingAs($this->enfermeiroUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => 'em_seguimento',
            'nota' => 'Tentativa de seguimento por enfermeiro',
        ]);
        $responseTransitar->assertStatus(403);
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->fresh()->status);
    }

    /** @test */
    public function test_resolution_input_validation_adversarial_cases()
    {
        $gestante = $this->criarGestante();
        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA 160/110 mmHg',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        // 1. Invalid status value
        $responseInvalidStatus = $this->actingAs($this->medicoUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => 'hacked_status',
            'nota' => 'Nota válida para status inválido',
        ]);
        $responseInvalidStatus->assertSessionHasErrors('status');
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->fresh()->status);

        // 2. Missing note
        $responseMissingNote = $this->actingAs($this->medicoUser)->post("/alertas/{$alerta->id}/resolver", [
            'status' => 'resolvido',
            'nota' => '',
        ]);
        $responseMissingNote->assertSessionHasErrors('nota');
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->fresh()->status);

        // 3. Overly long note (> 1000 characters)
        $responseLongNote = $this->actingAs($this->medicoUser)->post("/alertas/{$alerta->id}/resolver", [
            'status' => 'resolvido',
            'nota' => str_repeat('X', 1001),
        ]);
        $responseLongNote->assertSessionHasErrors('nota');
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->fresh()->status);

        // 4. Valid resolution by Doctor succeeds
        $responseValid = $this->actingAs($this->medicoUser)->post("/alertas/{$alerta->id}/resolver", [
            'status' => 'resolvido',
            'nota' => 'Conduta médica: administrado anti-hipertensivo oral. PA estabilizada.',
        ]);
        $responseValid->assertSessionHasNoErrors();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alerta->fresh()->status);
        $this->assertEquals($this->medicoUser->id, $alerta->fresh()->resolvido_por);
    }

    // =========================================================================
    // 5. DATE RANGE FILTERING ON METRICS & PDF EXPORT
    // =========================================================================

    /** @test */
    public function test_metrics_and_pdf_export_handle_inverted_and_extreme_date_ranges()
    {
        $gestante = $this->criarGestante();
        Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA 160/110 mmHg',
            'status' => Alerta::STATUS_RESOLVIDO,
            'resolvido_por' => $this->medicoUser->id,
            'nota_resolucao' => 'Tratada',
            'resolvido_em' => now(),
            'created_at' => now()->subDays(2),
        ]);

        // 1. Inverted date range (Start date in future, End date in past)
        $responseInvertedMetrics = $this->actingAs($this->adminUser)->get('/alertas/metricas?data_inicio=2026-12-31&data_fim=2026-01-01');
        $responseInvertedMetrics->assertStatus(200);
        $responseInvertedMetrics->assertViewHas('totalAlertas', 0);
        $responseInvertedMetrics->assertViewHas('taxaResolucao', 0.0);
        $responseInvertedMetrics->assertViewHas('tempoMedioResolucao', 0.0);

        // PDF export with inverted dates must also return 200 without division by zero
        $responseInvertedPdf = $this->actingAs($this->adminUser)->get('/alertas/metricas/export-pdf?data_inicio=2026-12-31&data_fim=2026-01-01');
        $responseInvertedPdf->assertStatus(200);
        $this->assertEquals('application/pdf', $responseInvertedPdf->headers->get('Content-Type'));

        // 2. Far future date filter (0 alerts matching)
        $responseFutureMetrics = $this->actingAs($this->adminUser)->get('/alertas/metricas?data_inicio=2099-01-01&data_fim=2099-12-31');
        $responseFutureMetrics->assertStatus(200);
        $responseFutureMetrics->assertViewHas('totalAlertas', 0);

        // 3. Single day date range
        $today = now()->format('Y-m-d');
        $responseSingleDay = $this->actingAs($this->adminUser)->get("/alertas/metricas?data_inicio={$today}&data_fim={$today}");
        $responseSingleDay->assertStatus(200);
    }
}
