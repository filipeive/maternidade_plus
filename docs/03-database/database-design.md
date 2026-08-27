# 🗄️ Design da Base de Dados — Maternidade+

O esquema da base de dados do **Maternidade+** foi desenhado relacionalmente em MySQL / MariaDB garantindo integridade referencial através de Chaves Estrangeiras (`foreignId`) e índices de pesquisa rápidos.

---

## 📌 Principais Entidades Clínicas

- **`users`**: Utilizadores do sistema (Administradores, Médicos, Enfermeiros, Laboratoristas).
- **`patients`**: Registo principal de Gestantes e Puérperas.
- **`antenatal_histories`**: História obstétrica pregressa, triagem ARO, PIP e nutrição inicial.
- **`consultations`**: Registos de Consultas CPN, Pós-Parto e Emergência.
- **`maternal_prophylaxes`**: Cartão de Vacinas VAT, TIP Malária (SP), REMTIL, PTV HIV e Sífilis.
- **`births`**: Registos de Partos, puerpério e saúde neonatal do recém-nascido (RN).
- **`exams`**: Exames laboratoriais solicitados e resultados.
- **`home_visits`**: Visitas domiciliares e busca ativa de pacientes faltosas.
- **`alertas` & `alerta_acoes`**: Alertas precoces de risco e ações de acompanhamento.
- **`sms_logs`**: Histórico de envio de mensagens SMS via httpSMS.
