# 📜 CHANGELOG — Maternidade+

Todas as alterações notáveis efetuadas no projeto **Maternidade+**.

---

## [2.2.0] - 2026-08-27
### Adicionado
- Módulo **Livro Eletrónico CPN (MOD-SIS-B01)** alinhado com as normas oficiais do MISAU Moçambique.
- Módulo **Resumo Mensal Estatístico (MOD-SIS-B01-B)** com compilação automática dos 44 indicadores da Unidade Sanitária.
- Exportação oficial em PDF do formulário MOD-SIS-B01-B (`/mod-sis-b01/resumo-mensal/pdf`).
- Migrações `antenatal_histories` e `maternal_prophylaxes` para registo da vacinação VAT, TIP Malária (SP), REMTIL, PTV HIV e Sífilis.

## [2.1.0] - 2026-08-26
### Adicionado
- Modal de Conclusão Rápida de Consulta com notas clínicas, orientações médicas, agendamento automático e disparo de SMS de lembrete.
- Seleção automática do Tipo de Consulta (Trimesters ou Pós-Parto) com base na Idade Gestacional e registo de parto.
- Atalhos rápidos na lista de consultas (Hoje, Faltosas, Pós-Parto).
- Restrição de RBAC via Spatie Middleware para Administradores em `/users` e `/settings`.
- Uniformização de alertas do sistema exclusivamente via SweetAlert2.
- Refatoração completa das vistas de Visitas Domiciliares para Tailwind CSS.
