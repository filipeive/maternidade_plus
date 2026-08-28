# 📜 CHANGELOG — Maternidade+

Todas as alterações notáveis efetuadas no projeto **Maternidade+**.

---

## [2.3.0] - 2026-08-28
### Adicionado
- **Central Unificada de Notificações & SMS**: Nova central integrada com 5 abas (Notificações do Sistema, Pacientes Faltosas, SMS Individual, Histórico de Logs de Envio e Modelos MISAU).
- Nova tabela e modelo `system_notifications` para persistência e gestão de leitura de notificações clínicas e do sistema.
- Novo serviço `NotificationService` para sincronização em tempo real de eventos clínicos (alertas críticos, faltosas, exames concluídos, vacinas em atraso).
- Novo `NotificationController` com endpoints de API para dropdown na navbar (`/notifications/api/list`, `/notifications/api/count`) e gestão de leitura (`markRead`, `markAllRead`).
- Item de menu **"Notificações & SMS"** integrado na barra lateral (`sidebar`) em ambos os layouts (`app-tw.blade.php` e `app.blade.php`) com badge de pendências.
- Ações no módulo de alertas para marcação de leitura individual e em massa (`/alertas/{alerta}/marcar-lido`, `/alertas/marcar-todos-lidos`).

### Corrigido
- O contador de notificações (ícone do sino) e de alertas na navbar agora atualiza e persiste em tempo real ao clicar ou resolver alertas/notificações, eliminando o comportamento estático anterior.
- Padronização visual com recurso exclusivo a ícones profissionais FontAwesome em todas as abas, formulários, selects e templates de notificações, substituindo todos os emojis.
- Removido o modal residual global de QR Code que causava efeito visual de flash ao carregar a página `/scanner`, e adicionada regra global `<style>[x-cloak]</style>` nos layouts para evitar qualquer oscilação de componentes AlpineJS.
- Assistente Virtual com IA integrado e corrigido com suporte oficial ao modelo `gemini-2.5-flash` e fallback dinâmico entre Google Gemini Direct e OpenRouter.
- Nome da aplicação padronizado para **Maternidade+** em todas as barras de título de página, layouts e configurações `.env` (removendo a referência padrão "Laravel").

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
