# ⚙️ Serviços Desacoplados — Maternidade+

Documentação dos serviços auxiliares do sistema.

---

## 📱 1. `SmsService` (`app/Services/SmsService.php`)
Driver de comunicação via HTTP API com o fornecedor **httpSMS** (httpsms.com) para notificação de gestantes em Moçambique (+258).

- **`sendSms(string $to, string $message): array`**: Normaliza o número de destino para formato internacional `+258...` e envia o payload JSON via cURL.
- **`sendSmsAndLog(?int $patientId, string $to, string $message, ?int $alertaId = null): array`**: Dispara a mensagem e grava o histórico com estado (`enviado` ou `falha`) na tabela `sms_logs`.

---

## 🤖 2. `AiAssistantService` (`app/Services/AiAssistantService.php`)
Integração com a API do Google Gemini Direct para apoio à decisão clínica e triagem inteligente de orientações pré-natais.
