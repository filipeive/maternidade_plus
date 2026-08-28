# 🌸 Maternidade+ — Sistema Integrado de Saúde Materno-Infantil

<p align="center">
  <strong>Plataforma Digital de Vigilância Obstétrica, Consultas Pré-Natais (CPN), Puerpério e Notificação Clínica</strong><br>
  <em>Alinhada com as Normas e Instrumentos Oficiais do Ministério da Saúde de Moçambique (MISAU)</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/MISAU-Moçambique-008080?style=flat-square" alt="MISAU Moçambique">
  <img src="https://img.shields.io/badge/Status-Produção%20Ativa-success?style=flat-square" alt="Status">
</p>

---

## 📋 Sobre o Projeto

O **Maternidade+** é um sistema clínico de informação hospitalar e de cuidados primários desenvolvido para operacionalizar e modernizar a gestão de saúde materno-infantil em Moçambique. O sistema digitaliza o fluxo completo de atendimento à gestante — desde a captação pré-natal precoce ($\le$ 12 semanas) até ao puerpério (42 dias pós-parto), integrando vigilância de Alto Risco Obstétrico (ARO), agendamento automatizado, comunicação por SMS e emissão de relatórios oficiais do Sistema de Informação em Saúde (SIS).

---

## ✨ Principais Funcionalidades & Módulos

### 🩺 1. Ficha Pré-Natal Digital (FPN) & Histórico Obstétrico
- **Anamnese Completa**: Registo de dados sociodemográficos, NID, BI e morada geolocalizada.
- **Antecedentes Detalhados**: Histórico de 1ª a 6ª+ gestações (ano, via de parto, prematuridade, macrossomia, gemelaridade, mortalidade perinatal).
- **Cartão da Gestante em PDF**: Geração de cartão oficial de saúde materna em formato A4 para impressão ou consulta rápida via QR Code.

### 🚨 2. Triagem ARO & Vigilância Precoce
- **Estratificação Automática em 3 Níveis**:
  - **Nível I (Baixo Risco / Centro de Saúde)**: Acompanhamento de rotina pela ESMI.
  - **Nível II (Médio Risco / Hospital Rural ou Geral)**: Encaminhamento preventivo na 32ª semana.
  - **Nível III (Alto Risco / Hospital Provincial ou Central)**: Transferência imediata.
- **Protocolo & Guia de Transferência Hospitalar**: Checklist com validação de via venosa com Soro Ringer, algaliação, acompanhante para doação e documentação clínica.
- **Rastreio de Isoimunização Rh**: Deteção de risco em casais (Mãe Rh- / Parceiro Rh+) com recomendação de teste de Coombs Indireto às 30 semanas.

### 📖 3. Livro Eletrónico CPN & Relatórios SIS (MOD-SIS-B01)
- **Grelha Digital MOD-SIS-B01**: 53 colunas de preenchimento oficial do MISAU Moçambique.
- **Resumo Mensal da Unidade Sanitária (MOD-SIS-B01-B)**: Compilação instantânea dos 44 indicadores oficiais com exportação em PDF oficial.
- **Resumo Distrital (MOD-SIS-B01-C)** & **Provincial (MOD-SIS-B01-D)**: Consolidação multinível para a Direção Distrital de Saúde (DDS) e Serviço Provincial de Saúde (SPS).

### 👶 4. Maternidade, Partos & Cuidados ao Recém-Nascido
- Registo completo de partos (eutócico, cesariana, fórceps/vácuo, pélvico).
- Avaliação neonatal com índice **APGAR** ao 1º, 5º e 10º minuto e perímetro cefálico.
- Profilaxias imediatas: **Vitamina K1**, **Tetraciclina oftálmica a 1%**, **Vacina BCG**, **Pólio Zero** e megadose de Vitamina A materna.

### 🔬 5. Laboratório & Exames Críticos
- Fila de trabalho laboratorial e lançamento de resultados.
- Deteção imediata de exames críticos (HIV+, Sífilis/VDRL+, Anemia Grave, Glicemia alterada).
- Gatilho automático de alertas clínicos de alta prioridade com fluxo para resolução e edição de condutas.

### 💬 6. Central Unificada de Notificações & SMS (httpSMS)
- Painel integrado com abas para Notificações do Sistema, Busca Ativa de Gestantes Faltosas, Disparo Individual/Massa de SMS e Modelos MISAU.
- Notificações em tempo real com persistência na base de dados e contadores dinâmicos na navbar.

### 🤖 7. Assistente Clínico Inteligente (IA)
- Assistente conversacional clínico alimentado pelo **Google Gemini 2.5 Flash** (com suporte dinâmico a OpenRouter).
- Apoio à tomada de decisão clínica, esclarecimento de protocolos do MISAU e guias de conduta farmacológica no pré-natal e parto.

---

## 🛠️ Arquitetura & Tecnologias

- **Backend**: [Laravel 12.x](https://laravel.com) / PHP 8.2+
- **Frontend**: Blade Templates, [Tailwind CSS 3.x](https://tailwindcss.com), [Alpine.js 3.x](https://alpinejs.dev)
- **Gráficos & Visualização**: [Chart.js](https://www.chartjs.org/)
- **Alertas & Feedback**: [SweetAlert2](https://sweetalert2.github.io/)
- **Geração de PDF**: [Barryvdh Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **Leitura de Código**: [Html5-QRCode](https://github.com/mebjas/html5-qrcode)
- **Integração SMS**: [httpSMS API](https://httpsms.com/) (+258 Moçambique)
- **Controlo de Acesso (RBAC)**: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- **Base de Dados**: MySQL / MariaDB / SQLite

---

## 🚀 Instalação & Configuração Local

### Pré-requisitos
- PHP $\ge$ 8.2 com extensões `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- Composer $\ge$ 2.x
- Node.js $\ge$ 18.x & NPM
- Servidor MySQL ou SQLite

### Passo a Passo

1. **Clonar o Repositório**:
   ```bash
   git clone https://github.com/filipeive/maternidade_plus.git
   cd maternidade_plus
   ```

2. **Instalar Dependências PHP**:
   ```bash
   composer install
   ```

3. **Instalar Dependências Frontend**:
   ```bash
   npm install
   npm run build
   # ou para desenvolvimento com hot-reload:
   npm run dev
   ```

4. **Configurar o Ambiente**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Ajustar as Variáveis do `.env`**:
   ```env
   APP_NAME="Maternidade+"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=maternidade_plus
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha

   # Integração SMS (httpSMS)
   HTTPSMS_API_KEY=sua_chave_aqui
   HTTPSMS_FROM_NUMBER=+258840000000

   # Inteligência Artificial (Google Gemini)
   GEMINI_API_KEY=sua_chave_gemini
   GEMINI_MODEL=gemini-2.5-flash
   ```

6. **Executar Migrações & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

7. **Iniciar o Servidor de Desenvolvimento**:
   ```bash
   php artisan serve
   ```
   Aceda a `http://localhost:8000` no seu navegador.

---

## 👥 Perfis de Acesso Padrão (Seeders)

| Papel (Role) | E-mail Padrão | Responsabilidades |
| :--- | :--- | :--- |
| **Administrador** | `admin@maternidade.mz` | Gestão de utilizadores, auditoria, configurações e relatórios executivos |
| **Médico** | `medico@maternidade.mz` | Gestão ARO, conduta clínica avançada e transferências |
| **Enfermeiro (ESMI)** | `enfermeira@maternidade.mz` | CPN, puerpério, vacinação, profilaxias e livro MOD-SIS-B01 |
| **Laboratorista** | `lab@maternidade.mz` | Registo e processamento de exames e validação de resultados críticos |

---

## 📚 Documentação do Projeto (`/docs`)

O projeto possui uma documentação técnica viva e detalhada no diretório [`/docs`](docs/):

- [`docs/00-getting-started/`](docs/00-getting-started/project-overview.md) — Visão geral, glossário clínico e guias de início.
- [`docs/01-architecture/`](docs/01-architecture/system-architecture.md) — Arquitetura, estrutura de pastas e ciclo de vida.
- [`docs/02-requirements/`](docs/02-requirements/business-rules.md) — Requisitos e regras de negócio oficiais MISAU.
- [`docs/03-database/`](docs/03-database/database-design.md) — Esquemas de tabelas e diagramas relacionais.
- [`docs/04-backend/`](docs/04-backend/routes.md) — Controladores, modelos, serviços e rotas.
- [`docs/05-frontend/`](docs/05-frontend/blade.md) — Estrutura Blade e design system Tailwind.
- [`docs/06-features/`](docs/06-features/mod-sis-b01.md) — Especificação técnica dos módulos.
- [`docs/99-project-status/`](docs/99-project-status/project-checkpoint.md) — Checkpoints, changelog e roadmap.

---

## 📄 Licença & Direitos

Projeto desenvolvido com foco na melhoria dos indicadores de saúde materna e neonatal em Moçambique. Todos os direitos reservados.
