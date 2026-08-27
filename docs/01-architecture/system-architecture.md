# 🏗️ Arquitetura do Sistema — Maternidade+

O **Maternidade+** foi desenvolvido adotando a arquitetura em camadas no padrão **MVC (Model-View-Controller)** com serviços desacoplados para integrações externas (SMS, Inteligência Artificial, Relatórios PDF).

---

## 📐 Visão Geral dos Componentes

```mermaid
graph TD
    Client["Navegador Web / Dispositivo Móvel"] --> Router["Laravel Router (routes/web.php)"]
    Router --> Middleware["Auth & Role Middleware (Spatie)"]
    Middleware --> Controller["Controllers (App/Http/Controllers)"]
    
    Controller --> Model["Eloquent Models (App/Models)"]
    Model --> DB[("Base de Dados MySQL")]
    
    Controller --> Service1["SmsService (httpSMS API)"]
    Controller --> Service2["AiAssistantService (Google Gemini Direct)"]
    Controller --> Service3["DomPDF (Exportação de PDFs)"]
    
    Controller --> View["Blade Views (resources/views) + Tailwind CSS + Alpine.js"]
    View --> Client
```

---

## 🔒 Camada de Controlo de Acesso (RBAC)

O controlo de acessos a rotas e menus é regulado pela biblioteca `Spatie/laravel-permission`:
- Rotas sensíveis (como `/users` e `/settings`) são estritamente protegidas por `middleware('role:Administrador')`.
- O menu lateral no layout `resources/views/layouts/app-tw.blade.php` adapta-se dinamicamente às permissões do utilizador autenticado via directivas `@hasrole`.
