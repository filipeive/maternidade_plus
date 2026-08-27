# 📦 Modelos Eloquent — Maternidade+

Descrição dos principais modelos e atributos computados.

---

### `Patient.php`
- **Acessores**:
  - `idade`: Idade computada a partir de `data_nascimento`.
  - `getSemanasGestacionaisNaData(Carbon $data)`: Calcula as semanas gestacionais numa data específica a partir da DUM ou DPP.

### `Birth.php`
- **Acessores**:
  - `data_parto`: Aliased accessor para `data_hora_parto` garantindo compatibilidade sintática.

### `Consultation.php`
- **Acessores**:
  - `tipo_consulta_label`: Rótulo legível do tipo de consulta (`1º Trimestre`, `2º Trimestre`, `3º Trimestre`, `Pós-Parto / Puerpério`, `Emergência`).
