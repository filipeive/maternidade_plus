# 📋 Requisitos do Sistema — Maternidade+

Lista de Requisitos Funcionais (RF) e Não-Funcionais (RNF).

---

## 🟢 Requisitos Funcionais (RF)

- **RF-001**: O sistema deve permitir o cadastro, edição e consulta de Gestantes com dados biográficos, NID, documento BI, contacto e histórico médico.
- **RF-002**: O sistema deve efetuar o agendamento de consultas pré-natais (CPN) e de puerpério, selecionando automaticamente o tipo de consulta com base nas semanas gestacionais ou no registo de parto.
- **RF-003**: O sistema deve permitir a conclusão de consultas com inserção de notas clínicas, recomendações à mãe, agendamento automático da próxima consulta e envio de SMS de lembrete.
- **RF-004**: O sistema deve integrar o **Livro Eletrónico CPN (MOD-SIS-B01)** do MISAU, exibindo o progresso das coortes de 6 meses, profilaxias (TIP Malária/SP, VAT, Sal Ferroso, REMTIL) e PTV HIV/Sífilis.
- **RF-005**: O sistema deve compilar em 1-clique o **Resumo Mensal da Unidade Sanitária (MOD-SIS-B01-B)** e exportar o relatório oficial em formato PDF.
- **RF-006**: O sistema deve gerar Alertas Precoces de Risco Obstétrico (ARO) e notificar a equipa de saúde para intervenção proativa.
- **RF-007**: O sistema deve permitir a gestão de visitas domiciliares, planeamento de rotas e busca ativa de pacientes faltosas.
- **RF-008**: O sistema deve incluir um Scanner de QR Code dedicado para identificação rápida do cartão da gestante.

---

## 🔵 Requisitos Não-Funcionais (RNF)

- **RNF-001 (Desempenho)**: O tempo de resposta das páginas web não deve exceder 2 segundos em conexões 3G/4G normais.
- **RNF-002 (Disponibilidade)**: O sistema deve estar disponível 99.5% do tempo no servidor de produção.
- **RNF-003 (Usabilidade)**: A interface deve ser moderna, limpa (Tailwind CSS) e responsiva em computadores, tablets e smartphones.
- **RNF-004 (Segurança)**: O acesso às funcionalidades de administração de utilizadores (`/users`) e configurações (`/settings`) deve ser restrito por middleware de função (`role:Administrador`).
