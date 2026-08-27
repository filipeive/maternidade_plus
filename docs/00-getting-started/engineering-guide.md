# 🛠️ Guia de Engenharia & Boas Práticas — Maternidade+

Este guia estabelece os padrões de código, princípios de desenvolvimento e convenções adotadas no projeto **Maternidade+**.

---

## 📏 Padrões de Código & Convenções

### Backend (PHP / Laravel)
- **Convenção Naming**: CamelCase para métodos (`getSemanasGestacionaisNaData`), snake_case para colunas de BD (`data_hora_parto`, `tipo_consulta`), PascalCase para Models e Controllers.
- **Relacionamentos Eloquent**: Utilizar métodos descritivos e tipos explícitos (`belongsTo`, `hasMany`, `hasOne`).
- **Validação de Formulários**: Validar sempre os campos no Controller ou FormRequests dedicados antes de persisti-los.

### Frontend (Blade & Tailwind CSS)
- **Design System**: Utilizar a paleta unificada Tailwind CSS definida em `tailwind.config.js` (`brand-600` para verde institucional MISAU, `ocean-600` para azul saúde, `crimson-600` para alertas/erros, `gold-500` para notificações de atenção).
- **Notificações**: Utilizar **exclusivamente SweetAlert2** (`SwalToast` para notificações temporárias e `Swal.fire` modais para erros de validação `$errors->any()`). Evitar colocar banners estáticos HTML duplicados.
- **Micro-interações**: Usar Alpine.js (`x-data`, `x-show`, `@click`) para modais de ação rápida e formulários dinâmicos.

---

## ⚡ Fluxo de Deploy
O deploy em produção é automatizado através do script `./deploy.sh`:
```bash
./deploy.sh
```
O script executa:
1. Compilação dos assets frontend (`npm run build`).
2. Commit e push para o repositório GitHub (`main`).
3. Atualização automática no servidor de produção (`146.235.224.99`).
4. Execução de migrações (`php artisan migrate`) e otimização dos caches Laravel (`view:cache`, `route:cache`).
