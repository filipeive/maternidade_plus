# 🔗 Relacionamentos Eloquent — Maternidade+

Mapeamento das relações Eloquent entre os modelos da aplicação.

---

```php
// Patient.php
public function consultations() { return $this->hasMany(Consultation::class); }
public function births() { return $this->hasMany(Birth::class); }
public function antenatalHistory() { return $this->hasOne(AntenatalHistory::class); }
public function prophylaxis() { return $this->hasOne(MaternalProphylaxis::class)->withDefault(); }
public function alertas() { return $this->hasMany(Alerta::class); }
public function homeVisits() { return $this->hasMany(HomeVisit::class); }
public function smsLogs() { return $this->hasMany(SmsLog::class); }

// Consultation.php
public function patient() { return $this->belongsTo(Patient::class); }
public function user() { return $this->belongsTo(User::class); }
public function exams() { return $this->hasMany(Exam::class); }

// Birth.php
public function patient() { return $this->belongsTo(Patient::class); }
public function user() { return $this->belongsTo(User::class); }

// AntenatalHistory.php
public function patient() { return $this->belongsTo(Patient::class); }

// MaternalProphylaxis.php
public function patient() { return $this->belongsTo(Patient::class); }
```
