<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\AlertaAcao;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AlertaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $doctorUser;
    protected User $nurseUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Spatie roles and permissions
        $manageAlerts = Permission::firstOrCreate(['name' => 'manage_alerts', 'guard_name' => 'web']);
        $viewDashboard = Permission::firstOrCreate(['name' => 'view_dashboard', 'guard_name' => 'web']);
        $viewPatients = Permission::firstOrCreate(['name' => 'view_patients', 'guard_name' => 'web']);

        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $medicoRole = Role::firstOrCreate(['name' => 'Médico', 'guard_name' => 'web']);
        $enfermeiroRole = Role::firstOrCreate(['name' => 'Enfermeiro', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());
        $medicoRole->givePermissionTo([$manageAlerts, $viewDashboard, $viewPatients]);
        $enfermeiroRole->givePermissionTo([$viewDashboard, $viewPatients]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Teste',
            'email' => 'admin.teste@maternidade.mz',
        ]);
        $this->adminUser->assignRole('Administrador');

        $this->doctorUser = User::factory()->create([
            'name' => 'Dr. Médico Teste',
            'email' => 'medico.teste@maternidade.mz',
        ]);
        $this->doctorUser->assignRole('Médico');

        $this->nurseUser = User::factory()->create([
            'name' => 'Enf. Enfermeiro Teste',
            'email' => 'enfermeiro.teste@maternidade.mz',
        ]);
        $this->nurseUser->assignRole('Enfermeiro');
    }

    private function createPatient(string $nome = 'Ana Maluleque'): Patient
    {
        return Patient::create([
            'nome_completo' => $nome . ' ' . uniqid(),
            'data_nascimento' => '1998-05-15',
            'documento_bi' => '110100' . rand(100000, 999999) . 'F',
            'contacto' => '+25884' . rand(1000000, 9999999),
            'email' => 'ana.' . uniqid() . '@example.com',
            'endereco' => 'Matola, Bairro Fomento',
            'tipo_sanguineo' => 'B+',
            'data_ultima_menstruacao' => Carbon::now()->subWeeks(18)->format('Y-m-d'),
            'data_provavel_parto' => Carbon::now()->addWeeks(22)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_GESTANTE,
            'numero_gestacoes' => 2,
            'numero_partos' => 1,
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
            'mensagem' => 'Crise hipertensiva detectada.',
            'dados' => ['pressao' => '165/110'],
            'status' => Alerta::STATUS_ATIVO,
        ], $attributes));
    }

    // ==========================================
    // TIER 1: DASHBOARD & DEDICATED ALERTS PAGE
    // ==========================================

    public function test_dashboard_displays_top_15_alerts_sorted_by_severity(): void
    {
        $patient = $this->createPatient();

        // Create 6 Alto, 8 Médio, 6 Baixo alerts (total 20)
        for ($i = 0; $i < 6; $i++) {
            $this->createAlerta($patient, [
                'tipo' => 'bcf_anormal',
                'nivel' => Alerta::NIVEL_ALTO,
                'mensagem' => "Alerta Alto {$i}",
                'created_at' => Carbon::now()->subMinutes($i * 5),
            ]);
        }
        for ($i = 0; $i < 8; $i++) {
            $this->createAlerta($patient, [
                'tipo' => 'pressao_arterial_alta',
                'nivel' => Alerta::NIVEL_MEDIO,
                'mensagem' => "Alerta Médio {$i}",
                'created_at' => Carbon::now()->subMinutes($i * 5),
            ]);
        }
        for ($i = 0; $i < 6; $i++) {
            $this->createAlerta($patient, [
                'tipo' => 'vacinas_em_atraso',
                'nivel' => Alerta::NIVEL_BAIXO,
                'mensagem' => "Alerta Baixo {$i}",
                'created_at' => Carbon::now()->subMinutes($i * 5),
            ]);
        }

        $response = $this->actingAs($this->doctorUser)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Alerta Alto 0');
        $response->assertSee('Alerta Médio 0');
        // The top 15 should prioritize Alto and Medio over Baixo
        $response->assertDontSee('Alerta Baixo 5');
    }

    public function test_alertas_index_requires_authentication(): void
    {
        $response = $this->get('/alertas');
        $response->assertRedirect('/login');
    }

    public function test_alertas_index_returns_200_for_authenticated_users(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->get('/alertas');
        $response->assertStatus(200);
        $response->assertSee('Crise hipertensiva detectada');
    }

    public function test_alertas_filtering_by_level(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_ALTO, 'mensagem' => 'Urgência Hipertensiva']);
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_MEDIO, 'mensagem' => 'Vacina Pendente']);

        $responseAlto = $this->actingAs($this->doctorUser)->get('/alertas?nivel=' . Alerta::NIVEL_ALTO);
        $responseAlto->assertStatus(200);
        $responseAlto->assertSee('Urgência Hipertensiva');
        $responseAlto->assertDontSee('Vacina Pendente');

        $responseMedio = $this->actingAs($this->doctorUser)->get('/alertas?nivel=' . Alerta::NIVEL_MEDIO);
        $responseMedio->assertStatus(200);
        $responseMedio->assertSee('Vacina Pendente');
        $responseMedio->assertDontSee('Urgência Hipertensiva');
    }

    public function test_alertas_filtering_by_status(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, ['status' => Alerta::STATUS_ATIVO, 'mensagem' => 'Alerta Ativo Atual']);
        $this->createAlerta($patient, [
            'status' => Alerta::STATUS_RESOLVIDO,
            'mensagem' => 'Alerta Já Resolvido',
            'resolvido_por' => $this->doctorUser->id,
            'nota_resolucao' => 'Tratado.',
            'resolvido_em' => now(),
        ]);

        $responseAtivo = $this->actingAs($this->doctorUser)->get('/alertas?status=' . Alerta::STATUS_ATIVO);
        $responseAtivo->assertStatus(200);
        $responseAtivo->assertSee('Alerta Ativo Atual');
        $responseAtivo->assertDontSee('Alerta Já Resolvido');

        $responseResolvido = $this->actingAs($this->doctorUser)->get('/alertas?status=' . Alerta::STATUS_RESOLVIDO);
        $responseResolvido->assertStatus(200);
        $responseResolvido->assertSee('Alerta Já Resolvido');
        $responseResolvido->assertDontSee('Alerta Ativo Atual');
    }

    public function test_alertas_filtering_by_patient_name_search(): void
    {
        $patient1 = $this->createPatient('Beatriz Cossa');
        $patient2 = $this->createPatient('Zulmira Tembe');

        $this->createAlerta($patient1, ['mensagem' => 'Alerta da Beatriz']);
        $this->createAlerta($patient2, ['mensagem' => 'Alerta da Zulmira']);

        $response = $this->actingAs($this->doctorUser)->get('/alertas?search=Beatriz');
        $response->assertStatus(200);
        $response->assertSee('Alerta da Beatriz');
        $response->assertDontSee('Alerta da Zulmira');
    }

    public function test_alertas_filtering_by_date_range(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, [
            'mensagem' => 'Alerta Antigo Julho',
            'created_at' => Carbon::parse('2026-07-10 10:00:00'),
        ]);
        $this->createAlerta($patient, [
            'mensagem' => 'Alerta Recente Agosto',
            'created_at' => Carbon::parse('2026-08-15 10:00:00'),
        ]);

        $response = $this->actingAs($this->doctorUser)->get('/alertas?data_inicio=2026-08-01&data_fim=2026-08-30');
        $response->assertStatus(200);
        $response->assertSee('Alerta Recente Agosto');
        $response->assertDontSee('Alerta Antigo Julho');
    }

    public function test_alertas_filtering_by_type(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, ['tipo' => 'bcf_anormal', 'mensagem' => 'Batimento Fetal Bradicárdico']);
        $this->createAlerta($patient, ['tipo' => 'pos_termo', 'mensagem' => 'Gravidez Prolongada Pós Termo']);

        $response = $this->actingAs($this->doctorUser)->get('/alertas?tipo=bcf_anormal');
        $response->assertStatus(200);
        $response->assertSee('Batimento Fetal Bradicárdico');
        $response->assertDontSee('Gravidez Prolongada Pós Termo');
    }

    // ==========================================
    // TIER 1: RESOLUTION WORKFLOW & AUDIT TRAIL
    // ==========================================

    public function test_resolution_transition_to_em_seguimento_creates_audit_entry(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_EM_SEGUIMENTO,
            'nota' => 'Contacto telefónico realizado com a paciente para reagendamento.',
        ]);

        $response->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_EM_SEGUIMENTO, $alerta->status);

        $this->assertDatabaseHas('alerta_acoes', [
            'alerta_id' => $alerta->id,
            'user_id' => $this->doctorUser->id,
            'status_anterior' => Alerta::STATUS_ATIVO,
            'status_novo' => Alerta::STATUS_EM_SEGUIMENTO,
            'nota' => 'Contacto telefónico realizado com a paciente para reagendamento.',
        ]);
    }

    public function test_resolution_transition_to_resolvido_sets_fields_and_audit(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => 'Paciente compareceu ao centro de saúde, PA normalizada para 120/80 após medicação.',
        ]);

        $response->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alerta->status);
        $this->assertEquals($this->doctorUser->id, $alerta->resolvido_por);
        $this->assertNotNull($alerta->resolvido_em);
        $this->assertEquals('Paciente compareceu ao centro de saúde, PA normalizada para 120/80 após medicação.', $alerta->nota_resolucao);

        $this->assertDatabaseHas('alerta_acoes', [
            'alerta_id' => $alerta->id,
            'user_id' => $this->doctorUser->id,
            'status_anterior' => Alerta::STATUS_ATIVO,
            'status_novo' => Alerta::STATUS_RESOLVIDO,
        ]);
    }

    public function test_resolution_transition_to_ignorado(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_IGNORADO,
            'nota' => 'Falso positivo verificado por aferição manual repetida.',
        ]);

        $response->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_IGNORADO, $alerta->status);

        $this->assertDatabaseHas('alerta_acoes', [
            'alerta_id' => $alerta->id,
            'user_id' => $this->doctorUser->id,
            'status_novo' => Alerta::STATUS_IGNORADO,
        ]);
    }

    // ==========================================
    // TIER 2: VALIDATION & RBAC PERMISSIONS
    // ==========================================

    public function test_resolution_requires_note_validation(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => '', // Empty note
        ]);

        $response->assertSessionHasErrors(['nota']);
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->status);
    }

    public function test_resolution_note_max_characters_validation(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => str_repeat('A', 1005), // Exceeds 1000 chars
        ]);

        $response->assertSessionHasErrors(['nota']);
    }

    public function test_administrador_can_resolve_alert(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->adminUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => 'Resolução efetuada pelo Administrador.',
        ]);

        $response->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alerta->status);
    }

    public function test_medico_can_resolve_alert(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => 'Resolução efetuada pelo Médico.',
        ]);

        $response->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alerta->status);
    }

    public function test_enfermeiro_can_view_alertas_list(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient);

        $response = $this->actingAs($this->nurseUser)->get('/alertas');
        $response->assertStatus(200);
    }

    public function test_enfermeiro_is_forbidden_from_resolving_alert(): void
    {
        $patient = $this->createPatient();
        $alerta = $this->createAlerta($patient);

        $response = $this->actingAs($this->nurseUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => 'Tentativa de resolução por enfermeiro sem permissão.',
        ]);

        $response->assertStatus(403);
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_ATIVO, $alerta->status);
    }

    // ==========================================
    // TIER 1 & 3: INTEGRATIONS & NAVIGATION BADGE
    // ==========================================

    public function test_patient_show_displays_active_alerts_and_danger_banner(): void
    {
        $patient = $this->createPatient('Graça Machel');
        $this->createAlerta($patient, [
            'tipo' => 'sangramento_reportado',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'Sangramento vaginal activo detectado.',
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('patients.show', $patient));

        $response->assertStatus(200);
        $response->assertSee('Sangramento vaginal activo detectado');
        $response->assertSee('Alto');
    }

    public function test_navigation_badge_renders_active_high_severity_alert_count(): void
    {
        $patient = $this->createPatient();
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_ALTO, 'status' => Alerta::STATUS_ATIVO]);
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_ALTO, 'status' => Alerta::STATUS_ATIVO]);
        $this->createAlerta($patient, ['nivel' => Alerta::NIVEL_MEDIO, 'status' => Alerta::STATUS_ATIVO]); // Medium shouldn't count in high badge

        $response = $this->actingAs($this->doctorUser)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('alertasAltosCount', 2);
    }

    // ==========================================
    // TIER 4: END-TO-END MANAGEMENT JOURNEY
    // ==========================================

    public function test_end_to_end_alert_lifecycle_journey(): void
    {
        $patient = $this->createPatient('Teresa Amisse');
        $alerta = $this->createAlerta($patient, [
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'Crise hipertensiva grave detectada.',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        // Step 1: Doctor checks dashboard and sees the high alert
        $dashResponse = $this->actingAs($this->doctorUser)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Crise hipertensiva grave detectada');

        // Step 2: Doctor places alert in follow-up
        $transition1 = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_EM_SEGUIMENTO,
            'nota' => 'Paciente contactada por telefone, agendada consulta de urgência para hoje.',
        ]);
        $transition1->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_EM_SEGUIMENTO, $alerta->status);

        // Step 3: Doctor resolves alert after successful patient visit
        $transition2 = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/transitar", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => 'Paciente atendida, iniciada hidralazina IV, PA estabilizada em 125/85 mmHg.',
        ]);
        $transition2->assertRedirect();
        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alerta->status);
        $this->assertEquals($this->doctorUser->id, $alerta->resolvido_por);

        // Step 4: Verify complete audit trail exists in database
        $this->assertCount(2, $alerta->acoes);
        $this->assertDatabaseHas('alerta_acoes', [
            'alerta_id' => $alerta->id,
            'status_anterior' => Alerta::STATUS_ATIVO,
            'status_novo' => Alerta::STATUS_EM_SEGUIMENTO,
        ]);
        $this->assertDatabaseHas('alerta_acoes', [
            'alerta_id' => $alerta->id,
            'status_anterior' => Alerta::STATUS_EM_SEGUIMENTO,
            'status_novo' => Alerta::STATUS_RESOLVIDO,
        ]);
    }
}
