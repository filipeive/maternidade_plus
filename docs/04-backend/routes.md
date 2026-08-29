# 🗺️ Mapeamento de Rotas — Maternidade+

Rotas web e endpoints da aplicação registrados em `routes/web.php`.

---

## 🚨 1. Módulo de Alertas Clínicos (`/alertas`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/alertas` | `alertas.index` | `AlertaController@index` |
| `POST` | `/alertas/avaliar-todos` | `alertas.avaliar-todos` | `AlertaController@avaliarTodos` |
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
| `POST` | `/patients/{patient}/transfer` | `patients.transfer` | `PatientController@transfer` |
| `POST` | `/patients/{patient}/reactivate` | `patients.reactivate` | `PatientController@reactivate` |
| `GET` | `/patients/{patient}/transfer-guide/pdf` | `patients.transfer-guide.pdf` | `PatientController@transferGuidePdf` |
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

## 👥 9. Administração & Configurações (`/users`, `/settings` [Admin Only])

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/users` | `users.index` | `UserController@index` |
| `GET` | `/users/create` | `users.create` | `UserController@create` |
| `POST` | `/users` | `users.store` | `UserController@store` |
| `GET` | `/settings` | `settings.index` | `SettingsController@index` |
| `PATCH` | `/settings/general` | `settings.update-general` | `SettingsController@updateGeneral` |
| `PATCH` | `/settings/sms` | `settings.update-sms` | `SettingsController@updateSms` |
| `PATCH` | `/settings/ai` | `settings.update-ai` | `SettingsController@updateAi` |
| `PATCH` | `/settings/clinical` | `settings.update-clinical` | `SettingsController@updateClinical` |
| `PATCH` | `/settings/community` | `settings.update-community` | `SettingsController@updateCommunity` |
| `GET` | `/settings/backup` | `settings.backup` | `SettingsController@backupSettings` |
| `POST` | `/settings/clear-cache` | `settings.clear-cache` | `SettingsController@clearCache` |
| `POST` | `/settings/clear-logs` | `settings.clear-logs` | `SettingsController@clearLogs` |

---

## 🏡 10. Visitas Domiciliárias & Busca Ativa Comunitária / APEs (`/home_visits`)

| Método | URI | Nome da Rota | Ação / Controller |
| :--- | :--- | :--- | :--- |
| `GET` | `/home_visits` | `home_visits.index` | `HomeVisitController@index` |
| `GET` | `/home_visits/create` | `home_visits.create` | `HomeVisitController@create` |
| `POST` | `/home_visits` | `home_visits.store` | `HomeVisitController@store` |
| `GET` | `/home_visits/daily-schedule` | `home_visits.daily-schedule` | `HomeVisitController@dailySchedule` |
| `GET` | `/home_visits/active-search` | `home_visits.active-search` | `HomeVisitController@activeSearch` |
| `POST` | `/home_visits/schedule-active-search` | `home_visits.schedule-active-search` | `HomeVisitController@scheduleActiveSearch` |
| `POST` | `/home_visits/refer-patient` | `home_visits.refer-patient` | `HomeVisitController@referPatient` |
| `GET` | `/home_visits/route-planning` | `home_visits.route-planning` | `HomeVisitController@routePlanning` |
| `GET` | `/home_visits/report` | `home_visits.generate-report` | `HomeVisitController@generateReport` |
| `GET` | `/home_visits/by-patient/{patient}` | `home_visits.by-patient` | `HomeVisitController@byPatient` |
| `GET` | `/home_visits/{homeVisit}` | `home_visits.show` | `HomeVisitController@show` |
| `GET` | `/home_visits/{homeVisit}/edit` | `home_visits.edit` | `HomeVisitController@edit` |
| `PUT` | `/home_visits/{homeVisit}` | `home_visits.update` | `HomeVisitController@update` |
| `DELETE` | `/home_visits/{homeVisit}` | `home_visits.destroy` | `HomeVisitController@destroy` |
| `PUT` | `/home_visits/{homeVisit}/mark-not-found` | `home_visits.mark-not-found` | `HomeVisitController@markAsNotFound` |
| `PUT` | `/home_visits/{homeVisit}/complete` | `home_visits.complete` | `HomeVisitController@complete` |
| `PUT` | `/home_visits/{homeVisit}/reschedule` | `home_visits.reschedule` | `HomeVisitController@reschedule` |
| `PUT` | `/home_visits/{homeVisit}/resolve-at-facility` | `home_visits.resolve-at-facility` | `HomeVisitController@resolveAtFacility` |

