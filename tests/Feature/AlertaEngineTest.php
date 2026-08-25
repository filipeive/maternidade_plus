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

class AlertaEngineTest extends TestCase
{
    use RefreshDatabase;

    protected AlertaPrecoceService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlertaPrecoceService();

        $this->user = User::create([
            'name' => 'Dr. Teste',
            'email' => 'doutor@teste.mz',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    protected function criarGestante(array $atributos = []): Patient
    {
        static $biCounter = 1000;
        $biCounter++;

        return Patient::create(array_merge([
            'nome_completo' => 'Maria Moçambique ' . $biCounter,
            'data_nascimento' => '1995-05-15',
            'documento_bi' => '110100' . $biCounter . 'M',
            'contacto' => '+258841234567',
            'endereco' => 'Bairro Central, Maputo',
            'data_ultima_menstruacao' => now()->subWeeks(20)->format('Y-m-d'),
            'data_provavel_parto' => now()->addWeeks(20)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_GESTANTE,
            'numero_gestacoes' => 1,
            'numero_partos' => 0,
            'numero_abortos' => 0,
            'ativo' => true,
        ], $atributos));
    }

    /** @test */
    public function test_pressao_arterial_parser_handles_various_formats()
    {
        $this->assertEquals(['sistolica' => 120, 'diastolica' => 80], $this->service->parsePressaoArterial('120/80'));
        $this->assertEquals(['sistolica' => 140, 'diastolica' => 90], $this->service->parsePressaoArterial(' 140 / 90 '));
        $this->assertEquals(['sistolica' => 140, 'diastolica' => 90], $this->service->parsePressaoArterial('140x90'));
        $this->assertEquals(['sistolica' => 140, 'diastolica' => 90], $this->service->parsePressaoArterial('140 x 90'));
        $this->assertEquals(['sistolica' => 160, 'diastolica' => 110], $this->service->parsePressaoArterial('160-110'));
        $this->assertEquals(['sistolica' => 140, 'diastolica' => 90], $this->service->parsePressaoArterial('140/90 mmHg'));
        $this->assertEquals(['sistolica' => 130, 'diastolica' => 85], $this->service->parsePressaoArterial('PA: 130/85'));
        $this->assertNull($this->service->parsePressaoArterial(null));
        $this->assertNull($this->service->parsePressaoArterial(''));
        $this->assertNull($this->service->parsePressaoArterial('normal'));
        $this->assertNull($this->service->parsePressaoArterial('999/999')); // range fora do padrão fisiológico
    }

    /** @test */
    public function test_regra_1_pressao_arterial_elevada_e_grave()
    {
        Queue::fake();

        // 1. Pressão elevada (Médio: 140/90)
        $gestanteMedio = $this->criarGestante(['nome_completo' => 'Paciente PA Media']);
        Consultation::create([
            'patient_id' => $gestanteMedio->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '145/92',
            'status' => 'realizada',
        ]);

        $alertaMedio = Alerta::where('patient_id', $gestanteMedio->id)->first();
        $this->assertNotNull($alertaMedio);
        $this->assertEquals('pressao_arterial_alta', $alertaMedio->tipo);
        $this->assertEquals(Alerta::NIVEL_MEDIO, $alertaMedio->nivel);

        // 2. Pressão grave (Alto: >= 160/110)
        $gestanteAlto = $this->criarGestante(['nome_completo' => 'Paciente PA Grave']);
        Consultation::create([
            'patient_id' => $gestanteAlto->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '165/112 mmHg',
            'status' => 'realizada',
        ]);

        $alertaAlto = Alerta::where('patient_id', $gestanteAlto->id)->first();
        $this->assertNotNull($alertaAlto);
        $this->assertEquals('pressao_arterial_grave', $alertaAlto->tipo);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertaAlto->nivel);
    }

    /** @test */
    public function test_regra_2_bcf_anormal()
    {
        Queue::fake();

        // Bradicardia fetal (< 110 bpm)
        $gestante = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'batimentos_fetais' => 95,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);

        $alertaBcf = Alerta::where('patient_id', $gestante->id)->where('tipo', 'bcf_anormal')->first();
        $this->assertNotNull($alertaBcf);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertaBcf->nivel);
    }

    /** @test */
    public function test_regra_3_gestante_faltosa()
    {
        Queue::fake();

        // Sem consulta há mais de 30 dias
        $gestante = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'data_consulta' => now()->subDays(40),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);

        // Ao avaliar via serviço
        $this->service->avaliarPaciente($gestante);

        $alertaFaltosa = Alerta::where('patient_id', $gestante->id)->where('tipo', 'gestante_faltosa')->first();
        $this->assertNotNull($alertaFaltosa);
        $this->assertEquals(Alerta::NIVEL_MEDIO, $alertaFaltosa->nivel);
    }

    /** @test */
    public function test_regra_4_alto_risco_sem_seguimento()
    {
        Queue::fake();

        // Gestante de alto risco (ex: idade 40 anos + histórico diabetes)
        $gestante = $this->criarGestante([
            'data_nascimento' => '1984-01-01',
            'historico_medico' => 'diabetes gestacional prévia',
            'numero_abortos' => 2,
        ]);
        $this->assertEquals('Alto', $gestante->risco_gestacional);

        Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'data_consulta' => now()->subDays(35),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);

        // Avalia a gestante
        $this->service->avaliarPaciente($gestante);

        $alertaAltoRisco = Alerta::where('patient_id', $gestante->id)->where('tipo', 'alto_risco_sem_seguimento')->first();
        $this->assertNotNull($alertaAltoRisco);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertaAltoRisco->nivel);
    }

    /** @test */
    public function test_regra_5_vacinas_em_atraso()
    {
        Queue::fake();

        $gestante = $this->criarGestante();
        Vaccine::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'tipo_vacina' => 'tetanica',
            'data_administracao' => now()->subMonths(2),
            'proxima_dose' => now()->subDays(10), // Vencida
            'dose_numero' => 1,
            'local_aplicacao' => 'braco_esquerdo',
            'status' => 'pendente',
        ]);

        $alertas = $this->service->avaliarPaciente($gestante);
        $alertaVacina = Alerta::where('patient_id', $gestante->id)->where('tipo', 'vacinas_em_atraso')->first();
        $this->assertNotNull($alertaVacina);
        $this->assertEquals(Alerta::NIVEL_MEDIO, $alertaVacina->nivel);
    }

    /** @test */
    public function test_regra_6_exames_criticos_hiv_sifilis_anemia()
    {
        Queue::fake();

        $gestante = $this->criarGestante();
        $consultation = Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '1_trimestre',
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);

        Exam::create([
            'consultation_id' => $consultation->id,
            'tipo_exame' => 'teste_hiv',
            'resultado' => 'Reagente / Positivo',
            'status' => 'realizado',
            'data_solicitacao' => now()->subDays(5),
            'data_realizacao' => now()->subDays(2),
        ]);

        Exam::create([
            'consultation_id' => $consultation->id,
            'tipo_exame' => 'hemograma_completo',
            'resultado' => 'Hemoglobina 6.2 g/dL - Anemia grave microcítica',
            'status' => 'realizado',
            'data_solicitacao' => now()->subDays(5),
            'data_realizacao' => now()->subDays(2),
        ]);

        $this->service->avaliarPaciente($gestante);

        $alertasExames = Alerta::where('patient_id', $gestante->id)->where('tipo', 'exames_criticos')->get();
        $this->assertGreaterThanOrEqual(1, $alertasExames->count());
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertasExames->first()->nivel);
    }

    /** @test */
    public function test_regra_7_ganho_peso_anormal()
    {
        Queue::fake();

        // 1. Perda de peso
        $gestantePerda = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestantePerda->id,
            'user_id' => $this->user->id,
            'data_consulta' => now()->subDays(14),
            'tipo_consulta' => '1_trimestre',
            'peso' => 65.00,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        Consultation::create([
            'patient_id' => $gestantePerda->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'peso' => 62.50, // Perda de 2.5 kg
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);

        $alertaPeso = Alerta::where('patient_id', $gestantePerda->id)->where('tipo', 'ganho_peso_anormal')->first();
        $this->assertNotNull($alertaPeso);
        $this->assertEquals(Alerta::NIVEL_MEDIO, $alertaPeso->nivel);

        // 2. Ganho excessivo (> 2.0 kg/semana)
        $gestanteGanho = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestanteGanho->id,
            'user_id' => $this->user->id,
            'data_consulta' => now()->subDays(7),
            'tipo_consulta' => '2_trimestre',
            'peso' => 60.00,
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);
        Consultation::create([
            'patient_id' => $gestanteGanho->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'peso' => 63.50, // Ganho de 3.5 kg em 1 semana
            'pressao_arterial' => '120/80',
            'status' => 'realizada',
        ]);

        $alertaGanho = Alerta::where('patient_id', $gestanteGanho->id)->where('tipo', 'ganho_peso_anormal')->first();
        $this->assertNotNull($alertaGanho);
        $this->assertEquals(Alerta::NIVEL_MEDIO, $alertaGanho->nivel);
    }

    /** @test */
    public function test_regra_8_idade_gestacional_pos_termo()
    {
        Queue::fake();

        // 42 semanas de gestação
        $gestante = $this->criarGestante([
            'data_ultima_menstruacao' => now()->subWeeks(42)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_GESTANTE,
        ]);

        $alertas = $this->service->avaliarPaciente($gestante);
        $alertaPosTermo = collect($alertas)->firstWhere('tipo', 'idade_gestacional_pos_termo');
        $this->assertNotNull($alertaPosTermo);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertaPosTermo->nivel);
    }

    /** @test */
    public function test_regra_9_sangramento_com_e_sem_negacao()
    {
        Queue::fake();

        // Caso 1: Negação ("Sem sangramento", "Nega hemorragia") -> NÃO deve gerar alerta
        $this->assertFalse($this->service->temRelatoSangramento('Paciente em bom estado geral, sem sangramento vaginal.'));
        $this->assertFalse($this->service->temRelatoSangramento('Nega sangramento ou perdas líquidas.'));
        $this->assertFalse($this->service->temRelatoSangramento('Ausência de sangramento ativo.'));
        $this->assertFalse($this->service->temRelatoSangramento('Não refere sangramento ou cólicas.'));

        $gestanteSemSangramento = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestanteSemSangramento->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '120/80',
            'observacoes' => 'Utente nega sangramento e refere boa movimentação fetal.',
            'status' => 'realizada',
        ]);

        $alertaNaoEsperado = Alerta::where('patient_id', $gestanteSemSangramento->id)->where('tipo', 'sangramento_reportado')->first();
        $this->assertNull($alertaNaoEsperado);

        // Caso 2: Sangramento afirmativo presente -> DEVE gerar alerta ALTO
        $this->assertTrue($this->service->temRelatoSangramento('Paciente refere sangramento escuro moderado'));
        $this->assertTrue($this->service->temRelatoSangramento('Presença de hemorragia vaginal'));

        $gestanteComSangramento = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestanteComSangramento->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '120/80',
            'observacoes' => 'Gestante relata perda de sangue vivo e dor em baixo ventre.',
            'status' => 'realizada',
        ]);

        $alertaEsperado = Alerta::where('patient_id', $gestanteComSangramento->id)->where('tipo', 'sangramento_reportado')->first();
        $this->assertNotNull($alertaEsperado);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alertaEsperado->nivel);
    }

    /** @test */
    public function test_deduplicacao_nao_cria_alertas_ativos_duplicados()
    {
        Queue::fake();

        $gestante = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '165/110',
            'status' => 'realizada',
        ]);

        // Consulta já gerou 1 alerta via Observer
        $this->assertEquals(1, Alerta::where('patient_id', $gestante->id)->count());

        // Segunda avaliação não deve criar alerta duplicado
        $criados2 = $this->service->avaliarPaciente($gestante);
        $this->assertCount(0, $criados2);
        $this->assertEquals(1, Alerta::where('patient_id', $gestante->id)->count());
    }

    /** @test */
    public function test_workflow_transicao_status_e_audit_trail()
    {
        $gestante = $this->criarGestante();
        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA 160/110 mmHg',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        // 1. Mudar para em seguimento
        $alerta->marcarEmSeguimento($this->user, 'Iniciada medicação anti-hipertensiva');
        $this->assertEquals(Alerta::STATUS_EM_SEGUIMENTO, $alerta->fresh()->status);
        $this->assertEquals(1, $alerta->acoes()->count());
        $acao1 = $alerta->acoes()->first();
        $this->assertEquals('ativo', $acao1->de_status);
        $this->assertEquals('em_seguimento', $acao1->para_status);
        $this->assertEquals('Iniciada medicação anti-hipertensiva', $acao1->nota);
        $this->assertEquals($this->user->id, $acao1->user_id);

        // 2. Marcar como resolvido
        $alerta->marcarResolvido($this->user, 'PA controlada para 120/80 mmHg após tratamento');
        $alertaAtualizado = $alerta->fresh();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alertaAtualizado->status);
        $this->assertEquals($this->user->id, $alertaAtualizado->resolvido_por);
        $this->assertNotNull($alertaAtualizado->resolvido_em);
        $this->assertEquals('PA controlada para 120/80 mmHg após tratamento', $alertaAtualizado->nota_resolucao);
        $this->assertEquals(2, $alertaAtualizado->acoes()->count());
    }

    /** @test */
    public function test_command_artisan_alertas_avaliar()
    {
        Queue::fake();

        $this->criarGestante([
            'data_ultima_menstruacao' => now()->subWeeks(43)->format('Y-m-d'),
        ]);

        $this->artisan('alertas:avaliar')
            ->expectsOutput('Iniciando avaliação clínica de alertas precoces...')
            ->expectsOutput('Avaliação concluída com sucesso!')
            ->assertExitCode(0);

        $this->assertGreaterThanOrEqual(1, Alerta::count());
    }
}
