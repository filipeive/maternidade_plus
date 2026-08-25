<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alerta extends Model
{
    use HasFactory;

    const NIVEL_BAIXO = 'baixo';
    const NIVEL_MEDIO = 'medio';
    const NIVEL_ALTO = 'alto';

    const STATUS_ATIVO = 'ativo';
    const STATUS_EM_SEGUIMENTO = 'em_seguimento';
    const STATUS_RESOLVIDO = 'resolvido';
    const STATUS_IGNORADO = 'ignorado';

    protected $fillable = [
        'patient_id',
        'consultation_id',
        'tipo',
        'nivel',
        'mensagem',
        'dados',
        'status',
        'resolvido_por',
        'nota_resolucao',
        'resolvido_em',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'dados' => 'array',
        'resolvido_em' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function resolvidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolvido_por');
    }

    public function acoes(): HasMany
    {
        return $this->hasMany(AlertaAcao::class, 'alerta_id');
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class, 'alerta_id');
    }

    public function scopeAtivos($query)
    {
        return $query->whereIn('status', [self::STATUS_ATIVO, self::STATUS_EM_SEGUIMENTO]);
    }

    public function scopeNivel($query, string $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopeAlto($query)
    {
        return $query->where('nivel', self::NIVEL_ALTO);
    }

    public function getNivelLabelAttribute(): string
    {
        return match ($this->nivel) {
            self::NIVEL_ALTO => 'Alto',
            self::NIVEL_MEDIO => 'Médio',
            self::NIVEL_BAIXO => 'Baixo',
            default => 'Não definido',
        };
    }

    public function getNivelCorAttribute(): string
    {
        return match ($this->nivel) {
            self::NIVEL_ALTO => 'danger',
            self::NIVEL_MEDIO => 'warning',
            self::NIVEL_BAIXO => 'info',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_EM_SEGUIMENTO => 'Em Seguimento',
            self::STATUS_RESOLVIDO => 'Resolvido',
            self::STATUS_IGNORADO => 'Ignorado',
            default => 'Desconhecido',
        };
    }

    public function getStatusCorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ATIVO => 'danger',
            self::STATUS_EM_SEGUIMENTO => 'warning',
            self::STATUS_RESOLVIDO => 'success',
            self::STATUS_IGNORADO => 'secondary',
            default => 'secondary',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'pressao_arterial_alta' => 'Pressão Arterial Elevada',
            'pressao_arterial_grave' => 'Pressão Arterial Grave',
            'bcf_anormal' => 'BCF Anormal',
            'gestante_faltosa' => 'Gestante Faltosa',
            'alto_risco_sem_seguimento' => 'Alto Risco Sem Seguimento',
            'vacinas_em_atraso' => 'Vacinas em Atraso',
            'exames_criticos' => 'Exames Críticos',
            'ganho_peso_anormal' => 'Ganho de Peso Anormal',
            'idade_gestacional_pos_termo' => 'Gestação Pós-Termo',
            'sangramento_reportado' => 'Sangramento Reportado',
            default => ucfirst(str_replace('_', ' ', $this->tipo)),
        };
    }

    public function transitarStatus(string $novoStatus, User $user, ?string $nota = null): void
    {
        $antigoStatus = $this->status;

        $updates = ['status' => $novoStatus];
        if ($novoStatus === self::STATUS_RESOLVIDO) {
            $updates['resolvido_por'] = $user->id;
            $updates['nota_resolucao'] = $nota;
            $updates['resolvido_em'] = now();
        }

        $this->update($updates);

        $this->acoes()->create([
            'user_id' => $user->id,
            'de_status' => $antigoStatus,
            'para_status' => $novoStatus,
            'status_anterior' => $antigoStatus,
            'status_novo' => $novoStatus,
            'nota' => $nota,
        ]);
    }

    public function marcarResolvido(User $user, ?string $nota = null): void
    {
        $this->transitarStatus(self::STATUS_RESOLVIDO, $user, $nota);
    }

    public function marcarEmSeguimento(User $user, ?string $nota = null): void
    {
        $this->transitarStatus(self::STATUS_EM_SEGUIMENTO, $user, $nota);
    }

    public function marcarIgnorado(User $user, ?string $nota = null): void
    {
        $this->transitarStatus(self::STATUS_IGNORADO, $user, $nota);
    }
}
