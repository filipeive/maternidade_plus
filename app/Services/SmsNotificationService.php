<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsNotificationService
{
    protected string $apiKey;
    protected string $from;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.httpsms.key') ?? env('HTTPSMS_API_KEY', '');
        $this->from = config('services.httpsms.from') ?? env('HTTPSMS_FROM', '+258840000000');
        $this->endpoint = config('services.httpsms.endpoint') ?? env('HTTPSMS_ENDPOINT', 'https://api.httpsms.com/v1/messages/send');
    }

    /**
     * Envia notificação SMS de alerta de alto risco para a gestante via httpSMS.
     * Preserva privacidade (sem detalhes de diagnóstico clínico na mensagem).
     */
    public function sendHighRiskAlertSms(Alerta $alerta): bool
    {
        $patient = $alerta->patient;

        if (!$patient || empty($patient->contacto)) {
            SmsLog::create([
                'patient_id' => $patient?->id,
                'alerta_id' => $alerta->id,
                'telefone' => $patient?->contacto ?? 'N/D',
                'mensagem' => 'Tentativa de envio cancelada: contacto telefónico não disponível.',
                'status' => 'falha',
                'resposta_api' => null,
                'erro' => 'Paciente sem contacto telefónico registado.',
                'enviado_em' => null,
            ]);

            return false;
        }

        $primeiroNome = explode(' ', trim($patient->nome_completo))[0] ?? 'Utente';
        $mensagem = "Maternidade Plus: Sra. {$primeiroNome}, tem uma mensagem de saude importante no seu centro de saude. Por favor contacte ou compareca com urgencia.";

        // Normalização do número de telefone (formato Moçambique +258)
        $telefoneLimpo = preg_replace('/[^\d+]/', '', (string)$patient->contacto);
        if (!str_starts_with($telefoneLimpo, '+')) {
            if (str_starts_with($telefoneLimpo, '258')) {
                $telefoneLimpo = '+' . $telefoneLimpo;
            } else {
                $telefoneLimpo = '+258' . $telefoneLimpo;
            }
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint, [
                    'content' => $mensagem,
                    'from' => $this->from,
                    'to' => $telefoneLimpo,
                ]);

            if ($response->successful()) {
                SmsLog::create([
                    'patient_id' => $patient->id,
                    'alerta_id' => $alerta->id,
                    'telefone' => $telefoneLimpo,
                    'mensagem' => $mensagem,
                    'status' => 'enviado',
                    'resposta_api' => $response->body(),
                    'erro' => null,
                    'enviado_em' => now(),
                ]);

                return true;
            }

            SmsLog::create([
                'patient_id' => $patient->id,
                'alerta_id' => $alerta->id,
                'telefone' => $telefoneLimpo,
                'mensagem' => $mensagem,
                'status' => 'falha',
                'resposta_api' => $response->body(),
                'erro' => 'HTTP status: ' . $response->status() . ' - ' . $response->body(),
                'enviado_em' => null,
            ]);

            return false;
        } catch (Throwable $e) {
            Log::warning("Falha ao enviar SMS para o alerta {$alerta->id}: " . $e->getMessage());

            SmsLog::create([
                'patient_id' => $patient->id,
                'alerta_id' => $alerta->id,
                'telefone' => $telefoneLimpo,
                'mensagem' => $mensagem,
                'status' => 'falha',
                'resposta_api' => null,
                'erro' => $e->getMessage(),
                'enviado_em' => null,
            ]);

            return false;
        }
    }
}
