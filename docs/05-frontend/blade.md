# 🎨 Frontend & Vistas Blade — Maternidade+

A camada de apresentação do **Maternidade+** é construída com o motor de templates **Blade**, estilizada com **Tailwind CSS** e dinamizada com **Alpine.js** e **Chart.js**.

---

## 🏛️ Estrutura de Vistas (`resources/views`)

- **`layouts/app-tw.blade.php`**: Layout base principal Tailwind CSS com Sidebar, Header responsivo, leitor de mensagens flash SweetAlert2 e directiva `@stack('scripts')`.
- **`mod_sis_b01/`**: Vistas do Livro Eletrónico CPN, Resumo Mensal Estatístico e PDF.
- **`consultations/`**: Index com filtros de hoje/faltosas, modal de conclusão e agendamento de CPN.
- **`home_visits/`**: Agenda diária, rotas e busca ativa de faltosas.
- **`alertas/`**: Painel de vigilância ARO e gráficos de desempenho M&E.
