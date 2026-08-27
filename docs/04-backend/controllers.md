# 🎮 Controladores — Maternidade+

Lista de controladores HTTP e responsabilidades no sistema.

---

- **`ConsultationController`**: Gestão do agendamento, pré-seleção automática por semanas/parto, conclusão de consultas com notas e agendamento da próxima com envio de SMS de lembrete.
- **`ModSisB01Controller`**: Gestão do Livro Eletrónico CPN (MOD-SIS-B01) e compilação em 1-clique do Resumo Mensal Estatístico (MOD-SIS-B01-B) em web e PDF.
- **`PatientController`**: Cadastro de gestantes, histórico de cartão, triagem de risco e pesquisa AJAX.
- **`BirthController`**: Registo de partos, puerpério e vitais neonatais do recém-nascido.
- **`HomeVisitController`**: Gestão de visitas domiciliares, rotas diárias e busca ativa de pacientes faltosas.
- **`SmsNotificationController`**: Central de envio de SMS individual, notificações em massa para faltosas e logs de entrega.
- **`AlertaController` & `AlertaMetricasController`**: Vigilância de risco obstétrico e relatórios de impacto M&E com Chart.js.
- **`UserController`**: Gestão de utilizadores, funções RBAC e toggle de estado (Ativo / Inativo).
