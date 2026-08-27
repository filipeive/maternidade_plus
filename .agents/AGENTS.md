# 🤖 Diretrizes & Regras do Agente AI — Maternidade+

## 📚 Manutenção da Documentação (`docs/`)

- **Regra da Documentação Viva**: Sempre que for efetuada uma alteração no código do sistema (novos módulos, alteração de tabelas, novos controladores, rotas, regras de negócio ou fluxos de deploy), o agente **DEVE atualizar obrigatoriamente a documentação correspondente no diretório `/docs`**.
- **Ficheiros Principais de Acompanhamento**:
  - `docs/99-project-status/changelog.md`: Registar as alterações na seção `[Unreleased]` ou na nova versão.
  - `docs/99-project-status/project-checkpoint.md`: Atualizar o estado das funcionalidades ativas.
  - `docs/04-backend/routes.md`: Manter o mapeamento de rotas sincronizado.
  - `docs/03-database/`: Atualizar esquemas de tabelas e diagramas ER quando migrações forem adicionadas ou alteradas.
