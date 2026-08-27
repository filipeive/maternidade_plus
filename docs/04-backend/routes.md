# 🗺️ Mapeamento de Rotas — Maternidade+

Rotas web principais registradas no `routes/web.php`.

---

```
+-----------+---------------------------------------+---------------------------------------+
| Método    | URI                                   | Ação / Controller                     |
+-----------+---------------------------------------+---------------------------------------+
| GET       | /dashboard                            | DashboardController@index             |
| GET       | /patients                             | PatientController@index               |
| GET       | /patients/create                      | PatientController@create              |
| GET       | /consultations                        | ConsultationController@index          |
| GET       | /consultations/create/{patient?}      | ConsultationController@create         |
| PATCH     | /consultations/{consultation}/complete| ConsultationController@complete       |
| GET       | /mod-sis-b01                          | ModSisB01Controller@index             |
| GET       | /mod-sis-b01/resumo-mensal            | ModSisB01Controller@resumoMensal      |
| GET       | /mod-sis-b01/resumo-mensal/pdf        | ModSisB01Controller@exportPdf         |
| GET       | /home_visits/daily-schedule           | HomeVisitController@dailySchedule     |
| GET       | /sms/center                           | SmsNotificationController@index       |
| POST      | /sms/send-individual                  | SmsNotificationController@sendIndividual|
| GET       | /users                                | UserController@index [Admin Only]     |
| GET       | /settings                             | SettingController@index [Admin Only]  |
+-----------+---------------------------------------+---------------------------------------+
```
