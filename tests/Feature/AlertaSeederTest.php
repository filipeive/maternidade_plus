<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\AlertaAcao;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaSeederTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // TIER 1 & 2: SEEDER EXECUTION & RULE COVERAGE
    // ==========================================

    public function test_database_seeder_runs_successfully_and_populates_alerts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $totalAlertas = Alerta::count();
        $this->assertGreaterThanOrEqual(16, $totalAlertas, 'Seeder must generate a rich demo dataset of alerts');
    }

    public function test_seed_data_covers_all_9_clinical_rule_types(): void
    {
        $this->seed(DatabaseSeeder::class);

        $tipos = Alerta::pluck('tipo')->unique()->toArray();

        // 1. Pressão arterial
        $this->assertTrue(
            in_array('pressao_arterial_grave', $tipos) || in_array('pressao_arterial_alta', $tipos),
            'Seed data must include Blood Pressure alerts'
        );

        // 2. BCF Anormal
        $this->assertContains('bcf_anormal', $tipos, 'Seed data must include Fetal Heart Rate alerts');

        // 3. Gestante faltosa / Consulta atrasada
        $this->assertTrue(
            in_array('gestante_sem_consulta', $tipos) || in_array('consulta_agendada_perdida', $tipos),
            'Seed data must include Missed Consultation alerts'
        );

        // 4. Alto risco sem seguimento
        $this->assertContains('alto_risco_sem_seguimento', $tipos, 'Seed data must include High Risk without Follow-up alerts');

        // 5. Vacinas em atraso
        $this->assertContains('vacinas_em_atraso', $tipos, 'Seed data must include Overdue Vaccine alerts');

        // 6. Exames críticos
        $this->assertTrue(
            in_array('exame_critico_hiv', $tipos) || in_array('exame_critico_sifilis', $tipos) || in_array('exame_critico_anemia', $tipos),
            'Seed data must include Critical Lab Exam alerts'
        );

        // 7. Ganho de peso anormal
        $this->assertContains('ganho_peso_anormal', $tipos, 'Seed data must include Abnormal Weight Gain/Loss alerts');

        // 8. Pós-termo
        $this->assertContains('pos_termo', $tipos, 'Seed data must include Post-Term Pregnancy alerts');

        // 9. Sangramento reportado
        $this->assertContains('sangramento_reportado', $tipos, 'Seed data must include Bleeding Reported alerts');
    }

    // ==========================================
    // TIER 1 & 2: SEVERITY DISTRIBUTION & AUDIT TRAIL
    // ==========================================

    public function test_seed_data_contains_expected_severity_distribution(): void
    {
        $this->seed(DatabaseSeeder::class);

        $altoCount = Alerta::where('nivel', Alerta::NIVEL_ALTO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count();
        $medioCount = Alerta::where('nivel', Alerta::NIVEL_MEDIO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count();
        $baixoCount = Alerta::where('nivel', Alerta::NIVEL_BAIXO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count();

        $this->assertGreaterThanOrEqual(5, $altoCount, 'Expected at least 5 active Alto alerts in seed dataset');
        $this->assertGreaterThanOrEqual(8, $medioCount, 'Expected at least 8 active Médio alerts in seed dataset');
        $this->assertGreaterThanOrEqual(3, $baixoCount, 'Expected at least 3 active Baixo alerts in seed dataset');
    }

    public function test_seed_data_contains_pre_resolved_alerts_with_audit_trail(): void
    {
        $this->seed(DatabaseSeeder::class);

        $resolvedAlerts = Alerta::where('status', Alerta::STATUS_RESOLVIDO)->get();
        $this->assertNotEmpty($resolvedAlerts, 'Seed data must include pre-resolved alerts for demo');

        foreach ($resolvedAlerts as $alerta) {
            $this->assertNotNull($alerta->resolvido_por, 'Resolved alert must have resolvido_por user ID');
            $this->assertNotNull($alerta->resolvido_em, 'Resolved alert must have resolvido_em timestamp');
            $this->assertNotEmpty($alerta->nota_resolucao, 'Resolved alert must have resolution note');
        }

        // Verify audit trail entries exist for resolved alerts
        $auditCount = AlertaAcao::count();
        $this->assertGreaterThan(0, $auditCount, 'Seed data must include alerta_acoes audit trail entries');
    }
}
