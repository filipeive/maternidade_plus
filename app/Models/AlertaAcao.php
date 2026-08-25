<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaAcao extends Model
{
    use HasFactory;

    protected $table = 'alerta_acoes';

    protected $fillable = [
        'alerta_id',
        'user_id',
        'de_status',
        'para_status',
        'status_anterior',
        'status_novo',
        'nota',
    ];

    public function alerta(): BelongsTo
    {
        return $this->belongsTo(Alerta::class, 'alerta_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
