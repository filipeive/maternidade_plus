# 🗺️ Mapeamento de Rotas — Maternidade+

Rotas web e endpoints da aplicação registrados em `routes/web.php`.

---

## 🚨 1. Módulo de Alertas Clínicos (`/alertas`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/alertas` | `alertas.index` | `AlertaController@index` |
| `POST` | `/alertas/marcar-todos-lidos` | `alertas.marcar-todos-lidos` | `AlertaController@marcarTodosLidos` |
| `POST` | `/alertas/{alerta}/marcar-lido` | `alertas.marcar-lido` | `AlertaController@marcarLido` |
| `POST` | `/alertas/{alerta}/transitar` | `alertas.transitar` | `AlertaController@transitar` |
| `POST` | `/alertas/{alerta}/resolver` | `alertas.resolver` | `AlertaController@resolver` |
| `GET` | `/alertas/metricas` | `alertas.metricas` | `AlertaMetricasController@index` |
| `GET` | `/alertas/metricas/export-pdf` | `alertas.metricas.pdf` | `AlertaMetricasController@exportPdf` |

---

## 🩺 2. Gestantes & FPN (`/patients`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/patients` | `patients.index` | `PatientController@index` |
| `GET` | `/patients/create` | `patients.create` | `PatientController@create` |
| `POST` | `/patients` | `patients.store` | `PatientController@store` |
| `GET` | `/patients/{patient}` | `patients.show` | `PatientController@show` |
| `GET` | `/patients/{patient}/edit` | `patients.edit` | `PatientController@edit` |
| `PATCH` | `/patients/{patient}` | `patients.update` | `PatientController@update` |
| `DELETE` | `/patients/{patient}` | `patients.destroy` | `PatientController@destroy` |
| `GET` | `/patients/{patient}/card` | `patients.card` | `PatientController@card` |
| `GET` | `/patients/{patient}/card/pdf` | `patients.card.pdf` | `PatientController@cardPdf` |
| `GET` | `/patients/{patient}/history` | `patients.history` | `PatientController@history` |
| `GET` | `/patients/search/ajax` | `patients.search` | `PatientController@search` |

---

## 📅 3. Consultas Pré-Natais & Pós-Parto (`/consultations`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/consultations` | `consultations.index` | `ConsultationController@index` |
| `GET` | `/consultations/create/{patient?}` | `consultations.create` | `ConsultationController@create` |
| `POST` | `/consultations` | `consultations.store` | `ConsultationController@store` |
| `GET` | `/consultations/{consultation}` | `consultations.show` | `ConsultationController@show` |
| `GET` | `/consultations/{consultation}/edit` | `consultations.edit` | `ConsultationController@edit` |
| `PATCH` | `/consultations/{consultation}` | `consultations.update` | `ConsultationController@update` |
| `DELETE` | `/consultations/{consultation}` | `consultations.destroy` | `ConsultationController@destroy` |
| `PATCH` | `/consultations/{consultation}/complete` | `consultations.complete` | `ConsultationController@complete` |
| `PATCH` | `/consultations/{consultation}/confirm` | `consultations.confirm` | `ConsultationController@confirm` |
| `GET` | `/consultations/patient/{patient}` | `consultations.by-patient` | `ConsultationController@byPatient` |

---

## 📖 4. Livro Eletrónico SIS CPN (`/mod-sis-b01`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/mod-sis-b01` | `mod_sis_b01.index` | `ModSisB01Controller@index` |
| `GET` | `/mod-sis-b01/resumo-mensal` | `mod_sis_b01.resumo_mensal` | `ModSisB01Controller@resumoMensal` |
| `GET` | `/mod-sis-b01/resumo-mensal/pdf` | `mod_sis_b01.resumo_mensal.pdf` | `ModSisB01Controller@exportPdf` |
| `GET` | `/mod-sis-b01/resumo-distrital` | `mod_sis_b01.resumo_distrital` | `ModSisB01Controller@resumoDistrital` |
| `GET` | `/mod-sis-b01/resumo-provincial` | `mod_sis_b01.resumo_provincial` | `ModSisB01Controller@resumoProvincial` |

---

## 👶 5. Maternidade & Partos (`/births`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/births` | `births.index` | `BirthController@index` |
| `GET` | `/patients/{patient}/births/create` | `births.create` | `BirthController@create` |
| `POST` | `/patients/{patient}/births` | `births.store` | `BirthController@store` |
| `GET` | `/births/{birth}` | `births.show` | `BirthController@show` |
| `GET` | `/births/{birth}/edit` | `births.edit` | `BirthController@edit` |
| `PUT` | `/births/{birth}` | `births.update` | `BirthController@update` |
| `POST` | `/patients/{patient}/nova-gestacao` | `births.nova-gestacao` | `BirthController@novaGestacao` |
| `GET` | `/births/relatorio` | `births.relatorio` | `BirthController@relatorio` |

---

## 🔬 6. Exames & Laboratório (`/exams`, `/laboratory`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/exams` | `exams.index` | `ExamController@index` |
| `GET` | `/exams/create` | `exams.create` | `ExamController@create` |
| `POST` | `/exams` | `exams.store` | `ExamController@store` |
| `GET` | `/exams/pending-results` | `exams.pending-results` | `ExamController@pendingResults` |
| `GET` | `/exams/{exam}` | `exams.show` | `ExamController@show` |
| `GET` | `/exams/{exam}/edit` | `exams.edit` | `ExamController@edit` |
| `PUT` | `/exams/{exam}` | `exams.update` | `ExamController@update` |
| `GET` | `/exams/{exam}/result` | `exams.result-form` | `ExamController@resultForm` |
| `POST` | `/exams/{exam}/result` | `exams.store-result` | `ExamController@storeResult` |
| `GET` | `/laboratory` | `laboratory.index` | `LaboratoryController@index` |
| `GET` | `/laboratory/pending-queue` | `laboratory.pending-queue` | `LaboratoryController@pendingQueue` |
| `GET` | `/laboratory/alerts/critical` | `laboratory.critical-alerts` | `LaboratoryController@criticalAlerts` |
| `POST` | `/laboratory/exams/{exam}/process` | `laboratory.process-exam` | `LaboratoryController@processExam` |

---

## 💬 7. Central Unificada de Notificações & SMS (`/notifications`, `/sms`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/notifications` | `notifications.index` | `NotificationController@index` |
| `GET` | `/notifications/api/list` | `notifications.api-list` | `NotificationController@apiList` |
| `GET` | `/notifications/api/count` | `notifications.unread-count` | `NotificationController@unreadCount` |
| `PATCH` | `/notifications/{notification}/mark-read` | `notifications.mark-read` | `NotificationController@markRead` |
| `POST` | `/notifications/mark-all-read` | `notifications.mark-all-read` | `NotificationController@markAllRead` |
| `GET` | `/sms/center` | `sms.index` | `NotificationController@index` |
| `POST` | `/sms/send-single` | `sms.send-single` | `SmsNotificationController@sendSingle` |
| `POST` | `/sms/send-bulk` | `sms.send-bulk` | `SmsNotificationController@sendBulk` |

---

## 🤖 8. Assistente Clínico IA, Scanner & Ajuda (`/help`, `/scanner`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/scanner` | `scanner` | View `scanner.index` |
| `GET` | `/help` | `help.index` | `HelpController@index` |
| `GET` | `/help/manual` | `help.manual` | `HelpController@manual` |
| `POST` | `/help/ai/ask` | `help.ai.ask` | `HelpController@askAi` |

---

## 👥 9. Administração & Configurações (`/users`, `/settings` [Admin Only])

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/users` | `users.index` | `UserController@index` |
| `GET` | `/users/create` | `users.create` | `UserController@create` |
| `POST` | `/users` | `users.store` | `UserController@store` |
| `GET` | `/settings` | `settings.index` | `SettingsController@index` |
| `PATCH` | `/settings/general` | `settings.update-general` | `SettingsController@updateGeneral` |
| `PATCH` | `/settings/notifications` | `settings.update-notifications` | `SettingsController@updateNotifications` |

