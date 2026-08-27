# 🏛️ Decisões Arquiteturais (ADRs) — Maternidade+

Registos de Decisão de Arquitetura (Architecture Decision Records) tomadas durante o desenvolvimento.

---

## ADR-001: Uniformização de Notificações com SweetAlert2
- **Status**: Aceito e Implementado.
- **Contexto**: Anteriormente, a aplicação renderizava banners estáticos HTML de aviso (`<div class="alert-success-tw">`) no topo das páginas E TAMBÉM exibia toasters do SweetAlert2, gerando redundância visual.
- **Decisão**: Remover todos os banners estáticos nativos do layout base `app-tw.blade.php`. Todas as notificações do sistema (`success`, `error`, `warning`, `info` e erros de validação `$errors->any()`) passam a ser tratadas **exclusivamente via SweetAlert2** (Toasts flutuantes e modais interativos).

---

## ADR-002: Precedência de Rotas Estáticas em Grupos com Parâmetros Wildcard
- **Status**: Aceito e Implementado.
- **Contexto**: Rotas como `/home_visits/daily-schedule` ou `/home_visits/route-planning` retornavam erro 404 (`ModelNotFoundException`) porque o Laravel tentava interpretar a string `"daily-schedule"` como um ID de visita domiciliar (`/home_visits/{homeVisit}`).
- **Decisão**: Em todos os grupos de rotas no `routes/web.php`, todas as sub-rotas estáticas devem obrigatoriamente ser declaradas **antes** dos parâmetros dinâmicos `{id}` ou `{wildcard}`.

---

## ADR-003: Seleção Automática do Tipo de Consulta por Idade Gestacional & Parto
- **Status**: Aceito e Implementado.
- **Contexto**: A seleção manual do tipo de consulta no agendamento consumia tempo das enfermeiras e gerava inconsistências estatísticas.
- **Decisão**: Implementar lógica clínica inteligente no backend (`ConsultationController`) e no frontend (`Alpine.js / JS`):
  - Paciente com registo de parto (`Birth`) -> Auto-seleção `pos_parto`.
  - Semanas 1 a 12 -> Auto-seleção `1_trimestre`.
  - Semanas 13 a 27 -> Auto-seleção `2_trimestre`.
  - Semanas 28+ -> Auto-seleção `3_trimestre`.
