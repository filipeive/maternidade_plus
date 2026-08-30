# 📜 CHANGELOG — Maternidade+

Todas as alterações notáveis efetuadas no projeto **Maternidade+**.

---

## [2.17.0] - 2026-08-30
### Adicionado / Melhorado
- **Unificação das Consultas Faltosas / Atrasadas (`/consultations?atrasadas=1`)**:
  - Ajustado o filtro `atrasadas=1` no `ConsultationController` para capturar todas as consultas com datas ultrapassadas e gestantes ativas com alertas clínicos de assiduidade/abandono (`gestante_faltosa`).
  - Destaque visual na tabela de consultas com cores de alerta, badges pulsantes de *Faltosa* e botão rápido de encaminhamento para busca comunitária com activistas (**APE**).

---

## [2.16.0] - 2026-08-30
### Adicionado / Melhorado
- **Destaque Visual & Sinalização de Risco na Lista de Gestantes (`/patients`)**:
  - Linhas da tabela destacadas com cores contextuais (borda vermelha e fundo subtil para risco alto 🔴; borda dourada para alertas médios 🟡) até que a conduta seja resolvida.
  - Badges pulsantes de severidade (`Risco Alto`, `Alerta (X)`) e indicação da regra clínica disparada ao lado do nome da gestante.
  - Nova aba de filtro rápido no topo: **"Com Risco / Alertas (X)"** para isolar em 1 clique todas as gestantes com condutas pendentes.

---

## [2.15.0] - 2026-08-30
### Adicionado / Melhorado
- **Padronização da Central de Alertas Clínicos (`/alertas`)**:
  - Removido o botão ambíguo "Lido" da tabela principal de alertas.
  - Implementada abertura inteligente do modal com auto-leitura em background (`lido = true`).
  - Dinamização dos botões de ação na tabela: **"🔄 Atualizar Conduta"** (dourado) para alertas em seguimento, **"🩺 Tratar / Resolver"** (verde-petróleo) para alertas ativos e **"✏️ Editar"** para alertas já resolvidos.

---

## [2.14.1] - 2026-08-30
### Corrigido
- **Import do Modelo `Alerta` no `HomeVisitController`**:
  - Adicionado o namespace `use App\Models\Alerta;` e `use App\Models\Consultation;` em `HomeVisitController.php`, resolvendo erro `Class App\Http\Controllers\Alerta not found` na Busca Ativa de Pacientes Faltosas (`/home_visits/active-search`).

---

## [2.14.0] - 2026-08-30
### Adicionado / Melhorado
- **Unificação dos Critérios de Faltosas & Busca Ativa (`/home_visits/active-search`)**:
  - Alinhada a query do controlador `HomeVisitController@activeSearch` com o `AlertaPrecoceService` e as normas do MISAU: agora captura gestantes com alertas de faltosa ativos, consultas agendadas vencidas e pacientes sem consulta há $> 30$ dias.
- **Substituição de Emojis por Ícones Profissionais**:
  - Substituídos todos os emojis por ícones vetoriais FontAwesome e Tailwind nas interfaces de avaliações clínicas (`/alertas/avaliacoes`).
- **Resolução Definitiva de Variáveis de Alerta no Prontuário**:
  - Injeção explícita de `$alertasResolvidosPaciente` e `$alertasAtivosPaciente` diretamente do `PatientController@show` com fallback defensivo no Blade, evitando erros de variável indefinida tanto localmente quanto em produção.

---

## [2.13.0] - 2026-08-30
### Adicionado / Melhorado
- **Simplificação da Experiência de Triagem & Leitura Automática de Alertas**:
  - Removido o botão ambíguo "Lido" da interface do prontuário da paciente (`/patients/{id}`).
  - Implementada leitura e marcação automática no backend assim que o profissional clica em **"Tratar / Resolver"** ou **"Atualizar Conduta"**, reduzindo cliques desnecessários.
  - Assegurado que qualquer transição de estado (`transitarStatus`) marca o alerta como lido por omissão.
- **Nova Documentação Técnica & Clínica**:
  - Criado o documento detalhado [`docs/06-features/alertas-precoces-e-fluxo-clinico.md`](file:///home/fdev-ms/Filipe/maternidade_plus/docs/06-features/alertas-precoces-e-fluxo-clinico.md) cobrindo as 9 regras clínicas MISAU, o parser de linguagem natural com exclusão de negações médicas, o ciclo de vida dos alertas no prontuário e a automação do pós-parto.

---

## [2.12.0] - 2026-08-30
### Adicionado / Melhorado
- **Aprimoramento do Ciclo de Vida dos Alertas Clínicos & Transição Gestação-Parto**:
  - **Botão Dinâmico de Seguimento**: Alertas com status *Em Seguimento* agora exibem dinamicamente a ação **"🔄 Atualizar Conduta"** com estilização diferenciada, permitindo revisão ou encerramento progressivo da conduta clínica.
  - **Atalho Integrado de Registo de Parto**: Adicionado atalho contextual **"👶 Registar Parto"** tanto no cabeçalho do bloco de alertas quanto no interior do modal de conduta, integrando o encerramento automático de alertas da gestação e geração imediata das 3 consultas de puerpério MISAU (48h, 7d, 28d).
  - **Histórico Retrátil de Resoluções**: Criado accordion colapsável na ficha da paciente exibindo os alertas concluídos anteriormente com as respetivas condutas e datas de resolução.

---

## [2.11.0] - 2026-08-30
### Adicionado / Melhorado
- **Tratamento e Resolução Direta de Alertas no Prontuário da Gestante (`/patients/{id}`)**:
  - Implementado modal interativo em Alpine.js no topo do perfil da paciente para transitar e resolver alertas clínicos (Resolvido / Em Seguimento / Ignorado) com registo obrigatório de conduta médica e notas de auditoria clínica.
  - Sincronização automática em tempo real entre a ficha individual da gestante, a Central de Alertas Precoces (`/alertas`) e o Painel de Avaliações (`/alertas/avaliacoes`).
  - Ampliadas as permissões de gestão clínica de alertas para Médicos, Enfermeiras de SMI e Parteiras.
- **Correção de Query e Refatoração Clean Code**:
  - Corrigido erro SQL no `ReportController` relacionado à contagem de inscrições precoces e idades de gestantes (utilizando métodos de domínio do modelo `Patient`).
  - Refatoração de variáveis genéricas de fecho (`$q`) para nomes semânticos e descritivos (`$subQuery`, `$consultaQuery`, `$profilaxiaSubQuery`), aprimorando a manutenibilidade do código.

---

## [2.8.0] - 2026-08-28
### Adicionado / Melhorado
- **Expansão Global do Painel de Configurações (`/settings`)**:
  - **1. Unidade Sanitária & MISAU**: Gestão do Nome Oficial da US, Província (com seletor das 11 províncias moçambicanas), Distrito, Código SISMA/MISAU, Telefone de Urgência Obstétrica, E-mail Institucional e Médico Chefe/Responsável de SMI.
  - **2. Gateway SMS & Notificações Automáticas**: Configuração de credenciais httpSMS, remetente, ativação/desativação geral, antecedência de lembretes (1 a 3 dias), notificação de parceiros e templates customizáveis para CPN e busca ativa.
  - **3. Assistente Clínico IA**: Seleção de provedor (Google Gemini Direct / OpenRouter), modelo (`gemini-2.5-flash`, `claude-3.5-sonnet`, `gpt-4o-mini`), temperatura clínica, ativação de widget flutuante e diretrizes customizadas da unidade sanitária.
  - **4. Protocolos Clínicos & Parâmetros de Alerta ARO**: Limites de tolerância para gestante faltosa (dias), semanas de alerta de parto (DPP), limites de PA Sistólica e Diastólica para Pré-eclâmpsia, corte de Hemoglobina para Anemia Severa e automação de alertas precoces.
  - **5. Saúde Comunitária & Visitas de Terreno (APEs)**: Dias para reagendamento automático em caso de "Não Encontrada", dispensa automática de visita quando a paciente é atendida na US e disparo de SMS para activistas.
  - **6. Backup & Manutenção**: Exportação de backup dos parâmetros em formato JSON (`settings.backup`), leitor de logs com busca em tempo real e limpeza de caches do sistema.

---

## [2.7.0] - 2026-08-28
### Adicionado / Melhorado
- **Redesign Completo do Dashboard Clínico & Analítico (`/dashboard`)**:
  - **4 Gráficos Interativos em Chart.js**:
    1. *Evolução Mensal de Consultas CPN & Partos na Maternidade* (gráfico de linhas/área com gradientes).
    2. *Distribuição Gestacional por Trimestre & Pós-Parto* (gráfico de rosca/doughnut).
    3. *Taxa de Cobertura de Profilaxias MISAU* (barras horizontais para IPTp-SP Malária, Tétano, Ferro/Ácido Fólico, Mebendazol).
    4. *Desfecho do Trabalho Comunitário e Visitas de Terreno* (barras para realizadas, agendadas, não encontradas e dispensadas na US).
  - **6 Stat Cards / KPIs de Gestão Integrada**: Gestantes Ativas (com destaque ARO), Consultas Hoje/Semana, Partos no Mês, Faltosas para Busca Ativa Comunitária, Visitas Domiciliares e Transferências Inter-Hospitalares.
  - **Painéis Operacionais em Tempo Real**: Feed prioritário de Alertas Precoces & Alto Risco com botão "Tratar", Agenda de Consultas dos próximos dias, Painel de Faltosas com atribuição a Activistas Comunitárias (APEs), e Livro de Nascimentos Recentes na Maternidade com APGAR e peso neonatal.
  - **Barra de Ação Rápida & Cabeçalho**: Cabeçalho moderno em tom claro (clean light theme) com nome da Unidade Sanitária, identificação e perfil do utilizador autenticado, data formatada em português e busca rápida/scanner QR Code.

---

## [2.6.0] - 2026-08-28
### Adicionado / Melhorado
- **Módulo Nacional de Transferência & Inativação de Pacientes (MISAU)**:
  - Adicionado suporte a transferências inter-hospitalares e inter-provinciais de gestantes e puérperas com persistência na base de dados (`motivo_inativacao`, `data_transferencia`, `unidade_sanitaria_destino`, `provincia_destino`, `distrito_destino`, `motivo_transferencia`, `guia_transferencia_numero`, `resumo_clinico_transferencia`, `profissional_transferencia_id`).
  - Geração automática de **Guia Oficial de Transferência e Referência Obstétrica MISAU** com numeração rastreável (ex: `GT-202608-0042`).
  - Emissão de Guia de Transferência em **PDF A4** com checklist de segurança no transporte, resumo clínico de CPN, rastreios laboratoriais, antecedentes obstétricos e campos de assinatura e carimbo institucional.
  - Filtro em abas na listagem de gestantes (`/patients`): **Ativas**, **Transferidas**, **Inativas** e **Todas**.
  - Banner informativo proeminente no perfil da paciente transferida com botão direto para reimpressão da guia e ação rápida de **Reativação na US** (caso retorne à unidade).
  - Cancelamento automático e dispensa de visitas domiciliárias comunitárias de terreno para pacientes transferidas.

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
