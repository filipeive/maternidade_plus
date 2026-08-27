# 📁 Estrutura de Pastas — Maternidade+

Organização de diretórios do projeto **Maternidade+**.

```
maternidade_plus/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores da aplicação MVC
│   │   │   ├── AlertaController.php
│   │   │   ├── AlertaMetricasController.php
│   │   │   ├── BirthController.php
│   │   │   ├── ConsultationController.php
│   │   │   ├── ExamController.php
│   │   │   ├── HomeVisitController.php
│   │   │   ├── ModSisB01Controller.php
│   │   │   ├── PatientController.php
│   │   │   ├── SmsNotificationController.php
│   │   │   └── UserController.php
│   │   └── Middleware/           # Middlewares de transformação e segurança
│   ├── Models/                   # Modelos Eloquent
│   │   ├── Alerta.php
│   │   ├── AntenatalHistory.php
│   │   ├── Birth.php
│   │   ├── Consultation.php
│   │   ├── Exam.php
│   │   ├── HomeVisit.php
│   │   ├── MaternalProphylaxis.php
│   │   ├── Patient.php
│   │   ├── SmsLog.php
│   │   └── User.php
│   └── Services/                 # Serviços desacoplados
│       ├── AiAssistantService.php
│       └── SmsService.php
├── config/                       # Ficheiros de configuração Laravel
├── database/
│   ├── migrations/               # Esquemas de banco de dados
│   └── seeders/                  # Dados iniciais e permissões RBAC
├── docs/                         # Documentação técnica completa
├── public/                       # Assets públicos compilados (Vite, CSS, JS)
├── resources/
│   ├── css/                      # Estilos Tailwind CSS
│   ├── js/                       # Scripts JavaScript frontend
│   └── views/                    # Vistas Blade
│       ├── alertas/
│       ├── births/
│       ├── consultations/
│       ├── exams/
│       ├── home_visits/
│       ├── layouts/              # app-tw.blade.php
│       ├── mod_sis_b01/          # Livro CPN & Resumo Mensal MISAU
│       ├── patients/
│       ├── sms/
│       └── users/
├── routes/
│   └── web.php                   # Mapeamento de rotas web
├── deploy.sh                     # Pipeline automatizado de deploy em produção
└── tailwind.config.js            # Configuração do Design System Tailwind CSS
```
