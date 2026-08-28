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
   - Consultas Pré-Natais (CPN): Frequência recomendada (captação precoce ≤12 semanas, coorte de ≥4 contactos), triagem de sinais de risco e Alto Risco Obstétrico (ARO Níveis I, II e III).
   - Prevenção da Malária na Gravidez: Administração de IPTp-SP (Sulfadoxina-Pirimetamina) a partir da 13ª semana de gestação com intervalo mínimo de 4 semanas entre doses (SP1 a SP4+), e entrega de REMTIL (Rede Mosquiteira).
   - Imunização: Vacinação contra o Tétano (VAT 1ª a 5ª dose).
   - Rastreios Obrigatórios: Teste Rápido de HIV, Sífilis (VDRL), Hemoglobina (Anemia), Glicemia, Rastreio de Isoimunização Rh (Coombs Indireto às 30 sem).
   - Parto & Pós-Parto (Puerpério):
     - Parto seguro na Unidade Sanitária (Eutócico vs Cesariana, APGAR 1', 5', 10', perímetro cefálico, Vitamina K1, Tetraciclina oftálmica 1%, BCG, Pólio 0, Vitamina A materna).
     - Consultas de Puerpério obrigatórias pelo MISAU: 1ª consulta (48h / antes da alta), 2ª consulta (7 dias), 3ª consulta (28 dias a 42 dias com planeamento familiar).
   - Instrumentos Oficiais SIS: Ficha Pré-Natal (FPN), Livro de Registos MOD-SIS-B01 e Resumos Mensais (B01-B, B01-C, B01-D).
3. Diretrizes de Comunicação e Continuidade de Diálogo:
   - Você está a participar numa conversa contínua. Mantenha o contexto com base no histórico de mensagens.
   - NUNCA repita saudações genéricas (como "Olá!", "Bem-vindo ao sistema Maternidade+", "Sou o seu assistente...") em respostas subsequentes se a conversa já estiver a decorrer. Vá direto à resposta solicitada.
   - Seja conciso, objetivo, clínico, empático e use português claro de Moçambique. Utilize listas ou texto em negrito para facilitar a leitura rápida pelo profissional de saúde.
EOT;

        // 1. Tentar Gemini Direct primeiro (resposta ultrarrápida e direta com histórico)
        if (!empty($geminiKey)) {
            $result = $this->queryGeminiDirect($geminiKey, $systemInstruction, $userPrompt, $history);
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

    private function queryOpenRouter(string $apiKey, string $systemPrompt, string $userPrompt, array $history = []): array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            foreach ($history as $msg) {
                if (!empty($msg['content'])) {
                    $messages[] = [
                        'role' => ($msg['role'] ?? '') === 'user' ? 'user' : 'assistant',
                        'content' => (string) $msg['content']
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
                'max_tokens' => 1200,
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

    private function queryGeminiDirect(string $apiKey, string $systemPrompt, string $userPrompt, array $history = []): array
    {
        $models = [
            env('GEMINI_DIRECT_MODEL', 'gemini-2.5-flash'),
            'gemini-2.0-flash',
            'gemini-1.5-flash'
        ];

        // Formatar histórico multi-turn para a API do Gemini
        $formattedContents = [];
        
        foreach ($history as $msg) {
            if (empty($msg['content'])) continue;
            $role = ($msg['role'] ?? '') === 'user' ? 'user' : 'model';
            
            // Evitar começar com mensagem do modelo se não houver pergunta anterior
            if (empty($formattedContents) && $role === 'model') {
                continue;
            }
            
            // Agrupar mensagens consecutivas com o mesmo papel
            $lastIndex = count($formattedContents) - 1;
            if ($lastIndex >= 0 && $formattedContents[$lastIndex]['role'] === $role) {
                $formattedContents[$lastIndex]['parts'][0]['text'] .= "\n\n" . (string) $msg['content'];
            } else {
                $formattedContents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => (string) $msg['content']]
                    ]
                ];
            }
        }

        // Adicionar o prompt atual do utilizador
        $lastIndex = count($formattedContents) - 1;
        if ($lastIndex >= 0 && $formattedContents[$lastIndex]['role'] === 'user') {
            $formattedContents[$lastIndex]['parts'][0]['text'] .= "\n\n" . $userPrompt;
        } else {
            $formattedContents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $userPrompt]
                ]
            ];
        }

        $lastError = 'Modelo não disponível';

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                
                $payload = [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'contents' => $formattedContents,
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 1200
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return [true, trim($text)];
                    }
                }

                // Fallback caso a API rejeite system_instruction
                if ($response->status() === 400 && !empty($formattedContents)) {
                    $fallbackContents = $formattedContents;
                    if (isset($fallbackContents[0]['parts'][0]['text'])) {
                        $fallbackContents[0]['parts'][0]['text'] = $systemPrompt . "\n\n" . $fallbackContents[0]['parts'][0]['text'];
                    }
                    $fallbackResp = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(30)->post($url, [
                        'contents' => $fallbackContents
                    ]);

                    if ($fallbackResp->successful()) {
                        $data = $fallbackResp->json();
                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($text) {
                            return [true, trim($text)];
                        }
                    }
                }

                $lastError = 'Erro Gemini Direct (' . $model . '): ' . $response->status() . ' - ' . $response->body();
            } catch (\Exception $e) {
                $lastError = 'Exceção Gemini Direct (' . $model . '): ' . $e->getMessage();
            }
        }

        return [false, $lastError];
    }
}
