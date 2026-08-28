# 📜 CHANGELOG — Maternidade+

Todas as alterações notáveis efetuadas no projeto **Maternidade+**.

---

## [2.5.0] - 2026-08-28
### Adicionado / Melhorado
- **Módulo de Agentes Comunitários & Activistas de Saúde (APEs)**:
  - Adicionado perfil e permissões de **Agente Comunitário** (`Agente Comunitário`) no Spatie RBAC e seeders do sistema, permitindo que Enfermeiras e Activistas Comunitárias colaborem na busca ativa e seguimento domiciliar.
  - Criado utilizador comunitário padrão: `activista@maternidade.mz` (*Activista Comunitária Rosa Sitoe*).
- **Módulo de Busca Ativa de Pacientes Faltosas (`/home_visits/active-search`)**:
  - Nova interface moderna em Tailwind CSS para acompanhamento de gestantes e puérperas com consultas CPN ou pós-parto em atraso.
  - Encaminhamento individual ou em lote para a equipa comunitária com atribuição de responsável e instruções de terreno.
  - Funcionalidade de **"Atendida na US / Resolver Visita"**: permite que médicos e enfermeiras dispensem a visita no terreno quando a gestante comparecer à consulta na unidade sanitária.
- **Redesign Completo das Vistas de Visitas Domiciliárias (`resources/views/home_visits/`)**:
  - `show.blade.php`: Redesenhado integralmente no design system Tailwind CSS + Alpine.js com modais de "Completar Visita", "Reagendar", "Marcar Não Encontrada" e "Resolver na US".
  - `create.blade.php` e `edit.blade.php`: Suporte à seleção e atribuição de activistas/enfermeiros responsáveis.
  - `index.blade.php`: Ações rápidas de resolução na US, atalhos para busca ativa e estatísticas em tempo real.
- **Integração na Barra Lateral & Consultas**:
  - Atalhos diretos para busca ativa comunitária a partir da listagem de consultas CPN.

---

## [2.4.2] - 2026-08-28
### Adicionado / Melhorado
- **Assistente IA Clínico com Memória Conversacional Persistente**:
  - Implementação de persistência de histórico de mensagens via `localStorage` no browser (`maternidade_ai_chat_history`), mantendo as conversas ativas mesmo após recarregar ou navegar entre páginas.
  - Adicionado suporte multi-turn com envio do contexto das últimas conversas para as APIs do **Google Gemini Direct** e **OpenRouter**.
  - Ajuste nas instruções de sistema (System Prompt) para eliminar saudações repetitivas durante o diálogo contínuo.
  - Adicionado botão **"Limpar Chat"** no cabeçalho do widget flutuante e na central de ajuda para reinicialização sob demanda.
  - Renderização aprimorada de markdown (negrito, itálico, código inline e formatação clínica).
- **Segurança & DevSecOps no Deploy**:
  - Remoção do ficheiro `deploy.sh` do controlo de versões do Git e inclusão no `.gitignore`.
  - Criação do modelo `deploy.sh.example` com suporte ao carregamento de variáveis via `.env.deploy` ou variáveis de ambiente.

---

## [2.4.1] - 2026-08-28
### Modificado / Melhorado
- **Painel de Alertas Clínicos (`/alertas`)**: Diferenciação contextual dos botões de ação na tabela de alertas. Alertas já resolvidos ou ignorados exibem agora o botão **"Editar"** (com ícone e tema adaptado), abrindo o modal de edição da conduta e nota clínica em vez do botão "Tratar".
- **Gestão de Exames Laboratoriais (`/exams`)**: Adicionado botão de atalho rápido para **"Editar Exame"** diretamente na tabela principal de listagem.
- **Documentação Principal (`README.md`)**: Substituição completa do template inicial pelo README oficial do Maternidade+, cobrindo módulos clínicos MISAU, arquitetura, passos de instalação, seeders, variáveis de ambiente e referências aos manuais técnicos em `/docs`.
- **Sincronização da Documentação (`/docs`)**: Atualização do mapeamento completo de rotas em `docs/04-backend/routes.md` e checkpoint do projeto.

---

## [2.4.0] - 2026-08-28
### Adicionado
- **Upgrade Nacional MISAU Moçambique Seguro**: Implementação integral dos protocolos clínicos oficiais de Saúde Materno-Infantil baseados nos Manuais Técnicos do Pré-Natal, Ficha Pré-Natal (FPN), Livros de Registos (MOD-SIS-B01, B01-B, B01-C, B01-D) e Normas Nacionais de CPN.
- **Ficha Pré-Natal Digital (FPN)**: Anamnese completa com registo de antecedentes obstétricos detalhados por gestação anterior (1ª a 6ª+ gravidezes com ano, via de parto, local, prematuridade, macrossomia, gemelaridade e mortalidade perinatal).
- **Estratificação ARO MISAU & Guia de Transferência Hospitalar**: Algoritmo de risco obstétrico (Nível I - CS, Nível II - HR/HG às 32 sem, Nível III - HP/HC) com checklist de segurança obrigatório (acesso venoso calibroso com Soro Ringer, algaliação, acompanhante jovem para doação e documentação).
- **Rastreio de Isoimunização Rh**: Deteção precoce de incompatibilidade sanguínea de casal (Mãe Rh- e Parceiro Rh+) com recomendação de teste de Coombs Indireto na 30ª semana.
- **Protocolos Neonatais & Puerpério no Parto**: Escala APGAR ao 1º, 5º e 10º minuto, perímetro cefálico/craniano, registo de reanimação, aspiração, profilaxia ocular com Tetraciclina oftálmica a 1%, Vitamina K1 injetável, vacinas BCG e Pólio Zero, aleitamento materno na 1ª hora, megadose de Vitamina A materna e profilaxia TARV no parto.
- **Consolidação Estatística Distrital (MOD-SIS-B01-C)**: Resumo mensal de saúde materna compilando todas as unidades sanitárias do distrito.
- **Consolidação Estatística Provincial (MOD-SIS-B01-D)**: Resumo mensal provincial de saúde materna para o Serviço Provincial de Saúde (SPS).
- **Cartão da Gestante & FPN em PDF A4**: Layout oficial para impressão clínica com dados completos, código QR e histórico de consultas.

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
