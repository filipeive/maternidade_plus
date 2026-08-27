# 📚 Documentação Técnica — Maternidade+ (MISAU Moçambique)

Bem-vindo à documentação oficial do **Maternidade+**, a plataforma de Gestão de Saúde Materno-Infantil, Consultas Pré-Natais (CPN), Puerpério, Alertas Precoces de Risco Obstétrico (ARO) e Integração com os Livros Oficiais de Registos (MOD-SIS-B01) do **Ministério da Saúde de Moçambique (MISAU)**.

---

## 📂 Estrutura da Documentação

- [**00-getting-started**](00-getting-started/project-overview.md) — Visão geral do projeto, guia de engenharia, ambiente de desenvolvimento e glossário clínico.
- [**01-architecture**](01-architecture/system-architecture.md) — Arquitetura do sistema, ciclo de vida das requisições, estrutura de pastas e decisões arquiteturais (ADRs).
- [**02-requirements**](02-requirements/requirements.md) — Requisitos funcionais e não-funcionais, casos de uso e regras de negócio baseadas nas normas do MISAU.
- [**03-database**](03-database/database-design.md) — Modelo de dados, diagrama ER, esquemas de tabelas e relacionamentos Eloquent.
- [**04-backend**](04-backend/models.md) — Modelos, controladores, serviços (`SmsService`, `AiAssistantService`), middlewares, políticas e rotas Laravel.
- [**05-frontend**](05-frontend/blade.md) — Estrutura de vistas Blade, layout base Tailwind CSS (`app-tw.blade.php`), componentes e guia de estilo/Design System.
- [**06-features**](06-features/dashboard.md) — Detalhamento das funcionalidades (Gestantes, CPN, Partos, Visitas Domiciliárias, Livro MOD-SIS-B01, Central SMS, Alertas Precoces, Scanner QR).
- [**07-security**](07-security/security.md) — Políticas de segurança, RBAC (Spatie Roles & Permissions), autenticação e upload seguro de ficheiros.
- [**08-testing**](08-testing/testing-strategy.md) — Estratégia de testes automatizados, testes de integração e unitários.
- [**09-deployment**](09-deployment/deployment.md) — Pipeline de CI/CD em produção, configuração do servidor Nginx/PHP-FPM e script `./deploy.sh`.
- [**10-learning**](10-learning/exercises.md) — Guias de treino, desafios práticos e questões de revisão para novos desenvolvedores.
- [**99-project-status**](99-project-status/project-checkpoint.md) — Estado atual do projeto, registo de alterações (`CHANGELOG.md`) e roteiro futuro (`ROADMAP.md`).

---

## 🛠️ Tecnologias Principais

- **Framework**: Laravel 10.x (PHP 8.3)
- **Frontend**: Blade, Tailwind CSS 3.x, Alpine.js, Chart.js 4.x
- **Base de Dados**: MySQL / MariaDB
- **Notificações SMS**: httpSMS API Driver (`+258` Moçambique)
- **Inteligência Artificial**: Google Gemini Direct Service (AI Assistant)
- **Autenticação & RBAC**: Laravel Breeze + Spatie Permission (`Administrador`, `Médico`, `Enfermeiro`, `Laboratorista`)
