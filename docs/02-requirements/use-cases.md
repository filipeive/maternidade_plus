# 🎭 Casos de Uso — Maternidade+

Descrição detalhada dos principais casos de uso operacionais do sistema.

---

## 📌 UC-01: Realizar Atendimento de Consulta CPN & Agendar Próxima

- **Ator Principal**: Enfermeira ESMI / Médico.
- **Pré-condição**: Utilizador autenticado e paciente cadastrada.
- **Fluxo Principal**:
  1. A enfermeira acede à lista de consultas (`/consultations`) ou ao perfil da paciente.
  2. Clica no botão **"Concluir"** ou **"Marcar Realizada"** no registo da consulta.
  3. O modal interativo Alpine.js é exibido.
  4. A enfermeira preenche as *Notas Clínicas* e *Orientações Médicas*.
  5. Seleciona a *Data e Hora da Próxima Consulta* (sugerida automaticamente para +4 semanas).
  6. Mantém marcadas as opções `[x] Criar agendamento automático` e `[x] Enviar SMS de lembrete`.
  7. Clica em **"Gravar & Enviar SMS"**.
  8. O sistema atualiza a consulta atual para `realizada`, cria o registo da próxima consulta com status `agendada` e envia o SMS via httpSMS.
  9. Um SweetAlert Toast confirma a operação com sucesso.

---

## 📌 UC-02: Gerar Resumo Mensal Estatístico MISAU (MOD-SIS-B01-B)

- **Ator Principal**: Enfermeira-Chefe / Administrador.
- **Pré-condição**: Registos de CPN e profilaxias efetuados durante o mês.
- **Fluxo Principal**:
  1. O utilizador navega para `/mod-sis-b01/resumo-mensal`.
  2. Seleciona o *Mês e Ano de Referência* (ex: `2026-08`).
  3. O sistema calcula automaticamente todos os 44 indicadores MISAU (Nº de 1ªs CPN por idade, captação precoce ≤12sem, coorte de 6 meses com ≥4 consultas, TIP Malária, PTV HIV, VAT e desnutrição).
  4. O utilizador clica em **"Exportar PDF Oficial"**.
  5. O PDF `MOD-SIS-B01-B_Resumo_Mensal_2026-08.pdf` é descarregado para envio às autoridades de saúde.
