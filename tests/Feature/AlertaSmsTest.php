<?php

namespace Tests\Feature;

use App\Jobs\SendAlertSmsJob;
use App\Models\Alerta;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\AlertaPrecoceService;
use App\Services\SmsNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AlertaSmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Dr. Teste',
            'email' => 'sms_teste@maternidade.mz',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    protected function criarGestante(array $atributos = []): Patient
    {
        return Patient::create(array_merge([
            'nome_completo' => 'Joana Ernesto Macamo',
            'data_nascimento' => '1998-08-20',
            'documento_bi' => '110100456789J',
            'contacto' => '+258849876543',
            'endereco' => 'Bairro Maxaquene, Maputo',
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
    public function test_criacao_alerta_alto_dispara_job_sms()
    {
        Queue::fake();

        $gestante = $this->criarGestante();
        Consultation::create([
            'patient_id' => $gestante->id,
            'user_id' => $this->user->id,
            'data_consulta' => now(),
            'tipo_consulta' => '2_trimestre',
            'pressao_arterial' => '170/115', // Nível alto
            'status' => 'realizada',
        ]);

        $alerta = Alerta::where('patient_id', $gestante->id)->first();

        $this->assertNotNull($alerta);
        $this->assertEquals(Alerta::NIVEL_ALTO, $alerta->nivel);

        Queue::assertPushed(SendAlertSmsJob::class, function ($job) use ($alerta) {
            return $job->alerta->id === $alerta->id;
        });
    }

    /** @test */
    public function test_sms_notification_service_envia_requisicao_e_grava_log_sucesso()
    {
        Http::fake([
            'https://api.httpsms.com/v1/messages/send' => Http::response([
                'status' => 'success',
                'data' => ['id' => 'msg_12345', 'status' => 'delivered']
            ], 200),
        ]);

        $gestante = $this->criarGestante(['nome_completo' => 'Amélia Mondlane']);
        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA 160/110 mmHg',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        $smsService = new SmsNotificationService();
        $enviado = $smsService->sendHighRiskAlertSms($alerta);

        $this->assertTrue($enviado);

        // Verifica que a chamada HTTP foi feita com payload e headers corretos
        Http::assertSent(function ($request) {
            $data = $request->data();
            return str_contains($data['content'], 'Sra. Amélia') &&
                   !str_contains($data['content'], 'PA 160/110') && // Sem vazar diagnóstico
                   $data['to'] === '+258849876543';
        });

        // Verifica registro em sms_logs
        $this->assertDatabaseHas('sms_logs', [
            'patient_id' => $gestante->id,
            'alerta_id' => $alerta->id,
            'status' => 'enviado',
            'telefone' => '+258849876543',
        ]);
    }

    /** @test */
    public function test_sms_notification_service_trata_falha_de_api_sem_crash()
    {
        Http::fake([
            'https://api.httpsms.com/v1/messages/send' => Http::response([
                'error' => 'Invalid API key or balance exhausted'
            ], 401),
        ]);

        $gestante = $this->criarGestante();
        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'bcf_anormal',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'BCF 90 bpm',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        $smsService = new SmsNotificationService();
        $enviado = $smsService->sendHighRiskAlertSms($alerta);

        $this->assertFalse($enviado);

        // Registro em sms_logs como falha
        $this->assertDatabaseHas('sms_logs', [
            'patient_id' => $gestante->id,
            'alerta_id' => $alerta->id,
            'status' => 'falha',
        ]);
    }

    /** @test */
    public function test_sms_mensagem_preserva_privacidade_e_sem_diagnostico()
    {
        $gestante = $this->criarGestante([
            'nome_completo' => 'Esperança Chissano',
            'contacto' => '+258821234567',
        ]);

        $alerta = Alerta::create([
            'patient_id' => $gestante->id,
            'tipo' => 'exames_criticos',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'HIV Reagente',
            'status' => Alerta::STATUS_ATIVO,
        ]);

        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);

        $smsService = new SmsNotificationService();
        $smsService->sendHighRiskAlertSms($alerta);

        $log = SmsLog::where('alerta_id', $alerta->id)->first();
        $this->assertNotNull($log);

        // Não pode conter termos clínicos sensíveis
        $this->assertStringNotContainsStringIgnoringCase('hiv', $log->mensagem);
        $this->assertStringNotContainsStringIgnoringCase('positivo', $log->mensagem);
        $this->assertStringNotContainsStringIgnoringCase('sifilis', $log->mensagem);
        $this->assertStringNotContainsStringIgnoringCase('anemia', $log->mensagem);

        // Deve conter texto seguro em português moçambicano
        $this->assertStringContainsString('Sra. Esperança', $log->mensagem);
        $this->assertStringContainsString('mensagem de saude importante', $log->mensagem);
    }
}
