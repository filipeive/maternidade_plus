<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemNotification extends Model
{
    use HasFactory;

    protected $table = 'system_notifications';

    protected $fillable = [
        'user_id',
        'patient_id',
        'tipo',
        'titulo',
        'mensagem',
        'icone',
        'cor',
        'url',
        'lido',
        'lido_em',
        'lido_por',
    ];

    protected $casts = [
        'lido' => 'boolean',
        'lido_em' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lido_por');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeNaoLidos($query)
    {
        return $query->where('lido', false);
    }

    public function scopeParaUsuario($query, ?int $userId = null)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id');
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        });
    }

    public function marcarComoLido(?User $user = null): void
    {
        $this->update([
            'lido' => true,
            'lido_em' => now(),
            'lido_por' => $user?->id ?? auth()->id(),
        ]);
    }
}

