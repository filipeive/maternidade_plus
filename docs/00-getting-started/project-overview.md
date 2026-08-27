# 📌 Visão Geral do Projeto — Maternidade+

O **Maternidade+** é um sistema de informação de saúde materna e neonatal concebido para operacionalizar e digitalizar a gestão das consultas pré-natais (CPN), puerpério, vigilância de alto risco obstétrico (ARO) e profilaxias nas Unidades Sanitárias (US) do **Ministério da Saúde de Moçambique (MISAU)**.

---

## 🎯 Objetivos Principais

1. **Vigilância Obstétrica Continuada**: Acompanhar a gestante desde a captação precoce (≤12 semanas) até à 4ª+ consulta CPN e cuidados puerperais aos 42 dias.
2. **Triagem Automática de Alto Risco Obstétrico (ARO)**: Identificar de forma proativa fatores de risco (ex: idade <16 anos, hipertensão prévia, histórico de nados-mortos) e gerar alertas precoces com ações recomendadas.
3. **Comunicação por SMS (httpSMS)**: Notificar automaticamente as gestantes sobre a data das suas consultas, lembretes de exames e avisos a pacientes faltosas.
4. **Alinhamento com Instrumentos Oficiais MISAU**:
   - **Ficha Pré-Natal (FPN)** & Plano Individual de Parto (PIP).
   - **Livro Eletrónico CPN (MOD-SIS-B01)**.
   - **Resumo Mensal da Unidade Sanitária (MOD-SIS-B01-B)** com exportação em PDF oficial.

---

## 👥 Perfis de Utilizador & Papéis (RBAC)

- **Administrador**: Gestão total de utilizadores, permissões, configurações do sistema, logs técnicos e relatórios globais.
- **Médico**: Atendimento clínico especializado, consultas ARO, monitoria de exames laboratoriais e diagnósticos.
- **Enfermeiro (ESMI)**: Consultas pré-natais e de puerpério, administração de profilaxias (SP/Fansidar, VAT, REMTIL, Sal Ferroso), agendamento de consultas e emissão de SMS.
- **Laboratorista**: Processamento e validação de exames laboratoriais (VDRL/Sífilis, Hemoglobina, HIV, Glicemia, Urina Tipo I/II).
