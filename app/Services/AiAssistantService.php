<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantService
{
    /**
     * Envia um prompt para o assistente de IA da Maternidade+ (OpenRouter com fallback Gemini).
     *
     * @param string $userPrompt Pergunta do utilizador/profissional de saúde
     * @param array $history Histórico de mensagens do chat (opcional)
     * @return array [bool $success, string $responseMessage]
     */
    public function ask(string $userPrompt, array $history = []): array
    {
        $openRouterKey = config('services.openrouter.key') ?: env('OPENROUTER_API_KEY');
        $geminiKey     = config('services.gemini.key') ?: env('GEMINI_API_KEY');

        if (empty($openRouterKey) && empty($geminiKey)) {
            return [false, 'Nenhuma chave de API de IA configurada no servidor (.env). Defina GEMINI_API_KEY ou OPENROUTER_API_KEY.'];
        }

        $systemInstruction = <<<EOT
Você é o "Assistente Guia Maternidade+", um assistente virtual especialista em saúde materno-infantil integrado no sistema Maternidade+ de Moçambique.

Sua missão é:
1. Orientar profissionais de saúde (Médicos, Enfermeiras SMI, Técnicos de Laboratório, APEs) sobre como utilizar o sistema Maternidade+.
2. Fornecer orientações alinhadas com os protocolos e diretrizes de Saúde Materno-Infantil do MISAU (Ministério da Saúde de Moçambique):
   - Consultas Pré-Natais (ANC): Frequência recomendada (no mínimo 4 a 8 contactos), triagem de sintomas de alarme (pré-eclâmpsia, hemorragia, febre).
   - Prevenção da Malária na Gravidez: Administração de IPTp-SP (Sulfadoxina-Pirimetamina) a partir da 13ª semana de gestação, com intervalo mínimo de 4 semanas entre doses.
   - Imunização: Vacinação contra o Tétano (VAT / TTP).
   - Rastreio Obrigatório: Teste Rápido de HIV, Sífilis (VDRL), Hemoglobina (Anemia), Glicemia.
   - Parto & Pós-Parto (Puerpério):
     - Parto seguro na Unidade Sanitária (Eutócico vs Cesariana, APGAR, cuidados ao RN).
     - Consultas de Puerpério obrigatórias pelo MISAU: 1ª consulta (48h / antes da alta), 2ª consulta (7 dias), 3ª consulta (28 dias / 6 semanas com planeamento familiar).
3. Ser conciso, profissional, empático e usar português claro. Se a pergunta for sobre utilização do sistema, dê o caminho dos menus (ex: "Aceda a Clínico -> Gestantes -> Registar Parto").
EOT;

        // 1. Tentar Gemini Direct primeiro (resposta ultrarrápida e direta)
        if (!empty($geminiKey)) {
            $result = $this->queryGeminiDirect($geminiKey, $systemInstruction, $userPrompt);
            if ($result[0] === true) {
                return $result;
            }
            Log::warning('AiAssistantService: Gemini Direct falhou, tentando fallback OpenRouter...', ['error' => $result[1]]);
        }

        // 2. Fallback para OpenRouter
        if (!empty($openRouterKey)) {
            $result = $this->queryOpenRouter($openRouterKey, $systemInstruction, $userPrompt, $history);
            if ($result[0] === true) {
                return $result;
            }
            Log::warning('AiAssistantService: OpenRouter falhou...', ['error' => $result[1]]);
        }

        return [false, 'Não foi possível obter resposta do assistente de IA. Verifique a ligação ou tente novamente.'];
    }

    private function queryOpenRouter(string $apiKey, string $systemPrompt, string $userPrompt, array $history): array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            foreach ($history as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $msg['content']
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $userPrompt];

            $model = env('GEMINI_MODEL', 'google/gemini-2.5-flash');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => env('APP_URL', 'http://146.235.224.99/maternidade_plus'),
                'X-Title' => 'Maternidade+ Assistant',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.4,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                if ($content) {
                    return [true, trim($content)];
                }
            }

            return [false, 'Erro OpenRouter (' . $response->status() . '): ' . $response->body()];
        } catch (\Exception $e) {
            return [false, 'Exceção OpenRouter: ' . $e->getMessage()];
        }
    }

    private function queryGeminiDirect(string $apiKey, string $systemPrompt, string $userPrompt): array
    {
        $models = [
            env('GEMINI_DIRECT_MODEL', 'gemini-2.5-flash'),
            'gemini-2.0-flash',
            'gemini-1.5-flash'
        ];

        $lastError = 'Modelo não disponível';

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nPergunta do Utilizador: " . $userPrompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return [true, trim($text)];
                    }
                }

                $lastError = 'Erro Gemini Direct (' . $model . '): ' . $response->status();
            } catch (\Exception $e) {
                $lastError = 'Exceção Gemini Direct (' . $model . '): ' . $e->getMessage();
            }
        }

        return [false, $lastError];
    }
}
