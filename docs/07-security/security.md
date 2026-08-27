# 🔐 Segurança & RBAC — Maternidade+

Diretrizes de segurança, privacidade de dados médicos de pacientes e controlo de acessos.

---

## 🛡️ Medidas de Proteção

1. **RBAC (Role-Based Access Control)**:
   - Utilização de `spatie/laravel-permission`.
   - Restrição estrita de `/users` e `/settings` apenas a Administradores (`role:Administrador`).
2. **Proteção CSRF**: Todas as requisições de formulários (`POST`, `PATCH`, `DELETE`) contêm o token `@csrf`.
3. **Injeção SQL**: Utilização exclusiva de *Prepared Statements* do Eloquent ORM.
4. **Sanitização de Saídas**: Utilização de `{{ $var }}` do Blade para prevenção de ataques XSS.
