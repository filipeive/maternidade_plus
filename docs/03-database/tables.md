# 📋 Esquema das Tabelas da Base de Dados — Maternidade+

Detalhamento das colunas e tipos das tabelas principais.

---

### Tabela `patients`
- `id`: bigint, PK
- `nome_completo`: string
- `data_nascimento`: date
- `documento_bi`: string, nullable
- `contacto`: string, nullable
- `contacto_emergencia`: string, nullable
- `semanas_gestacao`: integer, nullable
- `data_ultima_menstruacao`: date, nullable
- `data_provavel_parto`: date, nullable
- `status_atual`: string (`nao_gestante`, `gestante`, `pos_parto`)
- `ativo`: boolean

### Tabela `consultations`
- `id`: bigint, PK
- `patient_id`: foreignId -> `patients.id`
- `user_id`: foreignId -> `users.id`
- `data_consulta`: datetime
- `tipo_consulta`: string (`1_trimestre`, `2_trimestre`, `3_trimestre`, `pos_parto`, `emergencia`)
- `semanas_gestacao`: integer, nullable
- `peso`: decimal(5,2), nullable
- `pressao_arterial`: string, nullable
- `batimentos_fetais`: integer, nullable
- `altura_uterina`: decimal(4,1), nullable
- `observacoes`: text, nullable
- `orientacoes`: text, nullable
- `proxima_consulta`: datetime, nullable
- `status`: string (`agendada`, `confirmada`, `realizada`, `cancelada`)

### Tabela `maternal_prophylaxes`
- `id`: bigint, PK
- `patient_id`: foreignId -> `patients.id`
- `sp_1_dose`, `sp_2_dose`, `sp_3_dose`, `sp_4_dose`: date, nullable
- `remtil_entregue`: boolean
- `vat_1_dose` a `vat_5_dose`, `vat_reforco`: date, nullable
- `hiv_status_entrada`, `sifilis_resultado`: string, nullable
- `sal_ferroso_folico_3doses`: boolean
- `mebendazol_administrado`: date, nullable

### Tabela `alertas`
- `id`: bigint, PK
- `patient_id`: foreignId -> `patients.id`
- `consultation_id`: foreignId -> `consultations.id`, nullable
- `tipo`: string (regra clínica)
- `nivel`: string (`alto`, `medio`, `baixo`)
- `mensagem`: text
- `dados`: json, nullable
- `status`: string (`ativo`, `em_seguimento`, `resolvido`, `ignorado`)
- `lido`: boolean, default false
- `resolvido_por`: foreignId -> `users.id`, nullable
- `nota_resolucao`: text, nullable
- `resolvido_em`: datetime, nullable

### Tabela `system_notifications`
- `id`: bigint, PK
- `user_id`: foreignId -> `users.id`, nullable (notificação individual ou global)
- `patient_id`: foreignId -> `patients.id`, nullable
- `tipo`: string (`alerta_clinico`, `consulta_faltosa`, `exame_pronto`, `vacina_atraso`, `visita_referencia`, `sistema`)
- `titulo`: string
- `mensagem`: text
- `icone`: string
- `cor`: string (`danger`, `warning`, `info`, `success`)
- `url`: string, nullable
- `lido`: boolean, default false
- `lido_em`: datetime, nullable
- `lido_por`: foreignId -> `users.id`, nullable
- `created_at`, `updated_at`: timestamps
