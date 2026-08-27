<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * SmsService — Driver de envio de SMS via httpSMS API (httpsms.com)
 *
 * Utiliza o telemóvel registado para notificação de gestantes, lembretes de consultas pré-natais
 * e puerpério em Moçambique.
 */
class SmsService
{
    /**
     * Envia uma mensagem SMS.
     *
     * @param string $to Número de destino (formato local ou internacional +258)
     * @param string $message Conteúdo da mensagem SMS
     * @return array [bool $success, string $statusMessage]
     */
    public static function sendSms(string $to, string $message): array
    {
        $apiKey = config('services.httpsms.key') ?: env('HTTPSMS_KEY') ?: env('HTTPSMS_API_KEY');
        $from   = config('services.httpsms.from') ?: env('HTTPSMS_FROM');

        if (empty($apiKey) || empty($from)) {
            Log::warning('[SMS] Configuração do httpSMS ausente no .env (HTTPSMS_KEY ou HTTPSMS_FROM).');
            return [false, 'Configuração do httpSMS em falta no .env. (Defina HTTPSMS_KEY e HTTPSMS_FROM)'];
        }

        $toNormalized = self::normalizePhone($to);
        if ($toNormalized === null) {
            return [false, 'Número de telefone de destino inválido.'];
        }

        $fromNormalized = self::normalizePhone($from);
        if ($fromNormalized === null) {
            return [false, 'Número de origem (HTTPSMS_FROM) inválido no .env.'];
        }

        $url = 'https://api.httpsms.com/v1/messages/send';
        $body = json_encode([
            'content' => $message,
            'from'    => $fromNormalized,
            'to'      => $toNormalized,
        ]);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: '    . $apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT        => 10,
            ]);

            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                Log::error('[SMS] Erro cURL (httpSMS): ' . $error);
                return [false, 'Erro de rede: ' . $error];
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("[SMS] Mensagem enviada com sucesso via httpSMS para {$toNormalized}.");
                return [true, 'SMS enviado com sucesso.'];
            }

            $detail = $result['message'] ?? $response;
            if (is_array($detail) || is_object($detail)) {
                $detail = json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            Log::error("[SMS] Erro httpSMS HTTP {$httpCode}: {$detail}");
            return [false, 'httpSMS: ' . $detail];
        } catch (\Exception $e) {
            Log::error('[SMS] Exceção no envio via httpSMS: ' . $e->getMessage());
            return [false, 'Erro no serviço de SMS: ' . $e->getMessage()];
        }
    }

    /**
     * Normaliza números de telemóvel para formato E.164 (Moçambique +258 por defeito)
     */
    private static function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[^\d+]/', '', trim($phone));

        if (empty($phone)) {
            return null;
        }

        if (str_starts_with($phone, '+')) {
            return (strlen($phone) >= 10) ? $phone : null;
        }

        if (str_starts_with($phone, '258')) {
            return '+' . $phone;
        }

        if (strlen($phone) === 9 && (str_starts_with($phone, '84') || str_starts_with($phone, '85') || str_starts_with($phone, '86') || str_starts_with($phone, '87') || str_starts_with($phone, '82') || str_starts_with($phone, '83'))) {
            return '+258' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Envia SMS e grava o histórico na tabela sms_logs.
     */
    public static function sendSmsAndLog(?int $patientId, string $to, string $message, ?int $alertaId = null): array
    {
        [$success, $statusMessage] = self::sendSms($to, $message);

        try {
            \Illuminate\Support\Facades\DB::table('sms_logs')->insert([
                'patient_id' => $patientId,
                'alerta_id' => $alertaId,
                'telefone' => $to,
                'mensagem' => $message,
                'status' => $success ? 'enviado' : 'falha',
                'resposta_api' => $statusMessage,
                'erro' => $success ? null : $statusMessage,
                'enviado_em' => $success ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('[SMS] Erro ao gravar log de SMS: ' . $e->getMessage());
        }

        return [$success, $statusMessage];
    }
}
