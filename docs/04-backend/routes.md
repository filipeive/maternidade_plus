# 🗺️ Mapeamento de Rotas — Maternidade+

Rotas web principais registradas no `routes/web.php`.

---

```
+-----------+---------------------------------------+---------------------------------------+
| Método    | URI                                   | Ação / Controller                     |
+-----------+---------------------------------------+---------------------------------------+
| GET       | /dashboard                            | DashboardController@index             |
| GET       | /notifications                        | NotificationController@index          |
| GET       | /notifications/api/list               | NotificationController@apiList        |
| PATCH     | /notifications/{id}/mark-read         | NotificationController@markRead       |
| POST      | /notifications/mark-all-read          | NotificationController@markAllRead    |
| DELETE    | /notifications/{id}                   | NotificationController@destroy        |
| GET       | /alertas                              | AlertaController@index                |
| POST      | /alertas/{alerta}/marcar-lido         | AlertaController@marcarLido           |
| POST      | /alertas/marcar-todos-lidos           | AlertaController@marcarTodosLidos     |
| POST      | /alertas/{alerta}/transitar           | AlertaController@transitar            |
| POST      | /alertas/{alerta}/resolver            | AlertaController@resolver             |
| GET       | /patients                             | PatientController@index               |
| GET       | /patients/create                      | PatientController@create              |
| GET       | /consultations                        | ConsultationController@index          |
| GET       | /consultations/create/{patient?}      | ConsultationController@create         |
| PATCH     | /consultations/{consultation}/complete| ConsultationController@complete       |
| GET       | /mod-sis-b01                          | ModSisB01Controller@index             |
| GET       | /mod-sis-b01/resumo-mensal            | ModSisB01Controller@resumoMensal      |
| GET       | /mod-sis-b01/resumo-mensal/pdf        | ModSisB01Controller@exportPdf         |
| GET       | /home_visits/daily-schedule           | HomeVisitController@dailySchedule     |
| GET       | /sms/center                           | NotificationController@index          |
| POST      | /sms/send-single                      | SmsNotificationController@sendSingle  |
| POST      | /sms/send-bulk                        | SmsNotificationController@sendBulk    |
| GET       | /users                                | UserController@index [Admin Only]     |
| GET       | /settings                             | SettingController@index [Admin Only]  |
+-----------+---------------------------------------+---------------------------------------+
```
